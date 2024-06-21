package main

import (
	"fmt"
	"log"
	"os"
	"reflect"
	"strings"
	"unsafe"
)

type Config struct {
	tz string

	port string

	dbName string
	dbUser string
	dbPass string

	jwtSecret string

	uploadDir string
}

var config Config

func configInit() error {
	path := ".env.dev" // TODO(art): make it env dependent

	src, err := os.ReadFile(path)
	if err != nil {
		return err
	}

	typeInfo := reflect.TypeOf(config)

	lines := strings.Split(string(src), "\n")
	for i, line := range lines {
		line = strings.TrimSpace(line)
		if line == "" {
			continue
		}

		lineNum := i + 1

		kv := strings.Split(line, "=")
		if len(kv) != 2 {
			fmt.Printf("%s:%d: Invalid line. Must be key = value. Skipping...\n", path, lineNum)
			continue
		}

		k := strings.TrimSpace(kv[0])
		if k == "" {
			fmt.Printf("%s:%d: Empty key. Skipping...\n", path, lineNum)
			continue
		}

		v := strings.TrimSpace(kv[1])
		if v == "" {
			fmt.Printf("%s:%d: Empty value. Skipping...\n", path, lineNum)
			continue
		}

		field, found := typeInfo.FieldByName(k)
		if !found {
			fmt.Printf("%s:%d: Unknown config key `%s`. Skipping...\n", path, lineNum, k)
			continue
		}

		ptr := unsafe.Add(unsafe.Pointer(&config), field.Offset)

		switch field.Type.Kind() {
		case reflect.String:
			sptr := (*string)(ptr)
			*sptr = v
		default:
			log.Fatalf("Unhandled field type of config struct %s, %s\n", field.Name, field.Type.Kind())
		}
	}

	return nil
}
