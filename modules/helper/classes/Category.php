<?php

class Category {

    public static function get_tree($cat_list) {
        foreach ($cat_list as $key=>$item) {
            $cat_list[$item['parent_id']]['children'][] = &$cat_list[$key];
        }
        return isset($cat_list[0]['children']) ? $cat_list[0]['children'] : array();
    }
    
    public static function get_children_tree($cat_list, $pid=0) {
        $ret = array();
        foreach ($cat_list as &$item) {
            if ($item['parent_id'] == $pid) {
                $item['children'] = self::get_children_tree($cat_list, $item['id']);
                $ret[] = $item;
            }
        }
        return $ret;
    }
    
    public static function get_children_ids($cat_list, $pid=0) {
        $ret = array();
        foreach ($cat_list as $item) {
            if ($item['parent_id'] == $pid) {
                $ret[] = $item['id'];
                $children_ids = self::get_children_ids($cat_list, $item['id']);
                $ret = array_merge($ret, $children_ids);
            }
        }
        return $ret;
    }
    
    public static function get_parents_list($cat_list, $id) {
        $ret = array();
        foreach($cat_list as $item) {
            if ($item['id'] == $id) {
                $ret[] = $item;
                $parent_list = self::get_parents_list($cat_list, $item['parent_id']);
                $ret = array_merge($parent_list, $ret);
            }
        }
        return $ret;
    }
    
    public static function get_parents_ids($cat_list, $id) {
        $ret = array();
        foreach($cat_list as $item) {
            if ($item['id'] == $id) {
                $ret[] = $item['id'];
                $parent_ids = self::get_parents_ids($cat_list, $item['parent_id']);
                $ret = array_merge($parent_ids, $ret);
            }
        }
        return $ret;
    }
    
}
