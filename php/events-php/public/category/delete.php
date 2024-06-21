<?php
declare(strict_types = 1);

require_once "../../src/errors/errors.php"; errors\setup();
require_once "../../lib/env/env.php"; env\read("../../.env");

require_once "../../lib/webtok/webtok.php";
require_once "../../src/database.php";
require_once "../../src/auth/auth.php";

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

$db = database\connect();

auth\only_admin($db);

$s = $db->prepare("delete from category where id = ?");
$s->execute([$id]);

header("Location: /category");
