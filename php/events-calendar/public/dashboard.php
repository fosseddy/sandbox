<?php
declare(strict_types = 1);

require_once "../app.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

only_admin();
render_view("dashboard");
