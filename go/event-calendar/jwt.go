package main

import (
	"time"
	"github.com/golang-jwt/jwt/v5"
)

type JwtClaims struct {
	Id int `json:"id"`
	Kind string `json:"kind"`
	jwt.RegisteredClaims
}

func jwtSign(id int, kind string, expiresAt time.Time) (string, error) {
	claims := JwtClaims{
		id,
		kind,
		jwt.RegisteredClaims{ExpiresAt: jwt.NewNumericDate(expiresAt)},
	}
	tok := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	return tok.SignedString([]byte(config.jwtSecret))
}

func jwtSignTokenPair(id int) (string, string, error) {
	acc, err := jwtSign(id, "access", time.Now().Add(8 * time.Hour))
	if err != nil {
		return "", "", err
	}

	ref, err := jwtSign(id, "refresh", time.Now().Add(5 * 24 * time.Hour))
	if err != nil {
		return "", "", err
	}

	return acc, ref, nil
}
