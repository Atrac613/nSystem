<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "ml_common.php";
require_once "memberlist_inc.php";

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

$STYLE = load_style(3,0);

if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,9)){
		die("Access Denied");
	}
}

$modify = $_POST["modify"];
if($modify){

$arrowext = array('gif');
$limitk	= 3072;		//アップロード制限（KB キロバイト）
$putdir = "./img/";

	$upfile_size=$_FILES["uf_diary"]["size"];
	$upfile_name=$_FILES["uf_diary"]["name"];
	$upfile=$_FILES["uf_diary"]["tmp_name"];
			
	if($upfile_name != ""){
		$newname = "diary.gif";
		$pos = strrpos($upfile_name,".");	//拡張子取得
		$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		$ext = strtolower($ext);//小文字化
		if(!in_array($ext, $arrowext)){
			sub_msg("","","拡張子エラー","その拡張子ファイルはアップロードできません");
		}
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
		$nowsize = intval( $upfile_size /1024 );
			sub_msg("","","ファイルサイズエラー","最大アップ容量は... $limitk kb です<br>現在のファイルサイズは... $nowsize kb です");
		}

		move_uploaded_file($upfile, $putdir.$upfile_name);
		rename($putdir.$upfile_name, $putdir.$newname);
	}
	
	$upfile_size=$_FILES["uf_diary_up"]["size"];
	$upfile_name=$_FILES["uf_diary_up"]["name"];
	$upfile=$_FILES["uf_diary_up"]["tmp_name"];
	if($upfile_name != ""){
		$newname = "diary_up.gif";
		$pos = strrpos($upfile_name,".");	//拡張子取得
		$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		$ext = strtolower($ext);//小文字化
		if(!in_array($ext, $arrowext)){
			sub_msg("","","拡張子エラー","その拡張子ファイルはアップロードできません");
		}
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
		$nowsize = intval( $upfile_size /1024 );
			sub_msg("","","ファイルサイズエラー","最大アップ容量は... $limitk kb です<br>現在のファイルサイズは... $nowsize kb です");
		}

		move_uploaded_file($upfile, $putdir.$upfile_name);
		rename($putdir.$upfile_name, $putdir.$newname);
	}
	
	$upfile_size=$_FILES["uf_bazaar"]["size"];
	$upfile_name=$_FILES["uf_bazaar"]["name"];
	$upfile=$_FILES["uf_bazaar"]["tmp_name"];
	if($upfile_name != ""){
		$newname = "bazaar.gif";
		$pos = strrpos($upfile_name,".");	//拡張子取得
		$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		$ext = strtolower($ext);//小文字化
		if(!in_array($ext, $arrowext)){
			sub_msg("","","拡張子エラー","その拡張子ファイルはアップロードできません");
		}
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
		$nowsize = intval( $upfile_size /1024 );
			sub_msg("","","ファイルサイズエラー","最大アップ容量は... $limitk kb です<br>現在のファイルサイズは... $nowsize kb です");
		}

		move_uploaded_file($upfile, $putdir.$upfile_name);
		rename($putdir.$upfile_name, $putdir.$newname);
	}
	
	$default_table = intval($_POST["default_table"]);
	$diary_res = intval($_POST["diary_res"]);
	$show_max = intval($_POST["show_max"]);
	$reg_mode = intval($_POST["reg_mode"]);
	$reg_pass = $_POST["reg_pass"];
	$class_edit = intval($_POST["class_edit"]);
	$anon_mode = intval($_POST["anon_mode"]);
	$img_allow = intval($_POST["img_allow"]);
	$oekaki_mode = intval($_POST["oekaki_mode"]);
	$info_title = $_POST["info_title"];
	$info_body = $_POST["info_body"];
	
	$info_body = str_replace("\r\n", "\r", $info_body);
	$info_body = str_replace("\r", "\n", $info_body);
	
	$sql = "replace INTO `MEMBER_LIST_ENV` VALUES ('1','$default_table','$diary_res','$show_max','$reg_mode','$reg_pass','$class_edit','$anon_mode','$img_allow','$oekaki_mode','$info_title','$info_body')";
				
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sta_msg = "登録しました!!";
}

	$env_rows = load_env();

	$diary_res = $env_rows["diary_res"];
	$show_max = $env_rows["show_max"];

	$reg_mode = $env_rows["reg_mode"];
	$reg_pass = $env_rows["reg_pass"];
	
	$class_edit = $env_rows["class_edit"];
	$anon_mode = $env_rows["anon_mode"];
	
	$default_table = $env_rows["default_table"];
	$img_allow = $env_rows["img_allow"];
	
	$oekaki_mode = $env_rows["oekaki_mode"];
	
	$info_title = $env_rows["info_title"];
	$info_body = $env_rows["info_body"];

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
            <TD class="row_title" height="34"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
            <TD class="row_title" height="34" width="131">FINAL FANTASY XI<BR>
            <?php echo "$STYLE[site_name]"; ?></TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10" class="color2">&nbsp;</TD>
            <TD class="color2" height="34" width="200">&nbsp;Root Tool</TD>
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
                  <TD colspan="2" width="422"></TD>
                  <TD rowspan="5" align="right" width="148" valign="top">
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
<form method=post enctype='multipart/form-data' action=list_setting.php>
<TABLE width="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD colspan="2">メンバーリスト設定</TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD>日記画像<IMG src="img/diary.gif" width=15 height=15></TD>
      <TD><input type='file' name='uf_diary'></TD>
    </TR>
    <TR>
      <TD>日記UP画像<IMG src="img/diary_up.gif" width=13 heigh=9></TD>
      <TD><input type='file' name='uf_diary_up'></TD>
    </TR>
    <TR>
      <TD>バザー画像<IMG src="img/bazaar.gif" width=15 heigh=15></TD>
      <TD><input type='file' name='uf_bazaar'></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD>デフォルトテーブル</TD>
      <TD><SELECT name="default_table"><?php
