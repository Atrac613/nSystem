<?php
//ÉâÉCÉuÉâÉäåƒÇ—èoÇµ
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


$id = intval($_POST["id"]);
if($id){
	$sel_id = intval($_POST["sel_id"]);
	$b_name = $_POST["name"];
	$msg = $_POST["msg"];
	$mode = $_POST["mode"];
	$sta = intval($_POST["sta"]);
	
	
	$sql = "select * from `PHP_BESTPHOTO` where `id` ='$id'";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$img = $tmp_rows["img"];
	$date = $tmp_rows["date"];
	
	if(!$mode){
		$sql = "REPLACE INTO `PHP_BESTPHOTO` VALUES ('$id', '$sel_id' , '$b_name' ,'$msg', '$img', '$sta', '$date')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}

		$sta_msg = "ìoò^ÇµÇ‹ÇµÇΩ!!";
		
	}else{
		$sql = "delete from `PHP_BESTPHOTO` where `id` = '$id'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		unlink("bp/img/$img");
		unlink("bp/imgs/$img");
		
		$sta_msg = "çÌèúÇµÇ‹ÇµÇΩ!!";
	}
}

/*
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
	
	$sta_msg = "ìoò^ÇµÇ‹ÇµÇΩ!!";
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

	$sta_msg = "ìoò^ÇµÇ‹ÇµÇΩ!!";

}


$sql = "select * from `PHP_RIGHT_MENU` where `id` ='1'";

$result = $db->query($sql);
if (DB::isError($result)) {
	trigger_error($result->getMessage(), E_USER_ERROR);
}
	
$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
extract($user_rows);
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
				echo "ÇÊÇ§Ç±ÇªÅA$name Ç≥ÇÒ";
			}else{
				echo "ÇÊÇ§Ç±ÇªÅAÉQÉXÉgÇ≥ÇÒ";
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

                </TR>
                <TR>
                  <TD colspan="2" width="570" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
				
<TABLE>
  <TBODY>
    <TR>
      <TD>ïœçX</TD>
      <TD>çÌèú</TD>
      <TD>id</TD>
      <TD>sel_id</TD>
      <TD>name</TD>
      <TD>msg</TD>
      <TD>img</TD>
      <TD>img_sta</TD>
      <TD>sta</TD>
      <TD>date</TD>
    </TR>
				<?php
				
				$sql = "select * from `PHP_BESTPHOTO`";

				$result = $db->query($sql);
				if (DB::isError($result)) {
					trigger_error($result->getMessage(), E_USER_ERROR);
				}
		
				while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
					extract($tmp_rows);
					
					//echo "$id $sel_id $name $msg $img $img_sta $sta $date <BR>";
					if(file_exists("bp/img/$img")){
						$img_sta = "ok";
					}else{
						$img_sta = "ng";
					}
					
					if($sta == "0"){
						$sta0 = "selected";
					}elseif($sta == "1"){
						$sta1 = "selected";
					}else{
						$sta2 = "selected";
					}
					
					$date = gmdate("y/m/d H:i", $date+9*60*60);
					
					echo "<TR><form method=post enctype='multipart/form-data' action=bestphoto_setting.php>
					<input type=hidden name=id value=$id>
	<TD><input type=submit value='ëóêM'></TD>
	<TD><INPUT type='checkbox' name='mode' value='1'></TD>
      <TD>$id</TD>
      <TD><input type=text name=sel_id size=3 value='$sel_id'></TD>
      <TD><input type=text name=name size=5 value='$name'></TD>
      <TD><input type=text name=msg size=10 value='$msg'></TD>
      <TD>$img</TD>
	   <TD>$img_sta</TD>
      <TD><SELECT name=sta><option value=0 $sta0>now</option><option value=1 $sta1>old</option><option value=2 $sta2>que</option></SELECT></TD>
      <TD>$date</TD></form>
    </TR>";
		
				}
				
				
				?>
				
  </TBODY>
</TABLE>



				</TD></TR><TR><TD colspan="2" width="570" valign="top"></TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				
				
				

				<BR>
				<BR><HR width='420'><A href='javascript:history.back()'>ñﬂÇÈ</A><BR><BR><BR>
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