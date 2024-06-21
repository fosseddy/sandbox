<?php

namespace auth;

use web, view, http;

const COOKIE_NAME = "session";
const SECRET = "secret_string"; // TODO(art): this must be env variable;

class User
{
    public $id;
    public $name;
    public $password;
}

function router(): web\Router
{
    $r = new web\Router();

    $r->get("/", "auth\handle_login", ["auth\only_guest"]);
    $r->post("/", "auth\handle_login_post", ["auth\only_guest"]);

    $r->get("/register", "auth\handle_register", ["auth\only_guest"]);
    $r->post("/register", "auth\handle_register_post", ["auth\only_guest"]);

    $r->delete("/logout", "auth\handle_logout", ["auth\only_user"]);

    return $r;
}

function handle_login(array $ctx): void
{
    view\render("index", ["title" => "Login"]);
}

function handle_login_post(array $ctx): void
{
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $pass = htmlspecialchars(trim($_POST["password"] ?? ""));

    $errors = validate_credentials($name, $pass);

    if ($errors)
    {
        view\render("index", [
            "title" => "Login",
            "name" => $name,
            "errors" => $errors
        ]);
        exit;
    }

    $user = $ctx["db"]->query_one(
        "select id, name, password from user where name = ?",
        [$name],
        "auth\User"
    );

    if (!$user)
    {
        view\render("index", [
            "title" => "Login",
            "name" => $name,
            "errors" => ["user does not exist"]
        ]);
        exit;
    }

    if (!password_verify($pass, $user->password))
    {
        view\render("index", [
            "title" => "Login",
            "name" => $name,
            "errors" => ["wrong password"]
        ]);
        exit;
    }

    create_session($user->id);
    http\redirect("/dashboard");
}

function handle_register(): void
{
    view\render("register", ["title" => "Register"]);
}

function handle_register_post(array $ctx): void
{
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $pass = htmlspecialchars(trim($_POST["password"] ?? ""));

    $errors = validate_credentials($name, $pass);

    if ($errors)
    {
        view\render("register", [
            "title" => "Register",
            "name" => $name,
            "errors" => $errors
        ]);
        exit;
    }

    $user = $ctx["db"]->query_one("select id from user where name = ?", [$name]);

    if ($user)
    {
        view\render("register", [
            "title" => "Register",
            "name" => $name,
            "errors" => ["user already exist"]
        ]);
        exit;
    }

    $ctx["db"]->exec(
        "insert into user (name, password) values (?, ?)",
        [$name, password_hash($pass, PASSWORD_BCRYPT)]
    );

    create_session($ctx["db"]->last_id());
    http\redirect("/dashboard");
}

function handle_logout(array &$ctx)
{
    destroy_session();
    http\redirect("/");
}

function validate_credentials(string $name, string $pass): array
{
    $errors = [];

    if (strlen($name) < 3)
    {
        $errors[] = "name must be at least 3 characters long";
    }
    else if (strlen($name) > 12)
    {
        $errors[] = "name must be at most 12 characters long";
    }

    // at least 3 chars long; starts with alpha then word
    if (!preg_match("/^[a-zA-Z]{1}\w{2,}$/", $name))
    {
        $errors[] = "name must start with a letter and be at least 3 characters long";
    }

    if (strlen($pass) < 6)
    {
        $errors[] = "password must be at least 6 characters long";
    }

    return $errors;
}

function create_session(string $user_id): void
{
    $hash = hash_hmac("sha256", $user_id, SECRET);
    setcookie(COOKIE_NAME, "$user_id.$hash", [
        "expires" => time() + 60 * 60 * 24,
        "httponly" => true
    ]);
}

function destroy_session(): void
{
    setcookie(COOKIE_NAME, "", time() - 3600);
}

function attach_user(array &$ctx): void
{
    $sess = $_COOKIE["session"] ?? "";

    if (!$sess) return;

    [$user_id, $hash] = explode(".", $sess);

    if ($hash !== hash_hmac("sha256", $user_id, SECRET)) return;

    $user = $ctx["db"]->query_one(
        "select id, name from user where id = ?",
        [$user_id],
        "auth\User"
    );

    if (!$user) return;

    $ctx["user"] = $user;
}

function only_guest(array $ctx): void
{
    $user = $ctx["user"] ?? null;

    if ($user)
    {
        http\redirect("/dashboard");
        exit;
    }
}

function only_user(array $ctx): void
{
    $user = $ctx["user"] ?? null;

    if (!$user)
    {
        http\redirect("/");
        exit;
    }
}
