<?php

  set_time_limit(2);

//ライブラリ呼び出し
require_once "db_setting.php";
require_once "php_inc.php";
require_once "forum/forum_lib.php";
require_once "mognet_common.php";
//require_once "poll_inc.php";
require_once "right_menu_inc.php";

$db = db_init();

//page chk
page_mode();



$wbcookie= $_COOKIE["$db_name"];
//var_dump($wbcookie);
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

$STYLE = load_style(1,1);
//var_dump($STYLE);

function scan_phplog($line_id){
    $file = file('/var/log/php/php.log');
    $line = count($file);
    $file = end($file);
    if($line > $line_id){
      echo "<BR><BR><BR>
      <TABLE cellpadding=\"0\" cellspacing=\"0\">
        <TBODY>
          <TR>
            <TD height=\"34\" width=\"550\" colspan=\"2\" class=\"color2\">";
        echo "<font color='red'><b>Warning</b></font><BR>";
        echo "<B>Unable to continue because of a PHP_CORE or System error.<BR>";
        echo "Sorry, but this error is unrecoverable.</B><BR>";
        echo "Line : $line<BR>";
        echo "$file";
            echo "</TD>
          </TR>
          <TR>
            <TD>&nbsp;</TD>
          </TR>
        </TBODY>
      </TABLE>";

    }

}

function chk_post($c_name){
	global $db,$mog_net_group,$mog_net_domain;
	
	$send_user_mail = $c_name."@".$mog_net_group.".".$mog_net_domain;
	
	$sql = "select * from `POST_IN` where `receive` = '$send_user_mail' and `chk` = '0'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$post_rows = $result->numRows();
	
	if($post_rows == 0){
		return false;
	}else{
		return true;
	}
}

function access_count(){
	global $db;
	
	$sql = "select * from `ACCESS_COUNT`";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$row = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$total = $row["total"];
	
	$total = $total+1;
	
	$sql = "REPLACE INTO `ACCESS_COUNT` VALUES ('1','0','0','$total')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	return $total;
	
}

$total = access_count();

$max_news = $STYLE["max_news"];
$show_limit_time = $STYLE["limit_day"];
$limit_time_h = $show_limit_time * 24;

//sub_msg("10","readme.php","error","test");
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
                  <TD colspan="2" width="422" valign="top"><BR><DIV style="padding: 5px;"><FIELDSET>
                  <LEGEND class="blockTitle">NEWS</LEGEND>
                  <DIV class="blockContent">
                  &nbsp;&nbsp;-<?php echo "$STYLE[site_name]"; ?>-<BR>
				  <?php
					$sql = "select * from `PHP_I-N` where `i-n` = '1' and `sta` = '0' order by `date` desc";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$id = $user_rows["id"];
							$title = $user_rows["title"];
							$body = $user_rows["body"];
							$time = $user_rows["date"];
							
							$date = gmdate("y/m/d D H:i", $time+9*60*60);
							$body = str_replace("\n", "<br>", $body);
							
							$new_news = new_news($time);
                            echo "&nbsp;&nbsp;&nbsp;<A href='news_pc.php?id=$id'>・$title ( $date )</A>&nbsp;$new_news<BR>";

						}
					}else{
						echo "&nbsp;&nbsp;&nbsp;・Newsはありません。<BR>";
					}

				  ?>
				  &nbsp;&nbsp;&nbsp;→<A href='old_news.php?mode=wb'>過去のニュース</a><BR>
                  <BR>&nbsp;&nbsp;-FINALFANTASY XI Official-<BR>
                  <?php
					
					$db2 = db_init2();
					
					$sql = "select * from `FFXI_UPD` order by `time` desc limit 0,5";
					$result = $db2->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$title = $user_rows["title"];
							$url = $user_rows["url"];
                            $url = "http://www.playonline.com/".$url;
							$time = $user_rows["time"];
							$mb_count = mb_strwidth($title);
							

							$new_news = new_news($time);
							if($new_news && $mb_count > 55){
								$title = mb_substr($title,0,48);
								$title .= "...";
							}else{
								if($mb_count > 57){
									$title = mb_substr($title,0,54);
									$title .= "...";
								}
							}
							
							echo "&nbsp;&nbsp;&nbsp;<A href='$url' target='_blank'>$title</a>&nbsp;$new_news<BR>";
						}
					}else{
						echo "&nbsp;&nbsp;&nbsp;・Newsはありません。<BR>";
					}
                  ?>
				  &nbsp;&nbsp;&nbsp;→<A href='old_news.php?mode=official'>過去のニュース</a><BR>
                  </DIV></FIELDSET></DIV>
				  
				  
				  
