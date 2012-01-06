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
$f_mode = $_POST["f_mode"];
if($f_mode && $uid){
    $f_name = $_POST["name"];
    $f_jenre = $_POST["jenre"];
    $f_title = $_POST["title"];
    $f_url = $_POST["url"];
    $f_comment = $_POST["comment"];
	
    $f_jenre = intval($f_jenre);
    $f_comment = str_replace("\r", "\n", $f_comment);
	$f_comment = htmlspecialchars($f_comment);
    $f_title = htmlspecialchars($f_title);
    $date = time();
	//var_dump($f_name, $f_jenre , $f_title , $f_url , $f_comment);
    
    if($f_name && $f_title && $f_url && $f_comment){
 	   $sql = "REPLACE INTO `PHP_LINKS` VALUES ('','$uid', '$f_jenre', '$f_url', '$f_title', '$f_comment', '$f_name', '$date')";
	   $result = $db->query($sql);
	   if (DB::isError($result)) {
		  trigger_error($result->getMessage(), E_USER_ERROR);
       }
       $sta_msg = "登録しました!!";
       add_news('5',"","$f_name");
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
      <FORM method="post" enctype="multipart/form-data" action="links_reg.php">
      <INPUT size="20" type="text" name="name" value="<?php echo "$c_name"; ?>">
      </TD>
    </TR>
    <TR>
      <TD>ジャンル</TD>
      <TD>
      <select name="jenre">
      <option disabled selected>ジャンルを選択</option>
      <option value="0">ニュース</option>
      <option value="1">情報</option>
      <option value="2">ギルド</option>
      <option value="3">お気に入り</option>
      <option value="4">その他</option>
      </select>
      </TD>
    </TR>
    <TR>
      <TD>タイトル</TD>
      <TD>
      <INPUT size="60" type="text" name="title">
      </TD>
    </TR>
    <TR>
      <TD>URL</TD>
      <TD>
      <INPUT size="60" type="text" name="url" value="http://">
      </TD>
    </TR>
    <TR>
      <TD>コメント</TD>
      <TD>
      <INPUT size="60" type="text" name="comment">
      </TD>
    </TR>
    <TR>
      <TD>送信</TD>
      <TD>
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
