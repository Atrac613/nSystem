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
$STYLE = load_style(0,0);

$mode = $_POST["mode"];
if(!$mode){
	$mode = $_GET["mode"];
}

$id = intval($_GET["id"]);

if($mode == "reg"){
	regist();
}else{
	if($mode == "del"){
		page_del($id);
	}
}

function regist(){
	global $db,$sta_msg;
	
	$id = intval($_POST["id"]);
	$title = $_POST["title"];
	$text = $_POST["text"];
	
	if($id == "0"){
		$id = null;
	}
	
	
	if($title || $text){
		$sql = "REPLACE INTO `PHP_MANUAL` VALUES ('$id', '$title' , '$text')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}

		$sta_msg = "登録しました!!";
	
	}else{
		sub_msg("5","manual.php?mode=allview","エラー","タイトル・本文が未記入又は短すぎます。自動的に戻ります。");
	}
}


function page_allview(){
	global $db;
	
	echo "・[ページ番号] ページタイトル コマンド<BR><BR>";
	
	$sql = "select * from `PHP_MANUAL`";
	$result = $db->query($sql);
	
	while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$id = $tmp_rows["id"];
		$title = $tmp_rows["title"];
		$text = $tmp_rows["text"];
		
		echo "・[$id] $title <a href='manual.php?id=$id'>見る</a> <a href='manual.php?mode=modify&id=$id'>編集</a> <a href='manual.php?mode=del&id=$id'>削除</a><BR>";
		
	}
}

function page_del($id){
	global $db;
	
	if($id != 1){
		$sql = "delete from `PHP_MANUAL` where `id` = '$id'";
		$result = $db->query($sql);
		
		sub_msg("5","manual.php?mode=allview","削除終了","自動的に戻ります。");
	}else{
		sub_msg("5","manual.php?mode=allview","エラー","1ページは削除できません。自動的に戻ります。");
	}
}


function page_modify($id){
	global $db;

	if(!$id){
		echo "モード：新規作成";
	}else{
		echo "モード：編集　> $id ページ";
		
		$sql = "select * from `PHP_MANUAL` where `id` = '$id'";
		$result = $db->query($sql);
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		extract($tmp_rows);
	}
	
	echo "<BR>";

	echo "<form method=post enctype='multipart/form-data' action=manual.php>";
	echo 'タイトル<BR><input type="text" name="title" size="25" value="'.$title.'"><BR><BR>';
	echo 'html<BR><TEXTAREA rows="40" cols="60" name="text">'.$text.'</TEXTAREA>';
	echo "<BR><BR>";
	
	if(!$id){
		echo "<input type=submit value=Submit>";
	}else{
		echo "<input type=submit value=Modify>";
	}
	
	echo "<input type=hidden name=mode value=reg><input type=hidden name=id value=$id></form>";
}


function page_view($id){
	global $db;
	
	if(!$id){
		$id = 1;
	}

	$sql = "select * from `PHP_MANUAL` where `id` = '$id'";
	$result = $db->query($sql);
	$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	
	$chk = $result->numRows();
	if($chk){
		$title = $tmp_rows["title"];
		$text = $tmp_rows["text"];
		
		echo "<B>$title</B><BR><BR>";
		echo "$text";
	}else{
		echo "No Manual";
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
            <TD class="color2" height="34" width="200">&nbsp;マニュアル</TD>
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
                  <TD colspan="2" width="422"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top">
                  <TABLE>
                    <TBODY>
                     <TR>
                       <TD valign="middle" height="12"></TD>
                       <TD>
                  <BR>
				  <?php
					if($mode == "allview" || $mode == "reg"){
						page_allview();
					}elseif($mode == "modify"){
						page_modify($id);
					}else{
						page_view($id);
					}
				  ?>
                       </TD>
                       </TR>
                     </TBODY>
                     </TABLE>
					 
				<?php
				
				if($uid){
					if(usr_level($uid,9)){
					echo '<BR><BR>
                  <FORM method="post" enctype="multipart/form-data" action="manual.php">
				  <input type=hidden name=mode value=modify>
                  <INPUT type="submit" value="新規作成"></FORM>';
				  
					echo '
                  <FORM method="post" enctype="multipart/form-data" action="manual.php">
				  <input type=hidden name=mode value=allview>
                  <INPUT type="submit" value="編集"></FORM>';
					}
				}
				
				?>
					 
                     <BR><HR width='420'><A href='javascript:history.back()'>戻る</A><P></P>
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