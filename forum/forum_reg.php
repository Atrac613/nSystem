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

$addcategory = $_POST["addcategory"];
if($addcategory && usr_level($uid,9)){
    $category_name = $_POST["category_name"];
	$category_name = htmlspecialchars($category_name);

    //$auth_edit = $_POST["category_name"];
    //$auth_mode = $_POST["category_name"];

    $time = time();
	
	//var_dump($category_name);

    if($category_name && $addcategory){
 	   $sql = "REPLACE INTO `FORUM_CATEGORIES` VALUES ('','$category_name', '$uid', '$auth_edit', '$auth_mode', '0','$time', '$time')";
	   $result = $db->query($sql);
	   if (DB::isError($result)) {
		  trigger_error($result->getMessage(), E_USER_ERROR);
       }
       $sta_msg = "カテゴリーを追加しました!!";
	   
       $sql = "select * from `FORUM_CATEGORIES` WHERE `make_time` = '$time'";
       $result = $db->query($sql);
       $chk = $result->numRows();
       if($chk){
	   		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			$cat_id = $tmp_rows["cat_id"];
	   
		//ニュースの追加
		add_news('1',"$cat_id",'');
		}
		//sub_msg("3","list/forum_reg.php","Moogle","送ったクポ～♪");
    }
}

$addforum = $_POST["addforum"];
$sid = $_POST["sid"];
if($addforum && $sid && $uid){
    $forum_name = $_POST["forum_name"];
    $cat_id = $_POST["cat_id"];
    $forum_desc = $_POST["forum_desc"];
    $auth_edit = $_POST["auth_edit"];
    $forum_status = $_POST["forum_status"];
    $auth_mode = $_POST["level"];
	
	$forum_name = htmlspecialchars($forum_name);
    $forum_desc = htmlspecialchars($forum_desc);

    $time = time();
    
    $sid_edit = $_POST["sid_edit"];
    $sid_level = $_POST["sid_level"];

    if($forum_name && $addforum){
 	   $sql = "REPLACE INTO `FORUM_FORUMS` VALUES ('', '$cat_id', '$forum_name', '$forum_desc', '$forum_status', '$uid', '$auth_edit', '', '', '$auth_mode', '$time', '$time' ,'$sid')";
	   $result = $db->query($sql);
	   if (DB::isError($result)) {
		  trigger_error($result->getMessage(), E_USER_ERROR);
       }
	   
       //if($auth_edit == 3 || $auth_mode == 2){
           $sql = "select * from `FORUM_FORUMS` WHERE `sid` = '$sid'";
           $result = $db->query($sql);
           $chk = $result->numRows();
           if($chk){
				$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
				$forum_id = $tmp_rows["forum_id"];
		   }
		//}
       
       if($auth_edit == 3){
           $sql = "select * from `FORUM_TMPDATA_AUTH` WHERE `sid` = '$sid_edit'";
           $result = $db->query($sql);
           $chk = $result->numRows();
           if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $uid = $tmp_rows["uid"];
                
                $sql = "REPLACE INTO `FORUM_AUTH_ACCESS` VALUES ('', '$forum_id', '1','1', '$uid')";
	            $result = $db->query($sql);
	            if (DB::isError($result)) {
		            trigger_error($result->getMessage(), E_USER_ERROR);
                }
                
           }else{
               trigger_error("unknown sid = $sid_level", E_USER_ERROR);
           }
       }
       
       if($auth_mode == 2){
           $sql = "select * from `FORUM_TMPDATA_AUTH` WHERE `sid` = '$sid_level'";
           $result = $db->query($sql);
           $chk = $result->numRows();
           if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $uid = $tmp_rows["uid"];

                $sql = "REPLACE INTO `FORUM_AUTH_ACCESS` VALUES ('', '$forum_id', '1','2', '$uid')";
	            $result = $db->query($sql);
	            if (DB::isError($result)) {
		            trigger_error($result->getMessage(), E_USER_ERROR);
                }

           }else{
               trigger_error("unknown sid = $sid_level", E_USER_ERROR);
           }
       }
       
       $sta_msg = "フォーラムを追加しました!!";
	   //ニュースの追加
		add_news('2',"$forum_id",'');
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
                  <?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?>
                  <BR>フォーラムの管理</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
				  <?php
                    mt_srand(microtime()*100000);
	                $sid = md5(uniqid(mt_rand(),1));
					$sql = "select * from `FORUM_CATEGORIES`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					//var_dump($chk);
					//$cat_rows2 = $result->fetchRow(DB_FETCHMODE_ASSOC);
					//var_dump($cat_rows2);
					if($chk){
                        echo "<TABLE><TBODY>";
						while( $cat_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
						
							$cat_id = $cat_rows["cat_id"];
							//var_dump($cat_id);
							$cat_title = $cat_rows["cat_title"];
							$auth_mode = $cat_rows["auth_mode"];
							$auth_edit = $cat_rows["auth_edit"];
							$cat_master = $cat_rows["cat_master"];
							$time = $cat_rows["make_time"];
							$l_time = $cat_rows["last_time"];
							$date = gmdate("y/m/d D H:i", $time+9*60*60);
							//$date = strftime('%D' , $time);
							//var_dump($date);
							
							echo "<TR><TD>&nbsp;&nbsp;・<A href='viewcategories.php?c=$cat_id'>$cat_title</A> ($date)
        <A href='modify_category.php?c=$cat_id'>編集</A> <A href='delete_category.php?c=$cat_id'>削除</A><BR></TD></TR><TR><TD>";
       
                            $sql = "select * from `FORUM_FORUMS` WHERE `cat_id` = '$cat_id'";
							//var_dump($sql);
                            $forum_result = $db->query($sql);
                            $chk = $forum_result->numRows();
                            if($chk){
                                while( $forum_rows = $forum_result->fetchRow(DB_FETCHMODE_ASSOC) ){
                                    $forum_id = $forum_rows["forum_id"];
                                    $forum_name = $forum_rows["forum_name"];
                                    $forum_desc = $forum_rows["forum_desc"];
                                    
                                    $sql = "select * from `FORUM_POSTS` WHERE `forum_id` = '$forum_id'";
                                    $count_result = $db->query($sql);
                                    $chk = $count_result->numRows();
                                    
                                    echo "&nbsp;&nbsp;&nbsp;→<A href='viewforum.php?f=$forum_id'>$forum_name</A> ($forum_desc $chk 件)
                                    <A href='modify_forum.php?f=$forum_id'>編集</A> <A href='delete_forum.php?f=$forum_id'>削除</A><BR>";
                                }
                                echo "</TD></TR>";
                            }else{
                                echo "&nbsp;&nbsp;&nbsp;→まだ登録されていません。<BR></TD></TR>";
                            }
                            
                            echo '<TR><TD><FORM method="post" enctype="multipart/form-data" action="forum_reg_set.php">
                            &nbsp;&nbsp;';
                            echo "<INPUT type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">";
                            echo '<INPUT size="60" type="text" name="forum_name"><INPUT type="hidden" name="sid" value="'.$sid.'">
                            <INPUT type="hidden" name="addforum" value="1"><INPUT type="submit" name="addforum" value="submit">';

                            echo "<BR><BR></TD></FORM></TR>";

						}
                      echo "</TBODY></TABLE>";
					}else{
						echo "&nbsp;&nbsp;・まだ登録されていません。<BR><BR>";
					}

                   ?>
				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"><HR><BR></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
                  カテゴリーの追加<BR>
                  <FORM method="post" enctype="multipart/form-data" action="forum_reg.php">
                  &nbsp;&nbsp;&nbsp;<INPUT size="60" type="text" name="category_name">
                  <INPUT type="submit" name="addcategory" value="submit">
                  </FORM>
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