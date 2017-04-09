<?php 

defined('V') OR exit('非法访问');

class Image {
	protected $maxWidth;
	protected $maxHeight;
	protected $thumbName;
	protected $thumbWidth;
	protected $thumbHeight;

	private static $_instance;

	public function __construct(){
		$this->maxWidth = hasValue(C('upload','maxWidth'))?C('upload','maxWidth'):1800;
		$this->maxHeight = hasValue(C('upload','maxHeight'))?C('upload','maxHeight'):1800;
		$this->thumbName = hasValue(C('upload','thumbName'))?C('upload','thumbName'):'_thumb';
		$this->thumbWidth = hasValue(C('upload','thumbWidth'))?C('upload','thumbWidth'):300;
		$this->thumbHeight = hasValue(C('upload','thumbHeight'))?C('upload','thumbHeight'):300;
	}

	public function resize($src_name,$dst_name='@',$maxwidth='',$maxheight='',$percent=null){
		$maxwidth = empty($maxwidth)?$this->maxWidth:$maxwidth;
		$maxheight = empty($maxheight)?$this->maxHeight:$maxheight;

		$filename = ROOT_PATH.$src_name;

		if(!$fileinfo = $this->getInfo($filename)){
			return null;
		}

		$src_w = $fileinfo['width'];
		$src_h = $fileinfo['height'];
		$createFn = $fileinfo['createFn'];
		$outputFn = $fileinfo['outputFn'];

		if($dst_name){
			$dst_paths = $this->getPaths($dst_name);
			$path = $dst_paths['path']?$dst_paths['path']:$this->getPaths($src_name)['path'];
			$name = $dst_paths['name'];
			$p = ROOT_PATH.$path;
			if(!file_exists($p)){
				mkdir($p, 0777, true);
				chmod($p, 0777);
			}
			if('@' === $name){
				$ext = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
				$name = $this->getUniName().'.'.$ext;
			}
			$outname = $path.$name;
			$rename = $p.$name;
		}else{
			$outname = $src_name;
			$rename = $filename;
		}

		if(is_numeric($percent)){
			$dst_w = $src_w*$percent;
			$dst_h = $src_h*$percent;
		}else{
			$percent = $src_w/$src_h;
			if($maxwidth/$maxheight > $percent){
				$dst_w = $maxheight*$percent;
				$dst_h = $maxheight;
			}else{
				$dst_w = $maxwidth;
				$dst_h = $maxwidth/$percent;
			}
		}

		if($dst_w<=$src_w && $dst_h<=$src_h){
			return $outname;
		}

		$src_image = $createFn($filename);
		$dst_image = imagecreatetruecolor($dst_w, $dst_h);
		imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		$ret = $outputFn($dst_image, $rename);
		imagedestroy($dst_image);
		imagedestroy($src_image);
		if($ret){
			return $dst_name;
		}else{
			return null;
		}
	}

	public function thumb($src_name,$suffix='',$maxwidth='',$maxheight='',$percent=null){
		$maxwidth = empty($maxwidth)?$this->thumbWidth:$maxwidth;
		$maxheight = empty($maxheight)?$this->thumbHeight:$maxheight;
		$suffix = empty($suffix)?$this->thumbName:$suffix;
		$thumb_paths = $this->getPaths($src_name);
		$namearr = explode('.',$thumb_paths['name']);
		$ext = end($namearr);
		array_pop($namearr);
		$name = implode('.',$namearr);
		$dst_name = $name.$suffix.'.'.$ext;
		return $this->resize($src_name,$dst_name,$maxwidth,$maxheight,$percent);
	}

	public function code($width=100,$height=30,$count=4){
		$string = str_shuffle(join('',array_merge(range(0,9),range('a','z'),range('A','Z'))));
		$start = mt_rand(0,strlen($string)-$count-1);
		$code = substr($string,$start,$count);
		$font = ROOT_PATH.'libs/fonts/1.ttf';
		$image = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($image, mt_rand(210,255), mt_rand(210,255), mt_rand(210,255));
		imagefilledrectangle($image, 0, 0, $width, $height, $white);
		$fontsize = $height/1.7;
		$fh = imagefontheight($fontsize);
		$fw = imagefontwidth($fontsize);
		for ($j=0; $j < 50; $j++) { 
			imagesetpixel($image, mt_rand(0,$width), mt_rand(0,$height), $this->getColor($image));
		}
		for ($j=0; $j < 5; $j++) { 
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $this->getColor($image));
		}
		for ($i=0; $i < $count; $i++) { 
			$color = $this->getColor($image,2);
			imagettftext($image, $fontsize, mt_rand(0,20), ($width/$count)*$i+$fw/2, $height/3+$fh, $color, $font, $code[$i]);
		}
		for ($j=0; $j < 3; $j++) { 
			imagearc($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width/2), mt_rand(0,$height/2), mt_rand(0,360), mt_rand(0,360), $this->getColor($image));
		}
		header('content-type:image/png');
		imagepng($image);
		imagedestroy($image);
		return strtolower($code);
	}

	private function getColor($image, $type=1){
		if(1===$type){
			return imagecolorallocatealpha($image, mt_rand(100,255), mt_rand(100,255), mt_rand(100,255), mt_rand(50,100));
		}else{
			return imagecolorallocate($image, mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
		}
	}

	public function getInfo($filename){
		$info = getimagesize($filename);
		if(!$info || !isset($info['mime'])){
			return null;
		}
		$fileinfo['width'] = $info[0];
		$fileinfo['height'] = $info[1];
		$fileinfo['createFn'] = str_replace('/','createfrom',$info['mime']);
		$fileinfo['outputFn'] = str_replace('/','',$info['mime']);
		return $fileinfo;
	}

	public function getUniName(){
		return md5(uniqid(microtime(true),true));
	}

	public function getPaths($pathname){
		$patharr = explode('/', $pathname);
		$name = end($patharr);
		array_pop($patharr);
		$path = implode('/',$patharr);
		if(strlen($path)){
			$path = $path.'/';
		}
		return array('name'=>$name,'path'=>$path);
	}

	public static function getInstance($path='',$maxSize='',$allowExt=''){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self($path,$maxSize,$allowExt);
		}
		return self::$_instance;
	}

}


?>