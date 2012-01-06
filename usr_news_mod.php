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

$fmo = $_POST["fmo"];
$fmd = $_POST["fmd"];
if($fmo || $fmd && $uid){
	$fid = $_POST["fid"];
	$fid = intval($fid);
	
	if($fmd && $fid){
		$sql = "delete from `PHP_I-N` where `id` = '$fid'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$sta_msg = "削除しました!!";
	}elseif($fmo && $fid){
		$sql = "select * from `PHP_I-N` where `id` = '$fid'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$chk = $result->numRows();
		if($chk){
			$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			$id = $user_rows["id"];
			$i_n = $user_rows["i-n"];
			$title = $user_rows["title"];
			$body = $user_rows["body"];
			$date = $user_rows["date"];
			$title = addslashes($title);
			$body = addslashes($body);
			
			$sql = "REPLACE INTO `PHP_I-N` VALUES ('$id', '$i_n', '$title', '$body', '1', '$date')";
			$result = $db->query($sql);
			if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
			}
		
			$sta_msg = "編集しました!!";
		}else{
			$sta_msg = "編集失敗...";
		}
	}else{
		$sta_msg = "編集失敗...";
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
  				  <?php
					$sql = "select * from `PHP_I-N` where `i-n` = '1' order by `date` desc";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$fid = $user_rows["id"];
							$title = $user_rows["title"];
							$body = $user_rows["body"];
							$sta = $user_rows["sta"];
							$date = $user_rows["date"];
							$date = gmdate("y/m/d D H:i:s", $date+9*60*60);
						
							echo "<TR>
	<FORM method='post' enctype='multipart/form-data' action='usr_news_mod.php'>
	<input type=hidden name='fid' value='$fid'>
      <TD valign='middle' height='12'><INPUT type='submit' name='fmd' value='削除'>&nbsp;
	  ";
	  if(!$sta){
	  	echo "<INPUT type='submit' name='fmo' value='過去へ'>";
	  }
	  echo "
		</TD>
      <TD>
	  &nbsp;&nbsp;$title ($date)
      </TD>
    </TR></FORM>";
						}
					}else{
						echo "<TR><TD>・Newsはありません。</TD></TR>";
					}
				  
				  ?>

  </TBODY>
</TABLE>
                </TD></TR><TR><TD colspan="2" width="422" valign="top"><BR>Info</TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                  <TABLE>
  <TBODY>
  				  <?php
					$sql = "select * from `PHP_I-N` where `i-n` = '0' and `sta` = '0' order by `date` desc";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$fid = $user_rows["id"];
							$i_name = $user_rows["title"];
							$body = $user_rows["body"];
							$sta = $user_rows["sta"];
							$date = $user_rows["date"];
							$date = gmdate("y/m/d D H:i:s", $date+9*60*60);
							$body = str_replace("\n", "<br>&nbsp;&nbsp;", $body);
						
							echo "<TR>
	<FORM method='post' enctype='multipart/form-data' action='usr_news_mod.php'>
	<input type=hidden name='fid' value='$fid'>
      <TD valign='top' height='12'><INPUT type='submit' name='fmo' value='削除'>&nbsp;
		</TD>
      <TD>
	  &nbsp;&nbsp;$body <BR>&nbsp;&nbsp;$i_name さん($date)<BR><BR>
      </TD>
    </TR></FORM>";
						}
					}else{
						echo "<TR><TD>・infoはありません。</TD></TR>";
					}
				  
				  ?>
  </TBODY>
</TABLE>
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				*「削除」を押すと削除されます。削除されたデータは復旧できません。<BR>*「過去へ」を押すと過去ログに入ります。<BR>
				<FORM method="post" enctype="multipart/form-data" action="usr_news_mod.php">
				<INPUT type="submit" name="push" value="リロード"><BR><BR>
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