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
	if(!usr_level($uid,1)){
		die("Access Denied");
	}
}

$sc_size = $_POST["sc_size"];
$fo_size = $_POST["fo_size"];
$limit_day = $_POST["limit_day"];
$max_news = $_POST["max_news"];
$mail_news = $_POST["mail_news"];
$mail_forum = $_POST["mail_forum"];
$mail_list = $_POST["mail_list"];
$mail_sendfor = $_POST["mail_sendfor"];
$broadband = $_POST["broadband"];
$diary_mode = $_POST["diary_mode"];
$site_theme = $_POST["site_theme"];
//var_dump($_POST);
$f_mode = $_POST["f_mode"];
if($f_mode =="submit" && $uid){

	$sc_size = intval($sc_size);
	$fo_size = intval($fo_size);
	$limit_day = intval($limit_day);
	$max_news = intval($max_news);
	$mail_news = intval($mail_news);
	$mail_forum = intval($mail_forum);
	$mail_list = intval($mail_list);
	$broadband = intval($broadband);
	$diary_mode = intval($diary_mode);
	$site_theme = intval($site_theme);
	
	if(!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $mail_sendfor)){
		sub_msg("","","そのメールアドレスは登録に利用できません","半角英数字を利用してください");
	}
	
	$sql = "REPLACE INTO `PHP_USR_STYLE` VALUES ('$uid','$site_theme', '$sc_size', '$fo_size', '$limit_day', '$max_news', '$mail_news','$mail_forum','$mail_list','$mail_sendfor','$broadband','$diary_mode')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sta_msg = "登録しました!!";

}

if($uid){
	$sql = "select * from `PHP_USR_STYLE` where `uid` = '$uid'";
	$result = $db->query($sql);
	$chk = $result->numRows();
	if($chk){
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$f_sc_size = $user_rows["sc_size"];
		$f_fo_size = $user_rows["fo_size"];
		$limit_day = $user_rows["limit_day"];
		$max_news = $user_rows["max_news"];
		$mail_news = $user_rows["mail_news"];
		$mail_forum = $user_rows["mail_forum"];
		$mail_list = $user_rows["mail_list"];
		$mail_sendfor = $user_rows["mail_sendfor"];
		$broadband = $user_rows["broadband"];
		$diary_mode = $user_rows["diary_mode"];
		$site_theme = $user_rows["site_theme"];
	}
	
	if($f_sc_size == 1){
		$sel_sc_1 = "selected";
	}else{
		$sel_sc_0 = "selected";
	}

	if($f_fo_size == 1){
		$sel_fo_1 = "checked";
	}elseif($f_fo_size == 2){
		$sel_fo_2 = "checked";
	}else{
		$sel_fo_0 = "checked";
	}
	
	if($mail_news == 1){
		$sel_mail_news = "checked";
	}
	
	if($mail_forum == 1){
		$sel_mail_forum = "checked";
	}
	
	if($mail_list == 1){
		$sel_mail_list1 = "selected";
	}elseif($mail_list == 2){
		$sel_mail_list2 = "selected";
	}elseif($mail_list == 3){
		$sel_mail_list3 = "selected";
	}else{
		$sel_mail_list0 = "selected";
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
            <TD class="color2" height="34" width="200">&nbsp;SETUP</TD>
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
                  <TD colspan="2" width="422" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR>スタイル</TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                  <TABLE>
  <TBODY>
    <TR>
      <TD valign="middle" height="36">テーマ</TD>
      <TD>
      <FORM method="post" enctype="multipart/form-data" action="usr_style.php">
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
      <TD valign="middle" height="36">メール配達</TD>
      <TD>
	  <?php
      echo "<INPUT type='checkbox' name='mail_news' value='1' $sel_mail_news>news <INPUT type='checkbox' name='mail_forum' value='1' $sel_mail_forum>forum";
	  ?>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">メール配達<BR>メンバーリスト</TD>
      <TD>
		<SELECT name=mail_list>
			<option value=0 <?php echo "$sel_mail_list0"; ?>>利用しない</option>
            <option value=1 <?php echo "$sel_mail_list1"; ?>>フル</option>
            <option value=2 <?php echo "$sel_mail_list2"; ?>>バザー</option>
			<option value=3 <?php echo "$sel_mail_list3"; ?>>日記へのレス</option>
        </select>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">メール配送先</TD>
      <TD>
	  <?php
      echo "<INPUT type='text' name='mail_sendfor' value='$mail_sendfor' size='30'>";
	  ?>
      </TD>
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
      <TD>送信</TD>
      <TD>
      <INPUT type="submit" name="f_mode" value="submit">
      </TD>
    </TR></FORM>
  </TBODY>
</TABLE>
                <BR></TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				*「submit」を押すと送信され、上部に「登録しました!! 」と表示されるまでお待ちください。表示された場合は登録完了です。<BR>
				*スクリーンサイズを「1024*768以下」にすると上部のイメージが表示されなくなります。<BR>
				<BR><HR width='420'><A href='javascript:history.back()'>戻る</A>
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