<?php
use \Httpful\Request;

class GtaaSearch {
    const GTAA_SEARCH_ENDPOINT = "%s/gtaa/findconcepts?q=%s&scheme=Makers";
    const GTAA_LOOKUPCOMBINED_ENDPOINT = "%s/gtaa/lookupcombined?q=%s";
    const GTAA_LISTCOMBINED = "%s/gtaa/listcombined";

    private $query, $result;

    function __construct($query) {
        $this->query = $query;
        $this->result = $this->getResult();
    }

    public function getResultData() {
        return $this->result;
    }

    public static function lookupCombined($gtaaid) {
        $url = sprintf(self::GTAA_LOOKUPCOMBINED_ENDPOINT, API_ENDPOINT, $gtaaid);
        $req = Request::get($url)->send();

        return $req->body->response ? $req->body->response->wikidata : false;
    }

    public static function listCombined() {
        $url = sprintf(self::GTAA_LISTCOMBINED, API_ENDPOINT);
        $req = Request::get($url)->send();
        return $req->body->response;
    }

    private function getResult() {
        $url = sprintf(self::GTAA_SEARCH_ENDPOINT,
            API_ENDPOINT,
            urlencode($this->query)
        );

        $req = Request::get($url)->send();

        if (!$req->body->response) return [];

        // Check if we have matching Wikidata ID's
        return array_filter($req->body->response, function($match) {
            return $this->lookupCombined($match->id);
        });
    }
}