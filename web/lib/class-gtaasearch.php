<?php
use \Httpful\Request;

class GtaaSearch {
    const GTAA_LOOKUPCOMBINED = "%s/gtaa/lookupcombined?q=%s";
    const GTAA_LISTCOMBINED = "%s/gtaa/listcombined";
    const GTAA_FINDITEMS = "%s/gtaa/finditems?q=%s";

    private $query, $result;

    function __construct($query) {
        $this->query = $query;
        $this->result = $this->getResult();
    }

    public function getResultData() {
        return $this->result;
    }

    public static function lookupCombined($id) {
        $url = sprintf(self::GTAA_LOOKUPCOMBINED, API_ENDPOINT, $id);
        $req = Request::get($url)->send();
        return $req->body->response ? $req->body->response : false;
    }

    public static function listCombined() {
        $url = sprintf(self::GTAA_LISTCOMBINED, API_ENDPOINT);
        $req = Request::get($url)->send();
        return $req->body->response;
    }

    private function getResult() {
        $url = sprintf(self::GTAA_FINDITEMS, API_ENDPOINT, urlencode($this->query));
        $req = Request::get($url)->send();
        return $req->body->response;
    }
}