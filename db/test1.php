<?php
/*
 * 二进制存储！
 */
$fp = fopen("data1.dat", "wb");
/*
 * 先将 数字类型的12，转化为二进制字符！，再写入文件！
 */
$bin = pack('L', 12);
fwrite($fp, $bin, 4);     #写入文件
fclose($fp);



/*
 * 读取二进制！
 */
$fp = fopen("data1.dat", "rb");
/*
 * 先将 数字类型的12，转化为二进制字符！，再写入文件！
 */
$bin = fread($fp, 4);     #读取 二进制 存储文件
$pack = unpack('L', $bin);      #原样 数据对象 返回了~~
var_dump($pack);                #原样！
fclose($fp);