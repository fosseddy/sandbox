<?php
declare(strict_types = 1);

require_once "../../app.php";

only_admin();

switch ($_SERVER["REQUEST_METHOD"])
{
case "GET":
    handle_get();
    exit;
case "POST":
    handle_post();
    exit;
default:
    http_response_code(405);
    header("Allow: GET, POST");
    exit;
}

function handle_get(): void
{
    render_view("category/create", [
        "name" => "",
        "errors" => []
    ]);
}

function handle_post(): void
{
    global $database;

    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $errors = validate_category($name);

    if ($errors)
    {
        render_view("category/create", compact("name", "errors"));
        return;
    }

    $database
        ->prepare("insert into category (name) values (?)")
        ->execute([$name]);

    header("Location: /category");
}
