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

$STYLE = load_style(7,0);


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
		$max_news = "5";
		$show_limit_time = "1";
	}
	
}else{
	$max_news = "5";
	$show_limit_time = "1";
}

$limit_time_h = $show_limit_time * 24;

$m = $_GET["m"];
if($m == "rdn" ){
	$sql = "select * from `PHP_LINKS`";
	$result = $db->query($sql);
	$chk = $result->numRows();
	
	if($chk){
		$chk = $chk - 1;
		$rdn = mt_rand(0,$chk);
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC,$rdn);
		//$id = $user_rows["id"];
		$rdn_url = $user_rows["url"];
	
		//var_dump($rdn,$user_rows);
		
		//sub_msg("5","$rdn_url","ランダムリンク","すでにその名前は登録済みです。");
		if($rdn_url){
			$reload_str ="<META HTTP-EQUIV='Refresh' CONTENT='0;URL=$rdn_url'>";
		}
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<TITLE><?php echo "$STYLE[site_name]"; ?></TITLE>
<?php echo "$reload_str"; ?>
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
            <TD class="color2" height="34" width="200">&nbsp;Links</TD>
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
                  <TD colspan="2" width="422"><BR>FFXI関係のリンク集です。</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  &nbsp;&nbsp;・<A href="#0">ニュース</A><BR>
                  &nbsp;&nbsp;・<A href="#1">情報</A><BR>
                  &nbsp;&nbsp;・<A href="#2">ギルド</A><BR>
                  &nbsp;&nbsp;・<A href="#3">お気に入り</A><BR>
                  &nbsp;&nbsp;・<A href="#4">その他</A><BR><BR>
				  &nbsp;&nbsp;・<A href="?m=rdn">ランダムリンク</A><BR><BR>
				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"><HR></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
				  <?php
                    echo "<A name=\"0\"></A>&nbsp;&nbsp;--ニュース--<BR>";
					$sql = "select * from `PHP_LINKS` where `genre` = '0' order by `id`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$id = $user_rows["id"];
							$l_uid = $user_rows["uid"];
                            $url = $user_rows["url"];
							$title = $user_rows["title"];
							$comment = $user_rows["comment"];
                            $name = $user_rows["name"];
							$time = $user_rows["date"];
							$date = gmdate("y/m/d D H:i", $time+9*60*60);
							$body = str_replace("\n", "<br>", $comment);
							
							if($time + 86400*$show_limit_time > time()){
								$new ="<font color='red'>new!!</font>";
							}else{
								$new="";
							}

							echo "&nbsp;&nbsp;&nbsp;・<A href='$url'>$title</A><BR>";
                            echo "&nbsp;&nbsp;&nbsp;　→$comment<BR>";
                            echo "&nbsp;&nbsp;&nbsp;　登録者：$name さん ($date $new)<BR>";
                            if($uid){
                                if($uid == $l_uid){
                                    echo "&nbsp;&nbsp;&nbsp;　<A href='links_mod.php?id=$id'>このリンクを編集</a><BR><BR>";
                                }else{
                                    echo "<BR>";
                                }
                            }else{
                                echo "<BR>";
                            }
						}
					}else{
						echo "&nbsp;&nbsp;&nbsp;・まだ登録されていません。<BR><BR>";
					}
     
                    echo "<A name=\"1\"></A>&nbsp;&nbsp;--情報--<BR>";
					$sql = "select * from `PHP_LINKS` where `genre` = '1' order by `id`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$id = $user_rows["id"];
							$l_uid = $user_rows["uid"];
                            $url = $user_rows["url"];
							$title = $user_rows["title"];
							$comment = $user_rows["comment"];
                            $name = $user_rows["name"];
							$time = $user_rows["date"];
							$date = gmdate("y/m/d D H:i", $time+9*60*60);
							$body = str_replace("\n", "<br>", $comment);
							
							if($time + 86400*$show_limit_time > time()){
								$new ="<font color='red'>new!!</font>";
							}else{
								$new="";
							}

							echo "&nbsp;&nbsp;&nbsp;・<A href='$url'>$title</A><BR>";
                            echo "&nbsp;&nbsp;&nbsp;　→$comment<BR>";
                            echo "&nbsp;&nbsp;&nbsp;　登録者：$name さん ($date $new)<BR>";
                            if($uid){
                                if($uid == $l_uid){
                                    echo "&nbsp;&nbsp;&nbsp;　<A href='links_mod.php?id=$id'>このリンクを編集</a><BR><BR>";
                                }else{
                                    echo "<BR>";
                                }
                            }else{
                                echo "<BR>";
                            }
						}
					}else{
						echo "&nbsp;&nbsp;&nbsp;・まだ登録されていません。<BR><BR>";
					}

                    echo "<A name=\"2\"></A>&nbsp;&nbsp;--ギルド--<BR>";
					$sql = "select * from `PHP_LINKS` where `genre` = '2' order by `id`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$id = $user_rows["id"];
							$l_uid = $user_rows["uid"];
                            $url = $user_rows["url"];
							$title = $user_rows["title"];
							$comment = $user_rows["comment"];
                            $name = $user_rows["name"];
							$time = $user_rows["date"];
							$date = gmdate("y/m/d D H:i", $time+9*60*60);
							$body = str_replace("\n", "<br>", $comment);
							
							if($time + 86400*$show_limit_time > time()){
								$new ="<font color='red'>new!!</font>";
							}else{
								$new="";
							}

							echo "&nbsp;&nbsp;&nbsp;・<A href='$url'>$title</A><BR>";
                            echo "&nbsp;&nbsp;&nbsp;　→$comment<BR>";
                            echo "&nbsp;&nbsp;&nbsp;　登録者：$name さん ($date $new)<BR>";
                            if($uid){
                                if($uid == $l_uid){
                                    echo "&nbsp;&nbsp;&nbsp;　<A href='links_mod.php?id=$id'>このリンクを編集</a><BR><BR>";
                                }else{
                                    echo "<BR>";
                                }
                            }else{
                                echo "<BR>";
                            }
						}
					}else{
						echo "&nbsp;&nbsp;&nbsp;・まだ登録されていません。<BR><BR>";
					}

                    echo "<A name=\"3\"></A>&nbsp;&nbsp;--お気に入り--<BR>";
					$sql = "select * from `PHP_LINKS` where `genre` = '3' order by `id`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$id = $user_rows["id"];
							$l_uid = $user_rows["uid"];
                            $url = $user_rows["url"];
							$title = $user_rows["title"];
							$comment = $user_rows["comment"];
                            $name = $user_rows["name"];
							$time = $user_rows["date"];
							$date = gmdate("y/m/d D H:i", $time+9*60*60);
							$body = str_replace("\n", "<br>", $comment);
							
							if($time + 86400*$show_limit_time > time()){
								$new ="<font color='red'>new!!</font>";
							}else{
								$new="";
							}

							echo "&nbsp;&nbsp;&nbsp;・<A href='$url'>$title</A><BR>";
                            echo "&nbsp;&nbsp;&nbsp;　→$comment<BR>";
                            echo "&nbsp;&nbsp;&nbsp;　登録者：$name さん ($date $new)<BR>";
                            if($uid){
                                if($uid == $l_uid){
                                    echo "&nbsp;&nbsp;&nbsp;　<A href='links_mod.php?id=$id'>このリンクを編集</a><BR><BR>";
                                }else{
                                    echo "<BR>";
                                }
                            }else{
                                echo "<BR>";
                            }
						}
					}else{
						echo "&nbsp;&nbsp;&nbsp;・まだ登録されていません。<BR><BR>";
					}

                    echo "<A name=\"4\"></A>&nbsp;&nbsp;--その他--<BR>";
					$sql = "select * from `PHP_LINKS` where `genre` = '4' order by `id`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$id = $user_rows["id"];
							$l_uid = $user_rows["uid"];
                            $url = $user_rows["url"];
							$title = $user_rows["title"];
							$comment = $user_rows["comment"];
                            $name = $user_rows["name"];
							$time = $user_rows["date"];
							$date = gmdate("y/m/d D H:i", $time+9*60*60);
							$body = str_replace("\n", "<br>", $comment);
							
							if($time + 86400*$show_limit_time > time()){
								$new ="<font color='red'>new!!</font>";
							}else{
								$new="";
							}

							echo "&nbsp;&nbsp;&nbsp;・<A href='$url'>$title</A><BR>";
                            echo "&nbsp;&nbsp;&nbsp;　→$comment<BR>";
                            echo "&nbsp;&nbsp;&nbsp;　登録者：$name さん ($date $new)<BR>";
                            if($uid){
                                if($uid == $l_uid){
                                    echo "&nbsp;&nbsp;&nbsp;　<A href='links_mod.php?id=$id'>このリンクを編集</a><BR><BR>";
                                }else{
                                    echo "<BR>";
                                }
                            }else{
                                echo "<BR>";
                            }
						}
					}else{
						echo "&nbsp;&nbsp;&nbsp;・まだ登録されていません。<BR><BR>";
					}
     
                    if($uid){
                       if(usr_level($uid,2)){
                    echo "<BR><HR><TABLE><TBODY><TR><TD>お勧めリンク追加してください!!</TD></TR><TR><TD><FORM method=post enctype=multipart/form-data action='links_reg.php'>
                    <INPUT type='submit' value='追加'><BR><BR> </FORM></TD></TR></TABLE></TBODY>";
                       }
                    }
				  ?>
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