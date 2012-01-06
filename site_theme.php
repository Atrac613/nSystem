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

if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,9)){
		die("Access Denied");
	}
}

function form_default(){
	global $db;

	$sql = "select * from `PHP_SITE_THEME`";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	echo "<form method=post enctype='multipart/form-data' action='site_theme.php'>";
	//echo "<input type=hidden name=mode value=modify>";
	echo "<select name='site_theme'>";
	echo "<option value=0 selected>新規作成</option>";
	
	while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$id = $tmp_rows["id"];
		$title = $tmp_rows["title"];
		echo "<option value='$id'>$title</option>";
	}
	
	echo "</select> <input type=submit value='submit'></form>";

}

function form_new(){
	global $db;
	
	$site_theme = intval($_POST["site_theme"]);
	if($site_theme){
		$sql = "select * from `PHP_SITE_THEME` where `id` = '$site_theme'";

		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		
		$chk = $result->numRows();
		if($chk){
			extract($tmp_rows);
		}else{
			$site_theme = 0;
		}
	
	}

$img_dir = "./css/";
					
// ディレクトリ一覧取得、ソート
$ext       = ".+\_def.css$";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_css[] = $ent;
}
}
$d->close();

// ディレクトリ一覧取得、ソート
$ext       = ".+\_big.css$";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_big_css[] = $ent;
}
}
$d->close();

// ディレクトリ一覧取得、ソート
$ext       = ".+\_small.css$";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_small_css[] = $ent;
}
}
$d->close();

//$files=join('<BR>',$files);
//echo "$files<BR><BR>";

$img_dir = "./forum/img/";

// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$ext       = "cellpic";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_cellpic[] = $ent;
}
}
$d->close();
					
// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$ext       = "post";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_post[] = $ent;
}
}
$d->close();
					
// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$ext       = "reply";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_reply[] = $ent;
}
}
$d->close();

// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$ext       = "sta";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_sta[] = $ent;
}
}
$d->close();

// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$img_dir = "./img/";
$ext       = "topimage";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_topimage[] = $ent;
}
}
$d->close();

$ext       = "img_left";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_img_left[] = $ent;
}
}
$d->close();

$ext       = "img_right";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_img_right[] = $ent;
}
}
$d->close();

?>

<form method=post enctype='multipart/form-data' action=site_theme.php>

<?php
$site_theme = intval($_POST["site_theme"]);

if($site_theme != 0){
	echo "<input type=hidden name=site_theme value='$site_theme'>";
}else{
	echo "<input type=hidden name=site_theme value=0>";
	$title = "新規テーマ";
}

?>
<input type=hidden name=mode value=regist>
<TABLE width="100%" cellpadding="0" cellspacing="0">
  <TBODY>
  
    <TR>
      <TD colspan="2">テーマ名</TD>
    </TR>
    <TR>
      <TD colspan="2"><input type=text name=title size=25 value=<?php echo "$title"; ?>></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">CSS設定</TD>
    </TR>
    <TR>
      <TD><a href="./css/theme0_def.css">theme0_def.css</a></TD>
      <TD><?php 
	  
