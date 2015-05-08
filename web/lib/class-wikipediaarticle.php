<?php
use \Httpful\Request;

class WikipediaArticle {
    const WIKIPEDIA_ENDPOINT = "%s/wikipedia/article?q=%s&lang=%s";

    private $article, $title, $lang;

    // This is a bit of a hack
    private $ignoreImages = [
        "Comicsfilm.png"
    ];

    function __construct($title, $lang) {
        $this->title = $title;
        $this->lang = $lang;
        $this->article = $this->getArticle();
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

        $res = Request::get($url)->send();

        if (!isset($res->body->response)) {
            throw new Exception("No article available for this id");
        }

        $article = $res->body->response;

        if ($article->images) {
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

        return $article;
    }
}