package main

import (
	"fmt"
	"log"
	"math/rand"
	"net"
	"time"
)

const tripsCount = 10

func main() {
	conn, err := net.Dial("udp4", "localhost:3000")
	if err != nil {
		log.Fatal(err)
	}

	fmt.Println("client started")

	i := 1
	for {
		fmt.Fprintf(conn, "%d %d", i, time.Now().UnixNano())
		if rand.Intn(30) < 4 {
			fmt.Printf("shutting down client, %d packets sent\n", i)
			break
		}

		i++
		time.Sleep(1 * time.Second)
	}
}
