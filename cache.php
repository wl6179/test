<?php
header("Content-type:text/html;charset=utf-8");
header('Cache-Control:max-age=86400, must-revalidate');         //24小时的新鲜
header('Last-Modified:' . gmdate('D, d M Y H:i:s') . ' GMT');            //当前格林威治时间
header('Expires:' . gmdate('D, d M Y H:i:s', time() + '86400') . ' GMT');        //24小时后过期
echo '我不刷新';
echo '我刷新了';