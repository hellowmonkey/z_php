<?php 

defined('V') OR exit('非法访问');

class Form {

	public function __construct(){
		@session_start();
	}

	/**
	 * 验证用户名
	 * @param string $value
	 * @param int $length
	 * @return boolean
	 */
    public static function isNames($value, $minLen=2, $maxLen=20, $charset='ALL'){
        if(empty($value))
            return false;
        switch($charset){
            case 'EN': $match = '/^[_\w\d]{'.$minLen.','.$maxLen.'}$/iu';
                break;
            case 'CN':$match = '/^[_\x{4e00}-\x{9fa5}\d]{'.$minLen.','.$maxLen.'}$/iu';
                break;
            default:$match = '/^[_\w\d\x{4e00}-\x{9fa5}]{'.$minLen.','.$maxLen.'}$/iu';
        }
        return preg_match($match,$value);
    }

    /**
     * 验证密码
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public static function isPWD($value,$minLen=5,$maxLen=16){
        $match='/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{'.$minLen.','.$maxLen.'}$/';
        $v = trim($value);
        if(empty($v)) 
            return false;
        return preg_match($match,$v);
    }

    /**
     * 验证eamil
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public static function isEmail($value,$match='/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i'){
        $v = trim($value);
        if(empty($v)) 
            return false;
        return preg_match($match,$v);
    }

    /**
     * 验证电话号码
     * @param string $value
     * @return boolean
     */
    public static function isTelephone($value,$match='/^0[0-9]{2,3}[-]?\d{7,8}$/'){
        $v = trim($value);
        if(empty($v)) 
            return false;
        return preg_match($match,$v);
    }

    /**
     * 验证手机
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isMobile($value,$match='/^[(86)|0]?(13\d{9})|(15\d{9})|(18\d{9})$/'){
        $v = trim($value);
        if(empty($v)) 
            return false;
        return preg_match($match,$v);
    }
    /**
     * 验证邮政编码
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isPostcode($value,$match='/\d{6}/'){
        $v = trim($value);
        if(empty($v)) 
            return false;
        return preg_match($match,$v);
    }
    /**
     * 验证IP
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isIP($value,$match='/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/'){
        $v = trim($value);
        if(empty($v))
            return false;
        return preg_match($match,$v);
    }

    /**
     * 验证身份证号码
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isIDcard($value,$match='/^\d{6}((1[89])|(2\d))\d{2}((0\d)|(1[0-2]))((3[01])|([0-2]\d))\d{3}(\d|X)$/i'){
        $v = trim($value);
        if(empty($v)) 
            return false;
        else if(strlen($v)>18) 
            return false;
        return preg_match($match,$v);
    }

    /**
     * *
     * 验证URL
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isURL($value,$match='/^(http:\/\/)?(https:\/\/)?([\w\d-]+\.)+[\w-]+(\/[\d\w-.\/?%&=]*)?$/'){
        $v = strtolower(trim($value));
        if(empty($v)) 
            return false;
        return preg_match($match,$v);   
    }



	//utf8下匹配中文  
	public static function isChinese($subject){  
	    $pattern ='/([\x{4e00}-\x{9fa5}]){1}/u';  
	    return preg_match($pattern, $subject);
	} 
	/**
	* @手机号
	*/
	public static function isPhone($subject) {
		$pattern='/^(0|86|17951)?(13[0-9]|15[012356789]|1[78][0-9]|14[57])[0-9]{8}$/';
		return preg_match($pattern, $subject);
	}
	/**
	* @数字
	*/
	public static function isNumber($subject) {
		$pattern='/^[0-9]+$/';
		return preg_match($pattern, $subject);
	}
	/**
	* @年份 格式：yyyy
	*/
	public static function isYear($subject) {
		$pattern='/^(\d{4})$/';
		return preg_match($pattern, $subject);
	}
	/**
	* @月份 格式:mm
	*/
	public static function isMonth($subject) {
		$pattern='/^0?([1-9])$|^(1[0-2])$/';
		return preg_match($pattern, $subject);
	}
	/**
	* @日期 格式：yyyy-mm-dd
	*/
	public static function isDay($subject) {
		$pattern='/^(\d{4})-(0?\d{1}|1[0-2])-(0?\d{1}|[12]\d{1}|3[01])$/';
		return preg_match($pattern, $subject);
	}
	/**
	* @日期时间 格式：yyyy-mm-dd hh:ii:ss
	*/
	public static function isDateTime($subject) {
		$pattern='/^(\d{4})-(0?\d{1}|1[0-2])-(0?\d{1}|[12]\d{1}|3[01])\s(0\d{1}|1\d{1}|2[0-3]):[0-5]\d{1}:([0-5]\d{1})$/';
		return preg_match($pattern, $subject);
	}
	/**
	* @有效图片地址
	*/
	public static function isPhoto($subject) {
		$pattern='/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/';
		return preg_match($pattern, $subject);
	}
	/**
	* @URL地址
	*/
	public static function isUrlAddress($subject) {
		$pattern='/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/';
		return preg_match($pattern, $subject);
	}
	/**
	* @有效HTTP地址
	*/
	public static function EffectiveHttp($subject) {
		$pattern='/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/';
		return preg_match($pattern, $subject);
	}
	/**
	* @IPv4
	*/
	public static function Ipv4($subject) {
		$pattern='/^(((\d{1,2})|(1\d{2})|(2[0-4]\d)|(25[0-5]))\.){3}((\d{1,2})|(1\d{2})|(2[0-4]\d)|(25[0-5]))$/';
		return preg_match($pattern, $subject);
	}
	/**
	* @IPv6
	*/
	public static function Ipv6($subject) {
		$pattern='/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/';
		return preg_match($pattern, $subject);
	}
	/**
	* @匹配正则公共方法
	*/
	public static function Method($pattern, $subject){
		if(preg_match($pattern, $subject)){
			return true;
		}
		return false;
	}
}





 ?>