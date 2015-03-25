<?php
    define('ABSPATH', dirname(__FILE__));

    if (!file_exists(ABSPATH . '/config.php')) {
        die("Please create a config.php file. Use config-sample.php as an example.");
    }

    require ABSPATH . '/config.php';
    require ABSPATH . '/version.php';
    require ABSPATH . '/vendor/autoload.php';

    spl_autoload_register(function ($name) {
        $filename = ABSPATH . "/lib/class-$name.php";

        if (!file_exists($filename)) {
            throw new Exception("Could not find class $name");
        }

        require $filename;
    });