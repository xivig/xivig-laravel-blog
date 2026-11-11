<?php

$autoloadPath = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    throw new RuntimeException('Composer autoload.php not found. Run "composer install" first.');
}

return require $autoloadPath;
