<?php
use \Httpful\Request;

class Item extends Page  {
    private $qid, $pagetype;

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

        if (isset($this->item->sitelinks->{$this->lang})) {
            $title = $this->item->sitelinks->{$this->lang}->title;
            $article = new WikipediaArticle($title, $this->lang);
            $this->article = $article->getArticleData();
        }
    }

    public function getPageType() {
        // Right now, this is always 'person'
        return 'person';
    }
}