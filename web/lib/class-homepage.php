<?php
class Homepage extends Page {
    public $featured;

    function __construct() {
        parent::__construct();
        $this->recentItems = GtaaSearch::getRecentItems();
    }
}