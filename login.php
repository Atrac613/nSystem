<?php
//ライブラリ呼び出し
require_once "db_setting.php";
require_once "php_inc.php";
$db = db_init();

//page chk
page_mode();

function wb_set_cookie(){
	global $db_name,$db;
	
	if($_POST["name"] && $_POST["pass"]){
		$name = $_POST["name"];
		$pass = $_POST["pass"];
	
		$sql = "select * from `USER_DATA` where `name` = '$name' and `pass` = password('$pass')";
		$result = $db->query($sql);
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$usr_name = $user_rows["name"];
		$usr_pass = $user_rows["pass"];
		
		if($usr_name == $name){
			$uid = $user_rows["uid"];
		}
		
		if($uid){
		
			$sql = "select * from `USER_SESSION_ID` where `uid` = '$uid'";
			$result = $db->query($sql);
			$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		
			if($user_rows){
				$time = $user_rows["time"];
				$session_id = $user_rows["session_id"];
			}else{
				$session_id = md5(time());
				$time = time();
				$time = $time + 604800;
			
				$sql = "REPLACE INTO USER_SESSION_ID VALUES ('$uid', '$session_id', '$time')";
				$result = $db->query($sql);
				if (DB::isError($result)) {
    				trigger_error($result->getMessage(), E_USER_ERROR);
				}
			}
		
			if($time < time()){
				$session_id = md5(time());
				//$ntime = time() + 60;
				$ntime = time() + 604800;
			
				$sql = "REPLACE INTO USER_SESSION_ID VALUES ('$uid', '$session_id', '$ntime')";
				$result = $db->query($sql);
				if (DB::isError($result)) {
    				trigger_error($result->getMessage(), E_USER_ERROR);
				}
				
				$cookval = implode(",", array($name,$session_id));
				header("P3P: CP='ONLi COM CUR OUR'");
				setcookie ($db_name, $cookval,time()+31*24*3600);
				
				return true;
			}
		
			$cookval = implode(",", array($name,$session_id));
			header("P3P: CP='ONLi COM CUR OUR'");
			setcookie ($db_name, $cookval,time()+31*24*3600);

		}
	}else{
		header("P3P: CP='ONLi COM CUR OUR'");
		setcookie ($db_name);
	}
	//var_dump($_SESSION);
}

wb_set_cookie();

$name = $_POST["name"];
$pass = $_POST["pass"];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<META HTTP-EQUIV='Refresh' CONTENT='3; URL=index_pc.php'>
<TITLE><?php echo "$STYLE[site_name]"; ?></TITLE>
<LINK rel='stylesheet' href='css/theme0_def.css' type='text/css'>
</HEAD>
<BODY>
<P><?php if($name && $pass){ echo "ログイン";}else{ echo "ログアウト"; } ?>処理中です...<BR>
3秒後リロードします。</P>
</BODY>
</HTML>
