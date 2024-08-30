package main

import (
	"database/sql"
	"log"
	"net/http"
	"os"
	"path"
	"strings"
)

type Category struct {
	ID   int    `json:"_id"`
	Name string `json:"name"`
}

type CategoryBody struct {
	Name string
}

func initCategory() {
	http.HandleFunc("GET /api/categories", getCategories)
	http.HandleFunc("POST /api/category", postCategory)
	http.HandleFunc("GET /api/category/{id}", getCategory)
	http.HandleFunc("PUT /api/category/{id}", putCategory)
	http.HandleFunc("DELETE /api/category/{id}", deleteCategory)
}

func getCategories(w http.ResponseWriter, r *http.Request) {
	rows, err := database.Query("select id, name from category order by created_at desc")
	if err != nil {
		writeServerError(w, err)
		return
	}

	cats := make([]Category, 0, 32)
	for rows.Next() {
		var cat Category
		if err := rows.Scan(&cat.ID, &cat.Name); err != nil {
			writeServerError(w, err)
			return
		}
		cats = append(cats, cat)
	}

	if err := rows.Err(); err != nil {
		writeServerError(w, err)
		return
	}

	writeDataArray(w, 200, DataArray{cats})
}

func postCategory(w http.ResponseWriter, r *http.Request) {
	var admin Admin
	var body CategoryBody

	if !withAdmin(w, r, &admin) {
		return
	}

	if !withValidBody(w, r, &body, nil) {
		return
	}

	res, err := database.Exec("insert into category (name) values (?)", body.Name)
	if err != nil {
		writeServerError(w, err)
		return
	}

	id, _ := res.LastInsertId()
	writeData(w, 201, id)
}

func getCategory(w http.ResponseWriter, r *http.Request) {
	var admin Admin
	var cat Category

	if !withAdmin(w, r, &admin) {
		return
	}

	if !withCategory(w, r, &cat) {
		return
	}

	writeData(w, 200, cat)
}

func putCategory(w http.ResponseWriter, r *http.Request) {
	var admin Admin
	var cat Category
	var body CategoryBody

	if !withAdmin(w, r, &admin) {
		return
	}

	if !withCategory(w, r, &cat) {
		return
	}

	if !withValidBody(w, r, &body, &cat) {
		return
	}

	if _, err := database.Exec("update category set name = ? where id = ?", body.Name, cat.ID); err != nil {
		writeServerError(w, err)
		return
	}

	writeData(w, 200, cat.ID)
}

func deleteCategory(w http.ResponseWriter, r *http.Request) {
	var (
		admin  Admin
		cat    Category
		images []string
	)

	if !withAdmin(w, r, &admin) {
		return
	}

	if !withCategory(w, r, &cat) {
		return
	}

	rows, err := database.Query("select image from event where category_id = ? and image != ''", cat.ID)
	if err != nil {
		writeServerError(w, err)
		return
	}

	for rows.Next() {
		var img string

		if err := rows.Scan(&img); err != nil {
			writeServerError(w, err)
			return
		}

		images = append(images, img)
	}

	if err := rows.Err(); err != nil {
		writeServerError(w, err)
		return
	}

	if _, err := database.Exec("delete from category where id = ?", cat.ID); err != nil {
		writeServerError(w, err)
		return
	}

	for _, img := range images {
		if err := os.Remove(path.Join(config.uploadDir, img)); err != nil {
			log.Println(err)
		}
	}

	writeData(w, 200, cat.ID)
}

func withCategory(w http.ResponseWriter, r *http.Request, cat *Category) bool {
	row := database.QueryRow("select id, name from category where id = ?", r.PathValue("id"))
	if err := row.Scan(&cat.ID, &cat.Name); err != nil {
		if err == sql.ErrNoRows {
			writeError(w, 404, "id", "category not found")
		} else {
			writeServerError(w, err)
		}
		return false
	}
	return true
}

func withValidBody(w http.ResponseWriter, r *http.Request, body *CategoryBody, cat *Category) bool {
	var errs ErrParams

	if !withJSONBody(w, r, body) {
		return false
	}

	body.Name = strings.TrimSpace(body.Name)
	if body.Name == "" {
		errs = errs.add("name", "Введите название")
	} else if len(body.Name) > 100 {
		errs = errs.add("name", "Название не должно превышать 100 символов")
	} else {
		var id int
		query := "select id from category where name = ?"
		params := []any{body.Name}

		if cat != nil {
			query += " and id != ?"
			params = append(params, cat.ID)
		}

		row := database.QueryRow(query, params...)
		if err := row.Scan(&id); err == nil {
			errs = errs.add("name", "Такая категория уже существует")
		} else {
			if err != sql.ErrNoRows {
				writeServerError(w, err)
				return false
			}
		}
	}

	if len(errs) > 0 {
		writeErrors(w, 400, errs)
		return false
	}

	return true
}
