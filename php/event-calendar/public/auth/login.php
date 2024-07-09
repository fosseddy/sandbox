<?php
declare(strict_types = 1);

require_once "../../app.php";

only_guest();

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
    render_view("auth/login", [
        "name" => "",
        "pass" => "",
        "errors" => []
    ]);
}

function handle_post(): void
{
    global $database;

    $name = $_POST["name"] ?? "";
    $pass = $_POST["password"] ?? "";

    $name = htmlspecialchars(trim($name));
    $errors = validate_credentials($name, $pass);

    if ($errors)
    {
        render_view("auth/login", compact("name", "pass", "errors"));
        return;
    }

    $s = $database->prepare("select id, password from admin where name = ?");
    $s->execute([$name]);
    $s->setFetchMode(PDO::FETCH_CLASS, "Admin");
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
        render_view("auth/login", compact("name", "pass", "errors"));
        return;
    }

    login($admin->id);
    header("Location: /dashboard.php");
}
