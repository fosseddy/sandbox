package main

import (
	"fmt"
	"io"
	"log"
	"net"
	"os"
)

func main() {
	host, port, filename := os.Args[1], os.Args[2], os.Args[3]

	conn, err := net.Dial("tcp4", fmt.Sprintf("%s:%s", host, port))
	if err != nil {
		log.Fatal(err)
	}

	fmt.Fprintf(conn, "GET /%s HTTP/1.1\r\n", filename)
	fmt.Fprintf(conn, "Host: %s:%s\r\n", host, port)
	fmt.Fprint(conn, "User-Agent: Go Web Client\r\n")
	fmt.Fprint(conn, "\r\n")

	buf, err := io.ReadAll(conn)
	if err != nil {
		log.Fatal(err)
	}

	fmt.Print(string(buf))
}
