package main

import (
	"bytes"
	"database/sql"
	"fmt"
	"html/template"
	"io"
	"log"
	"math/rand/v2"
	"net/http"
	"os"
	"path"
	"strconv"
	"strings"
	"time"
)

type Event struct {
	ID         int       `json:"_id"`
	Name       string    `json:"name"`
	Date       time.Time `json:"date"`
	CategoryID int       `json:"category_id"`
	Duration   int       `json:"duration"`
	Location   string    `json:"location"`
	Organizer  string    `json:"organizer"`
	Image      string    `json:"image"`
	Category   Category  `json:"category"`
}

type EventForm struct {
	name      string
	date      time.Time
	category  int
	duration  int
	location  string
	organizer string
	image     string
	file      EventFormFile
	hasFile   bool
}

type EventFormFile struct {
	name string
	data bytes.Buffer
}

func initEvent() {
	http.HandleFunc("GET /api/events", getEvents)
	http.HandleFunc("POST /api/event", postEvent)
	http.HandleFunc("GET /api/event/{id}", getEvent)
	http.HandleFunc("PUT /api/event/{id}", putEvent)
	http.HandleFunc("DELETE /api/event/{id}", deleteEvent)
	http.HandleFunc("GET /event/nearest/{num}", getEventNearest)
}

func getEvents(w http.ResponseWriter, r *http.Request) {
	events := make([]Event, 0, 31)

	d, err := time.Parse("2006-1", r.URL.Query().Get("from"))
	if err != nil {
		writeDataArray(w, 200, DataArray{events})
		return
	}

	y, m, _ := d.Date()
	loc := d.Location()
	start := time.Date(y, m, 1, 0, 0, 0, 0, loc).Format(time.DateTime)
	end := time.Date(y, m+1, 0, 23, 59, 59, 0, loc).Format(time.DateTime)

	rows, err := database.Query(`
select
	id, name, date, category_id, duration, location, organizer, image
from
	event
where
	date between ? and ?`, start, end)

	if err != nil {
		writeServerError(w, err)
		return
	}

	stmt, err := database.Prepare("select id, name from category where id = ?")
	if err != nil {
		writeServerError(w, err)
		return
	}

	defer stmt.Close()

	for rows.Next() {
		var e Event

		err := rows.Scan(&e.ID, &e.Name, &e.Date, &e.CategoryID, &e.Duration, &e.Location, &e.Organizer, &e.Image)
		if err != nil {
			writeServerError(w, err)
			return
		}

		if err := stmt.QueryRow(e.CategoryID).Scan(&e.Category.ID, &e.Category.Name); err != nil {
			writeServerError(w, err)
			return
		}

		events = append(events, e)
	}

	if err := rows.Err(); err != nil {
		writeServerError(w, err)
		return
	}

	writeDataArray(w, 200, DataArray{events})
}

func postEvent(w http.ResponseWriter, r *http.Request) {
	var (
		admin Admin
		form  EventForm
	)

	if !withAdmin(w, r, &admin) {
		return
	}

	if !withEventForm(w, r, &form, nil) {
		return
	}

	if form.hasFile {
		form.image = form.file.name
	}

	tx, err := database.Begin()
	if err != nil {
		writeServerError(w, err)
		return
	}

	res, err := tx.Exec(`
insert into event
	(name, date, category_id, duration, location, organizer, image)
values
	(?, ?, ?, ?, ?, ?, ?)`, form.name, form.date.Format(time.DateTime), form.category, form.duration, form.location,
		form.organizer, form.image)

	if err != nil {
		writeServerError(w, err)
		return
	}

	if form.hasFile {
		var f *os.File
		var err error

		if f, err = os.Create(path.Join(config.uploadDir, form.file.name)); err == nil {
			if _, err = io.Copy(f, &form.file.data); err == nil {
				f.Close()
			}
		}

		if err != nil {
			tx.Rollback()
			writeServerError(w, err)
			return
		}
	}

	if err := tx.Commit(); err != nil {
		tx.Rollback()
		if err := os.Remove(path.Join(config.uploadDir, form.file.name)); err != nil {
			log.Println("remove form.file:", err)
		}
		writeServerError(w, err)
		return
	}

	id, _ := res.LastInsertId()
	writeData(w, 201, id)
}

