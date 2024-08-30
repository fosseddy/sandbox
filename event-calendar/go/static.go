package main

import (
	"io/fs"
	"net/http"
	"os"
)

func initStatic() error {
	if err := os.MkdirAll("public", 0700); err != nil {
		return err
	}

	if err := os.MkdirAll(config.uploadDir, 0700); err != nil {
		return err
	}

	http.Handle("GET /", http.FileServer(StaticFiles{http.Dir("public")}))

	return nil
}

type StaticFiles struct {
	http.FileSystem
}

func (s StaticFiles) Open(name string) (http.File, error) {
	f, err := s.FileSystem.Open(name)
	if err != nil {
		return nil, err
	}

	info, err := f.Stat()
	if err != nil {
		return nil, err
	}

	if !info.Mode().IsRegular() {
		return nil, fs.ErrNotExist
	}

	return f, nil
}
