package main

import (
	"errors"
	"time"

	"github.com/golang-jwt/jwt/v5"
)

type JwtClaims struct {
	ID   int    `json:"id"`
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
	acc, err := jwtSign(id, "access", time.Now().Add(8*time.Hour))
	if err != nil {
		return "", "", err
	}

	ref, err := jwtSign(id, "refresh", time.Now().Add(5*24*time.Hour))
	if err != nil {
		return "", "", err
	}

	return acc, ref, nil
}

func jwtVerify(tok string) (*JwtClaims, error) {
	parsed, err := jwt.ParseWithClaims(tok, &JwtClaims{}, func(tok *jwt.Token) (interface{}, error) {
		return []byte(config.jwtSecret), nil
	})
	if err != nil {
		return nil, err
	}

	claims, ok := parsed.Claims.(*JwtClaims)
	if !ok {
		return nil, errors.New("unknown claims type")
	}

	return claims, nil
}