func getEvent(w http.ResponseWriter, r *http.Request) {
	var (
		admin Admin
		event Event
	)

	if !withAdmin(w, r, &admin) {
		return
	}

	if !withEvent(w, r, &event) {
		return
	}

	row := database.QueryRow("select id, name from category where id = ?", event.CategoryID)
	if err := row.Scan(&event.Category.ID, &event.Category.Name); err != nil {
		writeServerError(w, err)
		return
	}

	writeData(w, 200, event)
}

func putEvent(w http.ResponseWriter, r *http.Request) {
	var (
		admin Admin
		event Event
		form  EventForm
	)

	if !withAdmin(w, r, &admin) {
		return
	}

	if !withEvent(w, r, &event) {
		return
	}

	if !withEventForm(w, r, &form, &event) {
		return
	}

	prevImg := event.Image

	event.Name = form.name
	event.Date = form.date
	event.CategoryID = form.category
	event.Duration = form.duration
	event.Location = form.location
	event.Organizer = form.organizer
	event.Image = form.image
	if form.hasFile {
		event.Image = form.file.name
	}

	_, err := database.Exec(`
update event
set
	name = ?
	date = ?
	category_id = ?
	duration = ?
	location = ?
	organizer = ?
	image = ?
where
	id = ? `, event.Name, event.Date.Format(time.DateTime), event.CategoryID, event.Duration, event.Location,
		event.Organizer, event.Image, event.ID)

	if err != nil {
		writeServerError(w, err)
		return
	}

	if prevImg != "" && prevImg != event.Image {
		if err := os.Remove(path.Join(config.uploadDir, prevImg)); err != nil {
			log.Println(err)
		}
	}

	writeData(w, 200, event.ID)
}

func deleteEvent(w http.ResponseWriter, r *http.Request) {
	var (
		admin Admin
		event Event
	)

	if !withAdmin(w, r, &admin) {
		return
	}

	if !withEvent(w, r, &event) {
		return
	}

	if _, err := database.Exec("delete from event where id = ?", event.ID); err != nil {
		writeServerError(w, err)
		return
	}

	if event.Image != "" {
		if err := os.Remove(path.Join(config.uploadDir, event.Image)); err != nil {
			log.Println(err)
		}
	}

	writeData(w, 200, event.ID)
}

func withEvent(w http.ResponseWriter, r *http.Request, e *Event) bool {
	row := database.QueryRow(`
select
	id, name, date, category_id, duration, location, organizer, image
from
	category
where
	id = ?`, r.PathValue("id"))

	if err := row.Scan(&e.ID, &e.Name, &e.Date, &e.CategoryID, &e.Duration, &e.Location, &e.Organizer, &e.Image); err != nil {
		if err == sql.ErrNoRows {
			writeError(w, 404, "id", "event not found")
		} else {
			writeServerError(w, err)
		}
		return false
	}
	return true
}

func getEventNearest(w http.ResponseWriter, r *http.Request) {
	w.WriteHeader(200)

	num, err := strconv.Atoi(r.PathValue("num"))
	if err != nil || num <= 0 {
		return
	}

	loc, err := time.LoadLocation("Asia/Yekaterinburg")
	if err != nil {
		log.Println(err)
		return
	}

	today := time.Now().In(loc)
	y, m, d := today.Date()
	h, min, s := today.Clock()
	today = time.Date(y, m, d, h, min, s, 0, time.UTC)

	var e Event
	row := database.QueryRow(`
select
	id, name, date, category_id, duration, location
from
	event
where
	date > ?
limit ?, 1`, today.Format(time.DateTime), num-1)

	if err := row.Scan(&e.ID, &e.Name, &e.Date, &e.CategoryID, &e.Duration, &e.Location); err != nil {
		if err != sql.ErrNoRows {
			log.Println(err)
		}
		return
	}

	row = database.QueryRow("select id, name from category where id = ?", e.CategoryID)
	if err := row.Scan(&e.Category.ID, &e.Category.Name); err != nil {
		if err != sql.ErrNoRows {
			log.Println(err)
		}
		return
	}

	t, err := template.ParseFiles("templates/nearest-event.html")
	if err != nil {
		log.Println(err)
		return
	}

	if err := t.Execute(w, e); err != nil {
		log.Println(err)
		return
	}
}

