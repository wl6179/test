<?php
header("Content-type:text/html;charset=utf-8");
/*
 * 定期把 Redis 队列插入 MySQL
 * 
 * 注意：
 * 这个PHP程序会一直执行状态，不会释放；
 * 如果更新了代码，必须重启 Apache 后，才能有所变化；
 * 稳定性堪忧，这种Cron我认为更适合使用 C 编写；
 * 性能消耗性如何，需要印证确认；
 * 此策略对MySQL是否能够接受，也需要印证确认
 * Chris.Wang
 */
$redis = new Redis();
$redis->connect('192.168.0.233', 6379);

$weibo = new Weibo();       //微博 SDK

/**
 * 永久循环语句，持续间隔执行命令：
 * 
 * 策略：
 * 每1秒钟只写入1条微博~进入MySQL.
 */
while (TRUE) {
    ob_start ();
    /**
     * 如果队列中有数据（元素）
     */
	if ($redis->lSize('weibo_list') > 0) {
		
	    //上线可去掉*************************************
	    /**
	     * 休息1秒执行1条插入MySQL操作：（否则就会一下全INSERT ALL记录完成了）[上线后可以取消此1秒延迟，将会快速插入记录~秒成！但不知是否性能最佳法？！]
	     *   //上线可去掉！！！
	     *   
	     * 难点：
	     * 我目前不能确定，用极快的频率批量INSERT INTO One Record To MySQL，是否最佳策略！MySQL是否能够负载上万上千万上亿的INSERT INTO One指令？
	     * 所以还值得在此处策略进一步思考确认！！chris.wang
	     * 
	     * 优点：
	     * 目前可以做到让INSERT INTO平均均匀的加入MySQL中，不会产生过载的峰值！
	     */
	    var_dump('休息1秒~');  //上线可去掉！！！
	    sleep(1);  //上线可去掉！！！
	    //上线可去掉*************************************
	    
	    $info = $redis->rpop('weibo_list');       //先出！同时rpop会让出去的这个元素被销毁（得到某个队列中最先进来的元素）
	    var_dump('结果：');
	    var_dump($info);
		$info = json_decode($info);           //解码JSON数据
		/**
		 * 微博接口 - 发微博！（写入数据库 MySQL）
		 */
		$test_result = $weibo->post($info->uid, $info->content, $info->timestamp);   //写入MySQL！
		var_dump($test_result);
		ob_flush();
// 		exit();
		//ob_end_clean ();
		
	}else {
	    
	    var_dump('休息3秒~');
		sleep(3);     //歇3秒，让出CPU给其它进程！（当队列中没有数据任务时）
		ob_flush();
		//ob_end_clean ();
		
	}
}

$redis->close();



/**
 * 模拟微博SDK - 我就是新浪
 */
class Weibo {
	/**
	 * 模拟发微博方法
	 */
    function post($uid, $content, $timestamp) {
        echo '<br><br><br>';
        echo '{$uid} 已写入 MySQL.';
        echo '<br>';
        echo '数据结构：';
        echo '<br>';
        
        $conn = mysqli_connect( '192.168.0.233', 'root', '666666', 'test' );
        //mysqli_select_db( $conn, 'test' );
        mysqli_set_charset( $conn , 'utf8mb4' );
        $sql = "INSERT INTO `weibo` (uid, content, date_mod) VALUES ('$uid', '$content', '$timestamp')";
        $result = mysqli_query( $conn , $sql );
        mysqli_close ( $conn );
        
		return $sql;
	}
}