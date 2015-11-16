<?php
header("Content-type:text/html;charset=utf-8");
$redis = new Redis();
$redis->connect('192.168.0.233', 6379);

$weibo_info = array(
	'uid'=>get_uid(),
    'content'=>get_content(),
    'timestamp'=>time()
);

var_dump(json_encode($weibo_info));

$test_result = $redis->lPush('weibo_list', json_encode($weibo_info));      //List类型，有先进先出特点，以作队列
var_dump($test_result);
$redis->close();



function get_uid() {
	return mt_rand(1, 1000000);
}

function get_content() {
    return '测试数据。' . mt_rand(1, 1000000);
}