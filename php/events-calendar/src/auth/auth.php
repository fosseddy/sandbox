<?php
declare(strict_types = 1);

namespace auth;
use webtok;
use PDO;

class Admin
{
    public int $id;
    public string $name;
    public string $password;
}

function validate_credentials(string $name, string $pass): array
{
    $errors = [];
    $passlen = mb_strlen($pass);

    if (!$name)
    {
        $errors["name"] = "name is required";
    }
    else if (!preg_match("/^[a-zA-Z]{1}\w{2,29}$/", $name))
    {
        $errors["name"] = "name is invalid";
    }

    if (!$pass)
    {
        $errors["password"] = "password is required";
    }
    else if ($passlen < 3 || $passlen > 20)
    {
        $errors["password"] = "password is invalid";
    }

    return $errors;
}

function generate_webtok(int $admin_id): array // [string, string]
{
    [$acc, $err] = webtok\sign($_ENV["WEBTOK_SECRET"], [
        "eat" => time() + 60 * 5,
        "id" => $admin_id,
        "kind" => "access"
    ]);

    if ($err)
    {
        throw $err;
    }

    [$ref, $err] = webtok\sign($_ENV["WEBTOK_SECRET"], [
        "eat" => time() + 60 * 60 * 24 * 7,
        "id" => $admin_id,
        "kind" => "refresh"
    ]);

    if ($err)
    {
        throw $err;
    }

    return [$acc, $ref];
}

function decode_webtok(string $kind, PDO $db): array // [?Admin, ?webtok\Error]
{
    $tok = $_COOKIE[$kind] ?? "";

    if (!$tok)
    {
        return [null, null];
    }

    [$data, $err] = webtok\verify($_ENV["WEBTOK_SECRET"], $tok);

    if ($err)
    {
        return [null, $err];
    }

    if ($data["kind"] !== $kind)
    {
        return [null, null];
    }

    $s = $db->prepare("select id, name from admin where id = ?");
    $s->execute([$data["id"]]);
    $s->setFetchMode(PDO::FETCH_CLASS, "auth\Admin");
    $admin = $s->fetch();

    return [$admin, null];
}

function refresh_webtok(PDO $db): ?Admin
{
    [$admin, $err] = decode_webtok("refresh", $db);

    if ($err || !$admin)
    {
        clear_cookie();
        return null;
    }

    login($admin->id);
    return $admin;
}

function set_cookie(string $acc, string $ref): void
{
    $opts = [
        "expires" => time() + 60 * 60 * 24 * 30,
        "httponly" => true,
        "path" => "/"
    ];

    setcookie("access", $acc, $opts);
    setcookie("refresh", $ref, $opts);
}

function clear_cookie(): void
{
    setcookie("access", "", 0, "/");
    setcookie("refresh", "", 0, "/");
}

function login(int $admin_id): void
{
    [$acc, $ref] = generate_webtok($admin_id);
    set_cookie($acc, $ref);
}

function decode_admin(PDO $db): ?Admin
{
    [$admin, $err] = decode_webtok("access", $db);

    if ($err)
    {
        if ($err->kind === "eat")
        {
            return refresh_webtok($db);
        }

        return null;
    }

    return $admin;
}

function only_admin(PDO $db): void
{
    if (!decode_admin($db))
    {
        header("Location: /auth/login.php");
        exit;
    }
}

function only_guest(PDO $db): void
{
    if (decode_admin($db))
    {
        header("Location: /dashboard.php");
        exit;
    }
}
