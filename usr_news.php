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

if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,5)){
		die("Access Denied");
	}
}

$f_mode = $_POST["f_mode"];
if($f_mode =="submit" && $uid){
	$n_title = $_POST["n_title"];
	$n_body = $_POST["n_body"];
	$i_name = $_POST["i_name"];
	$i_body = $_POST["i_body"];
	$date =time();
	
	if($n_title && $n_body){
		$n_body = str_replace("\r\n", "\r", $n_body);
		$n_body = str_replace("\r", "\n", $n_body);
		$n_title = htmlspecialchars($n_title);
		$n_body = htmlspecialchars($n_body);
	
		$sql = "REPLACE INTO `PHP_I-N` VALUES ('', '1', '$n_title', '$n_body', '0', '$date')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$sta_msg = "登録しました!!";
	}elseif($i_name && $i_body){
		$i_body = str_replace("\r\n", "\r", $i_body);
		$i_body = str_replace("\r", "\n", $i_body);
		$i_name = htmlspecialchars($i_name);
		$i_body = htmlspecialchars($i_body);
	
		$sql = "REPLACE INTO `PHP_I-N` VALUES ('', '0', '$i_name', '$i_body', '0', '$date')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$sta_msg = "登録しました!!";
	}else{
		$sta_msg = "登録失敗...";
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
            <TD class="color2" height="34" width="200">&nbsp;ニュース</TD>
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
                  <TD colspan="2" width="422"></TD>
                  <TD rowspan="5" align="right" width="148" valign="top">
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR>ニュース<BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                  <TABLE>
  <TBODY>
    <TR>
      <TD valign="middle" height="12"></TD>
      <TD>
      <FORM method="post" enctype="multipart/form-data" action="usr_news.php">
	  タイトル<BR>
      <INPUT size=50 type=text name=n_title>
	  <BR>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="12"></TD>
      <TD>ニュース<BR>
	  <TEXTAREA rows="11" cols="56" name="n_body"></TEXTAREA>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="12"></TD>
      <TD><INPUT type="submit" name="f_mode" value="submit"><BR><BR></TD>
    </TR></FORM>
  </TBODY>
</TABLE>
                </TD></TR><TR><TD colspan="2" width="422" valign="top">Info</TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                  <TABLE>
  <TBODY>
    <TR>
      <TD valign="middle" height="12"></TD>
      <TD>
      <FORM method="post" enctype="multipart/form-data" action="usr_news.php">
	  名前<BR>
      <INPUT size=50 type=text name=i_name value="<?php echo "$c_name" ?>">
	  <BR>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="12"></TD>
      <TD>内容<BR>
	  <TEXTAREA rows="11" cols="56" name="i_body"></TEXTAREA>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="12"></TD>
      <TD><INPUT type="submit" name="f_mode" value="submit"><BR><BR></TD>
    </TR></FORM>
  </TBODY>
</TABLE>
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				*「submit」を押すと送信され、上部に「登録しました!! 」と表示されるまでお待ちください。表示された場合は登録完了です。<BR>
				<FORM method="post" enctype="multipart/form-data" action="usr_news_mod.php">
				<INPUT type="submit" name="push" value="管理"><BR><BR>
				<BR><HR width='420'><A href='javascript:history.back()'>戻る</A><BR><BR><BR>
				</TD>
				</TR></FORM>
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