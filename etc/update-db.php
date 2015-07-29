<?php
// This script updates the database with a local copy of matched GTAA-Wikidata
// numbers.
// How this works?
// 1. Get all the Wikidata items having the GTAA property (P1741)
// 2. Check which Q numbers are not in the database
// 3. Look up those numbers using the Wikidata API
// 4. Check if they are 'makers', and if so, mark them as such
// 5. Enter the items in the database
require dirname(__FILE__) . '/../web/bootstrap.php';
use \Httpful\Request;

define('WDQ_URL', "http://wdq.wmflabs.org/api?q=CLAIM[1741]");
define('WIKIDATA_URL', "https://www.wikidata.org/w/api.php?action=wbgetentities&ids=%s&languages=en|nl&format=json&props=aliases|labels|descriptions|claims&languagefallback=1");
define('GTAA_ENDPOINT', 'http://openskos.beeldengeluid.nl/gtaa/%s.json');

define('PROP_FIELDOFWORK', 'P101');
define('PROP_COUNTRYOFCITIZENSHIP', 'P27');
define('PROP_OCCUPATION', 'P106');
define('PROP_IMAGE', 'P18');
define('PROP_DATEOFBIRTH', 'P569');
define('PROP_DATEOFDEATH', 'P570');

define('ITEM_ENTERTAINMENT', 'Q173799');
define('ITEM_NETHERLANDS', 'Q55');
define('ITEM_SINGER', 'Q177220');
define('ITEM_TVACTOR', 'Q10798782');
define('ITEM_FILMACTOR', 'Q10800557');
define('ITEM_ACTOR', 'Q33999');
define('ITEM_VOICEACTOR', 'Q2405480');
define('ITEM_TVPRESENTER', 'Q947873');
define('ITEM_PROGRAMMAKER', 'Q2548714');
define('ITEM_TVPRODUCER', 'Q578109');
define('ITEM_TVDIRECTOR', 'Q2059704');
define('ITEM_WRITER', 'Q36180');
define('ITEM_PRESENTER', 'Q13590141');
define('ITEM_RADIOHOST', 'Q2722764');
define('ITEM_RECORDPRODUCER', 'Q183945');

$entertainer = [
    [PROP_FIELDOFWORK, ITEM_ENTERTAINMENT],
    [PROP_OCCUPATION, ITEM_SINGER],
    [PROP_OCCUPATION, ITEM_TVACTOR],
    [PROP_OCCUPATION, ITEM_FILMACTOR],
    [PROP_OCCUPATION, ITEM_ACTOR],
    [PROP_OCCUPATION, ITEM_VOICEACTOR],
    [PROP_OCCUPATION, ITEM_TVPRESENTER],
    [PROP_OCCUPATION, ITEM_PROGRAMMAKER],
    [PROP_OCCUPATION, ITEM_TVPRODUCER],
    [PROP_OCCUPATION, ITEM_TVDIRECTOR],
    [PROP_OCCUPATION, ITEM_WRITER],
    [PROP_OCCUPATION, ITEM_PRESENTER],
    [PROP_OCCUPATION, ITEM_RADIOHOST],
    [PROP_OCCUPATION, ITEM_RECORDPRODUCER]
];

function hasClaim($claims, $prop, $item) {
    if (!isset($claims->$prop)) return false;

    foreach ($claims->$prop as $claim) {
        if ($claim->mainsnak->datavalue->value->{'numeric-id'} == substr($item, 1)) {
            return true;
        }
    }

    return false;
}

function isEntertainer($claims) {
    global $entertainer;

    foreach ($entertainer as $pair) {
        if (hasClaim($claims, $pair[0], $pair[1])) {
            return true;
        }
    }

    return false;
}

