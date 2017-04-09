<?php 
	class Z {
		private $configs = array();
		private $filepath;

		public function router(){
			$filepath = $this->getPath();
			$ctrl_path = ROOT_PATH.'ctrl/';
			$file = $ctrl_path.$filepath.'.php';
			if(!file_exists($file))	$file = $ctrl_path.$filepath.'/index.php';
			if(!file_exists($file)){
				return $ctrl_path.'error.php';
			}
			filter($_GET);
			filter($_POST);
			if(isset($_SESSION['alert'])){
				showMsg($_SESSION['alert']['msg'], $_SESSION['alert']['type']);
				unset($_SESSION['alert']);
			}
			return $file;
		}

		public function autoload(){
			$filepath = $this->getPath();
			$autoload = $this->config('autoload');
			$loadarr = array();
			foreach ($autoload as $key => $value) {
				if($key === '*' || strpos($filepath, $key.'/') === 0 || $filepath === $key){
					foreach ($value as $v) {
						$p = load($v);
						if(file_exists($p))	$loadarr[] = $p;
					}
				}
			}
			return $loadarr;
		}

		protected function getPath(){
			if(hasValue($this->filepath))	return $this->filepath;
			$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $this->config('config', 'default');
			if($path[0] === '/'){
				$path = substr($path, 1);
			}
			$paths = explode('&', $path);
			$filepath = empty($paths[0])?$this->config('config', 'default'):$paths[0];
			$query = isset($paths[1]) ? $paths[1] : '';
			if($filepath[strlen($filepath)-1] === '/'){
				$filepath = substr($filepath, 0, strlen($filepath)-1);
			}
			$querys = explode('/', $query);
			for($i=0;$i<count($querys);$i++){
				if(!isset($querys[$i]))	break;
				if(empty($querys[$i]))	array_splice($querys, $i, 1);
			}
			if(count($querys)%2){
				$_GET['action'] = end($querys);
				array_pop($querys);
			}
			for($i=0;$i<count($querys);$i+=2){
				if(isset($querys[$i+1]) && !empty($querys[$i+1]))    $_GET[$querys[$i]] = $querys[$i+1];
			}
			$this->filepath = $filepath;
			return $this->filepath;
		}

		public function config($filename, $key=''){
			$file = ROOT_PATH.'config/'.$filename.'.php';
			if(!file_exists($file))	return null;
			if(!isset($this->configs[$filename])){
				$this->configs[$filename] = include_once $file;
			}
			if(empty($key)){
				return $this->configs[$filename];
			}else{
				return $this->configs[$filename][$key];
			}
		}
	}
?>