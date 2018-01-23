<?php
namespace etophp\init;

use Illuminate\Database\Capsule\Manager as DB;
/**
* ETO PHP Init
*/
class init
{
	function __construct()
	{
	}

	public static function Run(){
		$dir =  dirname(dirname(dirname(dirname(__FILE__))));
		require $dir."/vendor/autoload.php";
		$DB = new DB;
		$DB->addConnection(require $dir."/Config/DBConfig.php");
		$DB->bootEloquent();
		require $dir.'/_Route/routes.php';
		\etophp\tool\Route::dispatch();
	}
}