<DIV style="padding: 5px;">
                  <FIELDSET><LEGEND class="blockTitle">INFO</LEGEND>
                  <DIV class="blockContent">
				  <?php
					$sql = "select * from `PHP_I-N` where `i-n` = '0' and `sta` = '0' order by `date` desc";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$body = $user_rows["body"];
							$date = $user_rows["date"];
							$new_news = new_news($date);
							$date = gmdate("y/m/d (D) H:i", $date+9*60*60);
							$body = str_replace("\n", "<br>&nbsp;&nbsp;&nbsp;&nbsp;", $body);

							echo "&nbsp;&nbsp;&nbsp;・$body <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$date $new_news<BR><BR>";
						}
					}else{
						echo "&nbsp;&nbsp;&nbsp;・Infoはありません。<BR>";
					}
					
					if(isset($uid)){
						if(chk_post($c_name)){
							echo "&nbsp;&nbsp;&nbsp;・モーグリ <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; お手紙がきてるクポ!!<BR><BR>";
						}
					}
					
					$show_limit_time = $show_limit_time * 86400;
					$limit_time = time() - $show_limit_time;
					//var_dump(time());
					
					
					
					$sql = "select * from `PHP_SITE_NEWS` where `time` > '$limit_time' order by `time` desc LIMIT 0,$max_news";
					//var_dump($sql);
					$tmp_result = $db->query($sql);
					$chk = $tmp_result->numRows();
					//var_dump($chk);
					if($chk){
						echo "&nbsp;&nbsp;&nbsp;・<B>以下サイト内過去$limit_time_h 時間の更新</b><br><br>";
						while( $user_rows = $tmp_result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$area = $user_rows["area"];
							$val = $user_rows["val"];
							$data = $user_rows["data"];
							$time = $user_rows["time"];
							$date = gmdate("y/m/d (D) H:i", $time+9*60*60);
							
							if($area == 0){
								$txt = $data;
							}elseif($area == 1){
								$sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$val'";
								$result = $db->query($sql);
								$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
								$cat_title = $tmp_rows["cat_title"];
								
								if(auth_read_cf("c",$val)){
								
									$txt = "フォーラムでカテゴリーが追加されました。<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									$txt .= "&gt;&gt; <A href='forum/viewcategories.php?c=$val'>$cat_title</A>";
								
								}
								
							}elseif($area == 2){
								$sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$val'";
								$result = $db->query($sql);
								$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
								$forum_name = $tmp_rows["forum_name"];
								$cat_id = $tmp_rows["cat_id"];
								
								if(auth_read_cf("c",$cat_id)){
									if(auth_read_cf("f",$val)){
								
										$txt = "フォーラムでフォーラムが追加されました。<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										$txt .= "&gt;&gt; <A href='forum/viewforum.php?f=$val'>$forum_name</A>";
									}
								}
								
							}elseif($area == 3){
								$sql = "select * from `FORUM_TOPIC` WHERE `topic_id` = '$val'";
								$result = $db->query($sql);
								$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
								$topic_title = $tmp_rows["topic_title"];
								$forum_id = $tmp_rows["forum_id"];
								$c = get_c($forum_id);
								if(auth_read_cf("c",$c)){
									if(auth_read_cf("f",$forum_id)){
										if(auth_read_t($val)){
								
									$txt = "フォーラムでトピックが追加されました。<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									$txt .= "&gt;&gt; <A href='forum/viewtopic.php?t=$val'>$topic_title</A>";
									
										}
								
									}
								}
							}elseif($area == 4){
								$sql = "select * from `FORUM_POSTS` WHERE `post_id` = '$val'";
								$result = $db->query($sql);
								$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
								$topic_id = $tmp_rows["topic_id"];
								$thread_id = $tmp_rows["thread_id"];
								$forum_id = $tmp_rows["forum_id"];
								
								$sql = "select * from `FORUM_POSTS_TXT` WHERE `post_id` = '$val'";
								$result = $db->query($sql);
								$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
								$post_subject = $tmp_rows["post_subject"];
								//var_dump($val);
								$c = get_c($forum_id);
								if(auth_read_cf("c",$c)){
									if(auth_read_cf("f",$forum_id)){
										if(auth_read_t($topic_id)){
										$txt = "フォーラムでレスがありました。<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										$txt .= "&gt;&gt; <A href='forum/viewtopic.php?t=$topic_id#$thread_id'>$post_subject </A>";
										}
									}
								}
							}elseif($area == 5){

								$txt = "$data さんがリンクを追加してくれました!!<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								$txt .= "&gt;&gt; <A href='links.php'>見てみる</A>";

							}elseif($area == 6){

								$txt = "$data さんがアルバムを追加してくれました!!<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								$txt .= "&gt;&gt; <A href='album.php'>見てみる</A>";

							}elseif($area == 7){

								$txt = "$data さんが新しいアンケート追加!!<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								$txt .= "&gt;&gt; <A href='poll.php?mode=show_poll&id=".$val."'>見てみる</A>";

							}else{
								$txt = "error area_code";
							}
							

							//$body = str_replace("\n", "<br>&nbsp;&nbsp;&nbsp;&nbsp;", $body);

							echo "&nbsp;&nbsp;&nbsp;・$txt <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$date<BR><BR>";
						}
					}else{
						//$limit_time_h = $show_limit_time * 24;
						echo "&nbsp;&nbsp;&nbsp;・サイトは過去$limit_time_h 時間内の更新はありません。";
					}

				  ?>
                </DIV></FIELDSET></DIV><BR>
				  
				  <?php
				  echo "&nbsp;Total: $total";
				  ?>
                  </TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top">
                  <BR>
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422" valign="top">
				  
				  
				  
				  </TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
                   <BR>
				  </TD>
                </TR>
              </TBODY>
            </TABLE>
            <?php
			if(find_oracle($uid)){
              scan_phplog(0);
			  }
            ?>
            </TD>
          </TR>
        </TBODY>
      </TABLE>
      </TD>
      <TD width="2" class="color3" background="img/<?php echo "$STYLE[img_right]"; ?>" rowspan="2"><IMG src="/img/spacer.gif" width="25" height="0"></TD>
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
