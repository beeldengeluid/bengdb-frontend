<?php
class Util {
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
}