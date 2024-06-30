<?php
declare(strict_types = 1);

namespace env;
use Exception;

function read(string $path): void
{
    $orig_path = $path;
    $path = realpath($path);

    if ($path === false)
    {
        throw new Exception("file '$orig_path' does not exist");
    }

    $lines = explode("\n", file_get_contents($path));

    foreach ($lines as $i => $line)
    {
        $i += 1; // art: line number should start with 1
        $line = trim($line);

        if (!$line)
        {
            continue;
        }

        $kv = explode("=", $line);

        if (count($kv) !== 2)
        {
            error_log("$path:$i: invalid line, skipping...");
            continue;
        }

        $k = trim($kv[0]);

        if (!$k)
        {
            error_log("$path:$i: empty key, skipping...");
            continue;
        }

        $v = trim($kv[1]);

        if (!$v)
        {
            error_log("$path:$i: empty value, skipping...");
            continue;
        }

        $_ENV[$k] = $v;
    }

}
