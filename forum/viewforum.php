<?php
//ライブラリ呼び出し
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

//global val
$f= intval($_GET["f"]);

//dump forum
$sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$f'";
$result = $db->query($sql);
$chk = $result->numRows();
if($chk){
    $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $cat_id = $user_rows["cat_id"];
	
	//認証関係
	if(!auth_read_cf("c",$cat_id)){
		//die("error auth");
		sub_msg("3","forum/forum.php","Error A-001","リロードします。");
	}else{	
		if(!auth_read_cf("f",$f)){
			//die("error auth");
			sub_msg("3","forum/forum.php","Error A-002","リロードします。");
		}
	}
	
	
    $forum_name = $user_rows["forum_name"];
	$forum_master = $user_rows["forum_master"];
	$make_time = $user_rows["make_time"];
	$make_date = strftime('%D' , $make_time);

    $sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$cat_id'";
    //var_dump($sql);
    $result = $db->query($sql);
    $chk = $result->numRows();
    if($chk){
        $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $cat_name = $user_rows["cat_title"];
    }
}else{
    $forum_name = "none";
	$forum_master = "none";
	$make_time = "none";
}

$pg = intval($_GET["pg"]);
$pg_max = "15";
$pg_plus = $pg_max + $pg;
if($pg <= 0){
	$pg = 0;
}
if($pg_plus <= 0){
	$pg_plus = $pg_max;
}
function page_link($pg,$f){
	global $db,$pg_max;
	$max = $pg_max;
	
	$prev = $pg - $max;
	if($prev < 0){
		$prev = "0";
	}
	$sql = "select * from `FORUM_TOPIC` WHERE `forum_id` = '$f' order by `last_time` desc LIMIT $prev , $max";
	//var_dump($sql);
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
    $chk = $result->numRows();
    if($chk && $pg != 0){
        $page_link = '<A href="viewforum.php?f='.$f.'&pg='.$prev.'">PREV</A>&nbsp;';
    }
	
	
	$next = $pg + $max;
	if($next < 0){
		$next = $max;
	}
	$sql = "select * from `FORUM_TOPIC` WHERE `forum_id` = '$f' order by `last_time` desc LIMIT $next , $max";
	//var_dump($sql);
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
    $chk = $result->numRows();
    if($chk){
        $page_link .= '<A href="viewforum.php?f='.$f.'&pg='.$next.'">NEXT</A>';
    }
	
	return $page_link;

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
            <TD class="row_title" height="34"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
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
                  <BR>フォーラム -カテゴリー閲覧-</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <?php
                  echo "
                  <TABLE>
                    <TBODY>
                      <TR>
                        <TD><A href='posting.php?mode=new_topic&f=$f'><IMG src='img/".$STYLE[post]."' border='0'></a></TD>
                        <TD valign='middle'>$cat_name >> $forum_name >> フォーラムインデックス</TD>
                      </TR>
                    </TBODY>
                  </TABLE>
                  <TABLE>";
                  ?>
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                    <?php
					$sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$f'";
					$result = $db->query($sql);
					
					$forum_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                    $forum_status = $forum_rows["forum_status"];
					$c = $forum_rows["cat_id"];
					$chk = $result->numRows();
					if($chk){
                             echo'                      <TR class="table_title">
                        <TH colspan="2" align="center" height="25" valign="middle" nowrap>トピック</TH>
                        <TH align="center" valign="middle" width="50" nowrap>記事</TH>
                        <TH width="80" align="center" valign="middle" nowrap>マスター</TH>
                        <TH align="center" valign="middle" nowrap>最終投稿</TH>
                      </TR>';

                            $sql = "select * from `FORUM_TOPIC` WHERE `forum_id` = '$f' order by `last_time` DESC LIMIT $pg , $pg_max";
                            //var_dump($sql);
							$forum_result = $db->query($sql);
                            $chk = $forum_result->numRows();
							//var_dump(rock_status("c",$c));
                            if($chk){
                                while( $forum_rows = $forum_result->fetchRow(DB_FETCHMODE_ASSOC) ){
                                    $topic_id = $forum_rows["topic_id"];
									
        							//認証関係スタート
        							if(auth_read_t($topic_id)){
									
                                    $topic_title = $forum_rows["topic_title"];
                                    $topic_master = $forum_rows["topic_master"];
                                    $topic_status = $forum_rows["topic_status"];
                                    $topic_replies = $forum_rows["topic_replies"];
                                    $topic_last_time = $forum_rows["last_time"];
                                    $date = gmdate("y/m/d (D) H:i", $topic_last_time+9*60*60);
									
									$topic_title = htmlspecialchars($topic_title);
									
                                    if(!rock_status("c",$c) || $forum_status == 1 || $topic_status == 1){
                                        $status_img = "img/".$STYLE[sta2];
                                    }else{
                                        $yes_time = time() - 86400;
                                        if($yes_time < $topic_last_time){
                                            $status_img = "img/".$STYLE[sta1];
                                        }else{
                                            $status_img = "img/".$STYLE[sta0];
                                        }
                                    }
                                echo '
                      <TR>
                        <TD height="6" width="53" align="center"><IMG src="'.$status_img.'"></TD>
                        <TD height="6" width="638"><A href="viewtopic.php?t='.$topic_id.'">'.$topic_title.'</a></TD>
                        <TD height="6" width="86" align="center" valign="middle">'.$topic_replies.'</TD>
                        <TD width="68" height="6" align="center" valign="middle">'.$topic_master.'</TD>
                        <TD width="113" height="6" align="center" valign="middle">'.$date.'</TD>
                      </TR>';
					  				}//認証関係終了
                                }
								
                               echo '
                      <TR>
                        <TD height="6" width="53" align="center"></TD>
                        <TD height="6" width="638">'.$page_link = page_link($pg,$f).'</TD>
                        <TD height="6" width="86" align="center" valign="middle">&nbsp;</TD>
                        <TD width="68" height="6" align="center" valign="middle">&nbsp;</TD>
                        <TD width="113" height="6" align="center" valign="middle">&nbsp;</TD>
                      </TR>';
								
					         }else{
                               echo '
                      <TR>
                        <TD height="6" width="53" align="center"><IMG src="img/'.$STYLE[sta3].'"></TD>
                        <TD height="6" width="638">トピックはありません。</TD>
                        <TD height="6" width="86" align="center" valign="middle">&nbsp;</TD>
                        <TD width="68" height="6" align="center" valign="middle">&nbsp;</TD>
                        <TD width="113" height="6" align="center" valign="middle">&nbsp;</TD>
                      </TR>';

                             }
                        
                    }else{
                        echo '
                      <TR>
                        <TD colspan="2" background="img/'.$STYLE[cellpic1].'" height="28">Information</TD>
                      </TR>
                      <TR>
                        <TD height="6">そのフォーラムは存在していません。</TD>
                      </TR>';
                    }
                    ?>
                    </TBODY>
                  </TABLE>
					<BR>
					<CENTER>
					<TABLE>
					  <TBODY>
					    <TR>
					      <TD width="20"><IMG src="img/<?php echo "$STYLE[sta0]"; ?>"></TD>
					      <TD>新着なし</TD>
					      <TD>&nbsp;</TD>
					      <TD width="20"><IMG src="img/<?php echo "$STYLE[sta1]"; ?>"></TD>
					      <TD>新着あり</TD>
					      <TD>&nbsp;</TD>
					      <TD width="20"><IMG src="img/<?php echo "$STYLE[sta2]"; ?>"></TD>
					      <TD>ロック</TD>
					      <TD>&nbsp;</TD>
					      <TD width="20"><IMG src="img/<?php echo "$STYLE[sta3]"; ?>"></TD>
					      <TD>未登録</TD>
					      <TD>&nbsp;</TD>
					    </TR>
				  </TBODY>
				</TABLE>
				</CENTER>
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