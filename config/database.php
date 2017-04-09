<?php

defined('V') OR exit('非法访问');

$db_config["hostname"] = "localhost"; //服务器地址
$db_config["username"] = "root"; //数据库用户名
$db_config["password"] = ""; //数据库密码
$db_config["database"] = "weibo"; //数据库名称
$db_config["charset"] = "utf8";//数据库编码
$db_config["pconnect"] = 0;//开启持久连接

return $db_config;

?>