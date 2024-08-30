<?php
declare(strict_types = 1);

$database = null;

function init_database(): void
{
    global $database;

    [
        "DB_HOST" => $host,
        "DB_NAME" => $name,
        "DB_USER" => $user,
        "DB_PASS" => $pass
    ] = $_ENV;

    $database = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
