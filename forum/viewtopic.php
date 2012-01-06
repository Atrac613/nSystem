<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "forum_lib.php";
require_once "../list/ml_common.php";
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

//global val
$t= intval($_GET["t"]);

//dump forum
$sql = "select * from `FORUM_TOPIC` WHERE `topic_id` = '$t'";
$result = $db->query($sql);
$chk = $result->numRows();
if($chk){
    $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $forum_id = $user_rows["forum_id"];
    $topic_title = $user_rows["topic_title"];
	$topic_title = htmlspecialchars($topic_title);
	//$forum_master = $user_rows["forum_master"];
	//$make_time = $user_rows["make_time"];

    $sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$forum_id'";
    $result = $db->query($sql);
    $chk = $result->numRows();
    if($chk){
        $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $cat_id = $user_rows["cat_id"];
        $forum_name = $user_rows["forum_name"];
		
		//認証関係
		if(!auth_read_cf("c",$cat_id)){
			//die("error auth");
			sub_msg("3","forum/forum.php","Error A-001","リロードします。");
		}else{	
			if(!auth_read_cf("f",$forum_id)){
				//die("error auth");
				sub_msg("3","forum/forum.php","Error A-002","リロードします。");
			}
		}
        
        $sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$cat_id'";
        $result = $db->query($sql);
        $chk = $result->numRows();
        if($chk){
           $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
           $cat_name = $user_rows["cat_title"];
        }
    }


}else{
    $forum_name = "none";
	$cat_name = "none";
	$topic_title = "none";
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
      <TD width="8" class="color3" background="../img/<?php echo "$STYLE[img_left]"; ?>" rowspan="2"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
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
                  <TD colspan="2" width="570"></TD>
                <TD rowspan="5" align="right" width="10" valign="top"><BR>
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422">
                  <BR>フォーラム -トピック閲覧-</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <?php
                  echo "
                  <TABLE>
                    <TBODY>
                      <TR>
                        <TD><A href='posting.php?mode=new_topic&f=$forum_id'><IMG src='img/".$STYLE[post]."' border='0'></a>&nbsp;<A href='posting.php?mode=reply&t=$t'><IMG src='img/".$STYLE[reply]."' border='0'></a></TD>
                        <TD valign='middle'>$cat_name >> $forum_name >> $topic_title</TD>
                      </TR>
                    </TBODY>
                  </TABLE>
                  <TABLE>";
                  ?>
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                    <?php
					$sql = "select * from `FORUM_TOPIC` WHERE `topic_id` = '$t'";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
                             echo'
                      <TR class="table_title">
                        <TH width="150" height="26" align="center">投稿者</TH>
                        <TH align="center" colspan="2">メッセージ</TH>
                      </TR>';

                            $sql = "select * from `FORUM_POSTS` WHERE `topic_id` = '$t' order by `post_id`";
                            $forum_result = $db->query($sql);
                            $chk = $forum_result->numRows();
                            if($chk){
                                $row_c = 1;
                                while( $forum_rows = $forum_result->fetchRow(DB_FETCHMODE_ASSOC) ){
                                    //td_cor
                                    if($row_c == 3){
                                        $row_c = 1;
                                    }

                                    $post_id = $forum_rows["post_id"];
                                    
                                    //認証関係スタート
                                    //var_dump(auth_read($post_id));
                                    if(auth_read($post_id)){
									
                                    $thread_id = $forum_rows["thread_id"];
                                    $topic_id = $forum_rows["topic_id"];
                                    $post_time = $forum_rows["post_time"];
                                    $enable_spcode = $forum_rows["enable_spcode"];
                                    $post_edit_time = $forum_rows["post_edit_time"];
                                    $post_edit_count = $forum_rows["post_edit_count"];
                                    $auth_mode = $forum_rows["auth_mode"];
									$enable_spcode = $forum_rows["enable_spcode"];
                                    $u_sid = $forum_rows["sid"];
                                    $date = gmdate("y/m/d (D) H:i", $post_edit_time+9*60*60);
                                    
                                    $sql = "select * from `FORUM_POSTS_TXT` WHERE `post_id` = '$post_id'";
                                    //var_dump($sql);
                                    $post_result = $db->query($sql);
                                    $chk = $post_result->numRows();
                                    
                                    if($chk){
                                        $post_rows = $post_result->fetchRow(DB_FETCHMODE_ASSOC);
                                        $post_subject = $post_rows["post_subject"];
                                        $post_text = $post_rows["post_text"];
                                        //$post_text = str_replace("\n", "<br>", $post_text);
										
								        $f_msgbody0 = str_replace("\r\n", "\r", $post_text);
								        $f_msgbody0 = str_replace("\r", "\n", $f_msgbody0);
										if(!$enable_spcode){
									        $f_msgbody0 = htmlspecialchars($f_msgbody0);
										}
								        $f_msgbody0 = str_replace("\n", "<br>", $f_msgbody0);
										$f_msgbody0 = make_clickable($f_msgbody0);
										//$ret = make_clickable($f_msgbody0);
										//var_dump($ret);
										$post_subject = htmlspecialchars($post_subject);
                                    }else{
                                        //die("Fatal Errror $sql");
										trigger_error("SQL Error $post_id", E_USER_ERROR);
                                    }
                                    
                                    $sql = "select * from `FORUM_USERS` WHERE `post_id` = '$post_id'";
                                    $post_usr_result = $db->query($sql);
                                    $chk = $post_usr_result->numRows();

                                    if($chk){
                                        $post_rows = $post_usr_result->fetchRow(DB_FETCHMODE_ASSOC);
										$post_uid = $post_rows["uid"];
                                        $post_username = $post_rows["post_username"];
                                        $post_userpass = $post_rows["post_userpass"];
                                        $post_mail = $post_rows["mail"];
                                        $post_url = $post_rows["url"];
                                        $post_file = $post_rows["file"];
                                        $post_face = $post_rows["face"];
										//var_dump($post_uid);
                                    }else{
                                        //die("Fatal Errror $sql");
										trigger_error("SQL Error $post_id", E_USER_ERROR);
                                    }
                                    
                                    
                                    
                                echo '
                      <TR>
                        <TD width="100" align="center" class="row'.$row_c.'">';
						if($uid){
	                        if($uid == $post_uid || find_root($uid)){
	                            echo '<A href="modify_post.php?p='.$post_id.'">削除 編集';
	                        }
						}
                        echo '<a name="'.$thread_id.'"></a></TD>
                        <TD width="540" class="row'.$row_c.'">['.$thread_id.'] '.$post_subject.'</TD>
                        <TD width="90" align="center" class="row'.$row_c.'"><A href="posting.php?mode=quote&p='.$post_id.'">引用返信</A></TD>
                      </TR>
                      <TR>
                        <TD rowspan="2" class="row'.$row_c.'" valign="top" align="center"><BR><img src=../face/'.$post_face.'.gif width=30 height=30 border=0><br>'.$post_username.'</TD>
                        <TD colspan="2" class="row'.$row_c.'" valign="top">'.$f_msgbody0.'<BR><BR>';

                        if($post_file){
						
							$pos = strrpos($post_file,".");	//拡張子取得
							$ext = substr($post_file,$pos+1,strlen($post_file)-$pos);
							$ext = strtolower($ext);
                            $file_name=$u_sid.".$ext";
							
                           echo "添付ファイル(<A href=dat/".$file_name.">$post_file</A>)";
                       }

                        echo '</TD>
                      </TR>
                      <TR>
                        <TD align="right" colspan="2" class="row'.$row_c.'">['.$date.']</TD>
                      </TR>
                      <TR>
                        <TD align="center" class="row'.$row_c.'"><A href="#'.($thread_id-1).'">△</A> <A href="#1">TOP</A> <A href="#'.($thread_id+1).'">▽</A></TD>
                        <TD colspan="2" class="row'.$row_c.'">';
                        if(find_user($post_username)){
                            echo '<a href="../list/'.$PLOF_SCRIPT.'?name='.$post_username.'">PROFILE</a> <a href="../list/'.$POST_SCRIPT.'?mode=new&name='.$post_username.'">PM</a> ';
                        }
                        if($post_mail){
                            echo '<A href="mailto:'.$post_mail.'">MAIL</A> ';
                        }
                        if($post_url){
                            echo '<A href="'.$post_url.'">URL</A>';
                        }
                        echo '
                        </TD>
                      </TR>
                      <TR>
                        <TD class="spaceRow" colspan="3" height="1"><IMG src="../img/spacer.gif" alt="" width="1" height="1"></TD>
                      </TR>
                      ';
                                $row_c = $row_c + 1;
                                     }//認証関係終了
                                }
					         }
                    }else{
                        echo '
                      <TR>
                        <TD colspan="2" background="img/cellpic1.gif" height="28">Information</TD>
                      </TR>
                      <TR>
                        <TD height="6">そのトピックは存在していません。</TD>
                      </TR>';
                    }
                    ?>
                    </TBODY>
                  </TABLE>
				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"><BR><HR><A href='javascript:history.back()'>戻る</A><BR></TD>
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