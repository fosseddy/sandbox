<?php
declare(strict_types = 1);

require_once "../app.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

if (decode_admin())
{
    header("Location: /dashboard.php");
    exit;
}

header("Location: /auth/login.php");
