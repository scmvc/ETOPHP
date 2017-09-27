<?php
/**
 *
 */
namespace BaseController;
class Controller {

	protected $logger = "";
	protected $assignArray = array();

	function __construct() {
		if (method_exists($this, 'init')) {
			$this->init();
		}
		global $logger;
		$this->logger = $logger;
	}

	protected function Upload($FILES, $A = "", $B = "") {
		$ReturnFILE = array();
		$FILES = array_keys($FILES);
		foreach ($FILES as $K => $V) {
			$FlodName = "Updata/" . date("Y-m-d");
			if (!is_dir($FlodName)) {
				mkdir($FlodName, 777, true);
			}
			if ($_FILES[$FILES[$K]]["error"] > 0) {
				return false;
			}
			$tempEXT = strstr($_FILES[$FILES[$K]]["name"], ".");
			$FileName = date("YmdHis") . UUID() . $tempEXT;
			move_uploaded_file($_FILES[$FILES[$K]]["tmp_name"], "{$FlodName}/" . $FileName);
			$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
			$FileName = $FlodName . "/" . $FileName;
			$ReturnFILE[] = $FileName;
		}
		return $ReturnFILE;
	}

	protected function CURL($URI, $Data = "", $header = "") {
		if (is_array($Data)) {
			$Data = json_encode($Data);
		}
		$ch = curl_init();
		$res = curl_setopt($ch, CURLOPT_URL, $URI);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $Data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($header, 'Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);
		if ($result == NULL) {
			return 0;
		}
		return $result;
	}

	protected function assign($key, $value) {
		$this->assignArray[$key] = $value;
	}

	protected function display($View = "") {
		$smarty = new \Smarty;
		global $ViewPath;
		$ViewPath = explode("\\", $ViewPath);
		$ViewPath[3] = explode("Controller", $ViewPath[3]);
		$ViewPath[3] = $ViewPath[3][0];
		if ($View == "") {
			$View = APP_PATH . "/" . $ViewPath[1] . "/View/" . $ViewPath[3] . "/";
		}

		$smarty->setTemplateDir($View); //设置模板目录
		$smarty->setCompileDir(SITE_ROOT . '/Cache/Smarty/templates_c/');
		$smarty->setConfigDir(SITE_ROOT . '/Cache/Smarty/smarty_configs/');
		$smarty->setCacheDir(SITE_ROOT . '/Cache/Smarty/smarty_cache/');
		if (APP_DEBUG) {
			//$smarty->debugging      = true;
			$smarty->caching = false;
			$smarty->cache_lifetime = 0;
		} else {
			//$smarty->debugging      = false;
			$smarty->caching = true;
			$smarty->cache_lifetime = 120;
		}
		foreach ($this->assignArray as $key => $value) {
			$smarty->assign($key, $value);
		}
		$smarty->display($View . $ViewPath[4] . ".blade.php");
	}
}