function fetchItems($items) {
    $url = sprintf(WIKIDATA_URL, implode("|", $items));
    echo "Getting Wikidata items: $url\n";
    $req = Request::get($url)->send();

    if (!isset($req->body->entities)) {
        die("Could not fetch api items\n");
    }

    foreach ($req->body->entities as $qid => $item) {
        echo "Parsing $qid\n";

        $label = $item->labels->nl->value;
        $description = $item->descriptions->nl->value;
        $gtaa = $item->claims->P1741[0]->mainsnak->datavalue->value;

        // Lookup is an 'index' that simply combines the label and all aliases
        if (isset($item->aliases->nl)) {
            $lookup = array_map(function($item) {
                return $item->value;
            }, $item->aliases->nl);
            $lookup[] = $label;
            $lookup = implode(",",  $lookup);
        } else {
            $lookup = $label;
        }

        $included = isEntertainer($item->claims) &&
                    hasClaim($item->claims, PROP_COUNTRYOFCITIZENSHIP, ITEM_NETHERLANDS);

        $newitem = ORM::for_table('combined')->create();

        $newitem->set([
            "gtaa" => $gtaa,
            "wikidata" => $qid,
            "label" => $label,
            "description" => $description,
            "lookup" => $lookup,
            "included" => $included,
            "data" => json_encode($item)
        ]);

        $newitem->save();

        echo "Saved $qid\n";
    }
}

function addItemDate($item, $propid, $proplabel) {
    $data = json_decode($item->data);

    if (isset($data->claims->{$propid})) {
        $date = $data->claims->{$propid}[0]->mainsnak->datavalue->value->time;
        echo "Setting $proplabel to $date\n";

        // Make sure fucking Cicero doesn't die in the year 2043
        if ($date[0] == "-") {
            $item->{$proplabel} = null;
            return;
        }

        $item->{$proplabel} = Util::parseProlepticDate($date, ['dateonly' => true]);
    }
}

function addMetadata() {
    echo "Now adding birthdate / image\n";

    $items = ORM::for_table('combined')->where_not_null('data')->find_many();

    foreach ($items as $item) {
        $data = json_decode($item->data);

        printf("Parsing %s\n", $data->id);

        if (isset($data->claims->P18)) {
            $img = $data->claims->P18[0]->mainsnak->datavalue->value;
            $item->image = $img;
        }

        addItemDate($item, PROP_DATEOFBIRTH, "birthdate");
        addItemDate($item, PROP_DATEOFDEATH, "deathdate");

        printf("Got %s/%s/%s\n", $item->image, $item->birthdate, $item->deathdate);

        $item->save();
    }


    echo("Okay, metadata added\n");
}

function fetchNewItems() {
    $totalFetched = 0;
    $req = Request::get(WDQ_URL)->send();
    echo "Got WDQ query\n";

    if (!isset($req->body->items)) {
        die("Could not fetch WDQ items\n");
    }

    $itemsToFetch = [];

    foreach ($req->body->items as $id) {
        $qid = "Q$id";
        $item = ORM::for_table('combined')->where('wikidata', $qid)->find_one();

        if ((bool) $item) {
            // $item is in the database
            echo "$qid is in the database\n";
            continue;
        }

        echo "$qid is NOT in the database\n";
        $itemsToFetch[] = $qid;

        // If we have 50 items (the limit for the API), fetch and add to db
        if (count($itemsToFetch) == 50) {
            fetchItems($itemsToFetch);
            $totalFetched += 50;
            $itemsToFetch = [];
        }
    }

    // And fetch remaining items if available
    if (count($itemsToFetch) > 0) {
        fetchItems($itemsToFetch);
    }

    $totalFetched += count($itemsToFetch);

    echo "That's all folks, fetched " . $totalFetched . " items\n" ;
}

function addGtaaLabels() {
    $items = ORM::for_table('combined')->where_null('gtaapreflabel')->find_many();

    foreach ($items as $item) {
        $url = sprintf(GTAA_ENDPOINT, $item->gtaa);
        $req = Request::get($url)->send();

        if (!isset($req->body->prefLabel)) {
            die("Could not fetch preflabel\n");
        }

        $prefLabel = $req->body->prefLabel[0];

        printf("%s : %s\n", $item->gtaa, $prefLabel);

        $item->gtaapreflabel = $prefLabel;

        $item->save();
    }
}

function main() {
    fetchNewItems();
    addMetadata();
    addGtaaLabels();
    die("That's all folks..\n");
}

main();