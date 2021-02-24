<?php


namespace Source\Core;


use PDO;

/**
 * Class Connect
 * @package Source\Core
 */
class Connect
{
    private static PDO $instance;

    /**
     * Connect constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (empty(self::$instance)){
            try {
                self::$instance = new \PDO(
                    "mysql:host=".CONF_DB_HOST.";dbname=".CONF_DB_NAME,
                    CONF_DB_USER,
                    CONF_DB_PASS,
                    [
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::ATTR_CASE => PDO::CASE_NATURAL,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                    ]
                );
            } catch (\PDOException $exception) {
                var_dump($exception);
            }
        }
        return self::$instance;
    }
}