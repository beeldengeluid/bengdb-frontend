<?php
class Properties {
    const IMAGE = "P18";
    const CREATOR = "P170";
    const DATE = "P571";
    const COUNTRY = "P17";
    const INSTANCE_OF = "P31"; // 'INSTANCEOF' is a system constant :/
    const MOVEMENT = "P135";
    const GENRE = "P136";
    const DEPICTS = "P180";
    const MATERIALSUSED = "P186";
    const COLLECTION = "P195";
    const INVENTORYNR = "P217";
    const LOCATEDIN = "P276";
    const ICONCLASS = "P1257";
    const PLACEOFBIRTH = "P19";
    const PLACEOFDEATH = "P20";
    const DATEOFBIRTH = "P569";
    const DATEOFDEATH = "P570";
    const CAUSEOFDEATH = "P509";
    const OCCUPATION = "P106";
    const LOCATION = "P625";
    const TERRITORY = "P131";
    const COMMONS_INSTITUTION_PAGE = "P1612";
    const COMMONS_CATEGORY = "P373";
    const CAST_MEMBER = "P161";
    const DIRECTOR = "P57";
    const CHILD = "P40";
    const SPOUSE = "P26";
    const AUTHOR = "P50";
    const EMPLOYER = "P108";

    public static $work = [
        "location" => self::LOCATION,
        "creator" => self::CREATOR,
        "country" => self::COUNTRY,
        "instanceOf" => self::INSTANCE_OF,
        "movement" => self::MOVEMENT,
        "genre" => self::GENRE,
        "depicts" => self::DEPICTS,
        "materialsUsed" => self::MATERIALSUSED,
        "collection" => self::COLLECTION,
        "inventoryNr" => self::INVENTORYNR,
        "locatedin" => self::LOCATEDIN,
        "iconclass" => self::ICONCLASS
    ];

    public static $person = [
        "placeOfBirth" => self::PLACEOFBIRTH,
        "dateOfBirth" => self::DATEOFBIRTH,
        "placeOfDeath" => self::PLACEOFDEATH,
        "dateOfDeath" => self::DATEOFDEATH,
        "country" => self::COUNTRY,
        "child" => self::CHILD,
        "spouse" => self::SPOUSE,
        "occupation" => self::OCCUPATION,
        "causeOfDeath" => self::CAUSEOFDEATH,
        "employer" => self::EMPLOYER
    ];

    public static $creator = [
        "movement" => self::MOVEMENT,
    ];

    public static $institution = [
        "location" => self::LOCATION,
        "country" => self::COUNTRY,
        "locatedin" => self::LOCATEDIN,
        "date" => self::DATE
    ];

    public static function init() {
        self::$creator = array_merge(self::$person, self::$creator);
    }
}

Properties::init();