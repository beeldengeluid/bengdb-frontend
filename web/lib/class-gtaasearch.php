<?php
use \Httpful\Request;

class GtaaSearch {
    const TABLE = "combined";

    public static function getPrettyItemById($id) {
        $item = self::lookupCombined($id);

        if (!empty($item->included) && !empty($item->image)) {
            unset($item->data);
            return $item;
        }
    }

    public static function lookupCombined($id, $type = "gtaa") {
        $item = ORM::for_table(self::TABLE)
            ->where($type, $id)
            ->limit(10)
            ->find_array();

        return count($item) === 0 ? false : (object) $item[0];
    }

    public static function search($q) {
        $q = strtolower($q);

        return ORM::for_table(self::TABLE)
            ->where_like('lookup', "%$q%")
            ->where('included', 1)
            ->limit(12)
            ->find_array();
    }

    public static function countItems() {
        return ORM::for_table(self::TABLE)->where('included', 1)->count();
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
        // were born on this day, and are 'included'
        $regex = sprintf(".{4}-%s-%s", date("m"), date("d"));

        return ORM::for_table(self::TABLE)
            ->where_not_null('image')
            ->where('included', 1)
            ->where_raw("`birthdate` REGEXP '$regex'")
            ->limit(4)
            ->find_array();
    }
}