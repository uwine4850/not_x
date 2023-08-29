<?php
require_once "vendor/autoload.php";

$dotenv = \Dotenv\Dotenv::createImmutable("/var/www/html/");
$dotenv->load();