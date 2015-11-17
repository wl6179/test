<?php
/*
 * Worker 端对象
 * CLI 端
 */
$worker = new GearmanWorker();
$worker->addServer("127.0.0.1", 4730);  #也连到调度服务
$worker->addFunction('sendmail', 'my_doSendMail');  #注册 sendmail 工人！

/*
 * 无限循环 - 随时接收调度发来的任务job
 */
while ($worker->work());    #循环监听调度+随时接纳处理任务！（守护进程）

/**
 * 业务逻辑 - 具体实现处理分来的计算任务job
 */
function my_doSendMail($job) {
	$info = unserialize($job->workload()); #从调度接收序列化的数据，并重新转化为原本的数组结构
	$test_result = mail($info['to'], $info['subject'], $info['message'], $info['headers']);    #发！
	
	var_dump('发了！真发了！！');
	return $test_result;
}