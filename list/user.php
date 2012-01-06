<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "user_inc.php";
require_once "ml_common.php";
require_once "memberlist_inc.php";
require_once "../function/graphic_lib.php";
require_once "../function/graphic_lib2.php";

$db = db_init();

//page chk
page_mode();

$wbcookie= $_COOKIE["$db_name"];
list($c_name,$c_session_id)=explode(",",$wbcookie);
if(user_chk()){
	$name = $c_name;
	$sql = "select * from `USER_DATA` where `name` = '$name'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$uid = $user_rows["uid"];
	$t_pass = $user_rows["pass"];
	
	$sql = "select * from `USER_SESSION_ID` where `uid` = '$uid' and `session_id` = '$c_session_id'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$chk = $result->numRows();
	if($chk){
		$pass = $t_pass;
	}
}

$STYLE = load_style(3,0);

//大本の認証
if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,4)){
		die("Access Denied");
	}
}

$mode = $_POST["mode"];
if(!$mode){
	$mode = $_GET["mode"];
}

if($mode == "modify"){
	if($_POST["modify_mode"] == "skill"){
		user_login_modify_skill($uid);
	}else{
		user_login_modify($uid);
	}
}


//----sub rutine----

function user_login(){
	global $db,$uid;
	
	//$name = $_POST["name"];
	//if(!$name){
	//	$name = $c_name;
	//}
	//$uid = "9d226cacf361be0";
	
	//$pass = $_POST["pass"];
	//if(!$pass){
	//	$pass = $t_pass;
	//}
	
	//$mod_name = $c_name;
	//$mod_pass = $t_pass;
	
	$mode = $_POST["mode"];
	if(!$mode){
		$mode = $_GET["mode"];
	}
	
	//wb_set_cookie($name,$pass);
	
	//if($mod_name == ""){
	//	$sql = "select * from `USER_DATA` where `name` = '$name'";
	//	$sql_name = $name;
	//}elseif($mod_name != "" && $name != ""){
	//	$sql = "select * from `USER_DATA` where `name` = '$mod_name'";
	//	$sql_name = $mod_name;
	//}elseif($_POST["modify_mode"] == "skill"){
	//	$sql = "select * from `USER_DATA` where `name` = '$mod_name'";
	//	$sql_name = $mod_name;
	//}else{
	//	error_msg("エラー処理の無いエラー","L371-function user_login");
	//}
	
	//$result = $db->query($sql);
	//$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	//if($user_rows != ""){
	//	sub_msg("","","そのアカウントは登録されていません","正しいアカウントをお使いください");
	//}
	

	if($mode == "mod_skill"){
		//$sql = "select `pass`,`name`,`uid` from `USER_DATA` where `pass` = '$pass' and `name` = '$sql_name'";
		//$result = $db->query($sql);
		//	if (DB::isError($result)) {
		//		trigger_error($result->getMessage(), E_USER_ERROR);
		//	}
		//$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		//	if($user_rows == ""){
				//error_msg("不正なアクセス","access failed.");
		//	}else{
		//		$uid = $user_rows["uid"];
				user_login_form_skill($uid);
				////user_login_form($main_true_name);
		//	}
	}elseif($mode == "show_log"){
		//$sql = "select `pass`,`name`,`uid` from `USER_DATA` where `pass` = '$pass' and `name` = '$sql_name'";
		//$result = $db->query($sql);
		//	if (DB::isError($result)) {
		//		trigger_error($result->getMessage(), E_USER_ERROR);
		//	}
		//$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		//	if($user_rows == ""){
				//error_msg("不正なアクセス","access failed.");
		//	}else{
		//		$uid = $user_rows["uid"];
				//log_header($name,$pass);
				//table_chk($uid);
		//		echo "ok";
				//user_log($main_true_name);
				//user_login_form($main_true_name);
		//	}
	}else{
		//$sql = "select `pass`,`name`,`uid` from `USER_DATA` where `pass` = '$pass' and `name` = '$sql_name'";
		//$result = $db->query($sql);
		//	if (DB::isError($result)) {
		//		trigger_error($result->getMessage(), E_USER_ERROR);
		//	}
		//$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		//	if($user_rows == ""){
				//error_msg("そのパスワードは正しくありません","正しいパスワードをお使いください");
		//	}else{
		//		$uid = $user_rows["uid"];
				//die("stop");
				user_login_form($uid);
		//	}
	}
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<TITLE><?php echo "$STYLE[site_name]"; ?></TITLE>
<?php echo "$STYLE[css]"; ?>

</HEAD>
<BODY>
<TABLE height="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD width="8" class="color3" background="../img/<?php echo "$STYLE[img_left]"; ?>" rowspan="2"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
      <TD width="750" valign="top">
	  <?php echo "$STYLE[topimage]"; ?>
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="row_title" height="34"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
            <TD class="row_title" height="34" width="131">FINAL FANTASY XI<BR>
            <?php echo "$STYLE[site_name]"; ?></TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10" class="color2">&nbsp;</TD>
            <TD class="color2" height="34" width="200">&nbsp;メンバーリスト</TD>
            <TD class="color2" align="right" height="34" width="396">
			<?php
			
			if($name){
				echo "ようこそ、$name さん";
			}else{
				echo "ようこそ、ゲストさん";
			}
			
			?>&nbsp;&nbsp;</TD>
          </TR>
        </TBODY>
      </TABLE>
      <TABLE height="100%" cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="color2"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
            <TD class="color2" width="131" valign="top">
            <TABLE class="TABLE_2">
              <TBODY>
              <?php
                   main_menu();

				  if($uid){
				  	sub_menu($uid);
				  }
			  ?>
                <TR>
                  <TD><BR><BR><BR>
					<?php login_form($uid); ?>
                  </TD>
                </TR>
              </TBODY>
            </TABLE>
            </TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10">&nbsp;</TD>
            <TD valign="top" colspan="2" width="596">
            <TABLE width="100%" cellpadding="0" cellspacing="0">
              <TBODY>
                <TR>
                  <TD colspan="2" width="570"></TD>
                <TD rowspan="5" align="right" width="10" valign="top"><BR>
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422">
                  <BR>登録データ修正</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
					<?php
					
					user_login();
					
					
					?>
				<BR><HR><A href='javascript:history.back()'>戻る</A><BR><BR>
				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
				 </TD>
                </TR>
              </TBODY>
            </TABLE>
            </TD>
          </TR>
        </TBODY>
      </TABLE>
      </TD>
      <TD width="25" class="color3" background="../img/<?php echo "$STYLE[img_right]"; ?>" rowspan="2"><IMG src="../img/spacer.gif" width="25" height="1"></TD>
      <TD class="color3" rowspan="2"></TD>
    </TR>
    <TR>
      <TD height="34">
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="color2" height="34"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
            <TD class="color2" height="34" width="131">&nbsp;</TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10">&nbsp;</TD>
            <TD height="34" width="596" colspan="2" class="color2">
			<?php copyright(); ?>
			</TD>
          </TR>
        </TBODY>
      </TABLE>
      </TD>
    </TR>
  </TBODY>
</TABLE>
</BODY>
</HTML>