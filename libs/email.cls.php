<?php 

defined('V') OR exit('非法访问');

class Email {

	static public function send($smtpemailto,$mailsubject,$mailbody){
		$config = C('email'); 
		//SMTP邮件服务器  
		$smtpserver = $config['smtpserver'];  
		//SMTP服务器端口  
		$smtpserverport = $config['smtpserverport'];  
		//SMTP用户邮箱地址  
		$smtpusermail = $config['smtpusermail'];  
		//SMTP用户名和密码  
		$smtpuser = $config['smtpuser'];  
		$smtppass = $config['smtppass'];  
		//是否是用身份验证  
		$isauth = $config['isauth'];  
		//邮件格式（HTML/TXT）,TXT为文本邮件   
		$mailtype = $config['mailtype']; 

		load('libs/smtp');
		//新建SMTP实例  
		$email = new Smtp($smtpserver, $smtpserverport, $isauth, $smtpuser, $smtppass);
		  
		//是否显示发送的调试信息   
		$email->debug = $config['debug'];  
		  
		//发送邮件  
		$ret = $email->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
		return $ret;
	}

}


?>