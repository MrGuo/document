<?php

$a = array(
    array('id' => 1, 'parent_id' => NULL, 'name' => '总部一', 'user_list' => array()),
    array('id' => 2, 'parent_id' => 1, 'name' => '东北区', 'user_list' => array()),
    array('id' => 3, 'parent_id' => 1, 'name' => '华北区', 'user_list' => array()),
    array('id' => 4, 'parent_id' => 2, 'name' => '技术部', 'user_list' => array(
        array('name' => 'rd_01'), 
        array('name' => 'rd_02'),
        array('name' => 'rd_03')
    )),
    array('id' => 5, 'parent_id' => 2, 'name' => '采购部', 'user_list' => array(
        array('name' => 'cai_01'), 
        array('name' => 'cai_02')
    )),
    array('id' => 6, 'parent_id' => 3, 'name' => '采购部', 'user_list' => array(
        array('name' => 'cai_01'), 
        array('name' => 'cai_02')
    )),
    array('id' => 7, 'parent_id' => 4, 'name' => '技术部-南方分部', 'user_list' => array(
        array('name' => 'rd_04'), 
        array('name' => 'rd_05'),
        array('name' => 'rd_06')
    )),
    array('id' => 10, 'parent_id' => NULL, 'name' => '总部二', 'user_list' => array()),
);

function find_son($parent_id) {
    global $a;
    $r = array();
    foreach ($a as $value) {
        if ($value['parent_id'] == $parent_id) {
            $r[] = $value;
        }
    }
    return $r;
}

function deep_sort($parent_id, &$arr) {
    $result = find_son($parent_id);
    if (empty($result)) {
        return;
    }
    # print_r($arr);
    foreach ($result as $value) {
        $arr[$value['parent_id']]['children'][$value['id']] = $value;
        deep_sort($value['id'], $arr[$value['parent_id']]['children']);
    }
}
$arr = [];
deep_sort(NULL, $arr);
# 递归去掉数字key

print_r($arr);

