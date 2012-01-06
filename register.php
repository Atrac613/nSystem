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


class register {

	function getTimeZoneList(){
		include_once './language/timezone.php';
		$time_zone_list = array ("-12" => _TZ_GMTM12, "-11" => _TZ_GMTM11, "-10" => _TZ_GMTM10, "-9" => _TZ_GMTM9, "-8" => _TZ_GMTM8, "-7" => _TZ_GMTM7, "-6" => _TZ_GMTM6, "-5" => _TZ_GMTM5, "-4" => _TZ_GMTM4, "-3.5" => _TZ_GMTM35, "-3" => _TZ_GMTM3, "-2" => _TZ_GMTM2, "-1" => _TZ_GMTM1, "0" => _TZ_GMT0, "1" => _TZ_GMTP1, "2" => _TZ_GMTP2, "3" => _TZ_GMTP3, "3.5" => _TZ_GMTP35, "4" => _TZ_GMTP4, "4.5" => _TZ_GMTP45, "5" => _TZ_GMTP5, "5.5" => _TZ_GMTP55, "6" => _TZ_GMTP6, "7" => _TZ_GMTP7, "8" => _TZ_GMTP8, "9" => _TZ_GMTP9, "9.5" => _TZ_GMTP95, "10" => _TZ_GMTP10, "11" => _TZ_GMTP11, "12" => _TZ_GMTP12);
		return $time_zone_list;
	}

}

//var_dump(count(register::getTimeZoneList()));
//$timezone = register::getTimeZoneList();
//var_dump($timezone);
//die();

$regist = $_POST["regist"];
if($regist){

	$sql = "select * from `USER_DATA`";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$sta = $result->numRows();
	
	if($sta >= 127){
		sub_msg("","","エラー","登録人数オーバーのため現在は登録できません。");
	}

	$name = $_POST["name"];
	$pass = $_POST["pass"];
	$vpass = $_POST["vpass"];
	$agree_disc = $_POST["agree_disc"];
	$mail = $_POST["mail"];
	$id = $_POST["id"];
	
	$f_reg_pass = $_POST["reg_pass"];
	
	$reg_mode = $env_rows["reg_mode"];
	$reg_pass = $env_rows["reg_pass"];
	
	if($reg_mode == 2){
		if($reg_pass != $f_reg_pass){
			sub_msg("","","エラー","登録許可パスワードが一致しません。");
		}
	}
	
	//test
	//$salt = salt();
	//$name = $salt;
	//"/^[＼w＼-＼.]+@[＼w＼-]+(＼.[＼w＼-]+)+$/"
	///^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is
	//var_dump($mail);
	//var_dump(preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $mail));
	if(!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $mail)){
		sub_msg("","","そのメールアドレスは登録に利用できません","半角英数字を利用してください");
	}
	
	$sql = "select * from `PHP_PRE_REG` where `id` = '$id' and `mail` = '$mail'";
	//var_dump($sql);
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$mail_status = $rows["status"];
	
//var_dump($mail_status);
	
	if(!$mail_status){
		sub_msg("","","そのメールアドレスは登録に利用できません","認証に失敗しました");
	}
	
	$sql = "delete from `PHP_PRE_REG` where `id` = '$id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	if(!isAlphaOrNum($pass)){
		sub_msg("","","そのパスワードは登録に利用できません","半角英数字を利用してください");
	}
	
	if(!isAlphaOrNumName($name)){
		sub_msg("","","その名前は登録に利用できません","半角英字を利用してください");
	}
	
	if($pass != $vpass){
		sub_msg("","","エラー","パスワードが一致しません。");
	}
	
	if(!$agree_disc){
		sub_msg("","","エラー","残念ですが、免責事項に同意できない場合は登録できません。");
	}

	$sql = "select `name` from `USER_DATA` where `name` = '$name'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	
	if($members){
		sub_msg("","","エラー","すでにその名前は登録済みです。");
	}
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = time();
	
	//大文字に
	$name = ucfirst($name);
	
	//設定
	$class = "新入生";
	$comment = "";
	$supportjob = 99;
	
	//make uid
	//$uid_md5 = md5(time());
	//$uid = substr($uid_md5, 0, 15);
	
    mt_srand(microtime()*100000);
	$uid = md5(uniqid(mt_rand(),1));
	$uid = substr($uid, 0, 15);
		
	$sql = "INSERT INTO USER_DATA VALUES ('$uid', '', '$name', password('$pass') )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "replace INTO USER_STA VALUES ('$uid', '$class', '$anon', '$race', '$face', '$size', '$relm', '$mainjob', '$supportjob', '$point', '$mrank', '$comment')";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	
	$sql = "replace INTO USER_PLOF VALUES ('$uid','$prof', '$handle', '$polhn', '$mail', '$home', '$comment2', '$prof_img' )";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO USER_LEV VALUES ('$uid', '$lev0', '$lev1', '$lev2', '$lev3', '$lev4', '$lev5', '$lev6', '$lev7', '$lev8', '$lev9', '$lev10', '$lev11', '$lev12', '$lev13', '$lev14', '$lev15', '$lev16', '$lev17')";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO USER_PROD VALUES ('$uid', '$prod0', '$prod1', '$prod2', '$prod3', '$prod4', '$prod5', '$prod6','$prod7', '$prod8', '$prod9', '$prod10', '$prod11', '$prod12', '$prod13', '$prod14', '$prod15', '$prod16' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO USER_SKL VALUES ('$uid', '$skl0', '$skl1', '$skl2', '$skl3', '$skl4', '$skl5', '$skl6', '$skl7', '$skl8', '$skl9', '$skl10', '$skl11', '$skl12', '$skl13', '$skl14', '$skl15', '$skl16', '$skl17', '$skl18', '$skl19', '$skl20', '$skl21', '$skl22', '$skl23', '$skl24', '$skl25', '$skl26', '$skl27', '$skl28', '$skl29', '$skl30', '$skl31', '$skl32' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO USER_IP VALUES ('$uid', '$ip', '$date' )";

	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	//LOGGING
	$sql = "select `no` from `USER_DATA` where `uid` = '$uid'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$u_no = $user_rows["no"];
	
	$sql = "INSERT INTO LOG_USER_DATA VALUES ('$max_id', '$uid', '$u_no', '$name', password('$pass') )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	//get new id
	$sql = "select `uid`,max(`id`) from `LOG_USER_DATA` where `uid` = '$uid' group by `uid`;";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$max_id = $user_rows["max(`id`)"];
	
	$sql = "INSERT INTO LOG_USER_STA VALUES ('$max_id', '$uid', '$class', '$anon', '$race', '$face', '$size', '$relm', '$mainjob', '$supportjob', '$point', '$mrank', '$comment' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_PLOF VALUES ('$max_id', '$uid','$prof', '$handle', '$polhn', '$mail', '$home', '$comment2', '$prof_img' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_LEV VALUES ('$max_id', '$uid','$lev0', '$lev1', '$lev2', '$lev3', '$lev4', '$lev5', '$lev6', '$lev7', '$lev8', '$lev9', '$lev10', '$lev11', '$lev12', '$lev13', '$lev14', '$lev15', '$lev16' , '$lev17' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_PROD VALUES ('$max_id', '$uid','$prod0', '$prod1', '$prod2', '$prod3', '$prod4', '$prod5', '$prod6','$prod7', '$prod8', '$prod9', '$prod10', '$prod11', '$prod12', '$prod13', '$prod14', '$prod15', '$prod16' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_SKL VALUES ('$max_id', '$uid','$skl0', '$skl1', '$skl2', '$skl3', '$skl4', '$skl5', '$skl6', '$skl7', '$skl8', '$skl9', '$skl10', '$skl11', '$skl12', '$skl13', '$skl14', '$skl15', '$skl16', '$skl17', '$skl18', '$skl19', '$skl20', '$skl21', '$skl22', '$skl23', '$skl24', '$skl25', '$skl26', '$skl27', '$skl28', '$skl29', '$skl30', '$skl31', '$skl32' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_IP VALUES ('$max_id', '$uid','$ip', '$date' )";

	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "select * from `USER_DATA`";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$sta = $result->numRows();
	
	if($sta==1){
		$sql = "REPLACE INTO `PHP_USR_LEVEL` VALUES ('$uid', '1', '1', '1', '1', '1', '1', '1', '1','1')";
		$msg="<BR><B>あなたはrootモードで登録しました。<br>rootユーザーの管理にはご注意ください。</B>";
	}else{
		$sql = "REPLACE INTO `PHP_USR_LEVEL` VALUES ('$uid', '0', '1', '1', '1', '1', '1', '1', '0','0')";
 	}
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "REPLACE INTO `PHP_USR_STYLE` VALUES ('$uid', '1','0', '0', '1', '40', '1','1','1','$mail','1','1')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$uid = "";
	sub_msg("15","index_pc.php","登録成功","自動的にトップページに戻ります。<BR><BR>トップページに戻ったら左のログインフォームからログイン処理を行ってください。<BR>$msg");
}

$mail = $_GET["mail"];
$id = $_GET["id"];
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
				<form method=post enctype='multipart/form-data' action=register.php>
				<INPUT type=hidden name=id value="<?php echo $id; ?>">
<TABLE width="100%">
  <TBODY>
    <TR>
      <TD>名前</TD>
      <TD><INPUT size=20 type=text name=name></TD>
    </TR>
    <TR>
      <TD>メールアドレス</TD>
      <TD><INPUT size=40 type=text name=mail value=<?php echo $mail; ?>></TD>
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
      <TD>パスワード</TD>
      <TD><INPUT size=20 type=password name=pass></TD>
    </TR>
    <TR>
      <TD>パスワード(確認)</TD>
      <TD><INPUT size=20 type=password name=vpass></TD>
    </TR>
    <TR>
      <TD>免責</TD>
      <TD><TEXTAREA name="disclaimer" rows="8" cols="60" readonly="readonly">
本規約は、当サイトにより提供されるコンテンツの利用条件を定めるものです。以下の利用条件をよくお読みになり、これに同意される場合にのみご登録いただきますようお願いいたします。

当サイトを利用するにあたり、以下に該当する又はその恐れのある行為を行ってはならないものとします。 

・公序良俗に反する行為 
・法令に違反する行為 
・犯罪行為及び犯罪行為に結びつく行為 
・他の利用者、第三者、当サイトの権利を侵害する行為 
・他の利用者、第三者、当サイトを誹謗、中傷する行為及び名誉・信用を傷つける行為 
・他の利用者、第三者、当サイトに不利益を与える行為 
・当サイトの運営を妨害する行為 
・事実でない情報を発信する行為 
・プライバシー侵害の恐れのある個人情報の投稿 
・その他、当サイトが不適当と判断する行為 

【免責】

利用者が当サイト及び当サイトに関連するコンテンツ、リンク先サイトにおける一切のサービス等をご利用されたことに起因または関連して生じた一切の損害（間接的であると直接的であるとを問わない）について、当サイトは責任を負いません。 
	  </TEXTAREA><br><input type='checkbox' name='agree_disc' value='1' />私は上記事項に同意します。 </TD>
    </TR>
	<?php
	if($env_rows["reg_mode"]==2){
	echo "
    <TR>
      <TD><BR></TD>
      <TD></TD>
    </TR>
    <TR>
      <TD>登録許可パスワード</TD>
      <TD><INPUT size=20 type=password name=reg_pass></TD>
    </TR>
	";
	}
	
	?>
    <TR>
      <TD></TD>
      <TD><input type=submit value=登録 name=regist></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
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