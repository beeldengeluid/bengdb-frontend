<?php
use \Httpful\Request;

class ApiRequest {
    public static function get($url) {
        $res = Request::get($url)->send();

        if ($res->code == 500) {
            throw new Exception("Could not connect to API", 500);
        }

        if (!isset($res->body->response)) {
            throw new Exception("This item does not exist", 404);
        }

        return $res->body->response;
    }
}