package main

import (
	"database/sql"
	"fmt"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

var database *sql.DB

func databaseInit() error {
	var err error

	dsn := fmt.Sprintf("%s:%s@/%s?parseTime=true", config.dbUser, config.dbPass, config.dbName)
	database, err = sql.Open("mysql", dsn)
	if err != nil {
		return err
	}

	database.SetConnMaxLifetime(time.Minute * 3)
	database.SetMaxOpenConns(10)
	database.SetMaxIdleConns(10)

	return database.Ping()
}
