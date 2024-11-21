package main

import (
	"bufio"
	"errors"
	"fmt"
	"io/fs"
	"log"
	"net"
	"net/textproto"
	"os"
)

func main() {
	server, err := net.Listen("tcp4", "localhost:3000")
	if err != nil {
		log.Fatal(err)
	}

	fmt.Println("server is listening on port 3000")

	for {
		client, err := server.Accept()
		if err != nil {
			log.Print(err)
			continue
		}
		go handleRequest(client)
	}
}

func handleRequest(client net.Conn) {
	fmt.Printf("%s connected\n", client.RemoteAddr())

	r := textproto.NewReader(bufio.NewReader(client))

	reqline, err := r.ReadLine()
	if err != nil {
		client.Close()
		fmt.Printf("%s disconnected\n", client.RemoteAddr())
		return
	}
	fmt.Printf("    '%s'\n", reqline)

	for {
		header, err := r.ReadLine()
		if err != nil {
			client.Close()
			fmt.Printf("%s disconnected\n", client.RemoteAddr())
			return
		}
		if header == "" {
			break
		}
		fmt.Printf("    '%s'\n", header)
	}

	var method, uri, httpver string
	n, _ := fmt.Sscanf(reqline, "%s %s %s", &method, &uri, &httpver)
	if n != 3 {
		client.Close()
		fmt.Printf("%s disconnected\n", client.RemoteAddr())
		return
	}

	filename := uri[1:]
	if filename == "" {
		filename = "index.html"
	}

	src, err := os.ReadFile(filename)
	if err != nil {
		status := "HTTP/1.1 500 Internal Server Error"
		body := "<h1>Something went wrong</h1>"

		if errors.Is(err, fs.ErrNotExist) {
			status = "HTTP/1.1 404 Not Found"
			body = "<h1>Page Not Found</h1>"
		}

		fmt.Fprintf(client, "%s\r\n", status)
		fmt.Fprint(client, "Server: Go Web Server\r\n")
		fmt.Fprintf(client, "Content-Length: %v\r\n", len(body))
		fmt.Fprint(client, "\r\n")
		fmt.Fprint(client, body)

		client.Close()
		fmt.Printf("%s disconnected\n", client.RemoteAddr())
		return
	}

	fmt.Fprint(client, "HTTP/1.1 200 OK\r\n")
	fmt.Fprint(client, "Server: Go Web Server\r\n")
	fmt.Fprintf(client, "Content-Length: %v\r\n", len(src))
	fmt.Fprint(client, "\r\n")
	fmt.Fprint(client, string(src))

	client.Close()
	fmt.Printf("%s disconnected\n", client.RemoteAddr())
}
