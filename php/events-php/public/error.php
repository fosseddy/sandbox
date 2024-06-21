<?php
declare(strict_types = 1);

require_once "../src/view/view.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

view\render("errors/view-error");
