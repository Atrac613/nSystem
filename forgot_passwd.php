<?php
//���C�u�����Ăяo��
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
      <TD>�X�e�b�v1</TD>
      <TD>�A�J�E���g��ݒ肵���Ƃ��ɓo�^����E���[���A�h���X����͂��Ă��������B</TD>
    </TR>
    <TR>
      <TD>���O</TD>
      <TD><INPUT size="20" type="text" name="name"></TD>
    </TR>
    <TR>
      <TD>���[���A�h���X</TD>
      <TD><INPUT size="30" type="text" name="mail"></TD>
    </TR>
    <TR>
      <TD></TD>
      <TD><input type=submit value=���M></TD>
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
      <TD>�X�e�b�v2</TD>
    </TR>
    <TR>
      <TD>�@�o�^�������[���A�h���X�Łu<b>[<?php echo "$STYLE[site_name]"; ?> �p�X���[�h�₢���킹]</b>�v�̌����Ŏ�M����E���[�����������������B����E���[���ł��ē�����URL�ɃA�N�Z�X���ăX�e�b�v3�֐i��ł��������B</TD>
    </TR>
    <TR>
      <TD><BR></TD>
    </TR>
    <TR>
      <TD>*����*</TD>
    </TR>
    <TR>
      <TD>���̑���� <?php echo "$now_date"; ?> ����<?php echo "$end_date"; ?> �܂łɍs��Ȃ��Ɩ����ɂȂ�܂��B</TD>
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
      <TD width=20%>�X�e�b�v3</TD>
      <TD>�V�p�X���[�h�ݒ�</TD>
    </TR>
    <TR>
      <TD>�V�p�X���[�h</TD>
      <TD><INPUT size="20" type="text" name="pass"></TD>
    </TR>
    <TR>
      <TD></TD>
      <TD><input type=submit value=���M></TD>
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
		sub_msg("","","����session_id�͗L���������؂�Ă��܂�","������session_id�����g����������");
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
			sub_msg("","","���̃��[���A�h���X�͗��p�ł��܂���","�Z�b�V�������I�����܂�");
		}
			
		//$mlfr="atrac@deep-emotion.com";
		$mlsb="[$db_name �p�X���[�h�₢���킹]";
		
		$body = "���̃��[���̓p�X���[�h�Ĕ��s�V�X�e�����瑗�M����܂����B\n";
		$body = "�p�X���[�h���ύX����܂����B\n";
		$body .= "���񃍃O�C�����͐V�����p�X���[�h�����g�����������B\n\n";
		$body .= "�₢���킹�z�X�g�F$ip\n";
		$body .= "�₢���킹�����F$now_date\n";
	
		mb_language('Japanese');
		mb_internal_encoding("SJIS");
		mb_send_mail($mail_sendfor, $mlsb, $body, "FROM: $mlfr\nContent-Type: text/plain;\n charset=\"iso-2022-jp\"\n");
	}
	
	$sql = "delete from `LOST_PASS` where `id` = '$u_id'";
	$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	
	sub_msg("","","�Z�b�V�������I�����܂�","�p�X���[�h�̍Đݒ肪�I�����܂���");

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
			sub_msg("","","���̃��[���A�h���X�͗��p�ł��܂���","�Z�b�V�������I�����܂�");
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
		$mlsb="[$db_name �p�X���[�h�₢���킹]";
		
		$body = "���̃��[���̓p�X���[�h�Ĕ��s�V�X�e�����瑗�M����܂����B\n";
		$body .= "���̃��[���ɂ��S�����肪�Ȃ��ꍇ�́A$mlfr �܂ŘA���������B\n\n";
		$body .= "���L�̃A�h���X�փA�N�Z�X���Ă��������A�p�X���[�h�̕ύX���s���Ă��������B\n";
		$body .= "���̑���� $now_date ����$end_date �܂łɍs��Ȃ��Ɩ����ɂȂ�܂��B\n\n";
		$body .= $PHP_CUR_PASS."forgot_passwd.php?mode=regist_passwd&name=$name&session_id=$session_id\n\n";
		$body .= "�₢���킹�z�X�g�F$ip\n";
		$body .= "�₢���킹�����F$now_date\n";
		
		mb_language('Japanese');
		mb_internal_encoding("SJIS");
		mb_send_mail($mail, $mlsb, $body, "FROM: $mlfr\nContent-Type: text/plain;\n charset=\"iso-2022-jp\"\n");

	}else{
		sub_msg("","","�Z�b�V�������I�����܂�","���O�������̓��[���A�h���X������������܂���B");
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
            <TD class="color2" height="34" width="200">&nbsp;�p�X���[�h�₢���킹</TD>
            <TD class="color2" align="right" height="34" width="396">
			<?php
			
			if($name){
				echo "�悤�����A$name ����";
			}else{
				echo "�悤�����A�Q�X�g����";
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
				<BR><HR width='420'><A href='javascript:history.back()'>�߂�</A><BR><BR><BR>
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