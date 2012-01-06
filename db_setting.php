<?

function getmicrotime(){ 
	list($usec, $sec) = explode(" ",microtime()); 
	return ((float)$sec + (float)$usec); 
} 

$microtime_start = getmicrotime();

//-----MySQL------
require_once "DB.php";

$db_user = "user";
$db_pass = "pass";
$db_host = "localhost";
$db_name = "nsystem";
$db_type = "mysql";
//------end-------

//default setting
$PHP_CUR_PASS = "http://www.windurstbreeze.com/";

?>
