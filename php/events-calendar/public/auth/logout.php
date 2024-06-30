<?php
declare(strict_types = 1);

require_once "../../src/auth/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

auth\clear_cookie();
header("Location: /auth/login.php");
