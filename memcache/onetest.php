<?php
header("Content-type:text/html;charset=utf-8");
$mc = new Memcache();
$mc->connect('192.168.0.233', 11211);

$uid = (int)$_GET['uid'];
if ($uid>0){
    $sql = "SELECT * FROM user WHERE uid='$uid'";
    $key = md5($sql);
    
    //当Mc没有此缓存数据时，走MySQL
    if (!($datas = $mc->get($key))) {   //将会从Mc取得一个完整的stdClass对象！！！~[即：Mc可以完整的将对象类型存储进Memory中]
        $conn = mysql_connect('192.168.0.233', 'root', '666666');
        mysql_select_db('test');
        mysql_set_charset('utf8mb4');   //*
        $result = mysql_query($sql);
        while ($row = mysql_fetch_object($result)){ //是一个stdClass对象！
            $datas[] = $row;
        }
        $mc->add($key, $datas);     //$datas是一个大数组对象！
        echo $key . '没有缓存数据，正在写入Mc缓存ing...';
        var_dump($datas);
    }else {
    //仅走Mc $mc->get
        echo $key . '已经有缓存数据！没走MySQL！！';
        var_dump($datas);
    }
}
?>
