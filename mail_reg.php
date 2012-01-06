<?php
//ライブラリ呼び出し
require_once "db_setting.php";
require_once "php_inc.php";
require_once "list/memberlist_inc.php";

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

function salt(){
	mt_srand((double)microtime() * mt_rand());
    $xx = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        . 'abcdefghijklmnopqrstuvwxyz';
    $salt = substr($xx, mt_rand(0,52), 1);
    $salt .= substr($xx, mt_rand(0,52), 1);
    $salt .= substr($xx, mt_rand(0,52), 1);
    $salt .= substr($xx, mt_rand(0,52), 1);

	return $salt;
}

function isAlphaOrNum($input){
	$pattern = "/^[a-zA-Z0-9]+$/";
	if(preg_match($pattern, $input)){
		return true;
	}else{
		return false;
	}
}
function isAlphaOrNumName($input){
	$pattern = "/^[a-zA-Z]+$/";
	if(preg_match($pattern, $input)){
		return true;
	}else{
		return false;
	}
}

$env_rows = load_env();

if(!$env_rows["reg_mode"]){
	die("Access Denied");
}

$sql = "select `mail_mode`,`mail_master` from `PHP_DEFAULT_STYLE`";
	
$result = $db->query($sql);
if (DB::isError($result)) {
	trigger_error($result->getMessage(), E_USER_ERROR);
}
$DEF_STYLE = $result->fetchRow(DB_FETCHMODE_ASSOC);
//var_dump($DEF_STYLE);
$mlfr=$DEF_STYLE["mail_master"];

function pre_regist_form(){
	global $db_name,$STYLE;
	
	$now_date = gmdate('Y/m/d (D) H:i:s', time()+9*60*60);
	$end_date = gmdate('Y/m/d (D) H:i:s', (86400+time())+9*60*60);

?>

<TABLE width="100%">
  <TBODY>
    <TR>
      <TD>ステップ2</TD>
    </TR>
    <TR>
      <TD>　登録したメールアドレスで「<b>[<?php echo "$STYLE[site_name]"; ?> メール認証]</b>」の件名で受信したEメールをご覧ください。このEメールでご案内したURLにアクセスしてステップ3へ進んでください。</TD>
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

function reg_form(){

?>

				<form method=post enctype='multipart/form-data' action=mail_reg.php>
				<INPUT type=hidden name=mode value="pre_regist_mail">
<TABLE width="100%">
  <TBODY>
    <TR>
      <TD>ステップ1</TD>
      <TD>Eメールアドレスを入力してください。<br>記入されたEメールアドレスへ認証用メールを送信します。</TD>
    </TR>
    <TR>
      <TD>メールアドレス</TD>
      <TD><INPUT size=40 type=text name=mail></TD>
    </TR>
	<?php
	/*
    <TR>
      <TD>タイムゾーン</TD>
      <TD><select name=timezone>
	  <?php
for($i=-12;$i<12;$i++){
	if($i==9){
	echo "<option value=$i selected>$timezone[$i]</option>\n";
	} else {
	echo "<option value=$i>$timezone[$i]</option>\n";
	}
}
	  ?></select>
	  </TD>
    </TR>
	*/
	?>

    <TR>
      <TD></TD>
      <TD><input type=submit value=登録 name=regist></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>

<?php

}


//var_dump(count(register::getTimeZoneList()));
//$timezone = register::getTimeZoneList();
//var_dump($timezone);
//die();

$mode =$_GET["mode"];
if(!$mode){
	$mode =$_POST["mode"];
}
	
if($mode == "pre_regist_mail"){

	$mail = $_POST["mail"];

	
	if(!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $mail)){
		sub_msg("","","そのメールアドレスは登録に利用できません","半角英数字を利用してください");
	}


	$ip = $_SERVER['REMOTE_ADDR'];
	$session_id = md5((time()*time()));
	
	$time = time();
	$end_time = $time + 86400;
	$now_date = gmdate('Y/m/d (D) H:i:s', $time+9*60*60);
	$end_date = gmdate('Y/m/d (D) H:i:s', (86400+$time)+9*60*60);
	
	$sql = "INSERT INTO `PHP_PRE_REG` VALUES ('', '$session_id','$mail', '', '$end_time','$ip' )";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	
	//$mlfr="atrac@deep-emotion.com";
	$mlsb="[$STYLE[site_name] メール認証]";
	
	$body = "このメールはメール認証システムから送信されました。\n";
	$body .= "このメールにお心当たりがない場合は、$mlfr まで連絡下さい。\n\n";
	$body .= "下記のアドレスへアクセスしていただき、メールの認証を行ってください。\n";
	$body .= "この操作は $now_date から$end_date までに行わないと無効になります。\n\n";
	$body .= $PHP_CUR_PASS."mail_reg.php?mode=regist_mail&session_id=$session_id\n\n";
	$body .= "問い合わせホスト：$ip\n";
	$body .= "問い合わせ時刻：$now_date\n";
	
	mb_language('Japanese');
	mb_internal_encoding("SJIS");
	mb_send_mail($mail, $mlsb, $body, "FROM: $mlfr\nContent-Type: text/plain;\n charset=\"iso-2022-jp\"\n");

	$uid = "";
	//sub_msg("0","","仮登録成功","$mail に認証メールを送信しました<br>メールに記入されているURLへアクセスして下さい");
}elseif($mode == "regist_mail"){
	$session_id = $_GET["session_id"];
	if(!$session_id){
		$session_id = $_POST["session_id"];
	}

	$sql = "select * from `PHP_PRE_REG` where `session_id` = '$session_id'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$u_id = $user_rows["id"];
	$time = $user_rows["end_date"];
	$ip = $user_rows["ip"];
	$mail = $user_rows["mail"];
	
	$n_time = time();
	if($time < $n_time){
		sub_msg("","","そのsession_idは有効期限が切れています","正しいsession_idをお使いください");
	}
	
	
	$sql = "REPLACE INTO `PHP_PRE_REG` VALUES ('$u_id', '', '$mail', '1' ,'','$ip')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	sub_msg("10","register.php?mail=$mail&id=$u_id","認証成功","メール認証が成功しました<br>次のステップに移ります<br>自動的にページが変わるまでお待ちください");
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
            <TD class="color2" height="34" width="200">&nbsp;新規登録</TD>
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
				
				if($mode =="pre_regist_mail"){
					pre_regist_form();
				}elseif($mode == "regist_mail"){
					//regist_pass_form();
				}else{
					reg_form();
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