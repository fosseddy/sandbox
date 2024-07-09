<?php
declare(strict_types = 1);

define("ROOT", __DIR__ . "/");

require_once ROOT . "errors.php";
init_errors();

require_once ROOT . "lib/env/env.php";
env\read(ROOT . ".env");

require_once ROOT . "database.php";
init_database();

require_once ROOT . "response.php";

require_once ROOT . "lib/webtok/webtok.php";
require_once ROOT . "auth.php";

require_once ROOT . "category.php";
require_once ROOT . "event.php";
