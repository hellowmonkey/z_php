<?php 
if(!is_object($db))	$db = new DB();
$lists = $db->get_all('select * from wb_users');
include V('index');
?>