<?php
class Util {
    public static $months = [
        "Januari",
        "Februari",
        "Maart",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Augustus",
        "September",
        "Oktober",
        "November",
        "December"
    ];

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
            self::$months[$date['month']],
            $date['year']
        );
    }
}