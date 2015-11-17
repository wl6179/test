<?php
/*
 * 客户端对象
 * 浏览器
 */
$client = new GearmanClient();
$client->addServer("127.0.0.1", 4730);  #连到调度服务

/*
 * array 邮件包对象
 */
$info = array(
	'to'       => '595574668@qq.com',
	'subject'  => 'test email 001',
	'message'  => 'Hi,This is a test email.',
	'headers'  => 'from:克里斯王@qq.com',
);

/*
 * 注意：调度只支持传递字符串。所以任何传递参数需要先序列化为字符串。
 * 默认是同步，会阻塞。
 */
$test_result = $client->doNormal('sendmail', serialize($info)); #发出邮件包（的工作）给叫 sendmail 的（发件）工人处理（异步：doBackground）
#注意：do()已被淘汰！
/*
 * 提示：
 * 如果是异步，会返回如下
 * string(14) "H:ubuntu-01:11"
 */

#测试数据
var_dump($test_result);
echo '<br>ok.';