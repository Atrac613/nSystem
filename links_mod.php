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

if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,2)){
		die("Access Denied");
	}
}

$f_id = $_POST["id"];
if($f_id == ""){
    $f_id = $_GET["id"];
}
$f_id = intval($f_id);
if($f_id){
    $sql = "select * from `PHP_LINKS` where `id` = '$f_id'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$f_uid = $user_rows["uid"];
    if($f_uid){
        if($f_uid != $uid){
            die("Access Denied");
        }
    }else{
        die("Access Denied");
    }
}else{
    die("Access Denied");
}

$f_mode = $_POST["f_mode"];
if($f_mode && $uid){
    $f_name = $_POST["name"];
    $f_jenre = $_POST["jenre"];
    $f_title = $_POST["title"];
    $f_url = $_POST["url"];
    $f_comment = $_POST["comment"];
	$f_del_link = $_POST["del_link"];
	
    $f_jenre = intval($f_jenre);
    $f_comment = str_replace("\r", "\n", $f_comment);
	$f_comment = htmlspecialchars($f_comment);
    $f_title = htmlspecialchars($f_title);
    $date = time();
    
	if($f_del_link){
		$sql = "delete from `PHP_LINKS` where `id` = '$f_id'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
		  trigger_error($result->getMessage(), E_USER_ERROR);
	    }
	    //$sta_msg = "削除しました";
		sub_msg("5","links.php","削除完了","削除しました。自動的に戻ります。");
	
	}else{
	
    if($f_name && $f_title && $f_url && $f_comment){
 	   $sql = "REPLACE INTO `PHP_LINKS` VALUES ('$f_id','$uid', '$f_jenre', '$f_url', '$f_title', '$f_comment', '$f_name', '$date')";
	   $result = $db->query($sql);
	   if (DB::isError($result)) {
		  trigger_error($result->getMessage(), E_USER_ERROR);
       }
       $sta_msg = "登録しました!!";
       add_news('5',"","$f_name");
    }
	
	if($f_id){
	    $sql = "select * from `PHP_LINKS` where `id` = '$f_id'";
		$result = $db->query($sql);
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$f_uid = $user_rows["uid"];
	    if($f_uid){
	        if($f_uid != $uid){
	            die("Access Denied");
	        }
	    }else{
	        die("Access Denied");
	    }
	}else{
	    die("Access Denied");
	}
	
	}
}


if($f_uid){
	$h_id = $user_rows["id"];
    $h_jenre = $user_rows["jenre"];
    $h_title = $user_rows["title"];
    $h_url = $user_rows["url"];
    $h_comment = $user_rows["comment"];
    $h_name = $user_rows["name"];
    $h_date = $user_rows["date"];
    $h_date = substr($h_date,2,8);
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
                  <TD colspan="2" width="422">
                  <?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?>
                  <BR>お勧めリンク追加してください!!</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <TABLE>
  <TBODY>
    <TR>
      <TD>名前</TD>
      <TD>
      <FORM method="post" enctype="multipart/form-data" action="links_mod.php">
      <INPUT size="20" type="text" name="name" value="<?php echo "$h_name"; ?>">
      </TD>
    </TR>
    <TR>
      <TD>ジャンル</TD>
      <TD>
      <select name="jenre">
      <option disabled>ジャンルを選択</option>
      <option value="0" <?php if($h_jenre == 0){ echo "selected"; } ?>>ニュース</option>
      <option value="1" <?php if($h_jenre == 1){ echo "selected"; } ?>>情報</option>
      <option value="2" <?php if($h_jenre == 2){ echo "selected"; } ?>>ギルド</option>
      <option value="3" <?php if($h_jenre == 3){ echo "selected"; } ?>>お気に入り</option>
      <option value="4" <?php if($h_jenre == 4){ echo "selected"; } ?>>その他</option>
      </select>
      </TD>
    </TR>
    <TR>
      <TD>タイトル</TD>
      <TD>
      <INPUT size="60" type="text" name="title" value="<?php echo "$h_title"; ?>">
      </TD>
    </TR>
    <TR>
      <TD>URL</TD>
      <TD>
      <INPUT size="60" type="text" name="url" value="<?php echo "$h_url"; ?>">
      </TD>
    </TR>
    <TR>
      <TD>コメント</TD>
      <TD>
      <INPUT size="60" type="text" name="comment" value="<?php echo "$h_comment"; ?>">
      </TD>
    </TR>
    <TR>
      <TD>削除</TD>
      <TD>
      <INPUT type="checkbox" name="del_link">
      </TD>
    </TR>
    <TR>
      <TD>送信</TD>
      <TD>
      <input type=hidden name='id' value='<?php echo "$f_id"; ?>'>
      <INPUT type="submit" name="f_mode" value="submit">
      </TD>
    </TR></FORM>
  </TBODY>
</TABLE>
				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"><BR>*「submit」を押すと送信され、上部に「登録しました!! 」と表示されるまでお待ちください。表示された場合は登録完了です。</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
                  <HR><A href='javascript:history.back()'>戻る</A>
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