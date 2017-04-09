<?php 

defined('V') OR exit('非法访问');

return array(
	'path' => 'upload',
	'maxSize' => 1024*1024*2,
	'maxWidth' => 1800,
	'maxHeight' => 1800,
	'thumbWidth' => 300,
	'thumbHeight' => 300,
	'thumbName' => '_thumb',
	'allowExt' => array('jpeg','jpg','png','gif')
);


?>