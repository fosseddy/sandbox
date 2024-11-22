package main

import (
	"errors"
	"fmt"
	"log"
	"net"
	"os"
	"time"
)

const tripsCount = 10

func main() {
	conn, err := net.Dial("udp4", "localhost:3000")
	if err != nil {
		log.Fatal(err)
	}

	lost := 0

	var rttsum, rttmin, rttmax time.Duration
	rttmin = time.Hour

	buf := make([]byte, 1024)

	for i := 1; i <= tripsCount; i++ {
		err = conn.SetReadDeadline(time.Now().Add(time.Second))
		if err != nil {
			log.Print(err)
			continue
		}

		start := time.Now()
		fmt.Fprintf(conn, "Ping %d %s", i, start.Format(time.TimeOnly))

		_, err := conn.Read(buf)
		if err != nil {
			if errors.Is(err, os.ErrDeadlineExceeded) {
				fmt.Println("Request timed out")
				lost++
			} else {
				log.Print(err)
			}
			continue
		}

		rtt := time.Since(start)
		rttsum += rtt
		if rtt < rttmin {
			rttmin = rtt
		}
		if rtt > rttmax {
			rttmax = rtt
		}
		fmt.Println(string(buf), rtt)
	}

	fmt.Println("--- stats ---")
	fmt.Printf("rtt min/max/avg = %s/%s/%s\n", rttmin, rttmax, rttsum/time.Duration((tripsCount-lost)))
	fmt.Printf("packet loss     = %d%%\n", int((float32(lost)/tripsCount)*100))
}
