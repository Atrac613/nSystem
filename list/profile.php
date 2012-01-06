<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "memberlist_inc.php";
require_once "ml_common.php";
require_once "profile_inc.php";

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

$user_name = $_POST["name"];
if(!$user_name){
	$user_name = $_GET["name"];
}

$sql = "select * from `USER_DATA` WHERE `name` = '$user_name'";
$result = $db->query($sql);
$chk = $result->numRows();
if($chk){
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$user_uid = $user_rows["uid"];
}else{
	sub_msg("","","エラー","ユーザーは未登録です");
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
                  <BR><?php echo $user_name; ?>さんのプロフィール</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
					<?php
					
					//モグネットアドレス
					$send = $user_name."@".$mog_net_group.".".$mog_net_domain;
					$send = strtolower($send);

					$sql = "select * from `USER_PLOF`,`USER_STA` WHERE USER_STA.uid = '$user_uid' AND USER_PLOF.uid = '$user_uid'";
					$result = $db->query($sql);
					if (DB::isError($result)) {
						trigger_error($result->getMessage(), E_USER_ERROR);
					}
					$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
					$user_rows = convert_to_sjis($user_rows);
					$user_prof = $user_rows["prof_mode"];
					
					if($user_prof == 1 ){
						$user_handle = $user_rows["handle"];
						$user_polhn = $user_rows["polhn"];
						$user_mail = $user_rows["mail"];
						$user_home = $user_rows["url"];
						$user_comment = $user_rows["comment"];
						$user_comment2 = $user_rows["comment_plof"];
						$user_prof_img = $user_rows["prof_img"];
						$pic_alt = "$user_name さん &gt; $user_comment";
						$user_comment2 = str_replace("\n", "<br>", $user_comment2);
					echo "<TR><TH colspan=3 class=\"table_title\">$user_name さんのプロフィール</TH></TR>";
						echo "<TR>";
	
						if($user_prof_img){
							$pos = strrpos($user_prof_img,".");
							$sam_img_name = substr($user_prof_img,0,$pos);
	
							if($image_type == "0"){
								$sam_img_name = $sam_img_name.".png";
							}else{
								$sam_img_name = $sam_img_name.".jpg";
							}
	
							if (file_exists($sam_dir.$sam_img_name)){
								$prof_img  = "<a href='$putdir$user_prof_img' target='_blank'>\n";
								$prof_img .= "<img src='$sam_dir$sam_img_name' height=150 width=200 border=0 alt=".'"'.$pic_alt.'"'."></a>\n";
							}else{
								$prof_img  = "<a href='$putdir$user_prof_img' target='_blank'>\n";
								$prof_img  .= "<img src='$putdir$user_prof_img' height=150 width=200 border=0 alt=".'"'.$pic_alt.'"'."></a>\n";
							}
							echo "<TD rowspan=6 width=200>$prof_img</TD>";
						}
	
						echo "<TD width=18% class=row2>ハンドル</TD><TD class=row0>$user_handle</TD></TR>\n";
						echo "<TR><TD width=18% class=row2>POLハンドル</TD><TD class=row0>$user_polhn</TD></TR>\n";
						echo "<TR><TD width=18% class=row2>e-mail</TD><TD class=row0><a href='mailto:$user_mail'>$user_mail</a></TD></TR>\n";
						echo "<TR><TD width=18% class=row2>モグネット</TD><TD class=row0>$send</TD></TR>\n";
						echo "<TR><TD width=18% class=row2>ホームページ</TD><TD class=row0><a href='$user_home' target='_blank'>$user_home</a></TD></TR>\n";
						echo "<TR><TD width=18% class=row2>コメント</TD><TD class=row0>$user_comment2</TD></tr></TBODY></TABLE>\n";

						chk_bazaar2($user_uid,$user_name);
						show_diary($user_uid,$user_name);

					} else {
						echo "<TR class=\"table_title\"><TH>$user_name さんのプロフィールは非公開となっています。</TH></TR></TBODY></TABLE>";
					}
					
					
					
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