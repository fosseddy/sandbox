<?php

namespace genres;

use http, view;

class Model
{
    public $id;
    public $name;

    function url()
    {
        return "/genres/detail?id=$this->id";
    }
}

function handle_index($ctx)
{
    $db = $ctx["db"];

    $genres = $db->query_many(
        "select id, name from genre order by name",
        "genres\Model"
    );

    view\render("genres/index", [
        "title" => "Genre List",
        "genres" => $genres
    ]);
}

function handle_detail($ctx)
{
    $db = $ctx["db"];
    $genre_id = $_GET["id"];

    $genre = $db->query_one(
        "select name from genre where id = ?",
        [$genre_id],
        "genres\Model"
    );

    if (!$genre) throw new http\Error(404);

    $books = $db->query_many(
        "select B.id, B.title, B.summary from book as B
         left join book_genres as BG on B.id = BG.book_id
         where BG.genre_id = ?",
        [$genre_id],
        "books\Model"
    );

    view\render("genres/detail", [
        "title" => "Genre Detail",
        "genre" => $genre,
        "books" => $books,
    ]);
}

function handle_create($ctx)
{
    view\render("genres/create", ["title" => "Create Genre"]);
}

function handle_store($ctx)
{
    $db = $ctx["db"];

    $errors = [];
    $genre = new Model();

    $genre->name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $namelen = strlen($genre->name);

    if ($namelen < 3 || $namelen > 255) $errors[] = "Genre name must be at least 3 characters long, but less than 255 characters";

    if ($errors)
    {
        view\render("genres/create", [
            "title" => "Create Genre",
            "genre" => $genre,
            "errors" => $errors
        ]);
        exit;
    }

    $db->query_one(
        "select id from genre where name = ?",
        [$genre->name],
        $genre
    );

    if ($genre->id)
    {
        http\redirect($genre->url());
        exit;
    }

    $db->exec("insert into genre (name) values (?)", [$genre->name]);

    $genre->id = $db->last_id();
    http\redirect($genre->url());
}
