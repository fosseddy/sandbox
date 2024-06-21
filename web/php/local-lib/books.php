<?php

namespace books;

use PDOException;
use view, http, authors;

class Model
{
    public $id;
    public $title;
    public $author_id;
    public $summary;
    public $ISBN;

    public $author;
    public $genres;

    function url()
    {
        return "/books/detail?id=$this->id";
    }
}

function handle_index($ctx)
{
    $db = $ctx["db"];

    $data = $db->query_many(
        "select B.id, B.title,
                A.first_name, A.family_name
         from book as B
         left join author as A on B.author_id = A.id
         order by B.title"
    );

    $books = [];
    foreach($data as $it)
    {
        $b = new Model();
        $b->author = new authors\Model();

        $b->id = $it["id"];
        $b->title = $it["title"];
        $b->author->first_name = $it["first_name"];
        $b->author->family_name = $it["family_name"];

        $books[] = $b;
    }

    view\render("books/index", [
        "title" => "Book List",
        "books" => $books
    ]);
}

function handle_detail($ctx)
{
    $db = $ctx["db"];
    $book_id = $_GET["id"];

    $data = $db->query_one(
        "select B.title, B.summary, B.ISBN,
                A.id as a_id, A.first_name, A.family_name
         from book as B
         left join author as A on B.author_id = A.id
         where B.id = ?",
        [$book_id]
    );

    if (!$data) throw new http\Error(404);

    $book = new Model();
    $book->author = new Authors\Model();

    $book->title = $data["title"];
    $book->summary = $data["summary"];
    $book->ISBN = $data["ISBN"];
    $book->author->id = $data["a_id"];
    $book->author->first_name = $data["first_name"];
    $book->author->family_name = $data["family_name"];

    $book_instances = $db->query_many(
        "select id, status, imprint, due_back from book_instance
         where book_id = ?",
        [$book_id],
        "book_instances\Model"
    );

    $genres = $db->query_many(
        "select G.id, G.name from genre as G
         left join book_genres as BG on G.id = BG.genre_id
         where BG.book_id = ?",
        [$book_id],
        "genres\Model"
    );

    view\render("books/detail", [
        "title" => "Book Detail",
        "book" => $book,
        "book_instances" => $book_instances,
        "genres" => $genres
    ]);
}

function handle_create($ctx)
{
    $db = $ctx["db"];

    $authors = $db->query_many(
        "select id, first_name, family_name from author order by family_name",
        "authors\Model"
    );

    $genres = $db->query_many("select id, name from genre", "genres\Model");

    view\render("books/create", [
        "title" => "Create Book",
        "authors" => $authors,
        "genres" => $genres
    ]);
}

function handle_store($ctx)
{
    $db = $ctx["db"];
    $b = new Model();
    $errors = [];

    // TODO(art): validation
    $b->title = htmlspecialchars(trim($_POST["title"] ?? ""));
    $b->author_id = htmlspecialchars(trim($_POST["author-id"] ?? ""));
    $b->summary = htmlspecialchars(trim($_POST["summary"] ?? ""));
    $b->ISBN = htmlspecialchars(trim($_POST["ISBN"] ?? ""));

    $b->genres = [];
    if (isset($_POST["genre"]))
    {
        foreach ($_POST["genre"] as $g)
        {
            $b->genres[] = htmlspecialchars(trim($g));
        }
    }

    if ($errors)
    {
        $authors = $db->query_many(
            "select id, first_name, family_name from author
             order by family_name",
            "authors\Model"
        );

        $genres = $db->query_many("select id, name from genre", "genres\Model");

        view\render("books/create", [
            "title" => "Create Book",
            "book" => $b,
            "authors" => $authors,
            "genres" => $genres,
            "errors" => $errors
        ]);
        exit;
    }

    $data = $db->query_one("select id from book where ISBN = ?", [$b->ISBN]);

    if ($data)
    {
        $errors[] = "Book with this ISBN already exist";

        view\render("books/create", [
            "title" => "Create Book",
            "book" => $b,
            "authors" => $authors,
            "genres" => $genres,
            "errors" => $errors
        ]);
        exit;
    }

    $db->transaction();
    try
    {
        $db->exec(
            "insert into book (title, author_id, summary, ISBN)
             values (?, ?, ?, ?)",
            [$b->title, $b->author_id, $b->summary, $b->ISBN],
        );

        $b->id = $db->last_id();

        $sql = "insert into book_genres (book_id, genre_id) values ";
        $binds = [];
        foreach ($b->genres as $i => $g_id)
        {
            if ($i > 0) $sql .= ", ";
            $sql .= "(?, ?)";
            $binds[] = $b->id;
            $binds[] = $g_id;
        }

        $db->exec($sql, $binds);
        $db->pdo->commit();
    }
    catch (PDOException $e)
    {
        $db->rollback();
        throw $e;
    }

    http\redirect($b->url());
}
