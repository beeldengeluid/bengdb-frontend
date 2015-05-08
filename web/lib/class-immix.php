<?php
class Immix {
    const IMMIX_IMAGESFORPERSON = "%s/immix/imagesforperson?q=%s";

    function __construct() {
    }

    public static function getImagesForPerson($person) {
        $url = sprintf(self::IMMIX_IMAGESFORPERSON, API_ENDPOINT, urlencode($person));

        try {
            $data = ApiRequest::get($url);

            // Fucking hell
            if (is_object($data)) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return $data;
    }
}