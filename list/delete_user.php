<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "user_inc.php";
require_once "ml_common.php";
require_once "memberlist_inc.php";
require_once "../function/graphic_lib.php";

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

$sql = "select `mail_mode`,`mail_master` from `PHP_DEFAULT_STYLE`";
	
$result = $db->query($sql);
if (DB::isError($result)) {
	trigger_error($result->getMessage(), E_USER_ERROR);
}
$DEF_STYLE = $result->fetchRow(DB_FETCHMODE_ASSOC);
//var_dump($DEF_STYLE);
$mlfr=$DEF_STYLE["mail_master"];

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

if($mode == "delete"){
	if($_GET["session_id"]){
		$session_id = $_GET["session_id"];
		if(!$session_id){
			$session_id = $_POST["session_id"];
		}

		$sql = "select * from `LOST_PASS` where `session-id` = '$session_id'";
		$result = $db->query($sql);
			if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
			}
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$u_id = $user_rows["id"];
		$time = $user_rows["time"];
		$u_uid = $user_rows["uid"];
	
		$n_time = time();
		if($time < $n_time){
			sub_msg("","","そのsession_idは有効期限が切れています","正しいsession_idをお使いください");
		}
		
		delete_user($u_uid);
		$uid ="";
		
		$sql = "delete from `LOST_PASS` where `id` = '$u_id'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		sub_msg("","","削除完了","ご利用ありがとうございました。");
		
	}else{
		
	$sql = "select * from `PHP_USR_STYLE` where `uid` = '$uid'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	$chk = $result->numRows();
	if($chk){
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$mail_sendfor = $user_rows["mail_sendfor"];
		
		
		if(!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $mail_sendfor)){
			sub_msg("","","このメールアドレスは利用できません","セッションを終了します");
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$session_id = md5((time()*time()));
		
		$time = time();
		$end_time = $time + 86400;
		$now_date = gmdate('Y/m/d (D) H:i:s', $time+9*60*60);
		$end_date = gmdate('Y/m/d (D) H:i:s', (86400+$time)+9*60*60);
		
		$sql = "INSERT INTO `LOST_PASS` VALUES ('','$uid' ,'$session_id' ,'$end_time' ,'$ip')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		//$mlfr="atrac@deep-emotion.com";
		$mlsb="[$db_name データ削除問い合わせ]";
		
		$body = "このメールはデータ削除システムから送信されました。\n";
		$body .= "このメールにお心当たりがない場合は、$mlfr まで連絡下さい。\n\n";
		$body .= "下記のアドレスへアクセスしていただき、削除申請を行ってください。\n\n";
		$body .= "削除されたデータは復旧できません。\n";
		$body .= "この操作は $now_date から$end_date までに行わないと無効になります。\n\n";
		$body .= $PHP_CUR_PASS."list/delete_user.php?mode=delete&session_id=$session_id\n\n";
		$body .= "問い合わせホスト：$ip\n";
		$body .= "問い合わせ時刻：$now_date\n";
	
		mb_language('Japanese');
		mb_internal_encoding("SJIS");
		mb_send_mail($mail_sendfor, $mlsb, $body, "FROM: $mlfr\nContent-Type: text/plain;\n charset=\"iso-2022-jp\"\n");

		sub_msg("","","完了","$mail_sendfor へ確認メールを送信しました。<BR>上記のアドレスへアクセスしていただき、削除申請を行ってください。<BR>この操作は $now_date から$end_date までに行わないと無効になります。");
	}else{
		sub_msg("","","セッションを終了します","名前もしくはメールアドレスが正しくありません。");
	}
		
		
		
	}
}

function default_form($uid){

	echo "あなたのデータを削除します。<BR>";
	echo "確認のためメールで認証してから削除となります。<BR><BR>";
	
	echo "削除対象エリア<BR>";
	echo "・メンバーリスト<BR>";
	echo "・日記<BR>";
	echo "・バザー<BR>";
	echo "・ポスト<BR>";
	echo "・サイトスタイル<BR><BR>";
	echo "削除非対象エリア<BR>";
	echo "・アルバム<BR>";
	echo "・投票<BR>";
	echo "・掲示板<BR><BR>";
	
	echo "<a href='delete_user.php?mode=delete'>ユーザーデータ削除</a><BR><BR>";

}

function delete_user($uid){
	global $db,$mog_net_group,$mog_net_domain;

	$sql = "select * from `USER_DATA` where `uid` = '$uid'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$name = $user_rows["name"];

	$sql = "delete from `USER_DATA` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "delete from `USER_PLOF` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "delete from `USER_PROD` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sql = "delete from `USER_STA` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "delete from `USER_SKL` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "delete from `USER_LEV` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "delete from `USER_IP` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "delete from `USER_SESSION_ID` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "select * from `USER_DIARY` where `uid` = '$uid'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	$chk = $result->numRows();
	if($chk){
		while($user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$diary_id = $user_rows["id"];
			
			$sql = "delete from `USER_DIARY` where `id` = '$diary_id'";
			$result2 = $db->query($sql);
			if (DB::isError($result2)) {
				trigger_error($result2->getMessage(), E_USER_ERROR);
			}
		}
	}
	
	$sql = "select * from `USER_DIARY_RES` where `uid` = '$uid'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	$chk = $result->numRows();
	if($chk){
		while($user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$diary_id = $user_rows["id"];
			
			$sql = "delete from `USER_DIARY_RES` where `id` = '$diary_id'";
			$result2 = $db->query($sql);
			if (DB::isError($result2)) {
				trigger_error($result2->getMessage(), E_USER_ERROR);
			}
		}
	}
	
	$sql = "select * from `USER_DIARY` where `uid` = '$uid'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	$chk = $result->numRows();
	if($chk){
		while($user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$diary_id = $user_rows["id"];
			
			$sql = "delete from `USER_DIARY` where `id` = '$diary_id'";
			$result2 = $db->query($sql);
			if (DB::isError($result2)) {
				trigger_error($result2->getMessage(), E_USER_ERROR);
			}
		}
	}

	$sql = "delete from `PHP_USR_LEVEL` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "delete from `PHP_USR_STYLE` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	//mail
	
	$send_user_mail = $name."@".$mog_net_group.".".$mog_net_domain;
	$send_user_mail = strtolower($send_user_mail);
	//var_dump($send_user_mail);
	
	$sql = "select * from `POST_IN` where `receive` = '$send_user_mail'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		while($user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$post_id = $user_rows["id"];
			
			$sql = "delete from `POST_IN` where `id` = '$post_id'";
			$result2 = $db->query($sql);
			if (DB::isError($result2)) {
				trigger_error($result2->getMessage(), E_USER_ERROR);
			}
		}
	}
	
	$sql = "select * from `POST_OUT` where `send` = '$send_user_mail'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		while($user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$post_id = $user_rows["id"];
			
			$sql = "delete from `POST_OUT` where `id` = '$post_id'";
			$result2 = $db->query($sql);
			if (DB::isError($result2)) {
				trigger_error($result2->getMessage(), E_USER_ERROR);
			}
		}
	}
	
}

//----sub rutine----
/*
function user_form(){
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
	

	if($_GET["session_id"]){
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
				user_delete_session($uid);
				////user_login_form($main_true_name);
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
				default_form($uid);
		//	}
	}
}
*/

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
                  <BR>登録データ削除</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
					<?php
					
					//user_form();
					default_form($uid);
					
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