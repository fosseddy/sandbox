#!/bin/php
<?php
declare(strict_types = 1);
error_reporting(E_ALL ^ E_WARNING);

define("BASE", __DIR__ . "/../");

set_exception_handler(function(Throwable $e) {
    fprintf(STDERR, "%s: %s in %s:%d\nStack trace:\n%s\n",
            get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(),
            $e->getTraceAsString());
    exit(1);
});

require_once BASE . "lib/env/env.php"; env\read(BASE . ".env");

require_once BASE . "src/database.php";
require_once BASE . "src/auth/auth.php";

function help($fd = STDERR): void
{
    fprintf($fd, "Usage: admin-manager.php [COMMAND]
COMMAND:
    list                  list all admins
    add [NAME] [PASSWORD] create new admin
    delete [NAME]         delete admin
NAME:
    Name of admin.
    Must be unique.
    Must have from 3 to 30 symbols.
    Can have _, letters and numbers.
    Must start with letter.
PASSWORD:
    Password of admin.
    Must have from 3 to 20 symbols.\n");
}

array_shift($argv);
$argc--;

if ($argc === 0)
{
    fprintf(STDERR, "Not enough arguments\n");
    help();
    exit(1);
}

if (in_array("--help", $argv))
{
    help(STDOUT);
    exit;
}

$cmd = array_shift($argv);
$argc--;

switch ($cmd)
{
case "list":
    $db = database\connect();
    $rows = $db->query("select name from admin");

    printf("Admins:\n");
    foreach ($rows as $r)
    {
        printf("   %s\n", $r["name"]);
    }

    break;
case "add":
    if ($argc < 2)
    {
        fprintf(STDERR, "Not enough arguments\n");
        help();
        exit(1);
    }

    $name = $argv[0];
    $pass = trim($argv[1]);
    $errors = auth\validate_credentials($name, $pass);

    if (isset($errors["name"]))
    {
        fprintf(STDERR, "Invalid name\n");
        help();
        exit(1);
    }

    if (isset($errors["password"]))
    {
        fprintf(STDERR, "Invalid password\n");
        help();
        exit(1);
    }

    $db = database\connect();

    $s = $db->prepare("select id from admin where name = ?");
    $s->execute([$name]);
    $s->setFetchMode(PDO::FETCH_CLASS, "auth\Admin");
    $doc = $s->fetch();

    if ($doc)
    {
        fprintf(STDERR, "Admin %s already exist\n", $name);
        help();
        exit(1);
    }

    $pass = password_hash($pass, PASSWORD_BCRYPT);

    $s = $db->prepare("insert into admin (name, password) values (?, ?)");
    $s->execute([$name, $pass]);

    printf("Admin %s successfully created\n", $name);
    break;
case "delete":
    if ($argc < 1)
    {
        fprintf(STDERR, "Not enough arguments\n");
        help();
        exit(1);
    }

    $name = $argv[0];
    $db = database\connect();

    $s = $db->prepare("select id from admin where name = ?");
    $s->execute([$name]);
    $s->setFetchMode(PDO::FETCH_CLASS, "auth\Admin");
    $doc = $s->fetch();

    if (!$doc)
    {
        fprintf(STDERR, "Admin %s does not exist\n", $name);
        help();
        exit(1);
    }

    $s = $db->prepare("delete from admin where name = ?");
    $s->execute([$name]);

    printf("Admin %s successfully deleted\n", $name);
    break;
default:
    fprintf(STDERR, "Unknown COMMAND %s\n", $cmd);
    help();
    exit(1);
}

exit;
