<?php
declare(strict_types = 1);

namespace view;
use Exception;

function render(string $templ_path, array $vars = []): void
{
    extract($vars);
    require_once __DIR__ . "/../" . $templ_path . ".php";
}
