<?php


/**
 * HashTable 类
 * @author Chris
 * Hash 表
 * 
 * 均匀分布 VS hash冲突
 * ————也许它们是相互冲突的！	#chris.wang
 */
class HashTable {
    private $buckets;       #存储记录的数组（Hash 表）
    private $size = 10;     #存储的固定长度（Hash 表）
    
    /**
     * 初始化
     * Hash 表的长度固定为 10
     */
	function __construct() {
		$this->buckets = new SplFixedArray($this->size);      #分配内存（Hash 表）
	}
	
	/**
	 * hash 函数
	 * 使用对字符串 ASCII 累加，取余的 hash算法
	 * 目的：有了hash函数，就可以实现 插入、查找 的方法
	 * @param string $key
	 * @return number
	 */
	private function hashfunc($key) {
		$strlen = strlen($key);
		$hasval = 0;          #累加数
		
		for ($i = 0; $i < $strlen; $i++) {
			$hasval += ord($key{$i});        #$key{$i}，这其实是一个 字符
		}
		
		return $hasval % $this->size;         #取余
	}
	
	/**
	 * 写入Hash表
	 * @param string $key
	 * @param mixed $val
	 */
	public function insert($key, $val) {
		$index = $this->hashfunc($key);       #“寻址”
		$this->buckets[$index] = $val;        #写入
	}
	
	/**
	 * 读取Hash表
	 * @param string $key
	 * @return mixed
	 */
	public function find($key) {
		$index = $this->hashfunc($key);       #“寻址”
		return $this->buckets[$index];        #读取
	}
}




/*
 * test
 */
$hastable = new HashTable();
$hastable->insert('key1', 'value111');
$hastable->insert('key2', 'value222');

var_dump($hastable->find('key1'));
var_dump($hastable->find('key2'));



/*
 * test
 * Hash表的冲突
*/
$hastable = new HashTable();
$hastable->insert('key1', 'value111111');
$hastable->insert('key12', 'value111222');

var_dump($hastable->find('key1'));
var_dump($hastable->find('key12'));
