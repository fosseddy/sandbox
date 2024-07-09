#!/bin/php
<?php
declare(strict_types = 1);
error_reporting(E_ALL ^ E_WARNING);

define("ROOT", __DIR__ . "/../");

set_exception_handler(function(Throwable $e) {
    fprintf(STDERR, "%s: %s in %s:%d\nStack trace:\n%s\n",
            get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(),
            $e->getTraceAsString());
    exit(1);
});

require_once ROOT . "lib/env/env.php"; env\read(ROOT . ".env");

require_once ROOT . "database.php";
require_once ROOT . "auth.php";

function help($fd = STDERR): void
{
    fprintf($fd, "Usage: admin-manager.php <COMMAND>
COMMAND:
    list                  list all admins
    add <NAME> <PASSWORD> create new admin
    delete <NAME>         delete admin
    help                  print help
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

$cmd = array_shift($argv);
$argc--;

switch ($cmd)
{
case "list":
    init_database();

    $rows = $database->query("select name from admin");

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
    $errors = validate_credentials($name, $pass);

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

    init_database();

    $s = $database->prepare("select id from admin where name = ?");
    $s->execute([$name]);
    $s->setFetchMode(PDO::FETCH_CLASS, "Admin");
    $doc = $s->fetch();

    if ($doc)
    {
        fprintf(STDERR, "Admin %s already exist\n", $name);
        help();
        exit(1);
    }

    $pass = password_hash($pass, PASSWORD_BCRYPT);

    $database
        ->prepare("insert into admin (name, password) values (?, ?)")
        ->execute([$name, $pass]);

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
    init_database();

    $s = $database->prepare("select id from admin where name = ?");
    $s->execute([$name]);
    $s->setFetchMode(PDO::FETCH_CLASS, "Admin");
    $doc = $s->fetch();

    if (!$doc)
    {
        fprintf(STDERR, "Admin %s does not exist\n", $name);
        help();
        exit(1);
    }

    $database
        ->prepare("delete from admin where name = ?")
        ->execute([$name]);

    printf("Admin %s successfully deleted\n", $name);
    break;

case "--help":
case "help":
    help(STDOUT);
    break;

default:
    fprintf(STDERR, "Unknown COMMAND %s\n", $cmd);
    help();
    exit(1);
}

exit;
