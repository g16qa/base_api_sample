<?php
$code = $_GET['code'];
file_put_contents('../auth/code.txt', $code);
$nowData = trim(@file_get_contents('../auth/code.txt'));
echo "更新：" . $nowData;
?>
