<?php
declare(strict_types = 1);

require_once "../../app.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

only_admin();

$cats = $database
            ->query("select id, name from category order by id desc")
            ->fetchAll(PDO::FETCH_CLASS, "Category");

render_view("category/index", ["categories" => $cats]);
