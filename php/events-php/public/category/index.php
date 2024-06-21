<?php
declare(strict_types = 1);

require_once "../../src/errors/errors.php"; errors\setup();
require_once "../../lib/env/env.php"; env\read("../../.env");

require_once "../../lib/webtok/webtok.php";
require_once "../../src/database.php";
require_once "../../src/auth/auth.php";
require_once "../../src/category/category.php";
require_once "../../src/view/view.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

$db = database\connect();

auth\only_admin($db);

$cats = $db->query("select id, name from category order by id desc")
           ->fetchAll(PDO::FETCH_CLASS, "category\Model");

view\render("category/view-index", ["categories" => $cats]);
