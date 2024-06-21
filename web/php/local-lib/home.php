<?php

namespace home;

use view;

function handle_index($ctx)
{
    $db = $ctx["db"];

    $author = $db->query_one("select count(*) as count from author");
    $book = $db->query_one("select count(*) as count from book");
    $genre = $db->query_one("select count(*) as count from genre");
    $bi = $db->query_one("select count(*) as count from book_instance");
    $bi_available = $db->query_one("select count(*) as count from book_instance where status = 0");

    view\render("index", [
        "title" => "Local Library Home",
        "book" => $book,
        "book_instance" => $bi,
        "book_instance_available" => $bi_available,
        "author" => $author,
        "genre" => $genre,
    ]);
}
