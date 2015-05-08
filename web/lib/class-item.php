<?php
use \Httpful\Request;

class Item extends Page  {
    private $qid, $pageType;

    const BENGWIKI_ENDPOINT = "http://beeldengeluidwiki.nl/index.php/";

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
                $this->bengwikilink = self::BENGWIKI_ENDPOINT . $gtaadata->bengwiki;
                $this->bengwikititle = str_replace("_", " ", $gtaadata->bengwiki);
            }

            if ($gtaadata->gtaapreflabel) {
                $this->bengimages = Immix::getImagesForPerson($gtaadata->gtaapreflabel);
            }

            $this->qid = $gtaadata->wikidata;
        }

        $this->wditem = new WikidataItem($this->qid, $this->lang);
        $this->wdlinkshere = new WikidataLinkshere($this->qid, $this->lang);
        $this->linkshere = $this->wdlinkshere->getData();
        $this->item = $this->wditem->getItemData();
        $this->pageType = $this->getPageType();
        $this->addValues();

        if (isset($this->item->sitelinks->{$this->lang})) {
            $title = $this->item->sitelinks->{$this->lang}->title;
            $article = new WikipediaArticle($title, $this->lang);
            $this->article = $article->getArticleData();
        }

        $this->setTitle($this->item->labels);
    }

    // Hairy code!
    private function getBirthDeathDetails() {
        $str = "";

        $hasPlaceOfBirth = !empty($this->item->placeOfBirth);
        $hasDateOfBirth = !empty($this->item->dateOfBirth);
        $hasPlaceOfDeath = !empty($this->item->placeOfDeath);
        $hasDateOfDeath = !empty($this->item->dateOfDeath);

        if ($hasPlaceOfBirth) {
            $str = $this->item->placeOfBirth[0]['label'];
        }

        if ($hasDateOfBirth && $hasPlaceOfBirth) {
            $str .= ", ";
        }

        if ($hasDateOfBirth) {
            $str .= Util::getDateStr($this->item->dateOfBirth[0]);
        }

        if (($hasPlaceOfDeath || $hasDateOfDeath) && ($hasPlaceOfBirth || $hasDateOfBirth)) {
            $str .= " – ";
        }

        if ($hasPlaceOfDeath) {
            $str .= $this->item->placeOfDeath[0]['label'];
        }

        if ($hasDateOfDeath && $hasPlaceOfDeath) {
            $str .= ", ";
        }

        if ($hasDateOfDeath) {
            $str .= Util::getDateStr($this->item->dateOfDeath[0]);
        }

        return $str;
    }

    private function addValues() {
        if ($this->pageType === 'person') {
            $this->wditem->addValues(Properties::$person);
            $this->item->birthDeathDetails = $this->getBirthDeathDetails();
        }
    }

    public function getPageType() {
        // So far, this is the only pagetype
        return 'person';
    }
}