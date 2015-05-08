<?php
class WikipediaLinks {
    const WIKIPEDIA_LINKS_ENDPOINT = "%s/wikipedia/links?q=%s&lang=%s";

    function __construct($title, $lang) {
        $this->title = $title;
        $this->lang = $lang;
        $this->links = $this->getLinks();
    }

    public function getQidByTitle($title) {
        $qid = array_filter($this->links, function($link) use ($title) {
            return $link->title == $title;
        });

        if (empty($qid)) {
            return false;
        } else {
            $qid = reset($qid);
            return isset($qid->wikidata) ? $qid->wikidata : false;
        }
    }

    private function getLinks() {
        $url = sprintf(
            self::WIKIPEDIA_LINKS_ENDPOINT,
            API_ENDPOINT,
            urlencode($this->title),
            $this->lang
        );

        return ApiRequest::get($url);
    }
}