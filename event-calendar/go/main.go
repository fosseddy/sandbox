// TODO(art): during server errors log wtf happened

package main

import (
	"fmt"
	"log"
	"net/http"
)

func main() {
	if err := configInit(); err != nil {
		log.Fatal(err)
	}

	if err := databaseInit(); err != nil {
		log.Fatal(err)
	}

	fmt.Printf("Database %s connected...\n", config.dbName)

	if err := initStatic(); err != nil {
		log.Fatal(err)
	}

	initAdmin()
	initCategory()
	initEvent()

	fmt.Printf("Server is listening on port %s\n", config.port)
	log.Fatal(http.ListenAndServe(":"+config.port, nil))
}
