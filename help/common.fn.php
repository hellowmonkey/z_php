<?php 

defined('V') OR exit('非法访问');

if(!function_exists('P')){
	function P($value){
		echo '<pre style="background:#f5f5f5;padding:10px;border-left:5px solid #0078AD;word-wrap:break-word;margin:20px;">';
		if(is_array($value) || is_object($value)){
			print_r($value);
		}else if(is_string($value) || is_numeric($value)){
			echo $value;
		}else{
			var_dump($value);
		}
		echo '</pre>';
	}
}

if(!function_exists('hasValue')){
	function hasValue($value){
		if(!isset($value))	return null;
		if(is_array($value)){
			return count($value);
		}else{
			return !empty($value);
		}
	}
}

if (!function_exists('filter')){
	function filter(&$v){
	  $flag=(int)get_magic_quotes_gpc();
	  if(is_string($v)){
		  $v=htmlspecialchars(trim($v));
		  if(0==$f) $v=addslashes($v);
		}
	}
}

if(!function_exists('C')){
	function C($filename, $key=''){
		global $z;
		return $z->config($filename, $key);
	}
}

if(!function_exists('U')){
	function U($url, $query=array(), $action=''){
		$querys = array();
		$suffix = C('config','suffix');
		$path = HOST.$url;
		if(is_string($query)){
			$action = $query;
			$query = array();
		}
		if(hasValue($query) || hasValue($action)){
			$path .= '/';
		}
		if(count($query)){
			foreach ($query as $key => $value) {
				if(!is_numeric($key))	$querys[] = $key;
				$querys[] = $value;
			}
		}
		if(!empty($action)){
			$querys[] = $action;
		}
		$path .= implode('/', $querys);
		return $path.$suffix;
	}
}

if(!function_exists('jump')){
	function jump($url, $msg='', $type='alert'){
		if(!hasValue($url))	return;
		if(hasValue($msg)){
			alert($msg, $type);
		}
		$u = strpos($url, HOST)===false?U($url):$url;
		echo '<script>location.href=\''.$u.'\'</script>';
	}
}

if(!function_exists('V')){
	function V($url){
		$url = 'tpl/'.$url;
		return ROOT_PATH.$url.'.php';
	}
}

if(!function_exists('load')){
	function load($url){
		if(strpos($url, 'help/') === 0 || strpos($url, '.fn') !== false){
			if(strpos($url, 'help/') === false)	$url = 'help/'.$url;
			if(strpos($url, '.fn') === false)	$url .= '.fn';
		}elseif(strpos($url, 'libs/') === 0 || strpos($url, '.cls') !== false){
			if(strpos($url, 'libs/') === false)	$url = 'libs/'.$url;
			if(strpos($url, '.cls') === false)	$url .= '.cls';
		}
		return ROOT_PATH.$url.'.php';
	}
}

if(!function_exists('alert')){
	function alert($msg, $type='alert'){
		if(!hasValue($msg))	return;
		$_SESSION['alert'] = array('msg'=>$msg,'type'=>$type);
	}
}

if(!function_exists('showMsg')){
	function showMsg($msg, $type='alert'){
		if(!hasValue($msg))	return;
		echo '<script>var timer=setInterval(function(){if($.'.$type.'){$.'.$type.'(\''.$msg.'\');clearInterval(timer)}},100);setTimeout(function(){if(timer)clearInterval(timer)},3000)</script>';
	}
}

?>