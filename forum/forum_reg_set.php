<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
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

$STYLE = load_style(4,1);

if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,7)){
		die("Access Denied");
	}
}

//forum set
$sid = $_POST["sid"];
$addforum = $_POST["addforum"];

$sid_edit = $_POST["sid_edit"];
if(!$sid_edit){
    mt_srand(microtime()*100000);
	$sid_edit = md5(uniqid(mt_rand(),1));
}
$sid_level = $_POST["sid_level"];
if(!$sid_level){
    mt_srand(microtime()*100000);
	$sid_level = md5(uniqid(mt_rand(),1));
}

if($addforum && $sid && $uid){

    $forum_name = $_POST["forum_name"];
    $cat_id = $_POST["cat_id"];
    $forum_desc = $_POST["forum_desc"];
    $forum_status = $_POST["forum_status"];
    $forum_edit = $_POST["auth_edit"];
    $forum_level = $_POST["level"];
    
    $sql = "REPLACE INTO `FORUM_TMPDATA_MAKE_FORUM` VALUES ('$sid', '$sid_edit' ,'$sid_level','$forum_name','$forum_desc','$cat_id','$forum_status','$forum_edit','$forum_level')";
	$result = $db->query($sql);
    if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
    }

    $sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$cat_id'";
    $result = $db->query($sql);
    $chk = $result->numRows();
    if(!$chk){
        die("No category");
    }
}

$auth_edit = $_POST["auth_edit"];
$level = $_POST["level"];
$local_count = $_POST["local_count"];
if($local_count == 1){
    $local_count = 2;
}else{
    $local_count = 1;
}

$sel_edit = $_POST["selected_edit"];
$sel_level = $_POST["selected_level"];

if($sel_edit){
	$sql = "select * from `USER_DATA`";
	$result = $db->query($sql);
	$chk = $result->numRows();
		if($chk){
            if($_POST["selected_guest"]){
                $str_uid = "guest,";
            }
			while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
			$f_usr_name = $user_rows["name"];
            $f_chk_usrs = $_POST["selected_$f_usr_name"];
                if($f_chk_usrs){
                    $f_usr_uid = $user_rows["uid"];
                    $str_uid .= "$f_usr_uid".",";
                }
            }
            //if($str_uid){
                $sid_edit = $_POST["sid_edit"];
                $sql = "REPLACE INTO `FORUM_TMPDATA_AUTH` VALUES ('$sid_edit', '$str_uid')";
	            $result = $db->query($sql);
             	if (DB::isError($result)) {
             		trigger_error($result->getMessage(), E_USER_ERROR);
             	}
                if($str_uid){
                    $msg_level = "1";
                }
            //}
        }
}

if($sel_level){
	$sql = "select * from `USER_DATA`";
	$result = $db->query($sql);
	$chk = $result->numRows();
		if($chk){
            if($_POST["selected_guest"]){
                $str_uid = "guest,";
            }
			while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
			$f_usr_name = $user_rows["name"];
            $f_chk_usrs = $_POST["selected_$f_usr_name"];
                if($f_chk_usrs){
                    $f_usr_uid = $user_rows["uid"];
                    $str_uid .= "$f_usr_uid".",";
                }
            }
            //if($str_uid){
                $sid_level = $_POST["sid_level"];
                $sql = "REPLACE INTO `FORUM_TMPDATA_AUTH` VALUES ('$sid_level', '$str_uid')";
	            $result = $db->query($sql);
             	if (DB::isError($result)) {
             		trigger_error($result->getMessage(), E_USER_ERROR);
             	}
                if($str_uid){
                    $msg_level = "1";
                }
            //}
        }
}

