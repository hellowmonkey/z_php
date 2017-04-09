<?php 
session_start();

// cookie
if(!function_exists('set_cookie')){
	function set_cookie($name, $value = '', $expire = 1, $path = '/', $domain = '', $secure = FALSE){
		if(is_numeric($expire) && $expire>0){
			$expire = time() + intval($expire)*24*60*60;
		}else{
			$expire = -24*60*60;
		}
		setcookie($name, $value, $expire, $path, $domain, $secure );
	}
}

if(!function_exists('del_cookie')){
	function del_cookie($name, $value = '', $expire = '', $path = '/', $domain = ''){
		set_cookie($name, '', '', $path, $domain);
	}
}

if(!function_exists('get_cookie')){
	function get_cookie($name='')
	{
		if(empty($name)){
			return $_COOKIE;
		}
		if(!isset($_COOKIE[$name]))	return null;
		return $_COOKIE[$name];
	}
}


// session
if(!function_exists('set_session')){
	function set_session($key, $value){
		$_SESSION[$key] = $value;
		return session_id();
	}
}

if(!function_exists('del_session')){
	function del_session($key){
		if(isset($_SESSION[$key])){
			unset($_SESSION[$key]);
			session_destroy();
		}
	}
}

if(!function_exists('get_session')){
	function get_session($key='')
	{
		if(empty($key)){
			return $_SESSION;
		}
		if(!isset($_SESSION[$key]))	return null;
		return $_SESSION[$key];
	}
}

if(!function_exists('files')){
	function files($name='')
	{
		if(empty($name)){
			return $_FILES;
		}
		if(!isset($_FILES[$name]))	return null;
		return $_FILES[$name];
	}
}

if(!function_exists('post')){
	function post($key=''){
		if(empty($key)){
			return $_POST;
		}
		if(!isset($_POST[$key])){
			return null;
		}
		return $_POST[$key];
	}
}

if(!function_exists('get')){
	function get($key=''){
		if(empty($key)){
			return $_GET;
		}
		if(!isset($_GET[$key])){
			return null;
		}
		return $_GET[$key];
	}
}


?>