for($i=0;$i<count($MENU);$i++){
	if($i == $default_table){
		echo "<option value=$i selected>$MENU[$i]</option>\n";
	}else{
		echo "<option value=$i>$MENU[$i]</option>\n";
	}
}
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>日記へのレス</TD>
      <TD><SELECT name="diary_res"><?php
	  
		if($diary_res){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>拒否</option>";
		echo "<option value=1 $sel1>許可</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>日記最大表示数</TD>
      <TD><input type=text name="show_max" size=3 value="<?php echo "$show_max"; ?>"></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2"></TD>
    </TR>
    <TR>
      <TD>登録モード</TD>
      <TD><SELECT name="reg_mode"><?php
	  	
		$sel0="";
		$sel1="";
		$sel2="";
		
		if($reg_mode == 1){
			$sel1 = "selected";
		}elseif($reg_mode == 2){
			$sel2 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>登録不可</option>";
		echo "<option value=1 $sel1>登録許可</option>";
		echo "<option value=2 $sel2>登録許可(要パスワード)</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>登録時パスワード</TD>
      <TD><input type=text name="reg_pass" size=10 value="<?php echo "$reg_pass"; ?>"></TD>
    </TR>
    <TR>
      <TD>クラスの編集</TD>
      <TD><SELECT name="class_edit"><?php
	  
		$sel0="";
		$sel1="";
		$sel2="";
		
		if($class_edit){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>拒否</option>";
		echo "<option value=1 $sel1>許可</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>アノン可否</TD>
      <TD><SELECT name="anon_mode"><?php
		$sel0="";
		$sel1="";
		$sel2="";
		if($anon_mode){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>拒否</option>";
		echo "<option value=1 $sel1>許可</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>日記の画像可否</TD>
      <TD><SELECT name="img_allow"><?php
		$sel0="";
		$sel1="";
		$sel2="";
		if($img_allow){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>拒否</option>";
		echo "<option value=1 $sel1>許可</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>お絵かき可否</TD>
      <TD><SELECT name="oekaki_mode"><?php
		$sel0="";
		$sel1="";
		$sel2="";
		if($oekaki_mode){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>拒否</option>";
		echo "<option value=1 $sel1>許可</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>インフォメーション<BR>(タイトル)</TD>
      <TD><input type=text name="info_title" size=20 value="<?php echo "$info_title"; ?>"></TD>
    </TR>
    <TR>
      <TD>インフォメーション<BR>(本文)</TD>
      <TD><TEXTAREA name="info_body" rows=5 cols=40><?php echo "$info_body"; ?></TEXTAREA></TD>
    </TR>
    <TR>
      <TD><input type=submit value=Modify name=modify></TD>
      <TD></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
<BR>
				</TD></TR><TR><TD colspan="2" width="422" valign="top"></TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				*「Modify」を押すと送信され、上部に「登録しました!! 」と表示されるまでお待ちください。表示された場合は登録完了です。<BR><BR>
				*インフォメーションはタイトルを入力することで有効になります。
				<BR><BR>
				*日記画像、日記UP画像、バザー画像は<b>.gif</b>のみアップできます。サイズはサンプルをご覧ください。
				
				<BR>
				<BR><HR width='420'><A href='javascript:history.back()'>戻る</A><BR><BR><BR>
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