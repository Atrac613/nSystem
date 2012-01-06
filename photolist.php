<?php
//ライブラリ呼び出し
require_once "db_setting.php";
require_once "php_inc.php";
$db = db_init();

//page chk
page_mode();

$wbcookie= $_COOKIE["$db_name"];
list($c_name,$c_session_id)=explode(",",$wbcookie);
if(user_chk()){
	$sql = "select * from `USER_DATA` where `name` = '$c_name'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$uid = $user_rows["uid"];
}

$STYLE = load_style(0,0);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<TITLE><?php echo "$STYLE[site_name]"; ?></TITLE>
<SCRIPT language="JavaScript" src="popup.js"></SCRIPT>
<?php echo "$STYLE[css]"; ?>

</HEAD>
<BODY>
<DIV align="center"><BR>
<table border='0' width='400' cellpadding='1' cellspacing='1'><tr><td class='color0' align="center">
ギャラリー</td></tr></table><BR>
<TABLE>
  <TBODY>
  <?php
	$sql = "select * from `PHP_BESTPHOTO` where `sta` = '1' or `sta` = '0' order by `date` desc";
	$result = $db->query($sql);
	$localcount = 0;
	echo "<TR>\n";
	while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
		$sel_id = $user_rows["sel_id"];
		$img = $user_rows["img"];
		$date = $user_rows["date"];
		$date = gmdate("y/m/d (D) H:i", $date+9*60*60);
    
		echo "<TD align='center'><a href=javascript:mode('next&id=$sel_id')><IMG src='bp/imgs/$img' width='144' height='108' border='0' alt='$date'><BR>$date</a></TD>\n";
		$localcount++;
		if (((($localcount) % 3) == 0)) echo "</TR><TR>\n";
	}
	echo "</TR><TR>\n";
	
	?>
  </TBODY>
</TABLE><BR>
<a href="JavaScript:close('list')">CLOSE</a></DIV>
</BODY>
</HTML>

