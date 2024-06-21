<?php
declare(strict_types = 1);

require_once "../src/errors/errors.php"; errors\setup();
require_once "../lib/env/env.php"; env\read("../.env");

require_once "../lib/webtok/webtok.php";
require_once "../src/database.php";
require_once "../src/auth/auth.php";
require_once "../src/view/view.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
{
    http_response_code(405);
    header("Allow: GET");
    exit;
}

auth\only_admin(database\connect());
?>

<?php view\render("view/partial-header", ["title" => "Dashboard"]) ?>

<ul>
    <li><a href="/category">Category</a></li>
    <li><a href="/event">Events</a></li>
    <li><a href="/auth/logout.php">Logout</a></li>
</ul>

<?php view\render("view/partial-footer") ?>
