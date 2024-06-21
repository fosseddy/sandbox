<?php

declare(strict_types=1);

const BASE_DIR = __DIR__ . "/..";
const CORE_DIR = BASE_DIR . "/../core";

function debug($val)
{
    echo "<pre>";
    var_dump($val);
    echo "</pre>";
}

require_once CORE_DIR . "/web.php";
require_once CORE_DIR . "/view.php";
require_once CORE_DIR . "/path.php";
require_once CORE_DIR . "/http.php";
require_once CORE_DIR . "/database.php";

require_once path\from_base("auth.php");
require_once path\from_base("dashboard.php");

$app = new web\App();
$app->ctx = [
    "db" => new database\Connection("localhost", "phpauth", "user", "1234")
];

$app->add_router(auth\router());
$app->add_router(dashboard\router());

$app->middleware = ["auth\attach_user"];

try
{
    $app->handle_request();
}
catch (http\Error $e)
{
    http_response_code(404);
    echo "Page Not Found";
}
catch (Exception $e)
{
    error_log($e->getMessage());

    http_response_code(500);
    echo "Something went wrong and it is not your fault";
}
