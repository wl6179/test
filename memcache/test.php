<?php
$mc = new Memcache();
$mc->connect('192.168.0.233', 11211);

$mc->set('key', 'value', 0, 10);        //0类型，10秒时限
$val = $mc->get('key');
$mc->delete('key');
$mc->flush();       //清空！
$mc->close();

var_dump($val);
?>