<?php

declare(strict_types=1);

date_default_timezone_set("Asia/Tomsk");
session_start();
try {
    $app = require_once __DIR__ . "/../core/bootstrap.php";
    $app->run();
} catch (\Throwable $exception) {
    echo "<pre>";
    print_r($exception);
    echo "</pre>";
}
