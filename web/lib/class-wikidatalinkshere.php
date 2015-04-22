<?php
class WikidataLinkshere {
    const WIKIDATA_ENDPOINT = "%s/wikidata/linkshere?q=%s&lang=%s&resolvedata=minimal";

    private $qid, $data, $lang;

    function __construct($qid, $lang) {
        $this->qid = $qid;
        $this->lang = $lang;
        $this->data = $this->getLinksHere();
    }

    public function getData() {
        return $this->data;
    }

    private function getLinksHere() {
        $url = sprintf(self::WIKIDATA_ENDPOINT, API_ENDPOINT, $this->qid, $this->lang);

        try {
            $data = ApiRequest::get($url);
        } catch (Exception $e) {
            return false;
        }

        $items = array_values((array) $data);

        // Filter out Category links
        return array_filter($items, function($item) {
            $labels = isset($item->labels) ? $item->labels : false;
            return strpos($labels, ":") === false && $labels;
        });
    }
}