<?php
declare(strict_types = 1);

require_once "../../src/errors/errors.php"; errors\setup();
require_once "../../lib/env/env.php"; env\read("../../.env");

require_once "../../lib/webtok/webtok.php";
require_once "../../src/database.php";
require_once "../../src/auth/auth.php";
require_once "../../src/category/category.php";
require_once "../../src/view/view.php";

if (!isset($_GET["id"]))
{
    header("Location: /category");
    exit;
}

$db = database\connect();

auth\only_admin($db);

switch ($_SERVER["REQUEST_METHOD"])
{
case "GET":
    handle_get($db);
    exit;
case "POST":
    handle_post($db);
    exit;
default:
    http_response_code(405);
    header("Allow: GET, POST");
    exit;
}

function handle_get(PDO $db): void
{
    $id = $_GET["id"];

    $s = $db->prepare("select id, name from category where id = ?");
    $s->execute([$id]);
    $s->setFetchMode(PDO::FETCH_CLASS, "category\Model");
    $cat = $s->fetch();

    if (!$cat)
    {
        header("Location: /category");
        return;
    }

    view\render("category/view-update", [
        "name" => $cat->name,
        "errors" => []
    ]);
}

function handle_post(PDO $db): void
{
    $id = $_GET["id"];
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $errors = category\validate($db, $name, $id);

    if ($errors)
    {
        view\render("category/view-update", compact("name", "errors"));
        return;
    }

    $s = $db->prepare("update category set name = ? where id = ?");
    $s->execute([$name, $id]);

    header("Location: /category");
}
