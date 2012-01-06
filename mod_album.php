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

if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,6)){
		die("Access Denied");
	}
}

$session_id= $_GET["id"];
if(!$session_id){
    $session_id= $_POST["session_id"];
}

$f_mode = $_POST["f_mode"];
if($f_mode =="1" && $uid){
    $del = $_POST["del"];
    $album_del = $_POST["album_del"];
    $album_id = $_POST["album_id"];
    $comment = $_POST["comment"];

    $sql = "select * from `PHP_ALBUM` where `session_id` = '$session_id'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$a_uid = $user_rows["uid"];
    $a_name = $user_rows["name"];

    //$img_dir = "./album/$a_name/$session_id/img/";
    //$sam_dir = "./album/$a_name/$session_id/imgs/";
    $img_dir = "./album/$session_id/img/";
    $sam_dir = "./album/$session_id/imgs/";
    $image = "$a_name"."_"."$album_id";
    
    if($a_uid){
       if($a_uid != $uid){
          die("Access Denied");
       }
    }else{
        //die("Fatal error: E_NOTICE unknown session_id = $session_id");
		
		sub_msg("5","album.php","存在しないセッション","リロードします。");
    }

    if($album_del){
        $sql = "delete from `PHP_ALBUM` where `session_id` = '$session_id' and `uid` = '$uid'";
        $result = $db->query($sql);
        if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
        $ext = ".+\.png$|.+\.jpe?g$";
        $d = dir($img_dir);
        while ($ent = $d->read()) {
           if (eregi($ext, $ent)) {
              $del_files = $img_dir.$ent;
              unlink($del_files);
              $sql = "select * from `PHP_ALBUM_COMMENT` where `img` = '$del_files'";
	          $result = $db->query($sql);
	          $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	          $c_id = $user_rows["id"];
              if($c_id){
                 $sql = "delete from `PHP_ALBUM_COMMENT` where `id` = '$c_id'";
                 $result = $db->query($sql);
                 if (DB::isError($result)) {
                    trigger_error($result->getMessage(), E_USER_ERROR);
                 }
              }
           }
        }
        $d->close();
        $d = dir($sam_dir);
        while ($ent = $d->read()) {
           if (eregi($ext, $ent)) {
              $del_files = $sam_dir.$ent;
              unlink($del_files);
              $sql = "select * from `PHP_ALBUM_COMMENT` where `img` = '$del_files'";
	          $result = $db->query($sql);
	          $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	          $c_id = $user_rows["id"];
              if($c_id){
                 $sql = "delete from `PHP_ALBUM_COMMENT` where `id` = '$c_id'";
                 $result = $db->query($sql);
                 if (DB::isError($result)) {
                    trigger_error($result->getMessage(), E_USER_ERROR);
                 }
              }
           }
        }
        $d->close();
        
        $del_dir = "./album/$session_id/img/";
        rmdir($del_dir);
        $del_dir = "./album/$session_id/imgs/";
        rmdir($del_dir);
        $del_dir = "./album/$session_id/";
        rmdir($del_dir);
        //$sta_msg = "削除しました。";
        sub_msg("5","album.php","削除しました。","リロードします。");
    }elseif($del){
        $sql = "select * from `PHP_ALBUM_COMMENT` where `img` = '$img_dir$image'";
	    $result = $db->query($sql);
	    $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    $c_id = $user_rows["id"];
        //if(!$c_id){
        //    die("Fatal error: E_NOTICE unknown album_id = $album_id & SQL = $img_dir$image");
        //}
        $del_files = $img_dir.$image;
        if(file_exists($del_files)){
            unlink($del_files);
        }
        $del_files = $sam_dir.$image;
        if(file_exists($del_files)){
            unlink($del_files);
        }
        if($c_id){
            $sql = "delete from `PHP_ALBUM_COMMENT` where `id` = '$c_id'";
            $result = $db->query($sql);
            if (DB::isError($result)) {
               trigger_error($result->getMessage(), E_USER_ERROR);
            }
        }
        $sta_msg = "削除しました。";
    }else{
        $sql = "select * from `PHP_ALBUM_COMMENT` where `img` = '$img_dir$image'";
	    $result = $db->query($sql);
	    $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    $c_id = $user_rows["id"];

        $comment = $_POST["comment"];
		//var_dump($comment);
		
        if(!$comment || $comment){
           $comment = htmlspecialchars($comment);
           $sql = "REPLACE INTO `PHP_ALBUM_COMMENT` VALUES ('$c_id', '$uid', '$img_dir$image', '$comment')";
           $result = $db->query($sql);
           if (DB::isError($result)) {
              trigger_error($result->getMessage(), E_USER_ERROR);
           }
        }
        $a_title = $_POST["a_title"];
        if($a_title){
        $a_title = htmlspecialchars($a_title);
        $sql = "UPDATE `PHP_ALBUM` SET `title` = '$a_title' WHERE `session_id` = '$session_id'";
        $result = $db->query($sql);
           if (DB::isError($result)) {
              trigger_error($result->getMessage(), E_USER_ERROR);
           }
        }
        $sta_msg = "編集しました。";
    }

}

