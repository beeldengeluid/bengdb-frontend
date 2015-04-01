<?php
    require 'bootstrap.php';

    $app = new \Slim\Slim([
        'debug' => DEBUG
    ]);

    function render($template, $obj) {
        global $app;

        $loader = new Twig_Loader_Filesystem(ABSPATH . "/templates");

        $renderer = new Twig_Environment($loader, [
            "cache" => ABSPATH . "/cache",
            "debug" => DEBUG
        ]);

        if (DEBUG)  {
            $renderer->addExtension(new Twig_Extension_Debug());
        }

        $data = new ArrayObject($obj);
        echo $renderer->render("$template.html", $data->getArrayCopy());
        $app->stop();
    }

    function renderPage($id, $format = "html") {
        global $app, $renderer;

        // Check for Q items
        if (strtolower($id[0]) == "q") {
            $type = "wikidata";
        } else {
            $type = "gtaa";
        }

        try {
            $item = new Item($id, $type);
        } catch (Exception $e) {
            if (DEBUG) {
                throw $e;
            }

            error_log($e->getMessage());
            $code = $e->getCode();
            $page = new Page();
            $page->setErrorCode($code);

            $app->response->setStatus($code);
            render("error", $page);

            $app->stop();
        }

        if ($format == "json") {
            echo json_encode($item);
        } else {
            render($item->getPageType(), $item);
        }
    }

    // Homepage
    $app->get("/", function() use ($app) {
        if ($app->request->get('q')) {
            $q = $app->request->get('q');
            render("home", new SearchResult($q));
        } else {
            render("home", new Homepage());
        }
    });


    $app->get("/:id.json", function($id) use ($app) {
        $app->response->headers->set('Content-Type', 'application/json');
        renderPage($id, "json");
    });

    // Conventional URLS
    $app->get("/:id", function($id) {
        renderPage($id);
    });

    $app->run();