package main

import (
	"strings"
	"net/http"
	"regexp"
	"database/sql"
	"golang.org/x/crypto/bcrypt"
)

// art: from 3 to 30 chars long; starts with letter,
// then letter, digit or underscore
var adminNameRegexp = regexp.MustCompile(`^[a-zA-Z]{1}\w{2,29}$`)

func adminInit() {
	http.HandleFunc("POST /api/admin/login", postLogin)
	http.HandleFunc("POST /api/admin/refresh-token", postRefreshToken)
	http.HandleFunc("GET /api/admin/profile", getProfile)
}

func postLogin(w http.ResponseWriter, r *http.Request) {
	var body struct{Name string; Password string}

	if !readBody(w, r, &body) {
		return
	}

	body.Name = strings.TrimSpace(body.Name)
	body.Password = strings.TrimSpace(body.Password)

	var errs ErrParams

    if body.Name == "" {
		errs = errs.add("name", "Введите имя администратора")
    } else if !adminNameRegexp.MatchString(body.Name) {
		errs = errs.add("name", "Неверное имя администратора")
    }

    if body.Password == "" {
		errs = errs.add("password", "Введите пароль")
    } else if (len(body.Password) < 3 || len(body.Password) > 30) {
		errs = errs.add("password", "Неверный пароль")
    }

	if len(errs) > 0 {
		writeErrors(w, 400, errs)
		return
	}

	var id int
	var name string
	var pass string

	row := database.QueryRow("select id, name, password from admin where name = ?", body.Name)
	if err := row.Scan(&id, &name, &pass); err != nil {
		if err == sql.ErrNoRows {
			writeError(w, 400, "name", "Неверное имя администратора")
		} else {
			writeServerError(w, err)
		}
		return
	}

	if err := bcrypt.CompareHashAndPassword([]byte(pass), []byte(body.Password)); err != nil {
		writeError(w, 400, "password", "Неверный пароль")
		return
	}

	access, refresh, err := jwtSignTokenPair(id)
	if err != nil {
		writeServerError(w, err)
		return
	}

	writeData(w, 200, map[string]any{
		"access": access,
		"refresh": refresh,
		"profile": map[string]any{
			"id": id,
			"name": name,
		},
	})
}

func postRefreshToken(w http.ResponseWriter, r *http.Request) {
	http.Error(w, "POST /api/admin/refresh-token: Not Implemented", 404)
}

func getProfile(w http.ResponseWriter, r *http.Request) {
	http.Error(w, "GET /api/admin/profile: Not Implemented", 404)
}
