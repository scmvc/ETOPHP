<?php

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

