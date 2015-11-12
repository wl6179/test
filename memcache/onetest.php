<?php
header("Content-type:text/html;charset=utf-8");
$mc = new Memcache();
$mc->connect('192.168.0.233', 11211);
//var_dump(MEMCACHE_COMPRESSED );
$uid = (int)$_GET['uid'];
if ($uid>0){
    $sql = "SELECT * FROM user WHERE uid='$uid'";
    $key = md5($sql);
    
    //当Mc没有此缓存数据时，走MySQL
    if (!($datas = $mc->get($key))) {   //将会从Mc取得一个完整的stdClass对象！！！~[即：Mc可以完整的将对象类型存储进Memory中]
        
        echo $key . ' - MySQL is working for this data（缓存无此数据）~';
        
        $conn = mysql_connect('192.168.0.233', 'root', '666666');
        mysql_select_db('test');
        mysql_set_charset('utf8mb4');   //*
        $result = mysql_query($sql);
        
        while ($row = mysql_fetch_object($result)){ //是一个stdClass对象！
            $datas[] = $row;
        }
        
        if ($mc->set($key, $datas, MEMCACHE_COMPRESSED, 5)){     //$datas是一个大数组对象！(用add会出现隐性问题，应该全用set)
            echo '&正在写入Mc缓存ing...Write OK!';
        }else {
            echo '&正在写入Mc缓存ing...Write Error!';
        }
        echo '<br />[MySQL]';
        var_dump($datas);   //数据类型来自MySQL.
        
    }else {
    //仅走Mc $mc->get
        
        echo $key . ' - Memcached is working for this data in 5 second（缓存有此数据）~';
                
        echo '<br />[Memcached]';
        var_dump($datas);   //数据类型来自Memcached.
        
    }
}
?>