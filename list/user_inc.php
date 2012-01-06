<?php

function isAlphaOrNum($input){
	$pattern = "/^[a-zA-Z0-9]+$/";
	if(preg_match($pattern, $input)){
		return true;
	}else{
		return false;
	}
}

function isAlphaOrNum2($input){
	$pattern = "/^[a-zA-Z]+$/";
	if(preg_match($pattern, $input)){
		return true;
	}else{
		return false;
	}
}

function user_login_form($uid){
	global $db,$env_rows,$ML_SCRIPT,$USR_SCRIPT,$PLOF_SCRIPT,$RACELIST,$RELMLIST,$JOBLIST,$JOB_MAX,$FACENAME,$SIZELIST;
	//$name = $_POST["name"];
	//$indexcolor = $env_rows["indexcolor"];

	//$sql = "select * from USER_DATA , USER_STA , USER_LEV , USER_IP where USER_DATA.uid = USER_STA.uid = USER_LEV.uid = USER_IP.uid";
	
	$sql = "select * from USER_DATA , USER_STA , USER_LEV , USER_PLOF , USER_IP where USER_DATA.uid = '$uid' and USER_STA.uid = '$uid' and USER_LEV.uid = '$uid' and USER_PLOF.uid = '$uid' and USER_IP.uid = '$uid'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	
	$user_rows = convert_to_sjis($user_rows);
	
	$user_uid = $user_rows["uid"];
	$user_no = $user_rows["no"];
	$user_name = $user_rows["name"];
	//$user_pass = $user_rows["pass"];
	$user_class = $user_rows["class"];
	//$user_broadband = $user_rows["broadband"];
	//$user_diary_mode = $user_rows["diary_mode"];
	//$user_mail_mode = $user_rows["mail_mode"];
	//$user_mail_sendfor = $user_rows["mail_sendfor"];
	$user_anon = $user_rows["anon"];
	$user_race = $user_rows["race"];
	$user_faceid = $user_rows["face"];
	$user_size = $user_rows["size"];
	$user_relm = $user_rows["relm"];
	$user_mainjob = $user_rows["mainjob"];
	$user_supportjob = $user_rows["supportjob"];
	$user_point = $user_rows["point"];
	$user_mrank = $user_rows["mrank"];
	$user_comment = $user_rows["comment"];
	$user_prof = $user_rows["prof_mode"];
	$user_handle = $user_rows["handle"];
	$user_polhn = $user_rows["polhn"];
	$user_mail = $user_rows["mail"];
	$user_home = $user_rows["url"];
	$user_comment2 = $user_rows["comment_plof"];
	
	$user_lev[0] = $user_rows["lev0"];
	$user_lev[1] = $user_rows["lev1"];
	$user_lev[2] = $user_rows["lev2"];
	$user_lev[3] = $user_rows["lev3"];
	$user_lev[4] = $user_rows["lev4"];
	$user_lev[5] = $user_rows["lev5"];
	$user_lev[6] = $user_rows["lev6"];
	$user_lev[7] = $user_rows["lev7"];
	$user_lev[8] = $user_rows["lev8"];
	$user_lev[9] = $user_rows["lev9"];
	$user_lev[10] = $user_rows["lev10"];
	$user_lev[11] = $user_rows["lev11"];
	$user_lev[12] = $user_rows["lev12"];
	$user_lev[13] = $user_rows["lev13"];
	$user_lev[14] = $user_rows["lev14"];
	$user_lev[15] = $user_rows["lev15"];
	$user_lev[16] = $user_rows["lev16"];
	$user_lev[17] = $user_rows["lev17"];
	
	$user_ip = $user_rows["ip"];
	$user_date = $user_rows["date"];
	$user_date = gmdate("Y/m/d (D) H:i:s", $user_date+9*60*60);
	
	
	echo "
　No. $user_no : '$user_name' さんのデータを修正します。<br>
　Last Updated : $user_date from $user_ip<br>
<form method=post enctype='multipart/form-data'  action=$USR_SCRIPT>

<input type=hidden name=mode value=modify>
<TABLE>
  <TBODY>
<TR><TD>name</TD><TD><input type=text name=name value='$user_name'></TD>
<TR><TD>pass</TD><TD><input type=text name=pass value='$user_pass'></TD>
<TR><TD>class</TD><TD><input type=text name=class value=".'"'.$user_class.'"'."></TD>
<TR><TD>anon mode</TD><TD><select name=anon>";
if($user_anon == 0){
echo "<option value=0 selected>off</option>";
echo "<option value=1>on</option>";
}else{
echo "<option value=0>off</option>";
echo "<option value=1 selected>on</option>";
}
echo "</select></TD>";

echo "<TR><TD>Race</TD><TD><select name=race>";
for($i=0;$i<count($RACELIST);$i++){
	if($i==$user_race){
	echo "<option value=$i selected>$RACELIST[$i]</option>\n";
	} else {
	echo "<option value=$i>$RACELIST[$i]</option>\n";
	}
}
echo "</select></TD>";
echo "<TR><TD>Face</TD><TD><SELECT name=face>";
for($i=0;$i<count($FACENAME);$i++){
$i_tmp2 = $i.'b';
	if($i == 0){
		echo "<option value=$i>$FACENAME[$i]</option>\n";
		echo "<optgroup label=$RACELIST[0]>";
	}else{
		if($user_faceid == $i_tmp2){
			echo "<option value=$i>$FACENAME[$i] A</option>\n";
			echo "<option selected value=".$i."b>$FACENAME[$i] B</option>\n";
		}elseif($user_faceid == $i){
			echo "<option selected value=$i>$FACENAME[$i] A</option>\n";
			echo "<option value=".$i."b>$FACENAME[$i] B</option>\n";
		}else{
		echo "<option value=$i>$FACENAME[$i] A</option>\n";
		echo "<option value=".$i."b>$FACENAME[$i] B</option>\n";
		}
		if($i==8 || $i==16 || $i==24 || $i==32 || $i==40 || $i==48 || $i==56){
		$i_tmp = $i /8;
		echo "<optgroup label=$RACELIST[$i_tmp]>";
		}
	}
}
echo "</select> <a href='../faces.htm'>顔リスト</a></TD>";
echo "<TR><TD>Size</TD><TD><SELECT name=size>";
for($i=0;$i<count($SIZELIST);$i++){
	if($i==$user_size){
		echo "<option value=$i selected>$SIZELIST[$i]</option>\n";
	} else {
		echo "<option value=$i>$SIZELIST[$i]</option>\n";
	}
}
echo "</select></TD>";
echo "<TR><TD>Realm</TD><TD><select name=relm>";
for($i=0;$i<count($RELMLIST);$i++){
	if($i==$user_relm){
		echo "<option value=$i selected>$RELMLIST[$i]</option>\n";
	} else {
		echo "<option value=$i>$RELMLIST[$i]</option>\n";
	}
}
echo "</select></TD>";
echo "<TR><TD>MainJob</TD><TD><select name=mainjob>";
for($i=0;$i<$JOB_MAX;$i++){
	if($i==$user_mainjob){
	echo "<option value=$i selected>$JOBLIST[$i]</option>\n";
	} else {
	echo "<option value=$i>$JOBLIST[$i]</option>\n";
	}
}
echo "</select></TD>";
echo "<TR><TD>SupportJob</TD><TD><select name=supportjob>";
echo "<option value=99>none</option>\n";
for($i=0;$i<$JOB_MAX;$i++){
	if($i==$user_supportjob){
	echo "<option value=$i selected>$JOBLIST[$i]</option>\n";
	} else {
	echo "<option value=$i>$JOBLIST[$i]</option>\n";
	}
}
echo "</select></TD>";

for($i=0;$i<$JOB_MAX;$i++){
echo "<TR><TD>$JOBLIST[$i]</TD><TD><input type=text size=2 name=lev$i value='$user_lev[$i]'></TD>\n";
}
//var_dump($user_comment);
echo "<TR><TD>個人ポイント</TD><TD><input type=text name=point value='$user_point'></TD>";
echo "<TR><TD>ミッションランク</TD><TD><input type=text name=mrank value='$user_mrank'></TD>";
echo '<TR><TD>comment</TD><TD><input type="text" name="comment" value="'.$user_comment.'" size="50" /></TD>';
echo "<TR><TD colspan=2><HR></TD></TR>";
echo "<TR><TD>profile 公開</TD><TD><select name=prof>";

if($user_prof == 0){
	echo "<option value=0 selected>off</option><option value=1>on</option>";
	} else {
	echo "<option value=0>off</option><option value=1 selected>on</option>";
	}
echo "</select></TD>";

echo "<TR><TD>ハンドル</TD><TD><input type=text name=handle value='$user_handle'></TD>";
echo "<TR><TD>POL内ハンドル</TD><TD><input type=text name=polhn value='$user_polhn'></TD>";
echo "<TR><TD>e-mail</TD><TD><input type=text name=mail value='$user_mail'></TD>";

echo "<TR><TD>homepage url</TD><TD><input type=text name=home size=50 value='$user_home'></TD>";
echo "<TR><TD>しゃしん</TD><TD><INPUT type=radio name=mod_prof_img value=no checked>変更しない <INPUT type=radio name=mod_prof_img value=yes>変更する <INPUT type=radio name=mod_prof_img value=del>削除 <input type='file' name='upfile'></TD>";
echo "<TR><TD>プロフィール用コメント</TD><TD><textarea name=comment2 rows=5 cols=60>$user_comment2</textarea></TD>";
echo "<TR><TD colspan=2><HR></TD></TR>";

echo "<TR><TD>保存するちょ～</TD><TD><input type=submit value='modify'></TD></form></TR>";
echo "<form method=post enctype='multipart/form-data' action=$USR_SCRIPT><input type=hidden name=mode value=mod_skill>";
echo "<TR><TD>スキルを入力</TD><TD><input type=submit value='submit'></TD></form></TR>";
echo "<TR><TD colspan=2><HR></TD></TR>";

echo "<TR><TD colspan=2>サブメニュー</TD></TR>";

echo "<form method=post enctype='multipart/form-data' action=viewlog.php>";
echo "<TR><TD>ログ表示</TD><TD><input type=submit value='ログ表示'></TD></form></TR>";

echo "<form method=post enctype='multipart/form-data' action=delete_user.php>";
echo "<TR><TD>データ削除</TD><TD><input type=submit value='データ削除'></TD></form></TR>";
echo "</table>";

}


