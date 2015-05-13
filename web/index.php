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

        // Dutch thousand seperators
        $renderer->getExtension('core')->setNumberFormat(0, ',', '.');

        if (DEBUG)  {
            $renderer->addExtension(new Twig_Extension_Debug());
        }

        $data = (new ArrayObject($obj))->getArrayCopy();
        $data['template'] = $template;
        $renderer->addGlobal('fullurl', $data['fullurl']);
        $renderer->addGlobal('root', $data['root']);
        echo $renderer->render("$template.html", $data);
        $app->stop();
    }

    function renderJson($data) {
        global $app;

        $app->response->headers->set('Content-Type', 'application/json');
        $app->response->headers->set('Access-Control-Allow-Origin', '*');
        $data = json_encode($data);

        if (!$data) {
            $data = json_encode(['error' => 'JSON encoding error']);
        }

        echo $data;

        $app->stop();
    }

    function getItem($id) {
        $type = Util::getIdType($id);

        if ($type == "unknown") {
            throw new Exception("Invalid ID", 400);
        }

        // Check if there is a GTAA id associated with this
        // Wikidata ID, if so, redirect
        if ($type == "wikidata") {
            $gtaaid = GtaaSearch::lookupCombined($id, "wikidata");

            if ($gtaaid) {
                throw new Exception(ROOT . "/" . $gtaaid->gtaa, 301);
            }
        }

        return new Item($id, $type);
    }

    function renderItem($id, $format = "html") {
        global $app, $renderer;

        try {
            $item = getItem($id);
        } catch (Exception $e) {
            $code = $e->getCode();

            if ($code == 301) {
                // Specific permanent redirect
                $app->redirect($e->getMessage());
            }

            if ($code == 302) {
                // Redirect, try without parameters
                $app->redirect("$id");
            }

            if (DEBUG) {
                throw $e;
            }

            error_log(
                sprintf(
                    "[bengdb-frontend] Error at %s(%s): %s (%s)",
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage(),
                    $e->getCode()
                )
            );

            $page = new Page();
            $page->setErrorCode($code);

            $app->response->setStatus($code);
            render("error", $page);

            $app->stop();
        }

        if ($format == "json") {
            renderJson($item);
        } else {
            render($item->getPageType(), $item);
        }
    }

    // Homepage
    $app->get("/", function() use ($app) {
        if ($app->request->get('q')) {
            $q = $app->request->get('q');

            // Maybe we've got a Wikidata ID or GTAA id? In that case, redirect
            // to the specific pages
            if (Util::getIdType($q) != "unknown") {
                $app->redirect($q);
            }

            render("home", new SearchResult($q));
        } else {
            render("home", new Homepage());
        }
    });

    $app->get("/about", function() use ($app) {
        render("about", new Homepage());
    });

    $app->get("/api/search", function() use ($app) {
        $q = $app->request->get('q');

        if (!$q) {
            renderJson(['error' => "Query is required"]);
        }

        $result = GtaaSearch::search($q);

        if (empty($result)) {
            renderJson(["error" => "No results"]);
        }

        $id = $result[0]['gtaa'];
        renderItem($id, "json");
    });

    $app->get("/:id.json", function($id) use ($app) {
        renderItem($id, "json");
    });

    // Conventional URLS
    $app->get("/:id", function($id) {
        renderItem($id);
    });

    $app->run();