package main

import (
	"database/sql"
	"net/http"
	"regexp"
	"strings"

	"golang.org/x/crypto/bcrypt"
)

type Admin struct {
	ID       int    `json:"_id"`
	Name     string `json:"name"`
	Password string `json:"-"`
}

// art: from 3 to 30 chars long; starts with letter,
// then letter, digit or underscore
var adminNameRegexp = regexp.MustCompile(`^[a-zA-Z]{1}\w{2,29}$`)

func initAdmin() {
	http.HandleFunc("POST /api/admin/login", postLogin)
	http.HandleFunc("POST /api/admin/refresh-token", postRefreshToken)
	http.HandleFunc("GET /api/admin/profile", getProfile)
}

func postLogin(w http.ResponseWriter, r *http.Request) {
	var body struct {
		Name     string
		Password string
	}

	if !withJSONBody(w, r, &body) {
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
	} else if len(body.Password) < 3 || len(body.Password) > 30 {
		errs = errs.add("password", "Неверный пароль")
	}

	if len(errs) > 0 {
		writeErrors(w, 400, errs)
		return
	}

	var admin Admin
	row := database.QueryRow("select id, name, password from admin where name = ?", body.Name)
	if err := row.Scan(&admin.ID, &admin.Name, &admin.Password); err != nil {
		if err == sql.ErrNoRows {
			writeError(w, 400, "name", "Неверное имя администратора")
		} else {
			writeServerError(w, err)
		}
		return
	}

	if err := bcrypt.CompareHashAndPassword([]byte(admin.Password), []byte(body.Password)); err != nil {
		writeError(w, 400, "password", "Неверный пароль")
		return
	}

	access, refresh, err := jwtSignTokenPair(admin.ID)
	if err != nil {
		writeServerError(w, err)
		return
	}

	writeData(w, 200, map[string]any{
		"access":  access,
		"refresh": refresh,
		"profile": admin,
	})
}

func postRefreshToken(w http.ResponseWriter, r *http.Request) {
	var body struct{ Token string }

	if !withJSONBody(w, r, &body) {
		return
	}

	body.Token = strings.TrimSpace(body.Token)

	if body.Token == "" {
		writeError(w, 400, "token", "token is required")
		return
	}

	decoded, err := jwtVerify(body.Token)
	if err != nil {
		writeError(w, 400, "token", "invalid token")
		return
	}

	var id int
	row := database.QueryRow("select id from admin where id = ?", decoded.ID)
	if err := row.Scan(&id); err != nil {
		if err == sql.ErrNoRows {
			writeError(w, 400, "token", "token belongs to non-existent admin")
		} else {
			writeServerError(w, err)
		}
		return
	}

	access, refresh, err := jwtSignTokenPair(decoded.ID)
	if err != nil {
		writeServerError(w, err)
		return
	}

	writeData(w, 200, map[string]any{
		"access":  access,
		"refresh": refresh,
	})
}

func getProfile(w http.ResponseWriter, r *http.Request) {
	var admin Admin

	if !withAdmin(w, r, &admin) {
		return
	}

	writeData(w, 200, admin)
}

func withAdmin(w http.ResponseWriter, r *http.Request, admin *Admin) bool {
	header := r.Header.Get("Authorization")
	if header == "" {
		writeError(w, 401, "header", "authorization header is not set")
		return false
	}

	parts := strings.Split(header, " ")
	if len(parts) != 2 {
		writeError(w, 401, "header", "invalid header")
		return false
	}

	schema, tok := parts[0], parts[1]
	if schema != "Bearer" {
		writeError(w, 401, "header", "invalid schema")
		return false
	}

	decoded, err := jwtVerify(tok)
	if err != nil {
		writeError(w, 401, "header", "invalid token")
		return false
	}

	if decoded.Kind != "access" {
		writeError(w, 401, "token", "invalid token kind")
		return false
	}

	row := database.QueryRow("select id, name from admin where id = ?", decoded.ID)
	if err := row.Scan(&admin.ID, &admin.Name); err != nil {
		if err == sql.ErrNoRows {
			writeError(w, 401, "token", "token belongs to non-existent admin")
		} else {
			writeServerError(w, err)
		}
		return false
	}

	return true
}
