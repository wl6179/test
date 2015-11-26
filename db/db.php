<?php
#针对 db::insert 方法的模式的3个选项
define("DB_INSERT", 1);
define("DB_REPLACE", 2);
define("DB_STORE", 3);

define("DB_BUCKET_SIZE", 262144);       #Hash表的桶大小
define("DB_KEY_SIZE", 128);             #key哈希值的固定长度
define("DB_INDEX_SIZE", DB_KEY_SIZE + 12);  #？

define("DB_KEY_EXISTS", 1);
define("DB_FAILURE", -1);
define("DB_SUCCESS", 0);

class DB {
    private $idx_fp;        #索引文件句柄
    private $dat_fp;        #数据文件句柄
    private $closed;        #DB是否关闭状态
    
    /**
     * 打开数据库
     * @param string $pathname
     * @return int
     */
	public function open($pathname) {
		$idx_path = $pathname . '.idx';
		$dat_path = $pathname . '.dat';
		
		if (!file_exists($idx_path)) {
			$init = true;    #是否需要 初始化
			$mode = 'w+b';   #是否 可写模式
		} else {
		    $init = false;
		    $mode = 'r+b';
		}
		
		/*
		 * 索引文件初始化，在没有任何数据的情况下，填充262144个整数0（的二进制字符）进索引文件中，约占1MB
		 */
		$this->idx_fp = fopen($idx_path, $mode);      #打开索引文件句柄
		if (!$this->idx_fp) {
			return DB_FAILURE;       #打开索引文件时，失败
		}
		if ($init) {
		    /*
		     * 初始化索引文件 - 创建文件；为 262144 个记录预留空间；（预写入索引块）
		     */
			$elem = pack('L', 0x00000000);       #二进制的 0（数字0！）
			for ($i = 0; $i < DB_BUCKET_SIZE; $i++) {
				fwrite($this->idx_fp, $elem, 4);    #创建新文件，并且以 数字0 即二进制的 0，作为结尾
			}
		}
		
		$this->dat_fp = fopen($dat_path, $mode);
		if (!$this->dat_fp) {
			return DB_FAILURE;       #打开数据文件时，失败
		}
		
		return DB_SUCCESS;        #打开数据库成功！（正常获得句柄）
		
	}
	
	/**
	 * hash函数 - Times33 算法（算是比较难以重复的Hash算法了~）！优点是分布均匀、效率极高！可‘承载量’狠大！
	 * 可‘承载量’狠大
	 * @param string $string
	 * @return int
	 */
	private function _hash($string) {
		$string = substr(md5($string), 0, 8);     #截取8个散列的字符
		$hash = 0;
		
		for ($i = 0; $i < 8; $i++) {
			$hash += 33 * $hash + ord($string{$i});      #累积变化$hash * 33倍 + 当前字符的ASCII
		}
		
		return $hash & 0x7FFFFFFF;        #（&与操作，目的是 将最高位(符号位)置0~变无符号数，只留最后几位？！）0x7FFFFFFF = int 2147483647
	}
	
