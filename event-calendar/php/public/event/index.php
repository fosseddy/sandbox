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

$events = $database
            ->query("select id, name from event")
            ->fetchAll(PDO::FETCH_CLASS, "Event");

echo "<pre>";
var_dump($events);
echo "</pre><br>";

render_view("event/index", ["events" => $events]);
