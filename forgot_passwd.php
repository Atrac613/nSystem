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

$STYLE = load_style(0,0);

function chk_poll_id($id){
	global $db;
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		return true;
	}else{
		return false;
	}

}

$sql = "select `mail_mode`,`mail_master` from `PHP_DEFAULT_STYLE`";
	
$result = $db->query($sql);
if (DB::isError($result)) {
	trigger_error($result->getMessage(), E_USER_ERROR);
}
$DEF_STYLE = $result->fetchRow(DB_FETCHMODE_ASSOC);
//var_dump($DEF_STYLE);
$mlfr=$DEF_STYLE["mail_master"];

function forgot_passwd_form(){

?>

				<form method=post enctype='multipart/form-data' action=forgot_passwd.php>
				<INPUT type=hidden name=mode value="forgot_passwd">
<TABLE width="100%">
  <TBODY>
    <TR>
      <TD>ステップ1</TD>
      <TD>アカウントを設定したときに登録したEメールアドレスを入力してください。</TD>
    </TR>
    <TR>
      <TD>名前</TD>
      <TD><INPUT size="20" type="text" name="name"></TD>
    </TR>
    <TR>
      <TD>メールアドレス</TD>
      <TD><INPUT size="30" type="text" name="mail"></TD>
    </TR>
    <TR>
      <TD></TD>
      <TD><input type=submit value=送信></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>

<?php

}

function forgot_passwd(){
	global $db_name;
	
	$now_date = gmdate('Y/m/d (D) H:i:s', time()+9*60*60);
	$end_date = gmdate('Y/m/d (D) H:i:s', (86400+time())+9*60*60);

?>

<TABLE width="100%">
  <TBODY>
    <TR>
      <TD>ステップ2</TD>
    </TR>
    <TR>
      <TD>　登録したメールアドレスで「<b>[<?php echo "$STYLE[site_name]"; ?> パスワード問い合わせ]</b>」の件名で受信したEメールをご覧ください。このEメールでご案内したURLにアクセスしてステップ3へ進んでください。</TD>
    </TR>
    <TR>
      <TD><BR></TD>
    </TR>
    <TR>
      <TD>*注意*</TD>
    </TR>
    <TR>
      <TD>この操作は <?php echo "$now_date"; ?> から<?php echo "$end_date"; ?> までに行わないと無効になります。</TD>
    </TR>
  </TBODY>
</TABLE>

<?php

}

function regist_pass_form(){

	$session_id = $_GET["session_id"];
	

?>
				<form method=post enctype='multipart/form-data' action=forgot_passwd.php>
				<INPUT type=hidden name=mode value="regist_passwd_db">
				<INPUT type=hidden name=session_id value="<?php echo "$session_id"; ?>">
<TABLE width="100%">
  <TBODY>
    <TR>
      <TD width=20%>ステップ3</TD>
      <TD>新パスワード設定</TD>
    </TR>
    <TR>
      <TD>新パスワード</TD>
      <TD><INPUT size="20" type="text" name="pass"></TD>
    </TR>
    <TR>
      <TD></TD>
      <TD><input type=submit value=送信></TD>
    </TR>
  	</form>
  </TBODY>
</TABLE>

<?php

}

$mode =$_GET["mode"];
if(!$mode){
	$mode =$_POST["mode"];
}
if($mode == "regist_passwd" || $mode == "regist_passwd_db"){
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

}

if($mode == "regist_passwd_db"){
	$pass = $_POST["pass"];
	
	$sql = "select * from `USER_DATA` where `uid` = '$u_uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$user_no = $user_rows["no"];
	$user_name = $user_rows["name"];
	
	$now_date = gmdate('Y/m/d (D) H:i:s', $time+9*60*60);
	$ip = $_SERVER['REMOTE_ADDR'];
			
	$sql = "REPLACE INTO USER_DATA VALUES ('$u_uid', '$user_no', '$user_name', password('$pass') )";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "select * from `PHP_USR_STYLE` where `uid` = '$u_uid'";
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
			
		//$mlfr="atrac@deep-emotion.com";
		$mlsb="[$db_name パスワード問い合わせ]";
		
		$body = "このメールはパスワード再発行システムから送信されました。\n";
		$body = "パスワードが変更されました。\n";
		$body .= "次回ログイン時は新しいパスワードをお使いください。\n\n";
		$body .= "問い合わせホスト：$ip\n";
		$body .= "問い合わせ時刻：$now_date\n";
	
		mb_language('Japanese');
		mb_internal_encoding("SJIS");
		mb_send_mail($mail_sendfor, $mlsb, $body, "FROM: $mlfr\nContent-Type: text/plain;\n charset=\"iso-2022-jp\"\n");
	}
	
	$sql = "delete from `LOST_PASS` where `id` = '$u_id'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	
	sub_msg("","","セッションを終了します","パスワードの再設定が終了しました");

}

