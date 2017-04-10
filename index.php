<?php 

header("Content-type: text/html; charset=utf-8");
function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT-8');
date_default_timezone_set("Asia/Shanghai");

// 常量定义
define('V', '1.0.0');
define('ROOT_PATH', str_replace('\\','/',dirname(__FILE__)).'/');
define('HOST', 'http://localhost/github/z_php/');
define('PUB', HOST.'public/');
define('DEBUG', 1);

ini_set('display_errors', DEBUG);

include ROOT_PATH.'help/common.fn.php';
include ROOT_PATH.'libs/z.cls.php';

$db = null;
$z = new Z();

foreach ($z->autoload() as $value) {
	if(hasValue($value))	include_once $value;
}

include $z->router();

?>