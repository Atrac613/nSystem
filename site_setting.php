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
	if(!usr_level($uid,9)){
		die("Access Denied");
	}
}

$mode = $_POST["mode"];
if($mode=="page"){

	$sql = "select * from `PHP_PM`";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$page_id = $tmp_rows["page_id"];
		$status = $_POST["page_id_$page_id"];
		
		$sql ="UPDATE `PHP_PM` SET `status` = '$status' WHERE `page_id` = '$page_id'";
		$result2 = $db->query($sql);
		
	}
	
	$sta_msg = "登録しました!!";
}

$sc_size = $_POST["sc_size"];
$fo_size = $_POST["fo_size"];
$limit_day = $_POST["limit_day"];
$max_news = $_POST["max_news"];
$mail_mode = $_POST["mail_mode"];
$mail_master = $_POST["mail_master"];
$broadband = $_POST["broadband"];
$diary_mode = $_POST["diary_mode"];
$site_name = $_POST["site_name"];
$site_theme = $_POST["site_theme"];

$modify = $_POST["modify"];
if($modify){

	$sc_size = intval($sc_size);
	$fo_size = intval($fo_size);
	$limit_day = intval($limit_day);
	$max_news = intval($max_news);
	$mail_mode = intval($mail_mode);
	$broadband = intval($broadband);
	$diary_mode = intval($diary_mode);
	
	if(!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $mail_master)){
		sub_msg("","","そのメールアドレスは登録に利用できません","半角英数字を利用してください");
	}
	
	$sql = "REPLACE INTO `PHP_DEFAULT_STYLE` VALUES ('1', '$site_name' , '$site_theme' ,'$sc_size', '$fo_size', '$limit_day', '$max_news', '$mail_mode','$mail_master','$broadband','$diary_mode')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sta_msg = "登録しました!!";

}


$sql = "select * from `PHP_DEFAULT_STYLE` where `id` ='1'";

$result = $db->query($sql);
if (DB::isError($result)) {
	trigger_error($result->getMessage(), E_USER_ERROR);
}
	
