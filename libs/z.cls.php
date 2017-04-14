<?php 
	class Z {
		private $configs = array();
		private $filePath = '';
		private $ctrlPath;
		private $querys = array();

		public function __construct(){
			$this->ctrlPath = ROOT_PATH.'ctrl/';
		}

		public function router(){
			$ctrl_path = $this->ctrlPath;
			if(!$filepath = $this->getPath()){
				return $ctrl_path.'error.php';
			}
			$querys = $this->querys;
			$str = '';
			foreach ($querys as $key => $value) {
				$str .= '_'.$key.'_'.$value;
			}
			$tmp_path = $filepath.$str.'.html';
			if(file_exists($tmp_path)){
				$file = $tmp_path;
			}else{
				$file = $ctrl_path.$filepath.'.php';
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
			if(!$filepath = $this->getPath())	return null;
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

		protected function getPath($_path=''){
			if($this->filePath !== '')	return $this->filePath;
			if(empty($_path)){
				$path = str_replace($this->config('config', 'suffix'), '', (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $this->config('config', 'default')));
			}else{
				$path = $_path;
			}
			if($path[0] === '/'){
				$path = substr($path, 1);
			}
			$path = ltrim($path, '/');
			$path = rtrim($path, '/');
			$path = $path.'/index';
			$filepath = $path;
			$filepaths = explode('/', $filepath);
			$index = count($filepaths) - 1;
			$querys = array();
			while (!file_exists($this->ctrlPath.$filepath.'.php')) {
				if($index < 0){
					if(empty($_path)){
						$rpath = $this->routerPath($path);
						if($path != $rpath){
							$this->filePath = $this->getPath($rpath);
							return $this->filePath;
						}
					}
					$this->filePath = null;
					return $this->filePath;
				}
				$filepath = rtrim(substr($filepath, 0, strlen($filepath) - strlen($filepaths[$index])), '/');
				if($index < (count($filepaths) - 1)){
					$querys[] = $filepaths[$index];
				}
				--$index;
			}
			if(count($querys))	$querys = array_reverse($querys);
			for($i=0;$i<count($querys);$i++){
				if(!isset($querys[$i]))	break;
				if(empty($querys[$i]))	array_splice($querys, $i, 1);
			}
			if($query_config = $this->config('querys', $filepath)){
				for ($i=0; $i < count($query_config); $i++) { 
					if(!isset($querys[$i]))	break;
					$_GET[$query_config[$i]] = $querys[$i];
					array_splice($querys, $i, 1);
				}
			}else{
				if(count($querys) % 2){
					$_GET['action'] = end($querys);
					array_pop($querys);
				}
			}
			for($i=0;$i<count($querys);$i+=2){
				if(isset($querys[$i+1]) && !empty($querys[$i+1]))    $_GET[$querys[$i]] = $querys[$i+1];
			}
			$this->filePath = $filepath;
			$this->querys = $_GET;
			return $this->filePath;
		}

		protected function routerPath($filepath){
			$routers = $this->config('router');
			$filepaths = explode('/', $filepath);
			$index = count($filepaths) - 1;
			$spath = '';
			while ($index > 0) {
				$filepath = rtrim(substr($filepath, 0, strlen($filepath) - strlen($filepaths[$index])), '/');
				$spath = $filepaths[$index].'/'.rtrim($spath, '/');
				foreach ($routers as $key => $value) {
					if($filepath == $key || $filepath == $key.'/index'){
						return $value.'/'.rtrim($spath, '/index');
					}
				}
				--$index;
			}
			return $path;
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
				if(isset($this->configs[$filename][$key])){
					return $this->configs[$filename][$key];
				}else{
					return null;
				}
			}
		}
	}
?>