package main

import (
	"net/http"
	"encoding/json"
	"fmt"
	"io"
	"strings"
)

func readBody(w http.ResponseWriter, r *http.Request, v any) bool {
	d := json.NewDecoder(r.Body)
	if err := d.Decode(v); err != nil {
		if (err == io.EOF) {
			writeError(w, 400, "body", "empty body")
		} else {
			switch err := err.(type) {
			case *json.UnmarshalTypeError:
				s := fmt.Sprintf("%s: wanted %s, but got %s", strings.ToLower(err.Field), err.Type, err.Value)
				writeError(w, 400, "body", s)
			case *json.InvalidUnmarshalError:
				writeServerError(w, err)
			default:
				writeError(w, 400, "body", "invalid json")
			}
		}
		return false
	}
	return true
}

