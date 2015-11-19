<?php
header("Content-type:text/html;charset=utf-8");

/**
 * caculator计算器
 */
class caculator {
	/**
	 * 加法
	 * @param int $a
	 * @param int $b
	 * @return number
	 * 
	 * 断言
	 * $assert (0, 0) == 0
	 * $assert (0, 1) == 1
	 * $assert (1, 0) == 1
	 * $assert (1, 1) == 2
	 * $assert (1, 2) == 4
	 */
    public function add($a, $b) {
		return $a + $b;
	}
	
	/**
	 * 加法2
	 * @param int $a
	 * @param int $b
	 * @return number
	 */
	public function add2($a, $b) {
	    return $a + $b;
	}
}
//assertClassHasStaticAttribute($attributeName, $className) 仅只能用于PHPUnit框架中！（当前PHP环境中不可用！）
//只能使用PHP内置的断言函数，仅此一个：
$test_result = assert('2 < 1', '2 is less than 1');
var_dump($test_result);