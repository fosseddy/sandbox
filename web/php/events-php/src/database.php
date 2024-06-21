<?php
declare(strict_types = 1);

namespace database;
use PDO;

function connect(): PDO
{
    [
        "DB_HOST" => $host,
        "DB_NAME" => $name,
        "DB_USER" => $user,
        "DB_PASS" => $pass
    ] = $_ENV;

    $db = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $db;
}