function user_login_form_skill($uid){
	global $db,$env_rows,$ML_SCRIPT,$USR_SCRIPT,$PLODLIST,$SKILLLIST;

	//$indexcolor = $env_rows["indexcolor"];
	
	$sql = "select * from USER_DATA , USER_PROD , USER_SKL , USER_IP where USER_DATA.uid = '$uid' and USER_PROD.uid = '$uid' and USER_SKL.uid = '$uid' AND USER_IP.uid = '$uid'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	
	$user_name = $user_rows["name"];
	$user_pass = $user_rows["pass"];
	
	$user_prod[0] = $user_rows["prod0"];
	$user_prod[1] = $user_rows["prod1"];
	$user_prod[2] = $user_rows["prod2"];
	$user_prod[3] = $user_rows["prod3"];
	$user_prod[4] = $user_rows["prod4"];
	$user_prod[5] = $user_rows["prod5"];
	$user_prod[6] = $user_rows["prod6"];
	$user_prod[7] = $user_rows["prod7"];
	$user_prod[8] = $user_rows["prod8"];
	$user_prod[9] = $user_rows["prod9"];
	$user_prod[10]= $user_rows["prod10"];
	$user_prod[11] = $user_rows["prod11"];
	$user_prod[12] = $user_rows["prod12"];
	$user_prod[13] = $user_rows["prod13"];
	$user_prod[14] = $user_rows["prod14"];
	$user_prod[15] = $user_rows["prod15"];
	$user_prod[16] = $user_rows["prod16"];
	
	$user_skl[0] = $user_rows["skl0"];
	$user_skl[1] = $user_rows["skl1"];
	$user_skl[2] = $user_rows["skl2"];
	$user_skl[3] = $user_rows["skl3"];
	$user_skl[4] = $user_rows["skl4"];
	$user_skl[5] = $user_rows["skl5"];
	$user_skl[6] = $user_rows["skl6"];
	$user_skl[7] = $user_rows["skl7"];
	$user_skl[8] = $user_rows["skl8"];
	$user_skl[9] = $user_rows["skl9"];
	$user_skl[10]= $user_rows["skl10"];
	$user_skl[11] = $user_rows["skl11"];
	$user_skl[12] = $user_rows["skl12"];
	$user_skl[13] = $user_rows["skl13"];
	$user_skl[14] = $user_rows["skl14"];
	$user_skl[15] = $user_rows["skl15"];
	$user_skl[16] = $user_rows["skl16"];
	$user_skl[17] = $user_rows["skl17"];
	$user_skl[18] = $user_rows["skl18"];
	$user_skl[19] = $user_rows["skl19"];
	$user_skl[20] = $user_rows["skl20"];
	$user_skl[21] = $user_rows["skl21"];
	$user_skl[22] = $user_rows["skl22"];
	$user_skl[23] = $user_rows["skl23"];
	$user_skl[24] = $user_rows["skl24"];
	$user_skl[25] = $user_rows["skl25"];
	$user_skl[26] = $user_rows["skl26"];
	$user_skl[27] = $user_rows["skl27"];
	$user_skl[28] = $user_rows["skl28"];
	$user_skl[29] = $user_rows["skl29"];
	$user_skl[30] = $user_rows["skl30"];
	$user_skl[31] = $user_rows["skl31"];
	$user_skl[32] = $user_rows["skl32"];
	
	$user_ip = $user_rows["ip"];
	$user_date = $user_rows["date"];
	$user_date = gmdate("Y/m/d (D) H:i:s", $user_date+9*60*60);
	
	echo "
　No. $user_no : '$user_name' さんのデータを修正します。(すきる)<br>
　Last Updated : $user_date from $user_ip<br>
<form method=post enctype='multipart/form-data' action=$USR_SCRIPT>
<input type=hidden name=mode value=modify>
<input type=hidden name=modify_mode value=skill>
<TABLE>
  <TBODY>
    <TR>";
	echo "<TD><TABLE><TBODY>";
for($i=0;$i<count($SKILLLIST);$i++){
	echo "<TR><TD>$SKILLLIST[$i]</TD><TD><input type=text size=2 name=skl$i value='$user_skl[$i]'></TD>\n";
}
	echo "</TBODY></TABLE></TD>\n";
	echo "<TD width=50></TD>";
	echo "<TD valign=top><TABLE><TBODY>";

for($i=0;$i<count($PLODLIST);$i++){
	echo "<TR><TD>$PLODLIST[$i]</TD><TD><input type=text name=prod$i value='$user_prod[$i]'></TD>\n";
}
	echo "</TBODY></TABLE></TD>\n";
    echo "</TR>";
echo "<TR><TD colspan=3><HR></TD></TR>";
echo "<TR><TD>保存するちょ～</TD><TD><input type=submit value='modify'></TD></form></TR>";

echo "</TBODY></table>";

}


