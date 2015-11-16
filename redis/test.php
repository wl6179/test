<?php
header("Content-type:text/html;charset=utf-8");
$redis = new Redis();
$redis->connect('192.168.0.233', 6379);

$redis->set('key', 'value');
$value = $redis->get('key');

var_dump($value);
$redis->close();