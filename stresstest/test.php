<?php
$num = 4;
if (isset($_POST['num'])) {
	$num = $_POST['num'];
}

$sum = 0;
for ($i = 0; $i <= $num; $i++) {
	$sum += $i;
}
echo $sum;