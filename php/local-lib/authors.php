<?php

namespace authors;

use view, http;

class Model
{
    public $id;
    public $first_name;
    public $family_name;
    public $date_of_birth;
    public $date_of_death;

    public $books;

    function name()
    {
        return "$this->family_name, $this->first_name";
    }

    function url()
    {
        return "/authors/detail?id=$this->id";
    }

    function lifespan()
    {
        $fmt = "M d, Y";
        $birth = date_create($this->date_of_birth)->format($fmt);

        if (!$this->date_of_death)
        {
            return $birth;
        }

        $death = date_create($this->date_of_death)->format($fmt);
        return "$birth - $death";
    }
}

function handle_index($ctx)
{
    $db = $ctx["db"];

    $authors = $db->query_many(
        "select id, first_name, family_name, date_of_birth, date_of_death
         from author order by family_name",
        "authors\Model"
    );

    view\render("authors/index", [
        "title" => "Author List",
        "authors" => $authors
    ]);
}

function handle_detail($ctx)
{
    $db = $ctx["db"];
    $author_id = $_GET["id"];

    $author = $db->query_one(
        "select first_name, family_name, date_of_birth, date_of_death
         from author where id = ?",
        [$author_id],
        "authors\Model"
    );

    if (!$author) throw new http\Error(404);

    $books = $db->query_many(
        "select id, title, summary from book where author_id = ?",
        [$author_id],
        "books\Model"
    );

    view\render("authors/detail", [
        "title" => "Author Detail",
        "author" => $author,
        "books" => $books
    ]);
}

function handle_create($ctx)
{
    view\render("authors/create", ["title" => "Create Author"]);
}

function handle_store($ctx)
{
    $db = $ctx["db"];

    $errors = [];
    $a = new Model();

    // TODO(art): validation
    $a->first_name = htmlspecialchars(trim($_POST["first-name"] ?? ""));
    $a->family_name = htmlspecialchars(trim($_POST["family-name"] ?? ""));
    $a->date_of_birth = htmlspecialchars(trim($_POST["date-of-birth"] ?? ""));
    $a->date_of_death = htmlspecialchars(trim($_POST["date-of-death"] ?? ""));

    if ($errors)
    {
        view\render("authors/create", [
            "title" => "Create Author",
            "author" => $a,
            "errors" => $errors
        ]);
        exit;
    }

    $data = $db->query_one(
        "select id from author where first_name = ? and family_name = ?",
        [$a->first_name, $a->family_name]
    );

    if ($data)
    {
        $errors[] = "This author already exist";

        view\render("authors/create", [
            "title" => "Create Author",
            "author" => $a,
            "errors" => $errors
        ]);
        exit;
    }

    if (!$a->date_of_death) $a->date_of_death = null;

    $db->exec(
        "insert into author
             (first_name, family_name, date_of_birth, date_of_death)
         values (?, ?, ?, ?)",
        [$a->first_name, $a->family_name, $a->date_of_birth, $a->date_of_death]
    );

    $a->id = $db->last_id();
    http\redirect($a->url());
}
