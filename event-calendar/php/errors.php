<?php
declare(strict_types = 1);

function init_errors(): void
{
    set_exception_handler(function(Throwable $e): void {
        header("Location: /error.php");

        $msg = sprintf("\n\n%s: %s in %s:%d\nStack trace:\n%s\n",
                       get_class($e),
                       $e->getMessage(),
                       $e->getFile(),
                       $e->getLine(),
                       $e->getTraceAsString());

        error_log($msg);
        exit;
    });
}
