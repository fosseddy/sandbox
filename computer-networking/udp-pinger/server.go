package main

import (
	"bytes"
	"fmt"
	"log"
	"math/rand"
	"net"
)

func main() {
	server, err := net.ListenPacket("udp4", "localhost:3000")
	if err != nil {
		log.Fatal(err)
	}

	fmt.Println("udp server is listening on port 3000")

	for {
		buf := make([]byte, 1024)
		n, client, err := server.ReadFrom(buf)
		if n <= 0 {
			if err != nil {
				log.Print(err)
			}
			continue
		}

		if rand.Intn(10) < 4 {
			continue
		}

		server.WriteTo(bytes.ToUpper(buf), client)

		if err != nil {
			log.Print(err)
		}
	}
}
