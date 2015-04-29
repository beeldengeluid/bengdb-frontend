<?php
use \Httpful\Request;

class GtaaSearch {
    const TABLE = "combined";

    public static function lookupCombined($id, $type = "gtaa") {
        $item = ORM::for_table(self::TABLE)->where($type, $id)->limit(10)->find_array();
        return count($item) === 0 ? false : (object) $item[0];
    }

    public static function search($q) {
        $q = strtolower($q);
        return ORM::for_table(self::TABLE)->where_like('lookup', "%$q%")->limit(10)->find_array();
    }

    public static function getRecentItems() {
        return ORM::for_table(self::TABLE)
            ->where('included', 1)
            ->where_not_null('image')
            ->order_by_desc('id')
            ->limit(4)
            ->find_array();
    }

    public static function getBornOnThisDay() {
        // Returns all items from the database that have a birthdate, and image, and
        // were born on this day
        $regex = sprintf(".{4}-%s-%s", date("m"), date("d"));

        return ORM::for_table(self::TABLE)
            ->where_not_null('image')
            ->where_raw("`birthdate` REGEXP '$regex'")
            ->limit(10)
            ->find_array();
    }
}