if($sid){
    $sql = "select * from `FORUM_TMPDATA_MAKE_FORUM` WHERE `sid` = '$sid'";
    $result = $db->query($sql);
    $chk = $result->numRows();
    if($chk){
        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $sid_level = $tmp_rows["sid_level"];
        $sid_edit = $tmp_rows["sid_edit"];
        $forum_name = $tmp_rows["forum_name"];
        $forum_desc = $tmp_rows["forum_desc"];
        $cat_id = $tmp_rows["cat_id"];
        $forum_status = $tmp_rows["forum_status"];
        $auth_edit = $tmp_rows["forum_edit"];
        $level = $tmp_rows["forum_level"];
    }else{
        trigger_error("unknown sid = $sid", E_USER_ERROR);
    }
}else{
    mt_srand(microtime()*100000);
	$sid = md5(uniqid(mt_rand(),1));
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
            <TD class="color2" height="34" width="200">&nbsp;Forum</TD>
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
                  <TD colspan="2" width="422"></TD>
                  <TD rowspan="5" align="right" width="148" valign="top">
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422">
                  <BR>フォーラムの追加</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <?php
                  
                  if($auth_edit == "3" && $sid_edit){

                  echo '
                  <FORM method=post enctype=multipart/form-data action="forum_reg_set.php">
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH colspan="2">編集可能ユーザー指定</TH>
                      </TR>
                                              ';
					$sql = "select * from `USER_DATA`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
                        $sql = "select * from `FORUM_TMPDATA_AUTH` where `sid` = '$sid_edit'";
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
                        echo "<TR><TD align='center' width='25' class='row1'><input type='checkbox' name='selected_guest' value='1'></TD>
                        <TD width='532' class='row1'>ゲスト</TD></TR>";
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$f_usr_name = $user_rows["name"];
                            $f_usr_uid = $user_rows["uid"];
                            if($chk){
                            if(in_array("$f_usr_uid",$tmp_uid_result)){
                                $chk_auth = "checked";
                            }else{
                                $chk_auth = "";
                            }
                            }else{
                                if($uid == $f_usr_uid){
                                     $chk_auth = "checked";
                                }else{
                                     $chk_auth = "";
                                }
                            }
							echo "<TR><TD align='center' width='25' class='row1'><input type='checkbox' name='selected_".$f_usr_name,"' value='1' ".$chk_auth."></TD>
                        <TD width='532' class='row1'>$f_usr_name</TD></TR>";
						}
					}else{
						echo "<TR><TD>ユーザーは登録されていません。</TD></TR>";
					}

                      echo '
                      <TR>
                        <TD class="color2" colspan="2">';
                        echo '<INPUT type="hidden" name="sid" value="'.$sid.'"><INPUT type="hidden" name="sid_edit" value="'.$sid_edit.'"><INPUT type="submit" name="selected_edit" value="Select"></TD>
                      </TR>
                      </FORM>
                    </TBODY>
                  </TABLE><BR>';
                 }
                 

                  if($level == "2" && $sid_level){

                  echo '
                  <FORM method=post enctype=multipart/form-data action="forum_reg_set.php">
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH colspan="2">拒否ユーザー指定</TH>
                      </TR>
                                              ';
					$sql = "select * from `USER_DATA`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
                        $sql = "select * from `FORUM_TMPDATA_AUTH` where `sid` = '$sid_level'";
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
                        echo "<TR><TD align='center' width='25' class='row1'><input type='checkbox' name='selected_guest' value='1' checked></TD>
                        <TD width='532' class='row1'>ゲスト</TD></TR>";
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$f_usr_name = $user_rows["name"];
                            $f_usr_uid = $user_rows["uid"];
                            if($chk){
                                if(in_array("$f_usr_uid",$tmp_uid_result)){
                                    $chk_auth = "checked";
                                }else{
                                    $chk_auth = "";
                                }
                            }else{
                                $chk_auth = "";
                            }
							echo "<TR><TD align='center' width='25' class='row1'><input type='checkbox' name='selected_".$f_usr_name,"' value='1' ".$chk_auth."></TD>
                        <TD width='532' class='row1'>$f_usr_name</TD></TR>";
						}
					}else{
						echo "<TR><TD>ユーザーは登録されていません。</TD></TR>";
					}

                      echo '
                      <TR>
                        <TD class="color2" colspan="2">';
                        echo '<INPUT type="hidden" name="sid" value="'.$sid.'"><INPUT type="hidden" name="sid_level" value="'.$sid_level.'"><INPUT type="submit" name="selected_level" value="Select"></TD>
                      </TR>
                      </FORM>
                    </TBODY>
                  </TABLE><BR>';
                 }
                 
                  ?>
                  
                  
                  <TABLE><TBODY>
                  <TR>
                    <TD>
                    <?php
                      if($local_count == 2){
                          echo '<FORM method="post" enctype="multipart/form-data" action="forum_reg.php">';
                      }else{
                          echo '<FORM method="post" enctype="multipart/form-data" action="forum_reg_set.php">';
                      }



                    ?>

                    フォーラムの名前</TD>
                    <TD>&nbsp;<INPUT size="50" type="text" name="forum_name" value="<?php echo "$forum_name"; ?>"></TD>
                  </TR>
                  <TR>
                    <TD>説明</TD>
                    <TD>&nbsp;<INPUT size="50" type="text" name="forum_desc" value="<?php echo "$forum_desc"; ?>"></TD>
                  </TR>
                  <TR>
                    <TD>カテゴリー</TD>
                    <TD>&nbsp;<select name="cat_id">
                    <?php
                    $sql = "select * from `FORUM_CATEGORIES`";
                    $result = $db->query($sql);
                       while( $forum_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
                          $w_cat_id = $forum_rows["cat_id"];
						  $cat_title = $forum_rows["cat_title"];
                          $select = "";
                          if($w_cat_id == $cat_id){
                              $select = "selected=\"selected\"";
                          }
                          echo "<option value='$w_cat_id' $select>$cat_title </option>\n";
                       }
                    ?>
                       </select>
                    </TD>
                  </TR>
                  <TR>
                    <TD>フォーラムのロック</TD>
                    <TD>&nbsp;<select name="forum_status">
                    <option value="0" <?php if($forum_status == "0"){ echo 'selected="selected"'; }?>>Unlocked</option>
                    <option value="1" <?php if($forum_status == "1"){ echo 'selected="selected"'; }?>>Locked</option>
                    </select>
                    </TD>
                  </TR>
                  <TR>
                    <TD>編集</TD>
                    <TD>&nbsp;<select name="auth_edit">
                    <option value="0" <?php if($auth_edit == "0"){ echo 'selected="selected"'; }?>>このアカウント</option>
                    <option value="1" <?php if($auth_edit == "1"){ echo 'selected="selected"'; }?>>メンバー</option>
                    <option value="2" <?php if($auth_edit == "2"){ echo 'selected="selected"'; }?>>webmaster</option>
                    <option value="3" <?php if($auth_edit == "3"){ echo 'selected="selected"'; }?>>指定</option>
                    </select>
                    </TD>
                  </TR>
                      <TR>
                        <TD>レベル</TD>
                        <TD>&nbsp;<SELECT name=level>
                        <option value=0 <?php if($level == "0"){ echo 'selected="selected"'; }?>>ノーマル</option>
                        <option value=1 <?php if($level == "1"){ echo 'selected="selected"'; }?>>ゲスト拒否</option>
                        <option value=2 <?php if($level == "2"){ echo 'selected="selected"'; }?>>指定</option>
                        </select></TD>
                      </TR>
                  <TR>
                    <TD>作成</TD>
                    <TD><Br>
                    <?php
                      if($local_count == 1){
                          echo '<INPUT type="submit" name="addforum" value="確認">';
                      }else{
                          echo '確認が完了しました。<BR><INPUT type="submit" name="addforum" value="submit">';
                      }



                    ?></TD><input type="hidden" name="local_count" value="<?php echo "$local_count";?>" />
                    <input type="hidden" name="sid" value="<?php echo "$sid";?>" />
                    <input type="hidden" name="sid_edit" value="<?php echo "$sid_edit";?>" />
                    <input type="hidden" name="sid_level" value="<?php echo "$sid_level";?>" /></FORM>
                  </TR>
                  </TBODY></TABLE>



				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"><HR><BR></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
                  <A href='javascript:history.back()'>戻る</A>
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