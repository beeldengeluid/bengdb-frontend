<?php
class SearchResult extends Page {
    public $searchresults, $q;

    function __construct($q) {
        parent::__construct();
        $this->q = trim($q);
        $this->isSearch = true;
        $this->searchresults = GtaaSearch::search($q);
    }
}