$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
extract($user_rows);

	if($sc_size == 1){
		$sel_sc_1 = "selected";
	}else{
		$sel_sc_0 = "selected";
	}

	if($fo_size == 1){
		$sel_fo_1 = "checked";
	}elseif($fo_size == 2){
		$sel_fo_2 = "checked";
	}else{
		$sel_fo_0 = "checked";
	}
	
	if($mail_mode == 1){
		$sel_mail_mode1 = "selected";
	}
	
	if($broadband == 1){
		$sel_bb1 = "selected";
	}else{
		$sel_bb0 = "selected";
	}
	
	if($diary_mode == 1){
		$sel_dm1 = "selected";
	}elseif($diary_mode == 2){
		$sel_dm2 = "selected";
	}else{
		$sel_dm0 = "selected";
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
            <TD class="color2" height="34" width="200">&nbsp;Root Tool</TD>
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
                  <TD colspan="2" width="422" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
<form method=post enctype='multipart/form-data' action=site_setting.php>
<TABLE width="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD colspan="2"><B>サイト全体</B></TD>
    </TR>
    <TR>
      <TD>サイト名</TD>
      <TD><input type=text name=site_name size=20 value="<?php echo "$site_name"; ?>"></TD>
    </TR>
    <TR>
      <TD colspan="2">&nbsp;</TD>
    </TR>
    <TR>
      <TD colspan="2"><B>デフォルトのサイトスタイル</B></TD>
    </TR>
	

	
    <TR>
      <TD valign="middle" height="36">スクリーンサイズ</TD>
      <TD>
	  <SELECT size="2" name="sc_size">
  		<?php
		echo "<OPTION value='0' $sel_sc_0 >1024*768以上</OPTION>";
  		echo "<OPTION value='1' $sel_sc_1 >1024*768以下</OPTION>";
		?>
	  </SELECT>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">テーマ</TD>
      <TD>
	  <select name='site_theme'>
  		<?php
	$sql = "select * from `PHP_SITE_THEME`";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$id = $tmp_rows["id"];
		$title = $tmp_rows["title"];
		
		if($site_theme == $id){
			echo "<option value='$id' selected>$title</option>";
		}else{
			echo "<option value='$id'>$title</option>";
		}
	}

		?>
	 </select>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">フォントサイズ</TD>
      <TD>
	  <?php
      echo "大<INPUT type='radio' name='fo_size' value='2' $sel_fo_2 >　中<INPUT type='radio' name='fo_size' value='0' $sel_fo_0 >　小<INPUT type='radio' name='fo_size' value='1' $sel_fo_1 >";
	  ?>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">フォントサイズ<BR>サンプル</TD>
      <TD><FONT CLASS="tx18_t">大</FONT>　<FONT CLASS="tx14_t">中</FONT>　<FONT CLASS="tx10">小</FONT><BR><BR></TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">最終宣告日</TD>
      <TD>
	  <?php
      echo "<INPUT type='text' name='limit_day' value='$limit_day' size='2'>";
	  ?>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">インフォメーション<BR>最大表示数</TD>
      <TD>
	  <?php
      echo "<INPUT type='text' name='max_news' value='$max_news' size='2'>";
	  ?>
      </TD>
    </TR>
    <TR>
      <TD colspan="2">&nbsp;</TD>
    </TR>
    <TR>
      <TD colspan="2"><B>デフォルトのメンバーリスト設定</B></TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">日記画像表示</TD>
      <TD>
		<SELECT name=broadband>
			<option value=0 <?php echo "$sel_bb0"; ?>>off</option>
            <option value=1 <?php echo "$sel_bb1"; ?>>on</option>
        </select>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">日記レスモード</TD>
      <TD>
		<SELECT name=diary_mode>
			<option value=0 <?php echo "$sel_dm0"; ?>>シンプル</option>
            <option value=1 <?php echo "$sel_dm1"; ?>>アドバンス</option>
            <option value=2 <?php echo "$sel_dm2"; ?>>フル</option>
        </select>
      </TD>
    </TR>
    <TR>
      <TD colspan="2">&nbsp;</TD>
    </TR>
    <TR>
      <TD colspan="2"><B>メール設定</B></TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">メール配達</TD>
      <TD>
		<SELECT name=mail_mode>
			<option value=0 <?php echo "$sel_mail_mode0"; ?>>利用しない</option>
            <option value=1 <?php echo "$sel_mail_mode1"; ?>>利用する</option>
        </select>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">メール配送先</TD>
      <TD>
	  <?php
      echo "<INPUT type='text' name='mail_master' value='$mail_master' size='30'>";
	  ?>
      </TD>
    </TR>
    <TR>
      <TD><input type=submit value=Modify name=modify></TD>
      <TD></TD>
    </TR>
    <TR>
      <TD colspan="2"></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
<BR>

				</TD></TR><TR><TD colspan="2" width="422" valign="top"></TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				
				
<form method=post enctype='multipart/form-data' action=site_setting.php>
<input type=hidden name=mode value=page>
<TABLE width="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD colspan="2"><B>ページ管理</B></TD>
    </TR>

      

  		<?php
	$sql = "select * from `PHP_PM`";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$page_id = $tmp_rows["page_id"];
		$page_name = $tmp_rows["page_name"];
		$status = $tmp_rows["status"];
	
		echo "<TR><TD width='180'>$page_name</TD>";
	
		echo "<TD><select name='page_id_$page_id'>";
	
	if($status){
		echo "<option value='0'>on</option>";
		echo "<option value='1' selected>off</option>";
	}else{
		echo "<option value='0' selected>on</option>";
		echo "<option value='1'>off</option>";
	}
	

		
		echo "</select></TD><TR>";
	
	}

		?>
    <TR>
      <TD><input type=submit value=Modify></TD>
      <TD></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
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