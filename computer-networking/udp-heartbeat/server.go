package main

import (
	"errors"
	"fmt"
	"log"
	"net"
	"os"
	"time"
)

func main() {
	server, err := net.ListenPacket("udp4", "localhost:3000")
	if err != nil {
		log.Fatal(err)
	}

	fmt.Println("udp server is listening on port 3000")

	buf := make([]byte, 1024)

	for {
		if err := server.SetReadDeadline(time.Now().Add(5 * time.Second)); err != nil {
			log.Print(err)
			continue
		}

		n, client, err := server.ReadFrom(buf)
		if n <= 0 {
			if err != nil {
				if errors.Is(err, os.ErrDeadlineExceeded) {
					fmt.Println("client has stopped")
				} else {
					log.Print(err)
				}
			}
			continue
		}

		var tag, timestamp int64
		n, _ = fmt.Sscanf(string(buf), "%d %d", &tag, &timestamp)
		if n != 2 {
			continue
		}

		diff := time.Since(time.Unix(0, timestamp))
		fmt.Println(client, tag, diff)
	}
}
