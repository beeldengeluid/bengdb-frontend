<?php
use \Httpful\Request;

class Item extends Page  {
    private $qid, $pageType;

    function __construct($id, $type) {
        parent::__construct();
        $this->fullurl = sprintf("%s/%s", $this->root, $id);

        if ($type == "wikidata") {
            $this->qid = $id;
        } else {
            $gtaadata = GtaaSearch::lookupCombined($id);

            if (!$gtaadata) {
                throw new Exception("Non-matched GTAA id", 404);
            }

            if ($gtaadata->bengwiki) {
                $this->bengwikitext = BengWiki::getPagetext($gtaadata->bengwiki);
            }

            $this->qid = $gtaadata->wikidata;
        }

        $this->wditem = new WikidataItem($this->qid, $this->lang);
        $this->item = $this->wditem->getItemData();
        $this->pageType = $this->getPageType();
        $this->addValues();

        if (isset($this->item->sitelinks->{$this->lang})) {
            $title = $this->item->sitelinks->{$this->lang}->title;
            $article = new WikipediaArticle($title, $this->lang);
            $this->article = $article->getArticleData();
        }
    }

    private function addValues() {
        if ($this->pageType === 'person') {
            $this->wditem->addValues(Properties::$person);
        }
    }

    public function getPageType() {
        if ($this->wditem->hasClaimWhere(Items::$person)) {
            return 'person';
        } else {
            throw new Exception("Invalid pagetype");
        }
    }
}