echo "<SELECT name=theme_def>";
for($i=0;$i<count($files_css);$i++){
	$pos=strpos($files_css[$i],'_');
	$val=substr($files_css[$i],5,$pos-5);
	if($val==$theme_def){
		echo "<option value='$val' selected>$files_css[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_css[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./css/theme0_big.css">theme0_big.css</a></TD>
      <TD><?php 
	  
echo "<SELECT name=theme_big>";
for($i=0;$i<count($files_big_css);$i++){
	$pos=strpos($files_big_css[$i],'_');
	$val=substr($files_big_css[$i],5,$pos-5);
	if($val==$theme_big){
		echo "<option value='$val' selected>$files_big_css[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_big_css[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./css/theme0_small.css">theme0_small.css</a></TD>
      <TD><?php 
	  
echo "<SELECT name=theme_small>";
for($i=0;$i<count($files_small_css);$i++){
	$pos=strpos($files_small_css[$i],'_');
	$val=substr($files_small_css[$i],5,$pos-5);
	if($val==$theme_small){
		echo "<option value='$val' selected>$files_small_css[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_small_css[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">サイトイメージ設定</TD>
    </TR>
    <TR>
      <TD><a href="./img/img_left0.gif">img_left0.gif</a></TD>
      <TD><?php 
	  
echo "<SELECT name=img_left>";
for($i=0;$i<count($files_img_left);$i++){
	$pos=strpos($files_img_left[$i],'.');
	$val=substr($files_img_left[$i],8,$pos-8);
	if($val==$img_left){
		echo "<option value='$val' selected>$files_img_left[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_img_left[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./img/img_right0.gif">img_right0.gif</a></TD>
      <TD><?php 
	  
echo "<SELECT name=img_right>";
for($i=0;$i<count($files_img_right);$i++){
	$pos=strpos($files_img_right[$i],'.');
	$val=substr($files_img_right[$i],9,$pos-9);
	if($val==$img_left){
		echo "<option value='$val' selected>$files_img_right[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_img_right[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">フォーラムイメージ設定</TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/cellpic0.gif">cellpic0</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_cellpic0>";
for($i=0;$i<count($files_cellpic);$i++){
	$pos=strpos($files_cellpic[$i],'.');
	$val=substr($files_cellpic[$i],7,$pos-7);
	if($val==$forum_cellpic0){
		echo "<option value=$val selected>$files_cellpic[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_cellpic[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/cellpic1.gif">cellpic1</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_cellpic1>";
for($i=0;$i<count($files_cellpic);$i++){
	$pos=strpos($files_cellpic[$i],'.');
	$val=substr($files_cellpic[$i],7,$pos-7);
	if($val==$forum_cellpic1){
		echo "<option value=$val selected>$files_cellpic[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_cellpic[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/post0.gif">post0</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_post>";
for($i=0;$i<count($files_post);$i++){
	$pos=strpos($files_post[$i],'.');
	$val=substr($files_post[$i],4,$pos-4);
	if($val==$forum_post){
		echo "<option value=$val selected>$files_post[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_post[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/reply0.gif">reply0</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_reply>";
for($i=0;$i<count($files_reply);$i++){
	$pos=strpos($files_reply[$i],'.');
	$val=substr($files_reply[$i],5,$pos-5);
	if($val==$forum_reply){
		echo "<option value=$val selected>$files_reply[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_reply[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/sta0.gif">sta0</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_sta0>";
for($i=0;$i<count($files_sta);$i++){
	$pos=strpos($files_sta[$i],'.');
	$val=substr($files_sta[$i],3,$pos-3);
	if($val==$forum_sta0){
		echo "<option value=$val selected>$files_sta[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_sta[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/sta1.gif">sta1</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_sta1>";
for($i=0;$i<count($files_sta);$i++){
	$pos=strpos($files_sta[$i],'.');
	$val=substr($files_sta[$i],3,$pos-3);
	if($val==$forum_sta1){
		echo "<option value=$val selected>$files_sta[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_sta[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/sta2.gif">sta2</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_sta2>";
for($i=0;$i<count($files_sta);$i++){
	$pos=strpos($files_sta[$i],'.');
	$val=substr($files_sta[$i],3,$pos-3);
	if($val==$forum_sta2){
		echo "<option value=$val selected>$files_sta[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_sta[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/sta3.gif">sta3</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_sta3>";
for($i=0;$i<count($files_sta);$i++){
	$pos=strpos($files_sta[$i],'.');
	$val=substr($files_sta[$i],3,$pos-3);
	if($val==$forum_sta3){
		echo "<option value=$val selected>$files_sta[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_sta[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">トップイメージ設定</TD>
    </TR>
    <TR>
      <TD>一般</TD>
      <TD><?php 
	  
echo "<SELECT name=img_0>";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);
	if($val==$img_0){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
	
}
echo "<option value=127>ランダム</option>\n";
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD>インデックス</TD>
      <TD><?php 
	  
echo "<SELECT name=img_1>";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);
	if($val==$img_1){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
	
}
echo "<option value=127>ランダム</option>\n";
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD>ニュース関係</TD>
      <TD><?php 
	  
echo "<SELECT name=img_2>";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);
	if($val==$img_2){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
}
echo "<option value=127>ランダム</option>\n";
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD>メンバーリスト</TD>
      <TD><?php 
	  
echo "<SELECT name=img_3>";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);
	if($val==$img_3){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
	
}
echo "<option value=127>ランダム</option>\n";
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD>フォーラム</TD>
      <TD><?php 
	  
echo "<SELECT name=img_4>";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);
	if($val==$img_4){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
	
}
echo "<option value=127>ランダム</option>\n";
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD>ユーザーコンテンツ</TD>
      <TD><?php 
	  
echo "<SELECT name=img_5>";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);
	if($val==$img_5){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
	
}
echo "<option value=127>ランダム</option>\n";
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD>アルバム</TD>
      <TD><?php 
	  
echo "<SELECT name=img_6>";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);
	if($val==$img_6){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
	
}
echo "<option value=127>ランダム</option>\n";
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD>リンク</TD>
      <TD><?php 
	  
echo "<SELECT name=img_7>";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);
	if($val==$img_7){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
	
}
echo "<option value=127>ランダム</option>\n";
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD>ランダム</TD>
      <TD><input type=text name=img_rdn size=3 value="<?php echo "$img_rdn"; ?>"></TD>
    </TR>

    <TR>
      <TD><input type=submit value=Select name=select></TD>
      <TD></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
	</form>
	<form method=post enctype='multipart/form-data' action=site_theme.php>
	<?php
		if($site_theme != 0){
			echo "<input type=hidden name=site_theme value='$site_theme'>";
		}else{
			echo "<input type=hidden name=site_theme value=0>";
		}
	?>
    <TR>
      <TD><input type=submit value="リロード / リセット"></TD>
      <TD></TD>
    </TR>
	</form>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
	<form method=post enctype='multipart/form-data' action=site_theme.php>
	<input type=hidden name=mode value=files>
    <TR>
      <TD><input type=submit value=ファイル管理></TD>
      <TD></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
<BR>
<?php

	form_default();

}
function form_files(){
	global $db;

$img_dir = "./css/";
					
// ディレクトリ一覧取得、ソート
$ext       = ".+\_def.css$";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_css[] = $ent;
}
}
$d->close();

// ディレクトリ一覧取得、ソート
$ext       = ".+\_big.css$";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_big_css[] = $ent;
}
}
$d->close();

// ディレクトリ一覧取得、ソート
$ext       = ".+\_small.css$";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_small_css[] = $ent;
}
}
$d->close();

//$files=join('<BR>',$files);
//echo "$files<BR><BR>";

$img_dir = "./forum/img/";

// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$ext       = "cellpic";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_cellpic[] = $ent;
}
}
$d->close();
					
// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$ext       = "post";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_post[] = $ent;
}
}
$d->close();
					
// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$ext       = "reply";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_reply[] = $ent;
}
}
$d->close();

// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$ext       = "sta";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_sta[] = $ent;
}
}
$d->close();

// ディレクトリ一覧取得、ソート
//$ext       = ".+\.png$|.+\.jpe?g$";
$img_dir = "./img/";
$ext       = "topimage";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_topimage[] = $ent;
}
}
$d->close();

$ext       = "img_left";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_img_left[] = $ent;
}
}
$d->close();

