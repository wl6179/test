<?php


/**
 * HashTable 类
 * @author Chris
 * Hash 表（含拉链法解决Hash表的hash冲突）
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
	 * 改进
	 */
	public function insert($key, $val) {
		$index = $this->hashfunc($key);       #“寻址”
		
		/*
		 * 更新链表
		 */
		if (isset($this->buckets[$index])) {
			$newNode = new HashNode($key, $val, $this->buckets[$index]);   #值（nextNode包含整个class hashNode对象）
		} else {
		    $newNode = new HashNode($key, $val, NULL);                      #新链表
		}
		
		$this->buckets[$index] = $newNode;        #写入！！！（存储链表结构）
	}
	
	/**
	 * 读取Hash表
	 * @param string $key
	 * @return mixed
	 * 改进
	 * 有点像递归
	 */
	public function find($key) {
		$index = $this->hashfunc($key);       #“寻址”
		
		$current = $this->buckets[$index];        #当前key的整个链表
		
		/*
		 * 遍历 当前key的 整个链表 ———— 每一个元素！
		 */
		while (isset($current)) {
			if ($current->key == $key) {     #此元素的key是否匹配 当前在查找的key~
				return $current->value;     #查找成功！！！
			}
			
			#否则将要继续遍历 下一个
			$current = $current->nextNode;   #赋予 下一个元素（节点） 的引用（这是像递归的地方）
		} #while...
		
		return NULL;        #读取 查找失败！
	}
}



/**
 * 链表
 * @author Chris
 * 拉链法解决Hash表的hash冲突
 */
class HashNode {
    public $key;
    public $value;
    public $nextNode;   #指针
    
    /**
     * 初始化链表
     * @param string $key
     * @param mixed $value
     * @param unkown $nextNode
     */
	function __construct($key, $value, $nextNode = NULL) {
		$this->key = $key;
		$this->value = $value;
		$this->nextNode = $nextNode;
	}
}



/*
 * test
 * 原来的Hash表的冲突如下
 * 现已解决
*/
$hastable = new HashTable();
$hastable->insert('key1', 'value111111');
$hastable->insert('key12', 'value111222');

var_dump($hastable->find('key1'));
var_dump($hastable->find('key12'));
