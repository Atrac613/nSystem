<?php
//ライブラリ呼び出し
require_once "db_setting.php";
require_once "php_inc.php";
require_once "right_menu_inc.php";

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

$STYLE = load_style(2,0);

$mode = $_GET["mode"];
if($mode == "wb"){
	$mode_str="$db_name";
}else{
	$mode_str="FINALFANTASY XI Official";
}

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
<SCRIPT language="JavaScript" src="popup.js"></SCRIPT>
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
            <TD class="color2" height="34" width="200">&nbsp;TOP</TD>
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
				<?php
					right_menu();
				?>
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422" valign="top"><BR>NEWS<BR>
				  
<FIELDSET>
                  <LEGEND class="blockTitle">&nbsp;<?php echo $mode_str; ?>&nbsp;</LEGEND>
                  <DIV class="blockContent">
				  <?php
				  
				 
				if($mode == "wb"){
					$sql = "select * from `PHP_I-N` where `i-n` = '1' order by `date` desc limit $offset , $showmax";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$id = $user_rows["id"];
							$title = $user_rows["title"];
							$body = $user_rows["body"];
							$date = $user_rows["date"];
							$date = gmdate("y/m/d (D) H:i", $date+9*60*60);
							$body = str_replace("\n", "<br>", $body);
						
							echo "&nbsp;&nbsp;&nbsp;<A href='news_pc.php?id=$id'>・$title ( $date )</A><BR>";
						}
						$sql = "select * from `PHP_I-N` where `i-n` = '1'";
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
								$page_links .="<a href='old_news.php?mode=official&offset=$local_count'>$local_count2</a>";
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
						echo "&nbsp;&nbsp;&nbsp;・Newsはありません。<BR>";
					}
				}else{
					
					$db2 = db_init2();
					
					$sql = "select * from `FFXI_UPD` order by `line` desc limit $offset , $showmax";
					$result = $db2->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$title = $user_rows["title"];
							$url0 = $user_rows["url"];
                            $url = "http://www.playonline.com/".$url0;
							$time = $user_rows["time"];
							
							$mb_count = mb_strwidth($title);
							

							$new_news = new_news($time);
							if($new_news && $mb_count > 57){
								$title = mb_substr($title,0,48);
								$title .= "...";
							}
							/*h
                            if($time + 86400*$show_limit_time < time()){
                                echo "&nbsp;&nbsp;&nbsp;<A href='ffxi_news.php?url=$url0'>■</a> <A href='$url' target='_blank'>$title</a><BR>";
                            }else{
                                echo "&nbsp;&nbsp;&nbsp;<A href='ffxi_news.php?url=$url0'>■</a> <A href='$url' target='_blank'>$title</a>&nbsp;<font color='red'>new!!</font><BR>";
                            }
							*/
							echo "&nbsp;&nbsp;&nbsp;<A href='ffxi_news.php?url=$url0'>■</a> <A href='$url' target='_blank'>$title</a> $new_news<BR>";
						}
						
						$sql = "select * from `FFXI_UPD`";
						$result = $db2->query($sql);
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
								$page_links .="<a href='old_news.php?mode=official&offset=$local_count'>$local_count2</a>";
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
						echo "&nbsp;&nbsp;&nbsp;・Newsはありません。<BR>";
					}
				}
				  
				  ?>
				  </DIV></FIELDSET>
				  
				  
				  </TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top">
				  <BR><HR width='420'><A href='javascript:history.back()'>戻る</A></TD>
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