function user_login_modify($uid){
	global $db,$env_rows,$arrowext,$limitk,$putdir,$W,$H,$sam_dir,$image_type,$ML_SCRIPT;
	
	$class_edit = $env_rows["class_edit"];
	
	//$mod_name = $_POST["mod_name"];
	$name = $_POST["name"];
	$pass = $_POST["pass"];
	$mainjob = $_POST["mainjob"];
	$supportjob = $_POST["supportjob"];
	
	$upfile_size=$_FILES["upfile"]["size"];
	$upfile_name=$_FILES["upfile"]["name"];
	$upfile=$_FILES["upfile"]["tmp_name"];
	
	//if(!isAlphaOrNum2($mod_name)){
	//	sub_msg("","","その名前は登録に利用できません","半角英数字を利用してください");
	//	//error_msg("その名前は登録に利用できません","半角英数字を利用してください");
	//}
	
	if(!isAlphaOrNum2($name)){
		sub_msg("","","その名前は登録に利用できません","半角英数字を利用してください");
		//error_msg("その名前は登録に利用できません","半角英数字を利用してください");
	}
	
	$sql = "select `name` from `USER_DATA` where `name` = '$name'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	
	if($members != 1){
		sub_msg("","","already registed","'$name' has already registed.");
		//error_msg("already registed","'$name' has already registed.");
	}
	
	$sql = "select * from `USER_DATA`,`USER_STA`,`USER_PLOF`,`USER_PROD`,`USER_SKL` where USER_DATA.uid = '$uid' and USER_PLOF.uid = '$uid' and USER_STA.uid = '$uid' and USER_PROD.uid = '$uid' and USER_SKL.uid = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$main_id = $user_rows["id"];
	$user_no = $user_rows["no"];
	
	
	if($name == ""){
		sub_msg("","","その名前は登録に利用できません","最低でも一文字以上は記入してください");
		//error_msg("その名前は登録に利用できません","最低でも一文字以上は記入してください");
	}
	if($pass != ""){
		if(!isAlphaOrNum($pass)){
			sub_msg("","","そのパスワードは登録に利用できません","半角英数字を利用してください");
			//error_msg("そのパスワードは登録に利用できません","半角英数字を利用してください");
		}
	
		//sub_msg("","","そのパスワードは登録に利用できません","最低でも一文字以上は記入してください");
		//error_msg("そのパスワードは登録に利用できません","最低でも一文字以上は記入してください");
	}else{
		$pass = $user_rows["pass"];
	}
	if($mainjob == $supportjob){
		sub_msg("","","main/job error","メインとサポートは同一のジョブにはできません");
		//error_msg("main/job error","メインとサポートは同一のジョブにはできません");
	}
	
	//$_POST = convert_to_sjis($_POST);
	//var_dump($_POST);
	
	$class = $_POST["class"];
	$broadband = $_POST["broadband"];
	$diary_mode = $_POST["diary_mode"];
	$mail_mode = $_POST["mail_mode"];
	$mail_sendfor = $_POST["mail_sendfor"];
	$anon = $_POST["anon"];
	$race = $_POST["race"];
	$face = $_POST["face"];
	$size = $_POST["size"];
	$relm = $_POST["relm"];
	$mainjob = $_POST["mainjob"];
	$supportjob = $_POST["supportjob"];
	$point = $_POST["point"];
	$mrank = $_POST["mrank"];
	$comment = $_POST["comment"];
	$prof = $_POST["prof"];
	$handle = $_POST["handle"];
	$polhn = $_POST["polhn"];
	$mail = $_POST["mail"];
	$home = $_POST["home"];
	$comment2 = $_POST["comment2"];
	$mod_prof_img = $_POST["mod_prof_img"];
	
	$comment2 = str_replace("\r\n", "\r", $comment2);
	$comment2 = str_replace("\r", "\n", $comment2);
	
	$class    = htmlspecialchars($class);
	$comment    = insert_safe($comment);
	//$comment = mb_convert_encoding($comment, "SJIS" , "auto");
	//var_dump($comment);
	
	$comment2    = htmlspecialchars($comment2);
	//$mail2    = htmlspecialchars($mail);
	//$home    = htmlspecialchars($home);
	
	if($class_edit){
		$class = $user_rows["class"];
	}
	//var_dump($user_rows["class"]);
	
	$lev0 = $_POST["lev0"];
	$lev1 = $_POST["lev1"];
	$lev2 = $_POST["lev2"];
	$lev3 = $_POST["lev3"];
	$lev4 = $_POST["lev4"];
	$lev5 = $_POST["lev5"];
	$lev6 = $_POST["lev6"];
	$lev7 = $_POST["lev7"];
	$lev8 = $_POST["lev8"];
	$lev9 = $_POST["lev9"];
	$lev10 = $_POST["lev10"];
	$lev11 = $_POST["lev11"];
	$lev12 = $_POST["lev12"];
	$lev13 = $_POST["lev13"];
	$lev14 = $_POST["lev14"];
	$lev15 = $_POST["lev15"];
	$lev16 = $_POST["lev16"];
	$lev17 = $_POST["lev17"];
	
	$prod0 = $user_rows["prod0"];
	$prod1 = $user_rows["prod1"];
	$prod2 = $user_rows["prod2"];
	$prod3 = $user_rows["prod3"];
	$prod4 = $user_rows["prod4"];
	$prod5 = $user_rows["prod5"];
	$prod6 = $user_rows["prod6"];
	$prod7 = $user_rows["prod7"];
	$prod8 = $user_rows["prod8"];
	$prod9 = $user_rows["prod9"];
	$prod10 = $user_rows["prod10"];
	$prod11 = $user_rows["prod11"];
	$prod12 = $user_rows["prod12"];
	$prod13 = $user_rows["prod13"];
	$prod14 = $user_rows["prod14"];
	$prod15 = $user_rows["prod15"];
	$prod16 = $user_rows["prod16"];
	
	$prof_img = $user_rows["prof_img"];
	
	$skl0 = $user_rows["skl0"];
	$skl1 = $user_rows["skl1"];
	$skl2 = $user_rows["skl2"];
	$skl3 = $user_rows["skl3"];
	$skl4 = $user_rows["skl4"];
	$skl5 = $user_rows["skl5"];
	$skl6 = $user_rows["skl6"];
	$skl7 = $user_rows["skl7"];
	$skl8 = $user_rows["skl8"];
	$skl9 = $user_rows["skl9"];
	$skl10= $user_rows["skl10"];
	$skl11 = $user_rows["skl11"];
	$skl12 = $user_rows["skl12"];
	$skl13 = $user_rows["skl13"];
	$skl14 = $user_rows["skl14"];
	$skl15 = $user_rows["skl15"];
	$skl16 = $user_rows["skl16"];
	$skl17 = $user_rows["skl17"];
	$skl18 = $user_rows["skl18"];
	$skl19 = $user_rows["skl19"];
	$skl20 = $user_rows["skl20"];
	$skl21 = $user_rows["skl21"];
	$skl22 = $user_rows["skl22"];
	$skl23 = $user_rows["skl23"];
	$skl24 = $user_rows["skl24"];
	$skl25 = $user_rows["skl25"];
	$skl26 = $user_rows["skl26"];
	$skl27 = $user_rows["skl27"];
	$skl28 = $user_rows["skl28"];
	$skl29 = $user_rows["skl29"];
	$skl30 = $user_rows["skl30"];
	$skl31 = $user_rows["skl31"];
	$skl32 = $user_rows["skl32"];
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = time();
	
		if($upfile_name && $mod_prof_img == "yes"){
			$pos = strrpos($upfile_name,".");	//拡張子取得
			$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
			$ext = strtolower($ext);//小文字化
			if(!in_array($ext, $arrowext)){
				error_msg("拡張子エラー","その拡張子ファイルはアップロードできません");
			}
			$limitb = $limitk * 1024;
			if($limitb < $upfile_size){
			$nowsize = intval( $upfile_size /1024 );
				error_msg("ファイルサイズエラー","最大アップ容量は... $limitk kb です<br>現在のファイルサイズは... $nowsize kb です");
			}
			$up_name = ucfirst($name);
			$newname = "prof_".$up_name.".$ext";
			move_uploaded_file($upfile, $putdir.$upfile_name);
			rename($putdir.$upfile_name, $putdir.$newname);
			$sam_size = getimagesize($putdir.$newname);
			if ($sam_size[0] > $W || $sam_size[1] > $H) {
				thumb_create($putdir.$newname,$W,$H,$sam_dir);
				//$new_file =$sam_dir."prof_".$up_name.".jpg";
				//$original_image = "$putdir$newname";
				//$image    =    new hft_image($original_image); 
				//$image->resize($W,$H, '0');
				//$image->output_resized($new_file, "JPEG"); 
			}
			$prof_img = $newname;
		}elseif($mod_prof_img == "del"){
			$prof_img = "";
		}else{
			//var_dump($prof_img);
		}
	if($_POST["pass"]){
		$sql = "replace INTO USER_DATA VALUES ('$uid', '$user_no', '$name', password('$pass') )";
	}else{
		$sql = "replace INTO USER_DATA VALUES ('$uid', '$user_no', '$name', '$pass' )";
	}
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "replace INTO USER_STA VALUES ('$uid', '$class', '$anon', '$race', '$face', '$size', '$relm', '$mainjob', '$supportjob', '$point', '$mrank', '$comment')";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "replace INTO USER_PLOF VALUES ('$uid','$prof', '$handle', '$polhn', '$mail', '$home', '$comment2', '$prof_img' )";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "replace INTO USER_LEV VALUES ('$uid', '$lev0', '$lev1', '$lev2', '$lev3', '$lev4', '$lev5', '$lev6', '$lev7', '$lev8', '$lev9', '$lev10', '$lev11', '$lev12', '$lev13', '$lev14', '$lev15', '$lev16', '$lev17' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "replace INTO USER_PROD VALUES ('$uid', '$prod0', '$prod1', '$prod2', '$prod3', '$prod4', '$prod5', '$prod6','$prod7', '$prod8', '$prod9', '$prod10', '$prod11', '$prod12', '$prod13', '$prod14', '$prod15', '$prod16' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "replace INTO USER_SKL VALUES ('$uid', '$skl0', '$skl1', '$skl2', '$skl3', '$skl4', '$skl5', '$skl6', '$skl7', '$skl8', '$skl9', '$skl10', '$skl11', '$skl12', '$skl13', '$skl14', '$skl15', '$skl16', '$skl17', '$skl18', '$skl19', '$skl20', '$skl21', '$skl22', '$skl23', '$skl24', '$skl25', '$skl26', '$skl27', '$skl28', '$skl29', '$skl30', '$skl31', '$skl32' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "replace INTO USER_IP VALUES ('$uid', '$ip', '$date' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	//アップデータチェック
	upd_chk_lv($uid);

	//$sql = "INSERT INTO LOG_USER_DATA VALUES ('', '$uid','$user_no', '$name', '$pass' )";
	
	if($_POST["pass"]){
		$sql = "INSERT INTO LOG_USER_DATA VALUES ('', '$uid','$user_no', '$name', password('$pass') )";
	}else{
		$sql = "INSERT INTO LOG_USER_DATA VALUES ('', '$uid','$user_no', '$name', '$pass' )";
	}
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	//get new id
	$sql = "select `uid`,max(`id`) from `LOG_USER_DATA` where `uid` = '$uid' group by `uid`;";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$max_id = $user_rows["max(`id`)"];
	
	$sql = "INSERT INTO LOG_USER_STA VALUES ('$max_id', '$uid', '$class', '$anon', '$race', '$face', '$size', '$relm', '$mainjob', '$supportjob', '$point', '$mrank', '$comment' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_PLOF VALUES ('$max_id', '$uid','$prof', '$handle', '$polhn', '$mail', '$home', '$comment2', '$prof_img' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_LEV VALUES ('$max_id', '$uid','$lev0', '$lev1', '$lev2', '$lev3', '$lev4', '$lev5', '$lev6', '$lev7', '$lev8', '$lev9', '$lev10', '$lev11', '$lev12', '$lev13', '$lev14', '$lev15', '$lev16', '$lev17' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_PROD VALUES ('$max_id', '$uid','$prod0', '$prod1', '$prod2', '$prod3', '$prod4', '$prod5', '$prod6','$prod7', '$prod8', '$prod9', '$prod10', '$prod11', '$prod12', '$prod13', '$prod14', '$prod15', '$prod16' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_SKL VALUES ('$max_id', '$uid','$skl0', '$skl1', '$skl2', '$skl3', '$skl4', '$skl5', '$skl6', '$skl7', '$skl8', '$skl9', '$skl10', '$skl11', '$skl12', '$skl13', '$skl14', '$skl15', '$skl16', '$skl17', '$skl18', '$skl19', '$skl20', '$skl21', '$skl22', '$skl23', '$skl24', '$skl25', '$skl26', '$skl27', '$skl28', '$skl29', '$skl30', '$skl31', '$skl32' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_IP VALUES ('$max_id', '$uid','$ip', '$date' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	sub_msg("5","list/$ML_SCRIPT","修正終了","修正を行いました。自動的にトップページへ戻ります。");
	//reload_css1();
	//echo "<table width=100%><TR bgcolor=$indexcolor><TD>修正終了</TD></TR></table>";
	//echo "修正を行いました。自動的にトップページへ戻ります。<BR>";
	//html_foot();
}


function user_login_modify_skill($uid){
	global $db,$env_rows,$ML_SCRIPT;
	
	//$indexcolor = $env_rows["indexcolor"];
	
	//$mod_name = $_POST["mod_name"];
	
	$sql = "select * from USER_DATA , USER_STA , USER_PLOF ,USER_LEV where USER_DATA.uid = '$uid' and USER_STA.uid = '$uid' and USER_PLOF.uid = '$uid' and USER_LEV.uid = '$uid'";
	
	$result = $db->query($sql);
	
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);	
	
	$uid = $user_rows["uid"];
	$user_no = $user_rows["no"];
	$name = $user_rows["name"];
	$pass = $user_rows["pass"];
	$class = $user_rows["class"];
	$broadband = $user_rows["broadband"];
	$diary_mode = $user_rows["diary_mode"];
	$mail_mode = $user_rows["mail_mode"];
	$mail_sendfor = $user_rows["mail_sendfor"];
	$anon = $user_rows["anon"];
	$race = $user_rows["race"];
	$faceid = $user_rows["face"];
	$size = $user_rows["size"];
	$relm = $user_rows["relm"];
	$mainjob = $user_rows["mainjob"];
	$supportjob = $user_rows["supportjob"];
	$point = $user_rows["point"];
	$mrank = $user_rows["mrank"];
	$comment = $user_rows["comment"];
	$prof = $user_rows["prof"];
	$handle = $user_rows["handle"];
	$polhn = $user_rows["polhn"];
	$mail = $user_rows["mail"];
	$home = $user_rows["home"];
	$comment2 = $user_rows["comment2"];
	$prof_img = $user_rows["prof_img"];
	
	$class = addslashes($class);
	$comment = addslashes($comment);
	$comment2 = addslashes($comment2);
	
	$lev0 = $user_rows["lev0"];
	$lev1 = $user_rows["lev1"];
	$lev2 = $user_rows["lev2"];
	$lev3 = $user_rows["lev3"];
	$lev4 = $user_rows["lev4"];
	$lev5 = $user_rows["lev5"];
	$lev6 = $user_rows["lev6"];
	$lev7 = $user_rows["lev7"];
	$lev8 = $user_rows["lev8"];
	$lev9 = $user_rows["lev9"];
	$lev10 = $user_rows["lev10"];
	$lev11 = $user_rows["lev11"];
	$lev12 = $user_rows["lev12"];
	$lev13 = $user_rows["lev13"];
	$lev14 = $user_rows["lev14"];
	$lev15 = $user_rows["lev15"];
	$lev16 = $user_rows["lev16"];
	
	$prod0 = $_POST["prod0"];
	$prod1 = $_POST["prod1"];
	$prod2 = $_POST["prod2"];
	$prod3 = $_POST["prod3"];
	$prod4 = $_POST["prod4"];
	$prod5 = $_POST["prod5"];
	$prod6 = $_POST["prod6"];
	$prod7 = $_POST["prod7"];
	$prod8 = $_POST["prod8"];
	$prod9 = $_POST["prod9"];
	$prod10 = $_POST["prod10"];
	$prod11 = $_POST["prod11"];
	$prod12 = $_POST["prod12"];
	$prod13 = $_POST["prod13"];
	$prod14 = $_POST["prod14"];
	$prod15 = $_POST["prod15"];
	$prod16 = $_POST["prod16"];
	
	$skl0 = $_POST["skl0"];
	$skl1 = $_POST["skl1"];
	$skl2 = $_POST["skl2"];
	$skl3 = $_POST["skl3"];
	$skl4 = $_POST["skl4"];
	$skl5 = $_POST["skl5"];
	$skl6 = $_POST["skl6"];
	$skl7 = $_POST["skl7"];
	$skl8 = $_POST["skl8"];
	$skl9 = $_POST["skl9"];
	$skl10= $_POST["skl10"];
	$skl11 = $_POST["skl11"];
	$skl12 = $_POST["skl12"];
	$skl13 = $_POST["skl13"];
	$skl14 = $_POST["skl14"];
	$skl15 = $_POST["skl15"];
	$skl16 = $_POST["skl16"];
	$skl17 = $_POST["skl17"];
	$skl18 = $_POST["skl18"];
	$skl19 = $_POST["skl19"];
	$skl20 = $_POST["skl20"];
	$skl21 = $_POST["skl21"];
	$skl22 = $_POST["skl22"];
	$skl23 = $_POST["skl23"];
	$skl24 = $_POST["skl24"];
	$skl25 = $_POST["skl25"];
	$skl26 = $_POST["skl26"];
	$skl27 = $_POST["skl27"];
	$skl28 = $_POST["skl28"];
	$skl29 = $_POST["skl29"];
	$skl30 = $_POST["skl30"];
	$skl31 = $_POST["skl31"];
	$skl32 = $_POST["skl32"];
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = time();
	
	//$sql = "REPLACE INTO USER_DATA VALUES ('$uid', '$user_no', '$name', '$pass' )";
	
	//$result = $db->query($sql);
	//if (DB::isError($result)) {
    //	trigger_error($result->getMessage(), E_USER_ERROR);
	//}
	
	//$sql = "REPLACE INTO USER_STA VALUES ('$uid', '$class', '$broadband', '$diary_mode', '$mail_mode', '$mail_sendfor', '$anon', '$race', '$face', '$size', '$relm', '$mainjob', '$supportjob', '$point', '$mrank', '$comment', '$prof', '$handle', '$polhn', '$mail', '$home', '$comment2', '$newname' )";
	
	//$result = $db->query($sql);
	//if (DB::isError($result)) {
    //	trigger_error($result->getMessage(), E_USER_ERROR);
	//}
	
	//$sql = "REPLACE INTO USER_LEV VALUES ('$uid', '$lev0', '$lev1', '$lev2', '$lev3', '$lev4', '$lev5', '$lev6', '$lev7', '$lev8', '$lev9', '$lev10', '$lev11', '$lev12', '$lev13', '$lev14', '$lev15', '$lev16' )";
	
	//$result = $db->query($sql);
	//if (DB::isError($result)) {
    //	trigger_error($result->getMessage(), E_USER_ERROR);
	//}
	
	$sql = "REPLACE INTO USER_PROD VALUES ('$uid', '$prod0', '$prod1', '$prod2', '$prod3', '$prod4', '$prod5', '$prod6','$prod7', '$prod8', '$prod9', '$prod10', '$prod11', '$prod12', '$prod13', '$prod14', '$prod15', '$prod16' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "REPLACE INTO USER_SKL VALUES ('$uid', '$skl0', '$skl1', '$skl2', '$skl3', '$skl4', '$skl5', '$skl6', '$skl7', '$skl8', '$skl9', '$skl10', '$skl11', '$skl12', '$skl13', '$skl14', '$skl15', '$skl16', '$skl17', '$skl18', '$skl19', '$skl20', '$skl21', '$skl22', '$skl23', '$skl24', '$skl25', '$skl26', '$skl27', '$skl28', '$skl29', '$skl30', '$skl31', '$skl32' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "REPLACE INTO USER_IP VALUES ('$uid', '$ip', '$date' )";

	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	upd_chk_skil($uid,$name);
	//LOGGING
	
	$sql = "INSERT INTO LOG_USER_DATA VALUES ('', '$uid', '$user_no', '$name', '$pass' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	//get new id
	$sql = "select `uid`,max(`id`) from `LOG_USER_DATA` where `uid` = '$uid' group by `uid`;";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$max_id = $user_rows["max(`id`)"];
	
	$sql = "INSERT INTO LOG_USER_STA VALUES ('$max_id', '$uid', '$class', '$anon', '$race', '$face', '$size', '$relm', '$mainjob', '$supportjob', '$point', '$mrank', '$comment' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_PLOF VALUES ('$max_id', '$uid','$prof', '$handle', '$polhn', '$mail', '$home', '$comment2', '$newname' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_LEV VALUES ('$max_id', '$uid','$lev0', '$lev1', '$lev2', '$lev3', '$lev4', '$lev5', '$lev6', '$lev7', '$lev8', '$lev9', '$lev10', '$lev11', '$lev12', '$lev13', '$lev14', '$lev15', '$lev16', '$lev17' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_PROD VALUES ('$max_id', '$uid','$prod0', '$prod1', '$prod2', '$prod3', '$prod4', '$prod5', '$prod6','$prod7', '$prod8', '$prod9', '$prod10', '$prod11', '$prod12', '$prod13', '$prod14', '$prod15', '$prod16' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_SKL VALUES ('$max_id', '$uid','$skl0', '$skl1', '$skl2', '$skl3', '$skl4', '$skl5', '$skl6', '$skl7', '$skl8', '$skl9', '$skl10', '$skl11', '$skl12', '$skl13', '$skl14', '$skl15', '$skl16', '$skl17', '$skl18', '$skl19', '$skl20', '$skl21', '$skl22', '$skl23', '$skl24', '$skl25', '$skl26', '$skl27', '$skl28', '$skl29', '$skl30', '$skl31', '$skl32' )";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO LOG_USER_IP VALUES ('$max_id', '$uid','$ip', '$date' )";

	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	sub_msg("5","list/$ML_SCRIPT","修正終了","修正を行いました。自動的にトップページへ戻ります。");
}

//chk_sta
$str_sp = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt;";
	function chk_stas($area,$user_rows,$log_rows){
		global $str_sp;

		if($user_rows["$area"] != $log_rows["$area"]){
			
			$str = "$str_sp"."$area が $log_rows[$area] から $user_rows[$area] に変更<BR>";
			return $str;
		}
	
	}

	function chk_lvs($area,$user_rows,$log_rows){
		global $str_sp,$JOBLIST;
		
		//$user_lev = $user_rows["area"];
		//$log_lev = $log_rows["area"];
		
		//$user_lev[$area]
		$area2 = "lev".$area;
		
		
		if($user_rows["$area2"] != $log_rows["$area2"]){
			//$str = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt;";
			if($user_rows["$area2"] >= 3){
				if($user_rows["$area2"] > $log_rows["$area2"]){
					$str = "$str_sp"."$JOBLIST[$area] が $log_rows[$area2] から $user_rows[$area2] にUP!!<BR>";
				}else{
					$str = "$str_sp"."$JOBLIST[$area] が $log_rows[$area2] から $user_rows[$area2] にDOWN...<BR>";
				}
			}else{
				$str = "$str_sp"."$JOBLIST[$area] が $log_rows[$area2] から $user_rows[$area2] に変更<BR>";
			}
			return $str;
		}
	
	}
	
	function chk_prod($area,$user_rows,$log_rows){
		global $str_sp,$PLODLIST;
		
		//$user_lev = $user_rows["area"];
		//$log_lev = $log_rows["area"];
		
		//$user_lev[$area]
		$area2 = "prod".$area;
		
		
		if($user_rows["$area2"] != $log_rows["$area2"]){
			//$str = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt;";
			if($user_rows["$area2"] > $log_rows["$area2"]){
				$str = "$str_sp"."$PLODLIST[$area] が $log_rows[$area2] から $user_rows[$area2] にUP!!<BR>";
			}else{
				$str = "$str_sp"."$PLODLIST[$area] が $log_rows[$area2] から $user_rows[$area2] にDOWN...<BR>";
			}
			
			return $str;
		}
	
	}

	function chk_skl($area,$user_rows,$log_rows){
		global $str_sp,$SKILLLIST;
		
		//$user_lev = $user_rows["area"];
		//$log_lev = $log_rows["area"];
		
		//$user_lev[$area]
		$area2 = "skl".$area;
		
		
		if($user_rows["$area2"] != $log_rows["$area2"]){
			//$str = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt;";
			if($user_rows["$area2"] > $log_rows["$area2"]){
				$str = "$str_sp"."$SKILLLIST[$area] が $log_rows[$area2] から $user_rows[$area2] にUP!!<BR>";
			}else{
				$str = "$str_sp"."$SKILLLIST[$area] が $log_rows[$area2] から $user_rows[$area2] にDOWN...<BR>";
			}
			
			return $str;
		}
	
	}

function upd_chk_lv($uid){
	global $db,$env_rows,$PLOF_SCRIPT,$RACELIST,$RELMLIST,$JOBLIST,$JOB_MAX,$FACENAME,$SIZELIST,$str_sp,$PHP_CUR_PASS;
	//var_dump($FACENAME);
	$sql = "select * from `LOG_USER_DATA` where `uid` = '$uid'";
	//var_dump($sql);
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if(!$chk){
		die("log faile");
	}
	
	$sql = "select `uid`,max(`id`) from `LOG_USER_DATA` where `uid` = '$uid' group by `uid`;";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$max_id = $user_rows["max(`id`)"];

	$str = "";
	
	$sql = "select * from USER_DATA , USER_STA , USER_LEV , USER_PLOF , USER_IP where USER_DATA.uid = '$uid' and USER_STA.uid = '$uid' and USER_LEV.uid = '$uid' and USER_PLOF.uid = '$uid' and USER_IP.uid = '$uid'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	
	//data
	$user_uid = $user_rows["uid"];
	$user_no = $user_rows["no"];
	$user_name = $user_rows["name"];
	$user_pass = $user_rows["pass"];
	
	//sta
	$user_class = $user_rows["class"];
	$user_anon = $user_rows["anon"];
	$user_race = $user_rows["race"];
	$user_faceid = $user_rows["face"];
	$user_size = $user_rows["size"];
	$user_relm = $user_rows["relm"];
	$user_mainjob = $user_rows["mainjob"];
	$user_supportjob = $user_rows["supportjob"];
	$user_point = $user_rows["point"];
	$user_mrank = $user_rows["mrank"];
	$user_comment = $user_rows["comment"];
	
	//plof
	$user_prof = $user_rows["prof_mode"];
	$user_handle = $user_rows["handle"];
	$user_polhn = $user_rows["polhn"];
	$user_mail = $user_rows["mail"];
	$user_home = $user_rows["url"];
	$user_comment2 = $user_rows["comment_plof"];
	$user_prof_img = $user_rows["prof_img"];
	
	//lev
	$user_lev[0] = $user_rows["lev0"];
	$user_lev[1] = $user_rows["lev1"];
	$user_lev[2] = $user_rows["lev2"];
	$user_lev[3] = $user_rows["lev3"];
	$user_lev[4] = $user_rows["lev4"];
	$user_lev[5] = $user_rows["lev5"];
	$user_lev[6] = $user_rows["lev6"];
	$user_lev[7] = $user_rows["lev7"];
	$user_lev[8] = $user_rows["lev8"];
	$user_lev[9] = $user_rows["lev9"];
	$user_lev[10] = $user_rows["lev10"];
	$user_lev[11] = $user_rows["lev11"];
	$user_lev[12] = $user_rows["lev12"];
	$user_lev[13] = $user_rows["lev13"];
	$user_lev[14] = $user_rows["lev14"];
	$user_lev[15] = $user_rows["lev15"];
	$user_lev[16] = $user_rows["lev16"];
	$user_lev[17] = $user_rows["lev17"];
	
	//$user_ip = $user_rows["ip"];
	//$user_date = $user_rows["date"];
	
	$sql = "select * from LOG_USER_DATA , LOG_USER_STA , LOG_USER_PLOF , LOG_USER_LEV , LOG_USER_IP where LOG_USER_DATA.id = LOG_USER_STA.id and LOG_USER_DATA.id = LOG_USER_PLOF.id and LOG_USER_DATA.id = LOG_USER_LEV.id and LOG_USER_DATA.id = LOG_USER_IP.id AND LOG_USER_DATA.id = '$max_id'";
	
	//var_dump($sql);

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$log_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	
	//data
	$user_no = $log_rows["no"];
	$str .= chk_stas("no",$user_rows,$log_rows);
	
	//$user_name = $log_rows["name"];
	$str .= chk_stas("name",$user_rows,$log_rows);
	
	$user_pass = $log_rows["pass"];
	$user_uid = $log_rows["uid"];
	
	//sta
	$user_class = $log_rows["class"];
	$str .= chk_stas("class",$user_rows,$log_rows);

	$user_anon = $log_rows["anon"];
	
	$log_mainjob = $log_rows["mainjob"];
	if($user_rows["mainjob"] != $log_rows["mainjob"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."メインジョブ が $JOBLIST[$log_mainjob] から $JOBLIST[$user_mainjob] に変更<BR>";
	}
	
	$log_supportjob = $log_rows["supportjob"];
	if($user_rows["supportjob"] != $log_rows["supportjob"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		if($log_supportjob == 99){
			$str .= "$str_sp"."サポートジョブ が なし から $JOBLIST[$user_supportjob] に変更<BR>";
		}elseif($user_supportjob == 99){
			
			$str .= "$str_sp"."サポートジョブ が $JOBLIST[$log_supportjob] から なし に変更<BR>";
		}else{
			$str .= "$str_sp"."サポートジョブ が $JOBLIST[$log_supportjob] から $JOBLIST[$user_supportjob] に変更<BR>";
		}
	}
	
	$user_comment = $log_rows["comment"];
	//if($user_rows["comment"] != $log_rows["comment"]){
	//	$str .= "$str_sp"."comment が $log_rows[comment] から $user_rows[comment] に変更<br>";
	//}
	$str .= chk_stas("comment",$user_rows,$log_rows);
	
	$log_race = $log_rows["race"];
	if($user_rows["race"] != $log_rows["race"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."race が $RACELIST[$log_race] から $RACELIST[$user_race] に変更<BR>";
	}
	
	$log_faceid = $log_rows["face"];
	if($user_rows["face"] != $log_rows["face"]){
		$pos_face_log = strpos($log_rows["face"],'b');
		if($pos_face_log){
			$log_faceid = intval($log_rows["face"]);
			$log_face = "$FACENAME[$log_faceid]"."B";
		}else{
			$log_face = "$FACENAME[$log_faceid]"."A";
		}
		
		$pos_face_user = strpos($user_rows["face"],'b');
		if($pos_face_user){
			$user_faceid = intval($user_rows["face"]);
			$user_face = "$FACENAME[$user_faceid]"."B";
		}else{
			$user_face = "$FACENAME[$user_faceid]"."A";
		}
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		//if(intval($user_rows["face"]) != intval($log_rows["face"])){
		//$log_faceid = intval($log_rows["face"]);
		//$user_faceid = intval($user_rows["face"]);
		$str .= "$str_sp"."face が $log_face から $user_face に変更<BR>";
		//}else{
		
		//}
	}
	
	$log_size = $log_rows["size"];
	if($user_rows["size"] != $log_rows["size"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."size が $SIZELIST[$log_size] から $SIZELIST[$user_size] に変更<BR>";
	}
	
	$log_relm = $log_rows["relm"];
	if($user_rows["relm"] != $log_rows["relm"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."relm が $RELMLIST[$log_relm] から $RELMLIST[$user_relm] に変更<BR>";
	}
	
	$log_point = $log_rows["point"];
	$str .= chk_stas("point",$user_rows,$log_rows);
	
	$log_mrank = $log_rows["mrank"];
	$str .= chk_stas("mrank",$user_rows,$log_rows);

	//$prof = $log_rows["prof"];
	$handle = $log_rows["handle"];
	if($user_rows["handle"] != $log_rows["handle"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."POL handle が変更<BR>";
	}
	
	$polhn = $log_rows["polhn"];
	if($user_rows["polhn"] != $log_rows["polhn"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."POL polhn が変更<BR>";
	}
	
	$mail = $log_rows["mail"];
	if($user_rows["mail"] != $log_rows["mail"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."POL mail が変更<BR>";
	}
	
	$home = $log_rows["home"];
	if($user_rows["home"] != $log_rows["home"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."POL home が変更<BR>";
	}
	
	$comment2 = $log_rows["comment2"];
	if($user_rows["comment2"] != $log_rows["comment2"]){
		//$str .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&gt; $str";
		$str .= "$str_sp"."POL comment2 が変更<BR>";
	}

	
	$user_lev[0] = $log_rows["lev0"];
	$str .= chk_lvs("0",$user_rows,$log_rows);

	$user_lev[1] = $log_rows["lev1"];
	$str .= chk_lvs("1",$user_rows,$log_rows);
	
	$user_lev[2] = $log_rows["lev2"];
	$str .= chk_lvs("2",$user_rows,$log_rows);
	
	$user_lev[3] = $log_rows["lev3"];
	$str .= chk_lvs("3",$user_rows,$log_rows);
	
	$user_lev[4] = $log_rows["lev4"];
	$str .= chk_lvs("4",$user_rows,$log_rows);
	
	$user_lev[5] = $log_rows["lev5"];
	$str .= chk_lvs("5",$user_rows,$log_rows);
	
	$user_lev[6] = $log_rows["lev6"];
	$str .= chk_lvs("6",$user_rows,$log_rows);
	
	$user_lev[7] = $log_rows["lev7"];
	$str .= chk_lvs("7",$user_rows,$log_rows);
	
	$user_lev[8] = $log_rows["lev8"];
	$str .= chk_lvs("8",$user_rows,$log_rows);
	
	$user_lev[9] = $log_rows["lev9"];
	$str .= chk_lvs("9",$user_rows,$log_rows);
	
	$user_lev[10] = $log_rows["lev10"];
	$str .= chk_lvs("10",$user_rows,$log_rows);
	
	$user_lev[11] = $log_rows["lev11"];
	$str .= chk_lvs("11",$user_rows,$log_rows);
	
	$user_lev[12] = $log_rows["lev12"];
	$str .= chk_lvs("12",$user_rows,$log_rows);
	
	$user_lev[13] = $log_rows["lev13"];
	$str .= chk_lvs("13",$user_rows,$log_rows);
	
	$user_lev[14] = $log_rows["lev14"];
	$str .= chk_lvs("14",$user_rows,$log_rows);
	
	$user_lev[15] = $log_rows["lev15"];
	$str .= chk_lvs("15",$user_rows,$log_rows);
	
	$user_lev[16] = $log_rows["lev16"];
	$str .= chk_lvs("16",$user_rows,$log_rows);
	
	$user_lev[17] = $log_rows["lev17"];
	$str .= chk_lvs("17",$user_rows,$log_rows);
	
	//$user_date = $log_rows["date"];	
	//$user_ip = $log_rows["ip"];
	//var_dump($str);
	//die();
	if($str){
		$txt = "<A href='$PHP_CUR_PASS"."list/"."$PLOF_SCRIPT?name=$user_name'>$user_name</A>さんのキャラが更新されました!!<BR>";
		//$txt .= "&gt;&gt; $str";
		$txt .= "$str";
		//die($txt);
		add_news(0,0,$txt);
	}
	
}

function upd_chk_skil($uid,$user_name){
	global $db,$PLOF_SCRIPT,$PHP_CUR_PASS;
	
	$sql = "select * from `LOG_USER_DATA` where `uid` = '$uid'";
	//var_dump($sql);
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if(!$chk){
		die("log faile");
	}
	
	$sql = "select `uid`,max(`id`) from `LOG_USER_DATA` where `uid` = '$uid' group by `uid`;";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$max_id = $user_rows["max(`id`)"];
	
	$str = "";
	
	$sql = "select * from `USER_DATA`,`USER_PROD`,`USER_SKL` where USER_DATA.uid = '$uid' and USER_PROD.uid = '$uid' and USER_SKL.uid = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	//var_dump($sql);
	//die();
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);

	$sql = "select * from LOG_USER_DATA , LOG_USER_PROD , LOG_USER_SKL , LOG_USER_IP where LOG_USER_DATA.id = LOG_USER_PROD.id AND LOG_USER_DATA.id = LOG_USER_SKL.id AND LOG_USER_DATA.id = LOG_USER_IP.id AND LOG_USER_DATA.id = '$max_id'";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$log_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	
	$prods = 16;
	for($i=0;$i<=$prods;$i++){
		$str .= chk_prod("$i",$user_rows,$log_rows);
	}
	
	
	//var_dump($str);
	//die();
	
	$skls = 32;
	for($i=0;$i<=$skls;$i++){
		$str .= chk_skl("$i",$user_rows,$log_rows);
	}
	//$str .= chk_skl("0",$user_rows,$log_rows);
	//var_dump($str);
	//die();
	
	if($str){
		$txt = "<A href='$PHP_CUR_PASS"."list/"."$PLOF_SCRIPT?name=$user_name'>$user_name</A>さんのキャラが更新されました!!<BR>";
		//$txt .= "&gt;&gt; $str";
		$txt .= "$str";
		//die($txt);
		add_news(0,0,$txt);
	}
	
}


?>