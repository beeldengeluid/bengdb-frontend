<?php
use \Httpful\Request;

class BengWiki {
    const BENGWIKI_ENDPOINT = "%s/bengwiki/%s?q=%s";

    function __construct() {
    }

    public static function getPagetext($q) {
        $url = sprintf(self::BENGWIKI_ENDPOINT, API_ENDPOINT, "pagetext", $q);
        $req = Request::get($url)->send();
        return isset($req->body->response) ? $req->body->response : false;
    }
}