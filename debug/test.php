<?php
header("Content-type:text/html;charset=utf-8");

/**
 * 待测试函数
 */
function factor1($n) {
	$factor = 1;
	/**
	 * 阶乘
	 */
	for ($i = 1; $i < $n; $i++) {
		$factor *= $i;
		debug($factor, $i);
	}
	//最终结果
	return $factor;
}

//调用
$test_result = factor1(4);



/**
 * 能提供稍微详细信息的 调试 函数
 * 针对 函数内部 使用
 */
function debug() {
	
    $arg_num = func_num_args();
	$arg_list = func_get_args();
	
	for ($i = 0; $i < $arg_num; $i++) {
		echo "第{$i}个变量的值为：", $arg_list[$i], PHP_EOL;
	}
	
	echo '当前所处文件名为：', __FILE__, PHP_EOL;
	
}