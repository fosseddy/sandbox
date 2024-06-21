<?php

declare(strict_types=1);

const BASE_DIR = __DIR__ . "/..";
const CORE_DIR = BASE_DIR . "/../core";

function debug($val)
{
    echo "<pre>";
    var_dump($val);
    echo "</pre>";
}

require_once CORE_DIR . "/web.php";
require_once CORE_DIR . "/http.php";
require_once CORE_DIR . "/view.php";
require_once CORE_DIR . "/path.php";
require_once CORE_DIR . "/database.php";

require_once path\from_base("home.php");
require_once path\from_base("books.php");
require_once path\from_base("authors.php");
require_once path\from_base("genres.php");
require_once path\from_base("book-instances.php");

$app = new web\App();
$app->ctx = [
    "db" => new database\Connection("localhost", "local_lib", "art", "qweqwe123")
];

$app->router->get("/", "home\handle_index");

$app->router->get("/books", "books\handle_index");
$app->router->get("/books/detail", "books\handle_detail", ["with_query_id"]);
$app->router->get("/books/create", "books\handle_create");
$app->router->post("/books/create", "books\handle_store");

$app->router->get("/authors", "authors\handle_index");
$app->router->get("/authors/detail", "authors\handle_detail", ["with_query_id"]);
$app->router->get("/authors/create", "authors\handle_create");
$app->router->post("/authors/create", "authors\handle_store");

$app->router->get("/genres", "genres\handle_index");
$app->router->get("/genres/detail", "genres\handle_detail", ["with_query_id"]);
$app->router->get("/genres/create", "genres\handle_create");
$app->router->post("/genres/create", "genres\handle_store");

$app->router->get("/book-instances", "book_instances\handle_index");
$app->router->get("/book-instances/detail", "book_instances\handle_detail", ["with_query_id"]);

try
{
    $app->handle_request();
}
catch (http\Error $e)
{
    http_response_code($e->status_code);
    view\render("error", [
        "title" => $e->getMessage() ?: "Something went wrong"
    ]);
}
catch (Exception $e)
{
    http_response_code(500);
    view\render("error", [
        "title" => "Something went wrong and it is not your fault"
    ]);
}

function with_query_id()
{
    $id = $_GET["id"] ?? "";
    if (!$id) throw new http\Error(400);
}
