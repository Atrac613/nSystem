<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "ml_common.php";
require_once "diary_inc.php";
require_once "memberlist_inc.php";
require_once "../function/graphic_lib.php";

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

$STYLE = load_style(3,1);


//setup
$env_rows = load_env();

$mode = $_POST["mode"];
if(!$mode){
	$mode = $_GET["mode"];
}

if($mode == "write_diary"){
	if(!$uid){
		die("Authorization Required");
	}else{
		if(!usr_level($uid,4)){
			die("Access Denied");
		}
	}
	write_diary();
}elseif($mode == "res_diary"){
	res_diary();
}elseif($mode == "modify" || $mode == "del"){
	if(!$uid){
		die("Authorization Required");
	}else{
		if(!usr_level($uid,4)){
			die("Access Denied");
		}
	}
	modify_diary();
}elseif($_POST["modify_mode"]){
	if(!$uid){
		die("Authorization Required");
	}else{
		if(!usr_level($uid,4)){
			die("Access Denied");
		}
	}
	my_res_edit();
}else{
	if($mode == "adv_res"){
		$diary_mode ="返信";
	}elseif($mode == "edit_res"){
		$diary_mode ="レスを修正します";
		//データチェック
		
		$sid = $_POST["sid"];
		if(!$sid){
			$sid = $_GET["sid"];
		}
		
		$sql = "select * from `USER_DIARY_RES` where `sid` = '$sid'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$chk = $result->numRows();
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$diary_name = $user_rows["name"];

		if(!$sid || !$chk){
			sub_msg("","","レスはありません","正しいレスをお使いください");
		}
		
	}elseif($mode == "edit_diary"){
		$diary_mode ="日記を修正します";
	}else{
		$diary_mode ="日記を書き込みます";
	}
}

function diary_form(){
	global $db,$uid;
	
	$mode = $_POST["mode"];
	if(!$mode){
		$mode = $_GET["mode"];
	}
	
	if($mode == "adv_res"){
		adv_res();
	}elseif($mode == "edit_diary"){
		if(!$uid){
			die("Authorization Required");
		}else{
			if(!usr_level($uid,4)){
				die("Access Denied");
			}
		}
		modify_diary();
	}elseif($mode == "edit_res"){
		if(!$uid){
			die("Authorization Required");
		}else{
			if(!usr_level($uid,4)){
				die("Access Denied");
			}
		}
		my_res_edit();
	}else{
		//write_forum
		if(!$uid){
			die("Authorization Required");
		}else{
			if(!usr_level($uid,4)){
				die("Access Denied");
			}
		}
		write_form();
		//sub_msg("","","エラー","モード指定なし");
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
                  <BR>日記 -<?php echo $diary_mode; ?>-</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
					<?php
					
					diary_form();
					
					
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