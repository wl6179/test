<?php
//require_once(dirname(__FILE__) . '/caculator.php');

class StackTest extends PHPUnit_Framework_TestCase {
	/**
	 * 测试空数组
	 */
	public function testEmpty() {
		//数组
		$stack = array();
		
		$this->assertEmpty($stack);       //断言数组是空的
		
		return $stack;        //返回数据，给依赖“我”的其它（子）函数！
	}
	
	/**
	 * @depends testEmpty
	 */
	public function testPush(array $stack) {   //参数array $stack，将从上边注释中的注解@depends定义的依赖函数testEmpty()中的return数据而来！（空数组）！！chris.wang
	    array_push($stack, 'foo');     //加工（空）数组
	    
	    $this->assertEquals('foo', $stack[count($stack) - 1]);     //断言数组第1个元素是 foo
	    $this->assertNotEmpty($stack);                             //断言数组是 非空
	    
		return $stack;        //返回数据，给依赖“我”的其它（子）函数！
	}
	
	/**
	 * @depends testPush
	 */
	public function testPop(array $stack) {   //参数array $stack，将从上边注释中的注解@depends定义的依赖函数testPush()中的return数据而来！（含1个元素的数组）！！chris.wang
	    
	    $this->assertEquals('foo', array_pop($stack));             //断言数组“挤出”的最后1个元素是 foo
	    $this->assertEmpty($stack);                                //断言（“挤出”后的）数组是 空
	    
	}
}