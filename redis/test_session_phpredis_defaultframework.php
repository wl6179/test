<?php
//如果未修改 php.ini，下面两行的注释去掉让其生效
//ini_set('session.save_handler' , 'redis');
//ini_set('session.save_path' , 'tcp://192.168.0.233:6379');

#正常使用 $_SESSION 瞧瞧：
session_start();
$_SESSION['sessionid'] = 'This is Session content!!!Chris.Wang';
echo $_SESSION['sessionid'];
echo '<br/>';

#不通过 正常 $_SESSION，直接从 Redis 捞数据瞧瞧：
$redis = new Redis();
$redis->connect('192.168.0.233' , 6379);
//在此，可以发现 Redis 是用 'PHPREDIS_SESSION:' + session_id() 作为 key，并是 String 形式来存储这个 key！
$test_result = $redis->get('PHPREDIS_SESSION:' . session_id());		#再次可试试能不能（直接）捞出 SESSION 数据~
var_dump('PHPREDIS_SESSION:' . session_id());
echo '<br>';
var_dump($test_result);