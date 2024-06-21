#!/bin/php
<?php

declare(strict_types = 1);
error_reporting(E_ALL ^ E_WARNING);

class Config
{
    public string $gh_username = "";
    public string $gh_apikey = "";
    public string $projects_dir = "";

    function is_valid(): bool
    {
        return $this->gh_username && $this->gh_apikey && $this->projects_dir;
    }
}

class Project
{
    public string $name = "";
    public string $path = "";

    function __construct($name, $dirpath)
    {
        $this->name = $name;
        $this->path = "$dirpath/$name";
    }
}

function print_usage($fd): void
{
    global $argv;

    fprintf(
        $fd,
        "Usage: %s [NAME|OPTION]\n" .
        "Creates new programming project\n" .
        "\n" .
        "NAME:\n" .
        "   project name in kebab-case\n" .
        "\n" .
        "OPTION:\n" .
        "   --help       shows this message\n" .
        "   --gen-config generates config file\n",
        $argv[0]
    );
}

function get_config_folder(): string
{
    $user = get_current_user();
    return "/home/$user/.config/create-project";
}

function get_config_path(): string
{
    $folder = get_config_folder();
    return "$folder/config";
}

function parse_config(): Config
{
    $config_path = get_config_path();
    $content = file_get_contents($config_path);

    if ($content === false)
    {
        fprintf(STDERR, "failed to read config file %s: file does not exist\n", $config_path);
        exit(1);
    }

    $config = new Config();

    $lines = explode("\n", $content);
    foreach ($lines as $line)
    {
        if (!trim($line)) continue;

        $kv = explode("=", $line);
        if (count($kv) != 2) continue;

        $key = trim($kv[0]);
        $value = trim($kv[1]);

        switch ($key)
        {
            case "gh_username":
                $config->gh_username = $value;
                break;
            case "gh_apikey":
                $config->gh_apikey = $value;
                break;
            case "projects_dir":
                $config->projects_dir = $value;
                break;
            default:
                fprintf(STDERR, "unknown config field %s\n", $key);
        }
    }

    if (!$config->is_valid())
    {
        fprintf(STDERR, "config is missing required fields\n");
        exit(1);
    }

    return $config;
}

function generate_config(): void
{
    $file = get_config_path();
    $folder = get_config_folder();

    if (!file_exists($file))
    {
        if (!file_exists($folder))
        {
            if (mkdir($folder, 0700, true) === false)
            {
                fprintf(STDERR, "failed to create config folder %s\n", $folder);
                exit(1);
            }
        }

        $content = "gh_apikey    =\n" .
                   "gh_username  =\n" .
                   "projects_dir = /absolute/path/to/dir\n";

        if (file_put_contents($file, $content) === false)
        {
            fprintf(STDERR, "failed to create config file %s\n", $file);
            exit(1);
        }
    }
}

function confirm(Project $p): void
{
    printf("Create project %s (y/n)\n", $p->path);
    $input = trim(fgets(STDIN));

    if ($input === false)
    {
        fprintf(STDERR, "failed to read input from user\n");
        exit(1);
    }

    if ($input !== "y")
    {
        printf("Canceling...\n");
        exit;
    }
}

function create_repo(Project $p, Config $config): void
{
    printf("Creating GitHub repository...\n");

    $c = curl_init("https://api.github.com/user/repos");

    curl_setopt_array($c, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode(["name" => $p->name]),
        CURLOPT_HTTPHEADER => [
            "content-type: application/json",
            "user-agent: php-script",
            "authorization: Bearer " . $config->gh_apikey
        ]
    ]);

    $res = curl_exec($c);

    if ($res === false)
    {
        fprintf(STDERR, "failed to make request: %s\n", curl_error($c));
        exit(1);
    }

    $info = curl_getinfo($c);

    if ($info["http_code"] !== 201)
    {
        $res = json_decode($res, true);

        fprintf(STDERR, "failed to create new repository: %s\n", $res["message"]);

        if (isset($res["errors"]))
        {
            foreach($res["errors"] as $err)
            {
                if (!isset($err["message"])) continue;
                fprintf(STDERR, "%s\n", $err["message"]);
            }
        }

        exit(1);
    }

    curl_close($c);
}

function delete_repo(Project $p, Config $config): void
{
    printf("Deleting GitHub repository...\n");

    $c = curl_init("https://api.github.com/repos/$config->gh_username/$p->name");

    curl_setopt_array($c, [
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "content-type: application/json",
            "user-agent: php-script",
            "authorization: Bearer " . $config->gh_apikey
        ]
    ]);

    $res = curl_exec($c);

    if ($res === false)
    {
        fprintf(STDERR, "failed to make request: %s\n", curl_error($c));
        exit(1);
    }

    $info = curl_getinfo($c);

    if ($info["http_code"] !== 204)
    {
        $res = json_decode($res, true);

        fprintf(STDERR, "failed to delete repository: %s\n", $res["message"]);

        if (isset($res["errors"]))
        {
            foreach($res["errors"] as $err)
            {
                if (!isset($err["message"])) continue;
                fprintf(STDERR, "%s\n", $err["message"]);
            }
        }

        exit(1);
    }

    curl_close($c);
}

function build_md_title(string $s): string
{
    $title = "# ";

    $words = explode("-", $s);
    foreach ($words as $i => $word)
    {
        if ($i > 0) $title .= " ";
        $title .= ucfirst($word);
    }

    return $title;
}

if ($argc < 2)
{
    fprintf(STDERR, "not enough arguments\n\n");
    print_usage(STDERR);
    exit(1);
}

$arg = $argv[1];

if (str_starts_with($arg, "--"))
{
    if ($arg == "--help")
    {
        print_usage(STDOUT);
        exit;
    }

    if ($arg == "--gen-config")
    {
        generate_config();
        exit;
    }

    fprintf(STDERR, "unknown option %s\n\n", $arg);
    print_usage(STDERR);
    exit(1);
}

$config = parse_config();
$project = new Project($arg, $config->projects_dir);

confirm($project);
create_repo($project, $config);

printf("Cloning repository into %s...\n", $project->path);

$md_title = build_md_title($project->name);
$res = exec(
    "cd $config->projects_dir && " .
    "git clone git@github.com:$config->gh_username/$project->name.git && " .
    "cd $project->name && " .
    "touch .gitignore && " .
    "echo '$md_title' > README.md && " .
    "git add . && " .
    "git commit -m 'initial commit' && " .
    "git push origin master",
    $output,
    $retcode
);

if ($res === false || $retcode !== 0)
{
    fprintf(STDERR, "failed to clone repository\n");
    delete_repo($project, $config);
    exit(1);
}

printf("Success\n");
