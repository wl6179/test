<?php
header("Content-type:text/html;charset=utf-8");

/*
 * var_dump
 */
$test_array = array(1, 2, 3);
foreach ($test_array as $v) {
	$v *= 2;
}
var_dump($test_array);

$test_array2 = array(1, 2, 3);
foreach ($test_array2 as & $v) {
	$v *= 2;
}
var_dump($test_array2);