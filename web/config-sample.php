<?php
    define('PATH', str_replace("/index.php", "", $_SERVER['PHP_SELF']));
    define('ROOT', "//localhost/git/beng/bengdb-frontend/web"); // NO trailing slash!
    define('API_ENDPOINT', 'http://localhost:5000');
    define('DEFAULT_LANGUAGE', 'nl');
    define('DEBUG', false);

    class Config {
        public static $primaryLanguages = [
          "nl",
          "en"
        ];
    }