if($mode == "forgot_passwd"){
	$name = $_POST["name"];
	$mail = $_POST["mail"];
	
	$sql = "select * from `USER_DATA` where `name` = '$name'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$u_uid = $user_rows["uid"];
	
	$sql = "select * from `PHP_USR_STYLE` where `uid` = '$u_uid' and `mail_sendfor` = '$mail'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	$chk = $result->numRows();
	if($chk){
		if(!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $mail)){
			sub_msg("","","このメールアドレスは利用できません","セッションを終了します");
		}
		

		$ip = $_SERVER['REMOTE_ADDR'];
		$session_id = md5((time()*time()));
		
		$time = time();
		$end_time = $time + 86400;
		$now_date = gmdate('Y/m/d (D) H:i:s', $time+9*60*60);
		$end_date = gmdate('Y/m/d (D) H:i:s', (86400+$time)+9*60*60);
		
		$sql = "INSERT INTO `LOST_PASS` VALUES ('','$u_uid' ,'$session_id' ,'$end_time' ,'$ip')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		
		//$mlfr="atrac@deep-emotion.com";
		$mlsb="[$db_name パスワード問い合わせ]";
		
		$body = "このメールはパスワード再発行システムから送信されました。\n";
		$body .= "このメールにお心当たりがない場合は、$mlfr まで連絡下さい。\n\n";
		$body .= "下記のアドレスへアクセスしていただき、パスワードの変更を行ってください。\n";
		$body .= "この操作は $now_date から$end_date までに行わないと無効になります。\n\n";
		$body .= $PHP_CUR_PASS."forgot_passwd.php?mode=regist_passwd&name=$name&session_id=$session_id\n\n";
		$body .= "問い合わせホスト：$ip\n";
		$body .= "問い合わせ時刻：$now_date\n";
		
		mb_language('Japanese');
		mb_internal_encoding("SJIS");
		mb_send_mail($mail, $mlsb, $body, "FROM: $mlfr\nContent-Type: text/plain;\n charset=\"iso-2022-jp\"\n");

	}else{
		sub_msg("","","セッションを終了します","名前もしくはメールアドレスが正しくありません。");
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
      <TD width="8" class="color3" background="img/<?php echo "$STYLE[img_left]"; ?>" rowspan="2"><IMG src="/img/spacer.gif" width="8" height="1"></TD>
      <TD width="750" valign="top">
	  <?php echo "$STYLE[topimage]"; ?>
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="row_title" height="34"><IMG src="/img/spacer.gif" width="8" height="1"></TD>
            <TD class="row_title" height="34" width="131">FINAL FANTASY XI<BR>
            <?php echo "$STYLE[site_name]"; ?></TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10" class="color2">&nbsp;</TD>
            <TD class="color2" height="34" width="200">&nbsp;パスワード問い合わせ</TD>
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
            <TD class="color2"><IMG src="/img/spacer.gif" width="8" height="1"></TD>
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
                  <TD rowspan="5" align="right" width="10" valign="top"></TD>
                </TR>
                <TR>
                  <TD colspan="2" width="570" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
				
				<?php
				$mode =$_GET["mode"];
				if(!$mode){
					$mode =$_POST["mode"];
				}
				
				if($mode =="forgot_passwd"){
					forgot_passwd();
				}elseif($mode == "regist_passwd"){
					regist_pass_form();
				}else{
					forgot_passwd_form();
				}
				
				?>
				
				</TD></TR><TR><TD colspan="2" width="422" valign="top"></TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				<BR><BR>
				
				<BR>
				<BR><HR width='420'><A href='javascript:history.back()'>戻る</A><BR><BR><BR>
				</TD>
				</TR>
              </TBODY>
            </TABLE>
            </TD>
          </TR>
        </TBODY>
      </TABLE>
      </TD>
      <TD width="25" class="color3" background="img/<?php echo "$STYLE[img_right]"; ?>" rowspan="2"><IMG src="/img/spacer.gif" width="25" height="1"></TD>
      <TD class="color3" rowspan="2"></TD>
    </TR>
    <TR>
      <TD height="34">
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="color2" height="34"><IMG src="/img/spacer.gif" width="8" height="1"></TD>
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