	/**
	 * 读取记录*
	 * @param unknown $key
	 * @return unknown
	 */
	public function fetch($key) {
		$offset = ($this->_hash($key) % DB_BUCKET_SIZE) * 4;      #偏移量？  *4:意思是每个‘链表’指针本身的大小就是4字节！
		
		/*
		 * 定位 索引文件指针
		 */
		fseek($this->idx_fp, $offset, SEEK_SET);      #索引文件的指针，移动到这么多【SEEK_SET - 设定位置等于 offset 字节】
		
		/*
		 * 读取 索引文件 从定位处，开始的4个字节！并从二进制转换为可用PHP类型对象 数字！
		 */
		$pos = unpack('L', fread($this->idx_fp, 4));  #解包成数字 L
		$pos = $pos[1];       #得到目标的 Hash链表，在索引文件中的偏移量（nextNode）！[1]就是->nextNode！
		
		$found = false;
		
		while ($pos) {
			fseek($this->idx_fp, $pos, SEEK_SET);
			$block = fread($this->idx_fp, DB_INDEX_SIZE);
			$cpkey = substr($block, 4, DB_KEY_SIZE);
			
			if (!strncmp($key, $cpkey, strlen($key))) {
				$dataoff = unpack('L', substr($block, DB_KEY_SIZE + 4, 4));
				$dataoff = $dataoff[1];
				
				$datalen = unpack('L', substr($block, DB_KEY_SIZE + 8, 4));
				$datalen = $datalen[1];
				
				$found = true;
				break;
			}
			
			$pos = unpack('L', substr($block, 0, 4));
			$pos = $pos[1];
		}
		
		if (!$found) {
			return NULL;
		}
		fseek($this->dat_fp, $dataoff, SEEK_SET);
		$data = fseek($this->dat_fp, $datalen);
		
		return $data;
	}
	
	
	public function insert($key, $data) {
		$offset = ($this->_hash($key) % DB_BUCKET_SIZE) * 4;
		
		$idxoff = fstat($this->idx_fp);
		$idxoff = intval($idxoff['size']);
		
		$datoff = fstat($this->dat_fp);
		$datoff = intval($data['size']);
		
		$keylen = strlen($key);
		if ($keylen > DB_KEY_SIZE) {
			return DB_FAILURE;
		}
		
		$block = pack('L', 0x00000000);
		$block .= $key;
		$space = DB_KEY_SIZE - $keylen;
		
		for ($i = 0; $i < $space; $i++) {
			$block .= pack('C', 0x00);
		}
		
		$block .= pack('L', $datoff);
		$block .= pack('L', strlen($data));
		
		fseek($this->idx_fp, $offset, SEEK_SET);
		$pos = unpack('L', fread($this->idx_fp, 4));
		$pos = $pos[1];
		
		if ($pos == 0) {
			fseek($this->idx_fp, $offset, SEEK_SET);
			fwrite($this->idx_fp, pack('L', $idxoff), 4);
			
			fseek($this->idx_fp, 0, SEEK_END);
			fwrite($this->idx_fp, $block, DB_INDEX_SIZE);
			
			fseek($this->dat_fp, 0, SEEK_END);
			fwrite($this->dat_fp, $data, strlen($data));
			
			return DB_SUCCESS;
		}
		
		$found = false;
		
		while ($pos) {
			fseek($this->idx_fp, $pos, SEEK_SET);
			$tmp_block = fread($this->idx_fp, DB_INDEX_SIZE);
			$cpkey = substr($tmp_block, 4, DB_KEY_SIZE);
			
			if (!strncmp($key, $cpkey, strlen($key))) {
				$dataoff = unpack('L', substr($tmp_block, DB_KEY_SIZE + 4, 4));
				$dataoff = $dataoff[1];
				$datalen = unpack('L', substr($tmp_block, DB_KEY_SIZE + 8, 4));
				$datalen = $datalen[1];
				$found = true;
				break;
			}
			
			$prev = $pos;
			$pos = unpack('L', substr($tmp_block, 0, 4));
			$pos = $pos[1];
		}
		
		if ($found) {
			return DB_KEY_EXISTS;
		}
		
		fseek($this->idx_fp, $prev, SEEK_SET);
		fwrite($this->idx_fp, pack('L', $idxoff), 4);
		fseek($this->idx_fp, 0, SEEK_END);
		fwrite($this->idx_fp, $block, DB_INDEX_SIZE);
		fseek($this->dat_fp, 0, SEEK_END);
		fwrite($this->dat_fp, $data, strlen($data));
		return DB_SUCCESS;
	}
	
	public function delete($key) {
		$offset = ($this->_hash($key) % DB_BUCKET_SIZE) * 4;
		fseek($this->idx_fp, $offset, SEEK_SET);
		
		$head = unpack('L', fread($this->idx_fp, 4));
		$head = $head[1];
		$curr = $head;
		$prev = 0;
		
		while ($curr) {
			fseek($this->idx_fp, $curr, SEEK_SET);
			$block = fread($this->idx_fp, DB_INDEX_SIZE);
			
			$next = unpack('L', substr($block, 0, 4));
			$next = $next[1];
			
			$cpkey = substr($block, 4, DB_KEY_SIZE);
			if (!strncmp($key, $cpkey, strlen($key))) {
				$found = true;
				break;
			}
			
			$prev = $curr;
			$curr = $next;
		}
		
		if (!$found) {
			return DB_FAILURE;
		}
		
		if ($prev == 0) {
			fseek($this->idx_fp, $offset, SEEK_SET);
			fwrite($this->idx_fp, pack('L', $next), 4);
		} else {
		    fseek($this->idx_fp, $prev, SEEK_SET);
		    fwrite($this->idx_fp, pack('L', $next), 4);
		}
		
		return DB_SUCCESS;
	}
	
	public function close() {
		if (!$this->closed) {
			fclose($this->idx_fp);
			fclose($this->dat_fp);
			$this->closed = true;
		};
	}
}




#测试1 - 写效率
$db = new DB();
$db->open('dbtest');

$start_time = explode(' ', microtime());
$start_time = $start_time[0] + $start_time[1];

for ($i = 0; $i < 10000; $i++) {
	$db->insert('key' . $i, 'value' . $i);
}

$end_time = explode(' ', microtime());
$end_time = $end_time[0] + $end_time[1];

$db->close();
echo 'proccess time in ' . ($end_time - $start_time) . ' seconds.';



#测试2 - 读效率
$db = new DB();
$db->open('dbtest');

$start_time = explode(' ', microtime());
$start_time = $start_time[0] + $start_time[1];

for ($i = 0; $i < 10000; $i++) {
    $db->fetch('key' . $i);
}

$end_time = explode(' ', microtime());
$end_time = $end_time[0] + $end_time[1];

$db->close();
echo 'proccess time in ' . ($end_time - $start_time) . ' seconds.';

