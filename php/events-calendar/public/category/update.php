<?php
declare(strict_types = 1);

require_once "../../app.php";

if (!isset($_GET["id"]))
{
    header("Location: /category");
    exit;
}

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
    global $database;

    $id = $_GET["id"];

    $s = $database->prepare("select id, name from category where id = ?");
    $s->execute([$id]);
    $s->setFetchMode(PDO::FETCH_CLASS, "Category");
    $cat = $s->fetch();

    if (!$cat)
    {
        header("Location: /category");
        return;
    }

    render_view("category/update", [
        "name" => $cat->name,
        "errors" => []
    ]);
}

function handle_post(): void
{
    global $database;

    $id = $_GET["id"];
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $errors = validate_category($name, $id);

    if ($errors)
    {
        render_view("category/update", compact("name", "errors"));
        return;
    }

    $database
        ->prepare("update category set name = ? where id = ?")
        ->execute([$name, $id]);

    header("Location: /category");
}
