<?php
use \Httpful\Request;

class GtaaSearch {
    public static function lookupCombined($id) {
        $item = ORM::for_table('combined')->where('gtaa', $id)->limit(10)->find_array();
        return count($item) === 0 ? false : (object) $item[0];
    }

    public static function search($q) {
        $q = strtolower($q);
        return ORM::for_table('combined')->where_like('lookup', "%$q%")->limit(10)->find_array();
    }

    public static function getRecentItems() {
        return ORM::for_table('combined')->order_by_desc('id')->limit(10)->find_array();
    }
}