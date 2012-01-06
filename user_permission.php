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

$selected = $_POST["selected"];
if($selected){
    //var_dump($t);
	$sql = "select * from `USER_DATA`";
	$result = $db->query($sql);
	$chk = $result->numRows();
		if($chk){
			while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
			$f_usr_name = $user_rows["name"];
			$f_uid = $user_rows["uid"];
			
			$tmp_root = $_POST["selected_root_$f_usr_name"];
			$tmp_site = $_POST["selected_site_$f_usr_name"];
            $tmp_link = $_POST["selected_link_$f_usr_name"];
			$tmp_news = $_POST["selected_news_$f_usr_name"];
			$tmp_guild = $_POST["selected_guild_$f_usr_name"];
			$tmp_poll = $_POST["selected_poll_$f_usr_name"];
			$tmp_album = $_POST["selected_album_$f_usr_name"];
			$tmp_forum = $_POST["selected_forum_$f_usr_name"];
			$tmp_content = $_POST["selected_content_$f_usr_name"];
			
			$sql = "REPLACE INTO `PHP_USR_LEVEL` VALUES ('$f_uid', '$tmp_root', '$tmp_site', '$tmp_link', '$tmp_news', '$tmp_guild', '$tmp_poll', '$tmp_album', '$tmp_forum' , '$tmp_content')";
			
			if(find_oracle($f_uid)){
				if($uid == $f_uid){
					$result_new = $db->query($sql);
				}
			}else{
				$result_new = $db->query($sql);
			}
            
			//var_dump($sql);
            if (DB::isError($result_new)) {
            	trigger_error($result_new->getMessage(), E_USER_ERROR);
            }
			
			}
			
			$sta_msg = "登録しました!!";
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
                  <TD colspan="2" width="422" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR>ユーザー権限 編集<BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
				<?php
				

                  echo '
                  <FORM method=post enctype=multipart/form-data action="user_permission.php">
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH colspan="9">許可ユーザー指定</TH>
                      </TR>
					  <TR class="color3">
					  	<TD>name</TD>
					  	<TD>root</TD>
						<TD>site</TD>
						<TD>link</TD>
						<TD>news</TD>
						<TD>list</TD>
						<TD>poll</TD>
						<TD>album</TD>
						<TD>forum</TD>
					  </TR>
                                              ';
					$sql = "select * from `USER_DATA`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
                        $sql = "select * from `PHP_USR_LEVEL`";
                        $result_tmp = $db->query($sql);
                        $chk = $result_tmp->numRows();
                        if($chk){
                            $tmp_rows = $result_tmp->fetchRow(DB_FETCHMODE_ASSOC);
                            $tmp_uid = $tmp_rows["uid"];
                            //var_dump($tmp_uid);
                            $tmp_uid = rtrim($tmp_uid,",");
                            $tmp_uid_result = split(",",$tmp_uid);
                            //var_dump($tmp_uid_result);

                        }

						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$f_usr_name = $user_rows["name"];
                            $f_usr_uid = $user_rows["uid"];

                            $sql = "select * from `PHP_USR_LEVEL` where `uid` = '$f_usr_uid'";
                        	$result_tmp = $db->query($sql);
                            $tmp_rows = $result_tmp->fetchRow(DB_FETCHMODE_ASSOC);
                            $tmp_root = $tmp_rows["root"];
							$tmp_site = $tmp_rows["site"];
                            $tmp_link = $tmp_rows["link"];
							$tmp_news = $tmp_rows["news"];
							$tmp_guild = $tmp_rows["guild"];
							$tmp_poll = $tmp_rows["poll"];
							$tmp_album = $tmp_rows["album"];
							$tmp_forum = $tmp_rows["forum"];
							//$chk_auth = "checked";
							
							if(find_oracle($f_usr_uid)){
								$f_usr_name0 = "&nbsp;<B>(Oracle)</B>";
							}else{
								$f_usr_name0 = "";
							}
							
							echo "<TR>";
							echo "<TD class='row1'>$f_usr_name$f_usr_name0</TD>";
							
							if($tmp_root){ $chk_auth = "checked"; }
							echo "<TD align='center' width='25' class='row1'><input type='checkbox' name='selected_root_".$f_usr_name,"' value='1' ".$chk_auth."></TD>";
							
							$chk_auth = "";
							
							if($tmp_site==1){ $chk_auth = "checked"; }
							echo "<TD align='center' width='25' class='row1'><input type='checkbox' name='selected_site_".$f_usr_name,"' value='1' ".$chk_auth."></TD>";
							
							$chk_auth = "";
							
							if($tmp_link){ $chk_auth = "checked"; }
							echo "<TD align='center' width='25' class='row1'><input type='checkbox' name='selected_link_".$f_usr_name,"' value='1' ".$chk_auth."></TD>";
							
							$chk_auth = "";
							
							if($tmp_news){ $chk_auth = "checked"; }
							echo "<TD align='center' width='25' class='row1'><input type='checkbox' name='selected_news_".$f_usr_name,"' value='1' ".$chk_auth."></TD>";
							
							$chk_auth = "";
							
							if($tmp_guild){ $chk_auth = "checked"; }
							echo "<TD align='center' width='25' class='row1'><input type='checkbox' name='selected_guild_".$f_usr_name,"' value='1' ".$chk_auth."></TD>";
							
							$chk_auth = "";
							
							if($tmp_poll){ $chk_auth = "checked"; }
							echo "<TD align='center' width='25' class='row1'><input type='checkbox' name='selected_poll_".$f_usr_name,"' value='1' ".$chk_auth."></TD>";
							
							$chk_auth = "";
							
							if($tmp_album){ $chk_auth = "checked"; }
							echo "<TD align='center' width='25' class='row1'><input type='checkbox' name='selected_album_".$f_usr_name,"' value='1' ".$chk_auth."></TD>";
							
							$chk_auth = "";
							
							if($tmp_forum){ $chk_auth = "checked"; }
							echo "<TD align='center' width='25' class='row1'><input type='checkbox' name='selected_forum_".$f_usr_name,"' value='1' ".$chk_auth."></TD>";
							
							$chk_auth = "";
							
							echo "</TR>";
						}
					}else{
						echo "<TR><TD colspan=9>ユーザーは登録されていません。</TD></TR>";
					}

                      echo '
                      <TR>
                        <TD class="color2" colspan="9">';
                        echo '<INPUT type="submit" name="selected" value="Select"></TD>
                      </TR>
                      </FORM>
                    </TBODY>
                  </TABLE>';
				
				?>
                </TD></TR><TR><TD colspan="2" width="422" valign="top"></TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				*「Select」を押すと送信され、上部に「登録しました!! 」と表示されるまでお待ちください。表示された場合は登録完了です。<BR><BR><BR>
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
