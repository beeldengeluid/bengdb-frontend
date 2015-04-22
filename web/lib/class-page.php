<?php
use \Httpful\Request;

class Page {
    public $root, $lang, $title, $fullurl, $errorCode, $langlabel, $langcodes;
    private $settings;

    function __construct() {
        $this->root = ROOT;

        // Ah, really, for f... sake
        $this->rootSlashed = substr(ROOT, -1) == "/" ? ROOT : ROOT . "/";

        $this->version = VERSION;
        $this->settings = new Settings();
        $this->title = "BengDB";
        $this->fullurl = $this->root;
        $this->langcodes = $this->getLangCodes();
        $this->langcodesPrimary = [];

        foreach (Config::$primaryLanguages as $code) {
            $this->langcodesPrimary[$code] = $this->langcodes[$code];
        }

        $this->lang = $this->getLanguage();
        $this->langlabel = $this->langcodes[$this->lang];
    }

    public function setErrorCode($code) {
        $this->errorCode = $code;
    }

    private function getLangCodes() {
        return [
            "en" => "English",
            "nl" => "Nederlands"
        ];
    }

    private function getLanguage() {
        if (!empty($_GET['lang'])) {
            $lang = strtolower($_GET['lang']);

            if (array_key_exists($lang, $this->langcodes)) {
                $this->settings->set("lang", $lang);
                return $lang;
            } else {
                // Redirect to the default URL
                throw new Exception("Invalid language code", 302);
            }
        }

        if (
            $this->settings->has("lang") &&
            array_key_exists($this->settings->get("lang"), $this->langcodes)
        ) {
            return $this->settings->get("lang");
        } else {
            $this->settings->set("lang", DEFAULT_LANGUAGE);
            return DEFAULT_LANGUAGE;
        }
    }
}