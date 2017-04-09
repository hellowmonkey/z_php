<?php 

defined('V') OR exit('非法访问');

class Uploader {
	protected $path;
	protected $maxSize;
	protected $allowExt;
	protected $maxWidth;
	protected $maxHeight;
	protected $thumbName;
	protected $thumbWidth;
	protected $thumbHeight;

	private static $_instance;

	public function __construct($path='',$maxSize='',$allowExt=''){
		$this->path = empty($path)?(hasValue(C('upload','path'))?C('upload','path'):'uploads'):$path;
		$this->maxSize = empty($maxSize)?(hasValue(C('upload','maxSize'))?C('upload','maxSize'):1024*1024*2):$maxSize;
		$this->allowExt = empty($allowExt)?(hasValue(C('upload','allowExt'))?C('upload','allowExt'):array('jpeg','jpg','png','gif')):$allowExt;
		$this->maxWidth = hasValue(C('upload','maxWidth'))?C('upload','maxWidth'):1800;
		$this->maxHeight = hasValue(C('upload','maxHeight'))?C('upload','maxHeight'):1800;
		$this->thumbName = hasValue(C('upload','thumbName'))?C('upload','thumbName'):'_thumb';
		$this->thumbWidth = hasValue(C('upload','thumbWidth'))?C('upload','thumbWidth'):300;
		$this->thumbHeight = hasValue(C('upload','thumbHeight'))?C('upload','thumbHeight'):300;
	}

	public function upload($file,$path='',$thumb=true,$maxWidth='',$maxHeight='',$thumbWidth='',$thumbHeight='',$thumbName='',$maxSize='',$allowExt=''){
		if(!hasValue($file)){
			throw new Exception("上传文件不能为空");
			exit;
		}
		$ret = array();
		$files = $this->getFiles($file);
		$path = empty($path)?$this->path:$path;
		$maxSize = empty($maxSize)?$this->maxSize:$maxSize;
		$allowExt = empty($allowExt)?$this->allowExt:$allowExt;
		$maxWidth = empty($maxWidth)?$this->maxWidth:$maxWidth;
		$maxHeight = empty($maxHeight)?$this->maxHeight:$maxHeight;
		$thumbName = empty($thumbName)?$this->thumbName:$thumbName;
		$thumbWidth = empty($thumbWidth)?$this->thumbWidth:$thumbWidth;
		$thumbHeight = empty($thumbHeight)?$this->thumbHeight:$thumbHeight;
		foreach ($files as $fileinfo) {
			$ret[] = $this->doupload($fileinfo,$path,$thumb,$maxWidth,$maxHeight,$thumbName,$thumbWidth,$thumbHeight,$maxSize,$allowExt);
		}
		if(1===count($ret)){
			return $ret[0];
		}else{
			return $ret;
		}
	}

	private function doupload($fileInfo,$path,$thumb,$maxWidth,$maxHeight,$thumbName,$thumbWidth,$thumbHeight,$maxSize,$allowExt){
		$res = array();
		$image = load('libs/image');
		//判断错误号
		if($fileInfo['error']===UPLOAD_ERR_OK){
			//检测上传得到小
			if($fileInfo['size']>$maxSize){
				$res['error']=$fileInfo['name'].'上传文件过大';
			}
			$ext=$this->getExt($fileInfo['name']);
			//检测上传文件的文件类型
			if(!in_array($ext,$allowExt)){
				$res['error']=$fileInfo['name'].'非法文件类型';
			}
			//检测文件是否是通过HTTP POST上传上来的
			if(!is_uploaded_file($fileInfo['tmp_name'])){
				$res['error']=$fileInfo['name'].'文件不是通过HTTP POST方式上传上来的';
			}
			if(count($res)) return $res;
			$rootpath = PATH_ROOT.'/'.$path;
			if(!file_exists($rootpath)){
				mkdir($rootpath,0777,true);
				chmod($rootpath,0777);
			}
			$uniName=$image->getUniName();
			$destination=$path.'/'.$uniName.'.'.$ext;
			$rootdestination=$rootpath.'/'.$uniName.'.'.$ext;
			if(!move_uploaded_file($fileInfo['tmp_name'],$rootdestination)){
				$res['error']=$fileInfo['name'].'文件移动失败';
			}
			$res['src']=$image->resize($destination,null,$maxWidth,$maxHeight);
			if($thumb){
				$res['src_thumb']=$image->thumb($destination,$thumbName,$thumbWidth,$thumbHeight);
			}
			return $res;
		}else{
			//匹配错误信息
			switch ($fileInfo ['error']) {
				case 1 :
					$res['error'] = '上传文件超过了PHP配置文件中upload_max_filesize选项的值';
					break;
				case 2 :
					$res['error'] = '超过了表单MAX_FILE_SIZE限制的大小';
					break;
				case 3 :
					$res['error'] = '文件部分被上传';
					break;
				case 4 :
					$res['error'] = '没有选择上传文件';
					break;
				case 6 :
					$res['error'] = '没有找到临时目录';
					break;
				case 7 :
				case 8 :
					$res['error'] = '系统错误';
					break;
			}
			return $res;
		}
	}

	private function getExt($filename){
		return strtolower(pathinfo($filename,PATHINFO_EXTENSION));
	}

	private function getFiles($file){
		$files = array();
		if(is_string($file['name'])){
			$files[0] = $file;
		}elseif(is_array($file['name'])){
			foreach($file['name'] as $key=>$val){
				$files[$key]['name']=$file['name'][$key];
				$files[$key]['type']=$file['type'][$key];
				$files[$key]['tmp_name']=$file['tmp_name'][$key];
				$files[$key]['error']=$file['error'][$key];
				$files[$key]['size']=$file['size'][$key];
			}
		}
		return $files;
	}

	public static function getInstance($path='',$maxSize='',$allowExt=''){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self($path,$maxSize,$allowExt);
		}
		return self::$_instance;
	}
	
}

?>