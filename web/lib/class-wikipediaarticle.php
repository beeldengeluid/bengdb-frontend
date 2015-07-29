<?php
class WikipediaArticle {
    const WIKIPEDIA_ENDPOINT = "%s/wikipedia/article?q=%s&lang=%s&cleanup=1";

    private $article, $title, $lang, $links;
    private $related = [];

    // This is a bit of a hack
    private $ignoreImages = [
        "Comicsfilm.png"
    ];

    function __construct($title, $lang) {
        $this->title = $title;
        $this->lang = $lang;
        $this->article = $this->getArticle();
    }

    private function addLinks($text) {
        $links = new WikipediaLinks($this->title, $this->lang);
        $dom = SimpleHtmlDom\str_get_html($text);

        // Loop through all the links, get the Wikidata id, check if that
        // is in the database, and if so, add the GTAA id
        foreach ($dom->find('a') as $a) {
            $qid = $links->getQidByTitle($a->title);

            if (!$qid) continue;

            $gtaa = GtaaSearch::lookupCombined($qid, "wikidata");

            if (!$gtaa) continue;

            $a->href = $gtaa->gtaa;
        }

        // Okay, now loop through everything again, check if we've actually
        // got a link. If that's NOT the case, remove the anchor reference
        foreach ($dom->find('a') as $a) {
            if (!$a->href) {
                $a->tag = "span";
                $a->removeAttribute('title');
                $a->removeAttribute('class');
            } else {
                // Nice to add them to 'related' as well
                if (!in_array($a->href, $this->related)) {
                    $this->related[] = $a->href;
                }
            }
        }

        return $dom->save();
    }

    public function getArticleData() {
        return $this->article;
    }

    private function getArticle() {
        $url = sprintf(
            self::WIKIPEDIA_ENDPOINT,
            API_ENDPOINT,
            urlencode($this->title),
            $this->lang
        );

        $article = ApiRequest::get($url);

        if (isset($article->images)) {
            $article->images = array_filter($article->images, function($img) {
                $path = pathinfo($img->title);
                $ext = strtolower($path['extension']);
                $hasext = in_array($ext, ["jpg", "png", "gif"]);
                $ignoreInTitle = Util::strInArray($img->title, $this->ignoreImages);
                return $hasext && !$ignoreInTitle;
            });

            $article->images = array_values($article->images);
            $article->firstimage = !empty($article->images) ? $article->images[0] : false;
        }

        if (defined('LOOKUP_LINKS') && LOOKUP_LINKS && isset($article->text)) {
            $article->text = $this->addLinks($article->text);
            $related = array_map("GtaaSearch::getPrettyItemById", $this->related);
            $article->related = array_values(array_filter($related));
        }

        return $article;
    }
}