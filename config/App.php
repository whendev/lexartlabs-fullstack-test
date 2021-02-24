<?php
date_default_timezone_set("America/Sao_Paulo");
// DATA BASE AND HOST
define("CONF_DB_HOST", $_ENV["CONF_DB_HOST"]);
define("CONF_DB_NAME", $_ENV["CONF_DB_DATABASE"]);
define("CONF_DB_USER", $_ENV["CONF_DB_USER"]);
define("CONF_DB_PASS", $_ENV["CONF_DB_PASSWORD"]);

// MESSAGE
define("CONF_MESSAGE_CLASS","alert alert-dismissible fade show");
define("CONF_MESSAGE_SUCCESS","alert-success");
define("CONF_MESSAGE_INFO","alert-primary");
define("CONF_MESSAGE_ERROR","alert-danger");
define("CONF_MESSAGE_WARNING","alert-warning");


// PROJECT URLs
define("CONF_URL_BASE", $_ENV["CONF_URL_BASE"]);
define("CONF_URL_TEST", $_ENV["CONF_URL_TEST"]);

// VIEW
define("CONF_VIEW_PATCH", __DIR__."/../resources/views/");
define("CONF_VIEW_APP", "app");
define("CONF_VIEW_ADMIN", "admin");
define("CONF_VIEW_EXT", "php");

// PASSWORD
define("CONF_PASSWD_MAX_LEN", 30);
define("CONF_PASSWD_MIN_LEN", 8);
define("CONF_PASSWD_ALGO", PASSWORD_DEFAULT);
define("CONF_PASSWD_OPTIONS", ["cost" => 10]);