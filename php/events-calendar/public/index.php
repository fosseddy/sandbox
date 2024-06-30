<?php
declare(strict_types = 1);

require_once "../src/errors/errors.php"; errors\setup();
require_once "../lib/env/env.php"; env\read("../.env");

require_once "../lib/webtok/webtok.php";
require_once "../src/database.php";
require_once "../src/auth/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

if (auth\decode_admin(database\connect()))
{
    header("Location: /dashboard.php");
    exit;
}

header("Location: /auth/login.php");