$ext       = "img_right";
$d = dir($img_dir);
while ($ent = $d->read()) {
if (eregi($ext, $ent)) {
$files_img_right[] = $ent;
}
}
$d->close();

?>

<form method=post enctype='multipart/form-data' action=site_theme.php>
<input type=hidden name=mode value=files_modify>
<TABLE width="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD colspan="2">ファイル管理</TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">テーマ削除</TD>
    </TR>
    <TR>
      <TD>テーマ</TD>
      <TD colspan="2"><?php 
	  
	$sql = "select * from `PHP_SITE_THEME`";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	echo "<select name='del_site_theme'>";
	echo "<option value='' selected>none</option>";
	
	while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$id = $tmp_rows["id"];
		$title = $tmp_rows["title"];
		echo "<option value='$id'>$title</option>";
	}
	
	echo "</select>";

	  
	  ?></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">CSS削除</TD>
    </TR>
    <TR>
      <TD><a href="./css/theme0_def.css">theme0_def.css</a></TD>
      <TD><?php 
	  
echo "<SELECT name=theme_def>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_css);$i++){
	$pos=strpos($files_css[$i],'_');
	$val=substr($files_css[$i],5,$pos-5);
	
	if($val==$theme_def){
		echo "<option value='$val' selected>$files_css[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_css[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./css/theme0_big.css">theme0_big.css</a></TD>
      <TD><?php 
	  
echo "<SELECT name=theme_big>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_big_css);$i++){
	$pos=strpos($files_big_css[$i],'_');
	$val=substr($files_big_css[$i],5,$pos-5);

	if($val==$theme_big){
		echo "<option value='$val' selected>$files_big_css[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_big_css[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./css/theme0_small.css">theme0_small.css</a></TD>
      <TD><?php 
	  
echo "<SELECT name=theme_small>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_small_css);$i++){
	$pos=strpos($files_small_css[$i],'_');
	$val=substr($files_small_css[$i],5,$pos-5);

	if($val==$theme_small){
		echo "<option value='$val' selected>$files_small_css[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_small_css[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">CSSアップデート</TD>
    </TR>
    <TR>
      <TD>theme_def</TD>
      <TD><input type='file' name='uf_theme_def'></TD>
    </TR>
    <TR>
      <TD>theme_big</TD>
      <TD><input type='file' name='uf_theme_big'></TD>
    </TR>
    <TR>
      <TD>theme_small</TD>
      <TD><input type='file' name='uf_theme_small'></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">サイトイメージ削除</TD>
    </TR>
    <TR>
      <TD><a href="./img/img_left0.gif">img_left0.gif</a></TD>
      <TD><?php 
	  
echo "<SELECT name=img_left>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_img_left);$i++){
	$pos=strpos($files_img_left[$i],'.');
	$val=substr($files_img_left[$i],8,$pos-8);

	if($val==$img_left){
		echo "<option value='$val' selected>$files_img_left[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_img_left[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./img/img_right0.gif">img_right0.gif</a></TD>
      <TD><?php 
	  
echo "<SELECT name=img_right>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_img_right);$i++){
	$pos=strpos($files_img_right[$i],'.');
	$val=substr($files_img_right[$i],9,$pos-9);

	if($val==$img_left){
		echo "<option value='$val' selected>$files_img_right[$i]</option>\n";
	} else {
		echo "<option value='$val'>$files_img_right[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">サイトイメージアップデート</TD>
    </TR>
    <TR>
      <TD>img_left</TD>
      <TD><input type='file' name='uf_img_left'></TD>
    </TR>
    <TR>
      <TD>img_right</TD>
      <TD><input type='file' name='uf_img_right'></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">フォーラムイメージ削除</TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/cellpic0.gif">cellpic0</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_cellpic>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_cellpic);$i++){
	$pos=strpos($files_cellpic[$i],'.');
	$val=substr($files_cellpic[$i],7,$pos-7);

	if($val==$cellpic1){
		echo "<option value=$val selected>$files_cellpic[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_cellpic[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/post0.gif">post0</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_post>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_post);$i++){
	$pos=strpos($files_post[$i],'.');
	$val=substr($files_post[$i],4,$pos-4);

	if($val==$post){
		echo "<option value=$val selected>$files_post[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_post[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/reply0.gif">reply0</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_reply>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_reply);$i++){
	$pos=strpos($files_reply[$i],'.');
	$val=substr($files_reply[$i],5,$pos-5);
	
	if($val==$reply){
		echo "<option value=$val selected>$files_reply[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_reply[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD><a href="./forum/img/sta0.gif">sta0</a></TD>
      <TD><?php 
	  
echo "<SELECT name=forum_sta>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_sta);$i++){
	$pos=strpos($files_sta[$i],'.');
	$val=substr($files_sta[$i],3,$pos-3);

	if($val==$sta1){
		echo "<option value=$val selected>$files_sta[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_sta[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">フォーラムイメージアップデート</TD>
    </TR>
    <TR>
      <TD>cellpic</TD>
      <TD><input type='file' name='uf_cellpic'></TD>
    </TR>
    <TR>
      <TD>post</TD>
      <TD><input type='file' name='uf_post'></TD>
    </TR>
    <TR>
      <TD>reply</TD>
      <TD><input type='file' name='uf_reply'></TD>
    </TR>
    <TR>
      <TD>sta</TD>
      <TD><input type='file' name='uf_sta'></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">トップイメージ削除</TD>
    </TR>
    <TR>
      <TD>top</TD>
      <TD><?php 
	  
echo "<SELECT name=img_>";
echo "<option value=''>None</option>\n";
for($i=0;$i<count($files_topimage);$i++){
	$pos=strpos($files_topimage[$i],'.');
	$val=substr($files_topimage[$i],8,$pos-8);

	if($val==$img_0){
		echo "<option value=$val selected>$files_topimage[$i]</option>\n";
	} else {
		echo "<option value=$val>$files_topimage[$i]</option>\n";
	}
}
echo "</select>";
	  
	  ?></TD>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2">トップイメージアップデート</TD>
    </TR>
    <TR>
      <TD>topimage</TD>
      <TD><input type='file' name='uf_topimage'></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD><input type=submit value=Modify name=modify></TD>
      <TD></TD>
    </TR>
	</form>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
	</form>
	<form method=post enctype='multipart/form-data' action=site_theme.php>
	<input type=hidden name=mode value=files>
    <TR>
      <TD><input type=submit value="リロード / リセット"></TD>
      <TD></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
<BR>
<?php

	form_default();

}

function db_regist(){
	global $db,$sta_msg;
	
	$site_theme = intval($_POST["site_theme"]);
	
	if($site_theme == 1){
		return;
	}
	
	$title = $_POST["title"];
	$site_theme = intval($_POST["site_theme"]);
	$theme_def = intval($_POST["theme_def"]);
	$theme_big = intval($_POST["theme_big"]);
	$theme_small = intval($_POST["theme_small"]);
	$img_left = intval($_POST["img_left"]);
	$img_right = intval($_POST["img_right"]);
	$forum_cellpic0 = intval($_POST["forum_cellpic0"]);
	$forum_cellpic1 = intval($_POST["forum_cellpic1"]);
	$forum_post = intval($_POST["forum_post"]);
	$forum_reply = intval($_POST["forum_reply"]);
	$forum_sta0 = intval($_POST["forum_sta0"]);
	$forum_sta1 = intval($_POST["forum_sta1"]);
	$forum_sta2 = intval($_POST["forum_sta2"]);
	$forum_sta3 = intval($_POST["forum_sta3"]);
	$img_0 = intval($_POST["img_0"]);
	$img_1 = intval($_POST["img_1"]);
	$img_2 = intval($_POST["img_2"]);
	$img_3 = intval($_POST["img_3"]);
	$img_4 = intval($_POST["img_4"]);
	$img_5 = intval($_POST["img_5"]);
	$img_6 = intval($_POST["img_6"]);
	$img_7 = intval($_POST["img_7"]);
	$img_rdn = intval($_POST["img_rdn"]);
	
	if(!$site_theme){
		$site_theme = "";
	}
	
	$sql = "REPLACE INTO `PHP_SITE_THEME` VALUES ('$site_theme', '$title' , '$theme_def' ,'$theme_big', '$theme_small', '$img_left', '$img_right', '$forum_cellpic0','$forum_cellpic1','$forum_post','$forum_reply','$forum_sta0','$forum_sta1','$forum_sta2','$forum_sta3','$img_0','$img_1','$img_2','$img_3','$img_4','$img_5','$img_6','$img_7','$img_rdn')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sta_msg = "登録完了!! テーマID = '$site_theme'";
}

function db_files(){
	global $db,$sta_msg;
	
	$arrowext = array('jpg','css','gif');
	$limitk = "5000";
	
	//削除関係
	$site_theme = intval($_POST["del_site_theme"]);
	
	if($site_theme == 1){
		return;
	}
	
	if($site_theme){
		$sql = "delete from `PHP_SITE_THEME` where `id` = '$site_theme'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	}

	$theme_def = intval($_POST["theme_def"]);
	if($theme_def){
		$str = "css/theme".$theme_def."_def.css";
		unlink("$str");
	}
	
	$theme_big = intval($_POST["theme_big"]);
	if($theme_big){
		$str = "css/theme".$theme_big."_big.css";
		unlink("$str");
	}
	
	$theme_small = intval($_POST["theme_small"]);
	if($theme_small){
		$str = "css/theme".$theme_small."_small.css";
		unlink("$str");
	}
	
	$img_left = intval($_POST["img_left"]);
	if($img_left){
		$str = "img/img_left".$img_left.".gif";
		unlink("$str");
	}
	
	$img_right = intval($_POST["img_right"]);
	if($img_right){
		$str = "img/img_right".$img_right.".gif";
		unlink("$str");
	}
	
	$forum_cellpic = intval($_POST["forum_cellpic"]);
	if($forum_cellpic > 1 ){
		$str = "forum/img/cellpic".$forum_cellpic.".gif";
		unlink("$str");
	}
	
	$forum_post = intval($_POST["forum_post"]);
	if($forum_post){
		$str = "forum/img/post".$forum_post.".gif";
		unlink("$str");
	}
	
	$forum_reply = intval($_POST["forum_reply"]);
	if($forum_reply){
		$str = "forum/img/reply".$forum_reply.".gif";
		unlink("$str");
	}
	
	$forum_sta = intval($_POST["forum_sta"]);
	if($forum_sta > 3){
		$str = "forum/img/sta".$forum_sta.".gif";
		unlink("$str");
	}
	
	$img_ = intval($_POST["img_"]);
	if($img_){
		$str = "img/topimage".$img_.".jpg";
		unlink("$str");
	}
	
	//アップロード関係
	$upfile_size=$_FILES["uf_theme_def"]["size"];
	$upfile_name=$_FILES["uf_theme_def"]["name"];
	$upfile=$_FILES["uf_theme_def"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^theme[0-9]+_def', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "css/".$upfile_name);
	}
	
	
	$upfile_size=$_FILES["uf_theme_big"]["size"];
	$upfile_name=$_FILES["uf_theme_big"]["name"];
	$upfile=$_FILES["uf_theme_big"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^theme[0-9]+_big', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "css/".$upfile_name);
	}
	
	
	$upfile_size=$_FILES["uf_theme_small"]["size"];
	$upfile_name=$_FILES["uf_theme_small"]["name"];
	$upfile=$_FILES["uf_theme_small"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^theme[0-9]+_small', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "css/".$upfile_name);
	}
	
	
	$upfile_size=$_FILES["uf_img_left"]["size"];
	$upfile_name=$_FILES["uf_img_left"]["name"];
	$upfile=$_FILES["uf_img_left"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^img_left[0-9]+', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "img/".$upfile_name);
	}
	
	
	$upfile_size=$_FILES["uf_img_right"]["size"];
	$upfile_name=$_FILES["uf_img_right"]["name"];
	$upfile=$_FILES["uf_img_right"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^img_right[0-9]+', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "img/".$upfile_name);
	}
	
	
	$upfile_size=$_FILES["uf_cellpic"]["size"];
	$upfile_name=$_FILES["uf_cellpic"]["name"];
	$upfile=$_FILES["uf_cellpic"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^cellpic[0-9]+', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "forum/img/".$upfile_name);
	}
	
	
	$upfile_size=$_FILES["uf_post"]["size"];
	$upfile_name=$_FILES["uf_post"]["name"];
	$upfile=$_FILES["uf_post"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^post[0-9]+', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "forum/img/".$upfile_name);
	}
	
	$upfile_size=$_FILES["uf_reply"]["size"];
	$upfile_name=$_FILES["uf_reply"]["name"];
	$upfile=$_FILES["uf_reply"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^reply[0-9]+', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "forum/img/".$upfile_name);
	}
	
	$upfile_size=$_FILES["uf_sta"]["size"];
	$upfile_name=$_FILES["uf_sta"]["name"];
	$upfile=$_FILES["uf_sta"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^sta[0-9]+', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "forum/img/".$upfile_name);
	}
	
	
	$upfile_size=$_FILES["uf_topimage"]["size"];
	$upfile_name=$_FILES["uf_topimage"]["name"];
	$upfile=$_FILES["uf_topimage"]["tmp_name"];
	
	$pos = strrpos($upfile_name,".");	//拡張子取得
	$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
	$ext = strtolower($ext);//小文字化

	if(ereg('^topimage[0-9]+', $upfile_name) && in_array($ext, $arrowext)){
	
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
			return;
		}
		//var_dump($_FILES);
		move_uploaded_file($upfile, "img/".$upfile_name);
	}
	
	$sta_msg = "修正しました。";
	$site_theme = 0;
}


$mode = $_POST["mode"];
if($mode == "regist"){
	db_regist();
}else{
	if($mode == "files_modify"){
		db_files();
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
                  <TD colspan="2" width="422" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">

	  <?php
	  
				$site_theme = $_POST["site_theme"];
				//var_dump($site_theme);
				if($site_theme == "0"){
					form_new();
				}else{
				
					if($site_theme){
						form_new();
					}elseif($_POST["mode"] == "files" || $_POST["mode"] == "files_modify"){
						form_files();
					}else{
						form_default();
					}
				}
	  
	  ?>
				</TD></TR><TR><TD colspan="2" width="422" valign="top"></TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				*「Select」を押すと送信され、上部に「登録しました!! 」と表示されるまでお待ちください。表示された場合は登録完了です。<BR><BR>
				
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