<?php

ob_start();

require __DIR__."/vendor/autoload.php";

use CoffeeCode\Router\Router;

$route = new Router($_ENV["CONF_URL_BASE"], ":");

$route->namespace("Source\Controllers");

// APP
$route->get("/", "App:home");
$route->get("/login", "App:login");
$route->post("/login", "App:login");
$route->post("/search", "App:search");
$route->get("/search/{web}/{category}/{search}/p/{page}", "App:search");
$route->get("/search/{web}/{category}/p/{page}", "App:search");

// ADMIN
$route->group("/admin");
$route->get("/", "Admin:home");
$route->get("/auth", "Admin:auth");


// Group error
$route->group("/error");
$route->get("/{errcode}", "App:error");

/**
 * This method executes the routes
 */
$route->dispatch();

/*
 * Redirect all errors
 */
if ($route->error()) {
    $route->redirect("/error/{$route->error()}");
}

ob_end_flush();