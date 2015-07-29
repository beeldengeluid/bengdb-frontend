<?php
    define('ABSPATH', dirname(__FILE__));

    if (!file_exists(ABSPATH . '/config.php')) {
        die("Please create a config.php file. Use config-sample.php as an example.");
    }

    require ABSPATH . '/config.php';
    require ABSPATH . '/version.php';
    require ABSPATH . '/vendor/autoload.php';

    date_default_timezone_set(TIMEZONE);

    // Setup autoloading
    spl_autoload_register(function ($name) {
        $filename = ABSPATH . strtolower("/lib/class-$name.php");

        if (!file_exists($filename)) {
            throw new Exception("Could not find class $name");
        }

        require $filename;
    });

    // Initialize database
    try {
        ORM::configure([
            "connection_string" => sprintf('mysql:host=%s;dbname=%s;charset=utf8;', DB_HOST, DB_DATABASE),
            "username" => DB_USER,
            "password" => DB_PASS
        ]);

        if (DEBUG) {
            // Log all queries
            ORM::configure([
                'logging' => true,
                'logger' => function($str) {
                    error_log($str);
                }
            ]);
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }