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

	$sql = "select * from `PHP_RIGHT_MENU_PAGE`";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$page_id = $tmp_rows["page_id"];
		$status = $_POST["page_id_$page_id"];
		
		$sql ="UPDATE `PHP_RIGHT_MENU_PAGE` SET `status` = '$status' WHERE `page_id` = '$page_id'";
		$result2 = $db->query($sql);
		
	}
	
	$sta_msg = "登録しました!!";
}


$modify = $_POST["modify"];
if($modify){

	$area0 = intval($_POST["area0"]);
	$area1 = intval($_POST["area1"]);
	$area2 = intval($_POST["area2"]);
	$areaX = intval($_POST["areaX"]);
	$html = $_POST["html"];

	$sql = "REPLACE INTO `PHP_RIGHT_MENU` VALUES ('1', '$area0' , '$area1' ,'$area2', '$areaX', '$html')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sta_msg = "登録しました!!";

}


$sql = "select * from `PHP_RIGHT_MENU` where `id` ='1'";

$result = $db->query($sql);
if (DB::isError($result)) {
	trigger_error($result->getMessage(), E_USER_ERROR);
}
	
$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
extract($user_rows);


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
<form method=post enctype='multipart/form-data' action=right_menu.php>
<TABLE width="100%" cellpadding="0" cellspacing="0">
  <TBODY>

    <TR>
      <TD colspan="2"><B>右メニューの設定</B></TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">ベストショット</TD>
      <TD>
		<SELECT name="area0">
			<option value=0 >off</option>
            <option value=1 <?php if($area0){ echo "selected"; } ?>>on</option>
        </select>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">Vana'diel Wind</TD>
      <TD>
		<SELECT name="area1">
			<option value=0 >off</option>
            <option value=1 <?php if($area1){ echo "selected"; } ?>>on</option>
        </select>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">投票</TD>
      <TD>
		<SELECT name="area2">
			<option value=0 >off</option>
            <option value=1 <?php if($area2){ echo "selected"; } ?>>on</option>
        </select>
      </TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">指定</TD>
      <TD>
		<SELECT name="areaX">
			<option value=0 >off</option>
            <option value=1 <?php if($areaX){ echo "selected"; } ?>>on</option>
        </select>
      </TD>
    </TR>
    <TR>
      <TD colspan="2">&nbsp;</TD>
    </TR>
    <TR>
      <TD colspan="2"><B>指定の場合</B></TD>
    </TR>
    <TR>
      <TD valign="middle" height="36">html</TD>
      <TD>
		<TEXTAREA name="html" rows=5 cols=40><?php echo "$html"; ?></TEXTAREA>
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
				
				
<form method=post enctype='multipart/form-data' action=right_menu.php>
<input type=hidden name=mode value=page>
<TABLE width="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD colspan="2"><B>ページ管理</B></TD>
    </TR>

      

  		<?php
	$sql = "select * from `PHP_RIGHT_MENU_PAGE`";

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