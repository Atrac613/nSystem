<?php
//���C�u�����Ăяo��
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "forum_lib.php";
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
//var_dump($STYLE);

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
            <TD class="row_title" height="34"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
            <TD class="row_title" height="34" width="131">FINAL FANTASY XI<BR>
            <?php echo "$STYLE[site_name]"; ?></TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10" class="color2">&nbsp;</TD>
            <TD class="color2" height="34" width="200">&nbsp;Forum</TD>
            <TD class="color2" align="right" height="34" width="396">
			<?php
			
			if($name){
				echo "�悤�����A$name ����";
			}else{
				echo "�悤�����A�Q�X�g����";
			}
			
			?>&nbsp;&nbsp;</TD>
          </TR>
        </TBODY>
      </TABLE>
      <TABLE height="100%" cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="color2"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
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
                  <BR>�t�H�[���� -�J�e�S���[�{��-</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TD colspan="2" align="center" height="25" valign="middle" nowrap><B>�t�H�[����</B></TD>
                        <TD align="center" valign="middle" width="86" nowrap><B>�g�s�b�N</B></TD>
                        <TD width="50" align="center" valign="middle" nowrap><B>�L��</B></TD>
                        <TD align="center" valign="middle" nowrap><B>�ŏI���e</B></TD>
                      </TR>
                    <?php
					$sql = "select * from `FORUM_CATEGORIES`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$cat_id = $user_rows["cat_id"];
							$cat_title = $user_rows["cat_title"];
							$time = $user_rows["time"];
							$date = strftime('%D' , $time);
							
							//�J�e�S���[�F�؊֌W
							if(auth_read_cf("c",$cat_id)){

                             echo '<TR>
                        <TD colspan="2" background="img/'.$STYLE[cellpic0].'" height="28"><A href="viewcategories.php?c='.$cat_id.'">'.$cat_title.'</A></TD>
                        <TD height="25" colspan="3" background="img/'.$STYLE[cellpic1].'"></TD>
                      </TR>';
                      
                            $sql = "select * from `FORUM_FORUMS` WHERE `cat_id` = '$cat_id'";
                            $forum_result = $db->query($sql);
                            $chk = $forum_result->numRows();
                            if($chk){
                                while( $forum_rows = $forum_result->fetchRow(DB_FETCHMODE_ASSOC) ){
                                    $forum_id = $forum_rows["forum_id"];
									$c = $forum_rows["cat_id"];
									
									//�t�H�[�����F�؊֌W�X�^�[�g
									if(auth_read_cf("c",$c)){
									if(auth_read_cf("f",$forum_id)){
									
                                    $forum_name = $forum_rows["forum_name"];
                                    $forum_desc = $forum_rows["forum_desc"];
                                    $forum_status = $forum_rows["forum_status"];
                                    $forum_topics = $forum_rows["forum_topics"];
                                    $forum_posts = $forum_rows["forum_posts"];
                                    $forum_last_time = $forum_rows["last_time"];
                                    $date = gmdate("y/m/d (D) H:i", $forum_last_time+9*60*60);
                                    if(!rock_status("c",$c) || $forum_status == 1){
                                        $status_img = "img/".$STYLE[sta2];
                                    }else{
                                        $yes_time = time() - 86400;
                                        if($yes_time < $forum_last_time){
                                            $status_img = "img/".$STYLE[sta1];
                                        }else{
                                            $status_img = "img/".$STYLE[sta0];
                                        }
                                    }
                                echo '
                      <TR>
                        <TD height="6" width="53" align="center"><IMG src="'.$status_img.'"></TD>
                        <TD height="6" width="638"><b><A href="viewforum.php?f='.$forum_id.'">'.$forum_name.'</a></b><BR>'.$forum_desc.'</TD>
                        <TD height="6" width="86" align="center" valign="middle">'.$forum_topics.'</TD>
                        <TD width="68" height="6" align="center" valign="middle">'.$forum_posts.'</TD>
                        <TD width="113" height="6" align="center" valign="middle">'.$date.'</TD>
                      </TR>';
					  					//�t�H�[�����F�؊֌W�G���h
										}
										}
                                }
					         }else{
                               echo '
                      <TR>
                        <TD height="6" width="53" align="center"><IMG src="img/'.$STYLE[sta3].'"></TD>
                        <TD height="6" width="638">�t�H�[�����͂���܂���B</TD>
                        <TD height="6" width="86" align="center" valign="middle">&nbsp;</TD>
                        <TD width="68" height="6" align="center" valign="middle">&nbsp;</TD>
                        <TD width="113" height="6" align="center" valign="middle">&nbsp;</TD>
                      </TR>';
					         }
							 
							 //�J�e�S���[�F�؊֌W�I��
							 }
                        }
                        
                    }else{
                        echo '
                      <TR>
                        <TD colspan="2" background="img/'.$STYLE[cellpic0].'" height="28">�J�e�S���[�͂���܂���B</TD>
                        <TD height="26" colspan="3" background="img/'.$STYLE[cellpic1].'"></TD>
                      </TR>
                      <TR>
                        <TD height="6" width="53">&nbsp;</TD>
                        <TD height="6" width="638">�t�H�[�����͂���܂���B</TD>
                        <TD height="6" width="86" align="center" valign="middle">&nbsp;</TD>
                        <TD width="68" height="6" align="center" valign="middle">&nbsp;</TD>
                        <TD width="113" height="6" align="center" valign="middle">&nbsp;</TD>
                      </TR>';
                    }
                    ?>
                    </TBODY>
                  </TABLE>
					<BR>
				<?php
				
				if($uid){
					if(usr_level($uid,7)){
					echo '
                  <FORM method="post" enctype="multipart/form-data" action="forum_reg.php">
                  <INPUT type="submit" value="�t�H�[�����Ǘ�"></FORM>';
					}
				}
				
				?>
					<CENTER>
					<TABLE>
					  <TBODY>
					    <TR>
					      <TD width="20"><IMG src="img/<?php echo "$STYLE[sta0]"; ?>"></TD>
					      <TD>�V���Ȃ�</TD>
					      <TD>&nbsp;</TD>
					      <TD width="20"><IMG src="img/<?php echo "$STYLE[sta1]"; ?>"></TD>
					      <TD>�V������</TD>
					      <TD>&nbsp;</TD>
					      <TD width="20"><IMG src="img/<?php echo "$STYLE[sta2]"; ?>"></TD>
					      <TD>���b�N</TD>
					      <TD>&nbsp;</TD>
					      <TD width="20"><IMG src="img/<?php echo "$STYLE[sta3]"; ?>"></TD>
					      <TD>���o�^</TD>
					      <TD>&nbsp;</TD>
					    </TR>
				  </TBODY>
				</TABLE>
				</CENTER>
				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"><BR><BR></TD>
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
            <TD class="color2" height="34"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
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