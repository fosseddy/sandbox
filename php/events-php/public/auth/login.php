<?php
declare(strict_types = 1);

require_once "../../src/errors/errors.php"; errors\setup();
require_once "../../lib/env/env.php"; env\read("../../.env");

require_once "../../lib/webtok/webtok.php";
require_once "../../src/database.php";
require_once "../../src/auth/auth.php";
require_once "../../src/view/view.php";

$db = database\connect();

auth\only_guest($db);

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
    view\render("auth/view-login", [
        "name" => "",
        "pass" => "",
        "errors" => []
    ]);
}

function handle_post(PDO $db): void
{
    $name = $_POST["name"] ?? "";
    $pass = $_POST["password"] ?? "";

    $name = htmlspecialchars(trim($name));
    $errors = auth\validate_credentials($name, $pass);

    if ($errors)
    {
        view\render("auth/view-login", compact("name", "pass", "errors"));
        return;
    }

    $s = $db->prepare("select id, password from admin where name = ?");
    $s->execute([$name]);
    $s->setFetchMode(PDO::FETCH_CLASS, "auth\Admin");
    $admin = $s->fetch();

    if (!$admin)
    {
        $errors["name"] = "name is invalid";
    }
    else if (!password_verify($pass, $admin->password))
    {
        $errors["password"] = "password is invalid";
    }

    if ($errors)
    {
        view\render("auth/view-login", compact("name", "pass", "errors"));
        return;
    }

    auth\login($admin->id);
    header("Location: /dashboard.php");
}