if($session_id){
	$sql = "select * from `PHP_ALBUM` WHERE `session_id` = '$session_id'";
	$result = $db->query($sql);
	$chk = $result->numRows();
		if($chk){
			$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			$a_session_id = $user_rows["session_id"];
			$a_title = $user_rows["title"];
			$a_name = $user_rows["name"];
			$a_date = $user_rows["date"];
			//$a_date = substr($a_date,2,8);
			$a_date = gmdate("y/m/d (D) H:i", $a_date+9*60*60);
		}else{
			sub_msg("5","album.php","存在しないセッション","リロードします。");
		}
  $start = $_GET["start"];

  $cols      = 3;
  $page_def  = 21;
    
}else{
    sub_msg("5","album.php","存在しないセッション","リロードします。");
	//die("Fatal error: E_NOTICE");
}
 if($session_id){
    $sql = "select * from `PHP_ALBUM` where `session_id` = '$session_id'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $a_name = $user_rows["name"];
    if($a_name){
        $img_dir = "./album/$session_id/img/";
        $ext = ".+\.png$|.+\.jpe?g$";
        $img_counter = 0;
        $c_pos = 1;

        $d = dir($img_dir);
           while ($ent = $d->read()) {
               if (eregi($ext, $ent)) {

                   if (((($img_counter) % ($page_def)) == 0)){
                       //var_dump($img_counter);
                      $p_pos .= "[<a href=\"$_SERVER[PHP_SELF]?id=$a_session_id&start=$img_counter\">$c_pos</a>] ";
                      $c_pos++;
                   }
                   $img_counter++;
               }

           }
        $d->close();
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<TITLE><?php echo "$STYLE[site_name]"; ?></TITLE>
<?php echo "$STYLE[css]"; ?>

<SCRIPT LANGUAGE="JavaScript">
<!--

function wincf() {
   if(window.confirm("本当に削除しますか?")){
      document.f_albumdel.f_mode.value = 1;
   }
   else{
      document.f_albumdel.f_mode.value = 0;
   }
   document.f_albumdel.submit();
}
//-->
</SCRIPT>
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
                <FORM method=post enctype=multipart/form-data action='mod_album.php'>
                  <TD colspan="2" width="422"><BR>
                  <?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?>
                  <?php echo "編集<INPUT type=\"checkbox\" name=\"f_mode\" value=\"1\">&nbsp;<INPUT size=\"40\" type=\"text\" name=\"a_title\" value=\"$a_title\"><input type=\"hidden\" name=\"session_id\" value=\"$a_session_id\">&nbsp;<INPUT type='submit' value='送信'><br>$a_name さん($a_date)"; ?></TD>
                </TR></FORM>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <table border="0" cellpadding="2"><tr>
                  <?php
                    $img_dir = "./album/$a_session_id/img/";
                    $sam_dir = "./album/$a_session_id/imgs/";
                    // ディレクトリ一覧取得、ソート
                    $ext       = ".+\.png$|.+\.jpe?g$";
                    $d = dir($img_dir);
                    while ($ent = $d->read()) {
                      if (eregi($ext, $ent)) {
                          $files[] = $ent;
                      }
                    }
                    $d->close();
                    // ソート
                    if($files){
                    rsort($files);

                    //ファイル表示開始
                    $maxs = count($files)-1;
                    $ends = $start+$page_def-1;
                    $counter = 0;
                    while (list($line, $filename) = each($files)) {
                      if (($line >= $start) && ($line <= $ends)) {
                          $image = rawurlencode($filename);
                          $pos = strrpos($image,".");	//拡張子取得
                          $sam_img_name = substr($image,0,$pos);
                          $ext = $sam_img_name.".jpg";
                          $picsize = filesize($img_dir.$image);
                          $picsize = intval($picsize / 1024);
                          $pic_alt = $image." $picsize"."[kb]";
                          
             	          $sql = "select * from `PHP_ALBUM_COMMENT` where `img` = '$img_dir$sam_img_name'";
						  //var_dump($sql);
                          $result = $db->query($sql);
                          $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                          $comment = $user_rows["comment"];
                          if(!$comment){
                              $comment = "コメント";
                          }
                              $pos1 = strrpos($image,"_");
                              $pos2 = strrpos($image,".");
                              $id = substr($image,$pos1+1,$pos2-$pos1-1);
                              intval($id);

                             //メインHTML
                             echo <<<EOD
                             <FORM method=post enctype=multipart/form-data action='mod_album.php'>
                             <td  align="center" valign="top"><a href="$img_dir$image" target=_blank>
                             <img src="$sam_dir$ext" border="0" height="129" width="172" alt="$pic_alt"></a><BR>
                             <INPUT size="25" type="text" name="comment" value="$comment"><BR>
                             編集<INPUT type="checkbox" name="f_mode" value="1">&nbsp;削除<INPUT type="checkbox" name="del" value="1">
                             <input type="hidden" name="session_id" value="$a_session_id"><input type="hidden" name="album_id" value="$id"><INPUT type='submit' value='送信'></FORM></TD>
EOD;
                             $counter++;
                             if (((($counter) % $cols) == 0)) echo "</tr><tr>";
                        }
                      }
                      //ﾍﾟｰｼﾞリンク
                      if ($start > 0) {
                        $prevstart = $start - $page_def;
                        $p_prev = "<a href=\"$_SERVER[PHP_SELF]?id=$a_session_id&start=$prevstart\">&lt;&lt;前へ</a>&nbsp;";
                      }else{
                          $p_prev = "&lt;&lt;前へ&nbsp;";
                      }
                      if ($ends < $maxs) {
                        $nextstart = $ends+1;
                        $p_next = "&nbsp;<a href=\"$_SERVER[PHP_SELF]?id=$a_session_id&start=$nextstart\">次へ&gt;&gt;</a>";
                      }else{
                          $p_next = "&nbsp;次へ&gt;&gt;";
                      }
                      echo "</tr></table>";
                      }else{
                          echo "<TD>No Data</TD></TR></table>";
                      }
                      echo "<HR>ページ： $p_prev$p_next |&nbsp;&nbsp;$p_pos<HR><BR>";
                      echo "<TABLE cellpadding='0' cellspacing='0'>
                             <TBODY>
                                <TR>
                                 <TD><FORM method=post enctype=multipart/form-data action='new_album.php'>
                                 <input type=\"hidden\" name=\"a_title\" value=\"$a_title\">
                                 <input type=\"hidden\" name=\"session_id\" value=\"$a_session_id\">
                            <INPUT type='submit' value='写真追加'></FORM></TD>
                                                             <TD>
                                 &nbsp;
                                 </TD>
                                 <TD>";
                      echo "<FORM method=post  name=f_albumdel enctype=multipart/form-data action='mod_album.php'>\n";
                      echo "<input type=\"hidden\" name=\"session_id\" value=\"$a_session_id\">\n";
                      echo "<input type=\"hidden\" name=\"album_del\" value=\"1\">\n";
                      echo "<input type=\"hidden\" name=\"f_mode\" value=\"\">\n";
                      echo "<INPUT type='submit' value='アルバム削除' onClick=wincf()></FORM></TD></TR>
                             </TBODY>
                            </TABLE>";
                  ?>
				 *画像一個ずつで編集します。編集する場合は「編集ボタン」をチェックしてください。<BR>
                      *削除したデータの復旧はできません。<BR><BR></TD>
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
                                 </TD>";
                                     $sql = "select * from `PHP_ALBUM` where `session_id` = '$a_session_id'";
                                     $result = $db->query($sql);
                                     $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                                     $a_uid = $user_rows["uid"];
                                 if($uid == $a_uid){
                                     echo "
                                 <TD>
                                 &nbsp;
                                 </TD>
                                 <TD>
                                   <FORM method=post enctype=multipart/form-data action='mod_album.php'>
                                   <input type=\"hidden\" name=\"session_id\" value=\"$a_session_id\">
                                   <INPUT type='submit' value='リロード'></FORM>
                                 </TD>
                                 ";
                                 }
                                 echo "
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
