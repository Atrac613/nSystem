<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "log_table_inc.php";
require_once "viewlog_inc.php";
//require_once "memberlist_inc.php";
require_once "ml_common.php";
$ML_SCRIPT = "viewlog.php";
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

//setup
  $sql = "select * from `MEMBER_LIST_ENV`";
  $result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$env_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);

$sort =intval($_GET["sort"]);
if(!$sort){
	$sort =intval($_POST["sort"]);
}
$view =intval($_GET["view"]);
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
				  <?php
	$p_limit = $_POST["p_limit"];
	if(!$p_limit){
		$p_limit = $_GET["p_limit"];
	}
	
	if(!$p_limit){
		$p_limit = "30";
	}
	
	if($p_limit == 100){
		$selected3 = "selected";
	}elseif($p_limit == 50){
		$selected2 = "selected";
	}else{
		$selected1 = "selected";
	}
	
	$p_mode = $_POST["p_mode"];
	
	$p_offset = $_POST["p_offset"];
	if(!$p_offset){
		$p_offset = $_GET["p_offset"];
	}
	if(!$p_offset){
		$p_offset = "0";
	}

	if($p_mode == " prev "){
		if($p_offset >= 0){
			$p_offset = $p_offset - $p_limit;
		}else{
			$p_offset = "0";
		}
	}elseif($p_mode == " next "){
		if($p_offset >= 0){
			$p_offset = $p_offset + $p_limit;
		}else{
			$p_offset = "0";
		}
	}else{
	
	}
	
	if($p_offset < 0){
		$p_offset = "0";
	}
	
	$n_offset = $p_offset + $p_limit;
				  
	  echo "<form method=post enctype='multipart/form-data' action=viewlog.php><BR>
	  <input type=hidden name=view value=$view><input type=hidden name=sort value=$sort><input type=hidden name=p_offset value=$p_offset>";
	  
	  echo "<select name=p_limit><option value=30 $selected1>30</option><option value=50 $selected2>50</option><option value=100 $selected3>100</option></select>";
	  echo "<input type=submit name=p_mode value=' prev '>";
	  echo "<input type=submit name=p_mode value=' next '>";
	  echo "  $p_offset - $n_offset max=$p_limit</form>";

				  ?>
                  表示モード >> <?php echo"<A href='viewlog.php?view=0&sort=$sort'>$MENU[0]</A> 
				  <A href='viewlog.php?view=1&sort=$sort'>$MENU[1]</A>
				  <A href='viewlog.php?view=2&sort=$sort'>$MENU[2]</A>
				  <A href='viewlog.php?view=3&sort=$sort'>$MENU[3]</A>
				  <A href='viewlog.php?view=4&sort=$sort'>$MENU[4]</A>
				  <A href='viewlog.php?view=5&sort=$sort'>$MENU[5]</A>
				  <A href='viewlog.php?view=6&sort=$sort'>$MENU[6]</A>
				  <A href='viewlog.php?view=7&sort=$sort'>$MENU[7]</A>"; 
				  ?></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
					<?php
					table_chk();
					?>
				<BR><HR><A href='javascript:history.back()'>戻る</A><BR>
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