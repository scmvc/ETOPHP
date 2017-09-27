<?php
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
function View($Path = "") {
	if (!$Path) {
		global $ViewPath;
		$ViewPath = explode("\\", $ViewPath);
		$ViewPath[3] = explode("Controller", $ViewPath[3]);
		$ViewPath[3] = $ViewPath[3][0];
		return APP_PATH . "/" . $ViewPath[1] . "/View/" . $ViewPath[3] . "/" . $ViewPath[4] . ".blade.php";
	} else {
		return APP_PATH . "/" . $Path . ".blade.php";
	}
}

function M($TABLE_NAME = "") {
	return new DB($TABLE_NAME);
}

/**
 * 数组分类排序
 * @param [array] $columnsArr [需要进行排序的数组]
 * @param [int] $plmid [所属分类ID]
 */
function getColumns($columnsArr, $plmid) {
	$menu = array();

	foreach ($columnsArr as $v) {
		if ($v['plmid'] == $plmid) {
			$menu[] = $v;

			$a = getColumns($columnsArr, $v['lmid']);
			foreach ($a as $vv) {
				$menu[] = $vv;
			}
		}
	}
	return $menu;
}

/**
 * 获取客户端真实IP
 */
function GETIP() {
	global $ip;

	if (getenv("HTTP_CLIENT_IP")) {
		$ip = getenv("HTTP_CLIENT_IP");
	} else if (getenv("HTTP_X_FORWARDED_FOR")) {
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	} else if (getenv("REMOTE_ADDR")) {
		$ip = getenv("REMOTE_ADDR");
	} else {
		$ip = "Unknow";
	}

	return $ip;

}

/**
 * UUID 生成
 */
function UUID() {
	$prefix = '';
	$uuid = '';
	$str = md5(uniqid(mt_rand(), true));
	$uuid = substr($str, 0, 8) . '-';
	$uuid .= substr($str, 8, 4) . '-';
	$uuid .= substr($str, 12, 4) . '-';
	$uuid .= substr($str, 16, 4) . '-';
	$uuid .= substr($str, 20, 12);
	return $prefix . $uuid;
}

/**
 *密码加密码
 */
function GenEncryption() {
	srand((double) microtime() * 1000000); //create a random number feed.
	$ychar = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z";
	$list = explode(",", $ychar);
	$authnum = "";
	for ($i = 0; $i < 6; $i++) {
		$randnum = rand(0, 61); // 10+26;
		$authnum .= $list[$randnum];
	}
	return $authnum;
}

//密码加密
function GenPassworw($password) {
	$Enc = GenEncryption();
	$Pwd = md5(md5($Enc . $password));
	return array("encryption" => $Enc, "password" => $Pwd);
}

//密码验证
function VerPassword($identity, $password) {
	$account = M("account_password")->where("account_identity = '$identity' and", "status = ", '1')->select("encryption,account_password")->find();
	$VerPwd = md5(md5($account["encryption"] . $password));
	if ($VerPwd == $account["account_password"]) {
		unset($account);
		unset($VerPwd);
		return true;
	} else {
		unset($account);
		unset($VerPwd);
		return false;
	}

}
/**
 * Json return
 * @param string $data   [description]
 * @param string $status [description]
 * @param string $msg    [description]
 * @param string $method [description]
 */
function JsonReturn($data = "", $status = "200", $msg = "", $method = "") {
	if ($method == '') {
		exit(json_encode(array("status" => $status, "data" => $data, "msg" => $msg)));
	} else {
		exit($method . "(" . json_encode(array("status" => $status, "data" => $data, "msg" => $msg)) . ")");
	}
}

function WriteLog($Log) {
	$logger = new Logger('LOGGS');
	$logURI = "Cache/log/" . date("Y-m-d-H-i") . ".log";
	$logger->pushHandler(new StreamHandler($logURI, Logger::DEBUG));
	$logger->pushHandler(new FirePHPHandler());
	$logger->addInfo($Log);
}

function SendMail($content,$mail){

	require "vendor/phpmailer/phpmailer/PHPMailerAutoload.php";
	$mail = new PHPMailer;
	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.163.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'MichaelJions';                 // SMTP username
	$mail->Password = 'a123456';                           // SMTP password
	// $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 25;                                    // TCP port to connect to

	$mail->setFrom('MichaelJions@163.com', 'Michael');
	$mail->addAddress('chen@cnscbv.com', 'Joe User');     // Add a recipient
	$mail->addAddress('chen@cnscbv.com');               // Name is optional
	$mail->addReplyTo('chen@cnscbv.com', 'Information');
	$mail->addCC('chen@cnscbv.com');
	$mail->addBCC('chen@cnscbv.com');

	// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = 'Here is the subject';
	$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if(!$mail->send()) {
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Message has been sent not';
	}


}
