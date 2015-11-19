<?php
require_once(dirname(__FILE__) . '/caculator.php');

class CaculatorTest extends PHPUnit_Framework_TestCase {
	/**
	 * 加法
	 */
	public function testAdd() {
		//实例化
		$caculator = new caculator();
		
		//测！
		$this->assertEquals(0, $caculator->add(0, 0));
		$this->assertEquals(1, $caculator->add(0, 1));
		$this->assertEquals(1, $caculator->add(1, 0));
		$this->assertEquals(2, $caculator->add(1, 1));
		//再测！
		$this->assertEquals(4, $caculator->add(1, 2));        //预测4断言中，应该是3.
		
		//再测！
		//$this->assertNotEquals(333, $caculator->add(1, 2));
	}
}