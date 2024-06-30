<?php
declare(strict_types = 1);

require_once "../../src/errors/errors.php"; errors\setup();
require_once "../../lib/env/env.php"; env\read("../../.env");

require_once "../../lib/webtok/webtok.php";
require_once "../../src/database.php";
require_once "../../src/auth/auth.php";
require_once "../../src/category/category.php";
require_once "../../src/view/view.php";

$db = database\connect();

auth\only_admin($db);

switch ($_SERVER["REQUEST_METHOD"])
{
case "GET":
    handle_get();
    exit;
case "POST":
    handle_post($db);
    exit;
default:
    http_response_code(405);
    header("Allow: GET, POST");
    exit;
}

function handle_get(): void
{
    view\render("category/view-create", [
        "name" => "",
        "errors" => []
    ]);
}

function handle_post(PDO $db): void
{
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $errors = category\validate($db, $name);

    if ($errors)
    {
        view\render("category/view-create", compact("name", "errors"));
        return;
    }

    $s = $db->prepare("insert into category (name) values (?)");
    $s->execute([$name]);

    header("Location: /category");
}