func withEventForm(w http.ResponseWriter, r *http.Request, form *EventForm, event *Event) bool {
	var errs ErrParams
	var err error
	var values map[string]string

	if !withMultipartForm(w, r, values, &form.file) {
		return false
	}

	form.hasFile = form.file.data.Len() > 0
	trim := strings.TrimSpace

	form.name = trim(values["name"])
	if form.name == "" {
		errs = errs.add("name", "name is required")
	} else if len(form.name) > 200 {
		errs = errs.add("name", "name only 200 chars")
	}

	date := trim(values["date"])
	if date == "" {
		errs = errs.add("date", "date is required")
	} else if form.date, err = time.Parse(time.RFC3339, date); err != nil {
		errs = errs.add("date", "date is invalid")
	}

	if form.category, err = strconv.Atoi(trim(values["category"])); err != nil {
		errs = errs.add("category", "expected number")
	} else if form.category == 0 {
		errs = errs.add("category", "category is required")
	} else {
		var id int
		if err := database.QueryRow("select id from category where id = ?", form.category).Scan(&id); err != nil {
			if err == sql.ErrNoRows {
				errs = errs.add("category", "category does not exist")
			} else {
				writeServerError(w, err)
				return false
			}
		}
	}

	if v, ok := values["duration"]; ok {
		if form.duration, err = strconv.Atoi(trim(v)); err != nil {
			errs = errs.add("duration", "expected number")
		} else if form.duration < 0 {
			errs = errs.add("duration", "duration must be greater or equal 0")
		}
	}

	if v, ok := values["location"]; ok {
		form.location = trim(v)
		if len(form.location) > 300 {
			errs = errs.add("location", "location only 300 chars")
		}
	}

	if v, ok := values["organizer"]; ok {
		form.organizer = trim(v)
		if len(form.organizer) > 250 {
			errs = errs.add("organizer", "organizer only 250 chars")
		}
	}

	if v, ok := values["image"]; ok {
		form.image = trim(v)
		if form.image != "" {
			if event == nil {
				errs = errs.add("image", "you can create image only by file upload")
			} else if event.Image != form.image {
				errs = errs.add("image", "you can update image only by file upload")
			}
		}
	}

	if len(errs) > 0 {
		writeErrors(w, 400, errs)
		return false
	}

	return true
}

func withMultipartForm(w http.ResponseWriter, r *http.Request, values map[string]string, file *EventFormFile) bool {
	mr, err := r.MultipartReader()
	if err != nil {
		writeServerError(w, err)
		return false
	}

	const (
		maxFormSize int64 = 1 << 20
		maxFileSize int64 = 5 << 20
	)

	var (
		formSize   int64 = 0
		field      bytes.Buffer
		fileParsed bool
	)

	for {
		p, err := mr.NextPart()
		if err != nil {
			if err == io.EOF {
				break
			}
			writeServerError(w, err)
			return false
		}

		defer p.Close()

		fileName, fieldName := p.FileName(), p.FormName()

		if fileName == "" {
			n, err := io.CopyN(&field, p, maxFormSize)
			if err != nil && err != io.EOF {
				writeServerError(w, err)
				return false
			}

			formSize += n
			if formSize > maxFormSize {
				writeError(w, 400, "form", "exceeded max form size")
				return false
			}

			values[fieldName] = field.String()

			field.Reset()
			continue
		}

		if fieldName != "file" {
			writeError(w, 400, fieldName, "only 'file' field can have file attached")
			return false
		}

		if fileParsed {
			writeError(w, 400, "file", fileName+": only single file is allowed")
			return false
		}

		fileParsed = true

		ct := p.Header.Get("Content-Type")
		if ct != "image/jpeg" && ct != "image/png" {
			writeError(w, 400, "file", "invalid MIME type")
			return false
		}

		n, err := io.CopyN(&file.data, p, maxFileSize+1)
		if err != nil && err != io.EOF {
			writeServerError(w, err)
			return false
		}

		if n > maxFileSize {
			writeError(w, 400, "file", "exceeded max file size")
			return false
		}

		randstr := strconv.FormatInt(rand.Int64(), 36) + strconv.FormatInt(time.Now().UnixNano(), 36)
		file.name = fmt.Sprintf("%.%", randstr, path.Base(ct))
	}

	return true
}
