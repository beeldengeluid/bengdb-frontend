<?php
class Homepage extends Page {
    public $featured;

    function __construct() {
        parent::__construct();
        $this->allItems = GtaaSearch::listCombined();
    }
}