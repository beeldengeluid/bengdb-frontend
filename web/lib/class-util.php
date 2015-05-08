<?php
class Util {
    public static $months = [
        "januari",
        "februari",
        "maart",
        "april",
        "mei",
        "juni",
        "juli",
        "augustus",
        "september",
        "oktober",
        "november",
        "december"
    ];

    public static function getIdType($id) {
        // Check for Q items
        if (strtolower($id[0]) == "q") {
            return "wikidata";
        } else if (ctype_digit($id)) {
            return "gtaa";
        } else {
            return "unknown";
        }
    }

    // HACK: This is really, pretty ugly
    // See < https://en.wikipedia.org/wiki/Proleptic_Gregorian_calendar >
    public static function parseProlepticDate($str, array $opts = []) {
        $date = substr($str, 1);
        $date = ltrim($date, '0');

        if (isset($opts['dateonly'])) {
            return substr($date, 0, 10);
        } else {
            return $date;
        }
    }

    public static function getDateStr(array $date) {
        return sprintf("%s %s %s",
            $date['day'],
            self::$months[$date['month'] - 1],
            $date['year']
        );
    }

    public static function strInArray($haystack, $needles) {
        $count = array_filter($needles, function($needle) use ($haystack) {
            return strpos($haystack, $needle) !== false;
        });

        $count = count($count);

        return $count > 0;
    }
}