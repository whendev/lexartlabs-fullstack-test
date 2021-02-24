<?php


use Symfony\Component\Dotenv\Dotenv;

require "vendor/autoload.php";

if (file_exists(__DIR__."/../../.env")){
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__."/../../.env");
}

