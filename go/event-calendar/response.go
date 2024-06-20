package main

import (
	"net/http"
	"encoding/json"
	"log"
)

type ErrParam struct {
	Param string `json:"param"`
	Message string `json:"message"`
}

type ErrParams []ErrParam

func (eps ErrParams) add(param string, message string) ErrParams {
	return append(eps, ErrParam{param, message})
}

type ErrResponse struct {
	Errors ErrParams `json:"errors"`
}

func writeError(w http.ResponseWriter, status int, param string, message string) {
	eps := ErrParams{{param, message}}
	er := ErrResponse{eps}

	if res, err := json.Marshal(&er); err != nil {
		w.WriteHeader(500)
		log.Println("writeError: json encoding failed:", err)	
	} else {
		w.WriteHeader(status)
		w.Write(res)
	}
}

func writeErrors(w http.ResponseWriter, status int, eps ErrParams) {
	er := ErrResponse{eps}

	if res, err := json.Marshal(&er); err != nil {
		w.WriteHeader(500)
		log.Println("writeError: json encoding failed:", err)	
	} else {
		w.WriteHeader(status)
		w.Write(res)
	}
}

func writeServerError(w http.ResponseWriter, err error) {
	writeError(w, 500, "server", "server error")
	log.Println(err)
}

type DataResponse struct {
	Data map[string]any `json:"data"`
}

type DataArrayResponse struct {
	Data DataArray `json:"data"`
}

type DataArray struct {
	Items []any `json:"items"`
}

func writeData(w http.ResponseWriter, status int, data map[string]any) {
	d := DataResponse{data}

	if res, err := json.Marshal(&d); err != nil {
		w.WriteHeader(500)
		log.Println("writeData: json encoding failed:", err)	
	} else {
		w.WriteHeader(status)
		w.Write(res)
	}
}

func writeDataArray(w http.ResponseWriter, status int, data DataArray) {
	da := DataArrayResponse{data}

	if res, err := json.Marshal(&da); err != nil {
		w.WriteHeader(500)
		log.Println("writeDataArray: json encoding failed:", err)	
	} else {
		w.WriteHeader(status)
		w.Write(res)
	}
}
