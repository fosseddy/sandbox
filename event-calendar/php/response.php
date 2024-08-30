<?php
declare(strict_types = 1);

function render_view(string $templ_path, array $vars = []): void
{
    extract($vars);
    require_once ROOT . "views/" . $templ_path . ".php";
}
