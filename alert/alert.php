<?php
define ("BASE_PATH", realpath(dirname(__FILE__)));
require_once('autoload.php');

// 读取当前小时与上一小时的日志文件。从文件中获取最后一次读取的日志时间

$curTime = date("Y-m-d H:i:s");
$handle = fopen("./last_modify.txt", 'r');
$time = trim(fread($handle, 100));
fclose($handle);
if (empty($time)) {
    $time = $curTime;
}

// 两段时间之间的所有日志
$mall = new \format\Mall($time);
$html = $mall->run()->error();

if (!empty($html)) {
    \lib\mailer\Send::run("业务报警邮件", $html);
}

$handle = fopen("./last_modify.txt", 'w');
fwrite($handle, $curTime);
fclose($handle);
