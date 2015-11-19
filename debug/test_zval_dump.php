<?php
header("Content-type:text/html;charset=utf-8");

/*
 * debug_zval_dump
 */
$test_array = array(1, 2, 3);
foreach ($test_array as $v) {
    debug_zval_dump($v);    #此时的$v一直都被foreach中array(1, 2, 3)中的三个元素同时引用着！
    
    $v *= 2;
    debug_zval_dump($v);        #引用为2！（因为这个$v *= 2时，$v是复制被计算的即$v*2的$v出来进行操作(乘法)的！）
    
    var_dump('---');
}
var_dump($test_array);
debug_zval_dump($test_array);

var_dump('*********************');
$test_array2 = array(1, 2, 3);
foreach ($test_array2 as &$v) {
    debug_zval_dump($v);    #此时的$v一直都被foreach中array(1, 2, 3)中的三个元素同时引用着，但是都同属一个内存地址 &$v！
    
    $v *= 2;
    debug_zval_dump($v);        #引用为1~（因为这个&$v都是同一个内存地址！）
}
var_dump($test_array2);
debug_zval_dump($test_array2);