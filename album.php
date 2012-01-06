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

$STYLE = load_style(6,0);

$offset = $_GET["offset"];
if($offset == ""){
	$offset = "0";
}
$showmax = 20;

if($uid){
	$sql = "select * from `PHP_USR_STYLE` where `uid` = '$uid'";
	$result = $db->query($sql);
	$chk = $result->numRows();
	if($chk){
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$show_limit_time = $user_rows["limit_day"];
		$max_news = $user_rows["max_news"];
		if(!$show_limit_time){
			$show_limit_time = "1";
		}
	}else{
		$show_limit_time = "1";
	}
	
}else{
	$show_limit_time = "1";
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
            <TD class="color2" height="34" width="200">&nbsp;アルバム</TD>
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
                  <TD colspan="2" width="422"><BR>ヴァナ・ディール冒険写真</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
				  <?php
					$sql = "select * from `PHP_ALBUM` order by `date` desc limit $offset , $showmax";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$session_id = $user_rows["session_id"];
							$title = $user_rows["title"];
							$name = $user_rows["name"];
							$date = $user_rows["date"];
							$new_news = new_news($date);
							$date = gmdate("y/m/d D H:i", $date+9*60*60);
							

							echo "&nbsp;&nbsp;&nbsp;<A href='showalbum.php?id=$session_id'>・$title  投稿者：$name さん($date)</A> $new_news<BR>";
						}
						$sql = "select * from `PHP_ALBUM`";
						$result = $db->query($sql);
						$count = $result->numRows();
						$local_count =0;
						$page_links = "&nbsp;";
						while( $upd_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
			
							if(((($local_count) % ($showmax)) == 0)){
								$local_count2= $local_count / $showmax;
								$local_count2++;
								if($offset == $local_count){
									$page_links .= "<b>";
								}
								$page_links .="<a href='album.php?offset=$local_count'>$local_count2</a>";
								if($offset == $local_count){
									$page_links .= "</b>&nbsp;";
								}else{
									$page_links .= "&nbsp;";
								}
							}
							$local_count +=1;
						}
						
						echo "<BR>$page_links";
					}else{
						echo "&nbsp;&nbsp;&nbsp;・アルバムはありませんでした。<BR>";
					}
				  
				  ?>
				  <BR><BR></TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422">
                  <?php
                    if($uid){
                      echo "<TABLE cellpadding='0' cellspacing='0'>
                             <TBODY>
                                <TR>
                                 <TD>
                                   <FORM method=post enctype=multipart/form-data action='new_album.php'>
                                   <INPUT type='submit' value='新規作成'></FORM>
                                 </TD>
                                </TR>
                             </TBODY>
                            </TABLE>";
                    }
                  ?>
                  </TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
                  <HR width='420'><A href='javascript:history.back()'>戻る</A><BR><BR><BR>
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