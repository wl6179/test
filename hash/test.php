<?php
/*
 * 特点
 *      平均分配，到3个‘服务器’
 *      
 * 均匀分布 VS hash冲突
 * ————也许它们是相互冲突的！	#chris.wang
 */
$test_result = hash1('abcddo', 3);
var_dump($test_result);

function hash1($key, $m) {
	$strlen = strlen($key);
	var_dump($key);        #瞧一眼输入字符串
	$hashval = 0;
	
	for ($i = 0; $i < $strlen; $i++) {
		$hashval += ord($key{$i});
		
		var_dump($hashval);   #实时累加数值
		var_dump($key{$i});   #$key{$i}，这其实是一个 字符！！！
	}
	
	return $hashval % $m;      #等于2（整数的直接取余法）
}