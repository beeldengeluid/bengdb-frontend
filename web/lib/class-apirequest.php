<?php
use \Httpful\Request;

class ApiRequest {
    public static function get($url) {
        $res = Request::get($url)->send();

        if ($res->code == 500) {
            throw new Exception("Could not connect to API", 500);
        }

        if ($res->code !== 200) {
            throw new Exception("Api error", $res->code);
        }

        if (isset($res->body->response)) {
            return $res->body->response;
        }

        if (isset($res->body)) {
            return $res->body;
        }

        throw new Exception("This item does not exist", 404);
    }
}