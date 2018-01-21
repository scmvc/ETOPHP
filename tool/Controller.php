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

}
