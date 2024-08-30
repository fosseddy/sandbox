<?php
declare(strict_types = 1);

require_once "../../app.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

$id = $_GET["id"] ?? "";

if (!$id)
{
    header("Location: /category");
    exit;
}

only_admin();

$database->prepare("delete from category where id = ?")->execute([$id]);

header("Location: /category");
