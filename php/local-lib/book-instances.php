<?php

namespace book_instances;

use view, http, books;

class Model
{
    const STATUS_TEXT = [
        "Available",
        "Maintenance",
        "Loaned",
        "Reserved"
    ];

    public $id;
    public $book_id;
    public $imprint;
    public $status;
    public $due_back;

    public $book;

    function url()
    {
        return "/book-instances/detail?id=$this->id";
    }

    function due_back_formatted()
    {
        return date_create($this->due_back)->format("D, d M Y");
    }

    function status_string()
    {
        return static::STATUS_TEXT[$this->status];
    }
}


function handle_index($ctx)
{
    $db = $ctx["db"];

    $data = $db->query_many(
        "select BI.id, BI.imprint, BI.status, BI.due_back, B.title
         from book_instance as BI
         left join book as B on BI.book_id = B.id"
    );

    $book_instances = [];
    foreach($data as $it)
    {
        $bi = new Model();
        $bi->book = new Books\Model();

        $bi->id = $it["id"];
        $bi->imprint = $it["imprint"];
        $bi->status = $it["status"];
        $bi->due_back = $it["due_back"];
        $bi->book->title = $it["title"];

        $book_instances[] = $bi;
    }

    view\render("book-instances/index", [
        "title" => "Book Instances List",
        "book_instances" => $book_instances
    ]);
}

function handle_detail($ctx)
{
    $db = $ctx["db"];
    $book_instance_id = $_GET["id"];

    $data = $db->query_one(
        "select BI.id, BI.imprint, BI.status, BI.due_back,
                B.title, B.id as b_id
         from book_instance as BI
         left join book as B on BI.book_id = B.id
         where BI.id = ?",
        [$book_instance_id]
    );

    if (!$data) throw new http\Error(404);

    $bi = new Model();
    $bi->book = new Books\Model();

    $bi->id = $data["id"];
    $bi->imprint = $data["imprint"];
    $bi->status = $data["status"];
    $bi->due_back = $data["due_back"];

    $bi->book->title = $data["title"];
    $bi->book->id = $data["b_id"];

    view\render("book-instances/detail", [
        "title" => "Book:",
        "book_instance" => $bi
    ]);
}
