<?php

function write_form(){
	global $db,$env_rows,$uid,$name,$DISRY_SCRIPT;
	
	$sql = "select * from `USER_DATA` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$user_no = $user_rows["no"];
	
	$sql = "select max(no),uid from `USER_DIARY` where `uid` = '$uid' group by uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$last_no = $diary_rows["max(no)"];
	
	if($last_no == ""){
		$last_diary_date = "初めての日記っすね";
	}else{
		$sql = "select * from `USER_DIARY` where `uid` = '$uid' and `no` = '$last_no'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$diary_date = $diary_rows["date"];
		$diary_date = gmdate("Y/m/d (D) H:i:s", $diary_date+9*60*60);
		
		$diary_ip = $diary_rows["ip"];
	
		$last_diary_date = "$diary_date from $diary_ip";
	}
	$date = gmdate("Y/m/d (D) H:i:s", time()+9*60*60);
	echo "　No. $user_no : '$name' さんの日記。<br>";
	echo "　Last Updated : $last_diary_date <br>";
	echo "<form method=post enctype='multipart/form-data' action=$DIARY_SCRIPT><input type=hidden name=mode value=write_diary>\n
title<BR>
<input type=text name=title size=50 value='$date'><BR>
body<BR>
<textarea name=body cols=65 rows=10></textarea><BR>";

if($env_rows["img_allow"]){
echo "
画像 (許可拡張子：<b> .jpg .png .gif</b> 最大：3MB) <input type='file' name='upfile'>";
}
echo "<BR><input type=submit value=write></form>
";

}

function write_diary(){
	global $db,$env_rows,$uid,$name,$ML_SCRIPT,$arrowext,$limitk,$putdir,$W,$H,$sam_dir,$image_type;
	
	if(!last_up_diary_time($uid)){
		sub_msg("","","おっと。連続で送信されました。","送信ボタンは1回だけ押してください。");
	}
		
	$title = $_POST["title"];
	$body = $_POST["body"];
		
	if($title == "" || $body == ""){
		sub_msg("","","あら","題名か本文が記入されてませんよん");
	}
	
	$sql = "select max(no),uid from `USER_DIARY` where `uid` = '$uid' group by uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$last_no = $diary_rows["max(no)"];
			
	if($last_no == ""){
		$last_diary_no = 1;
	}else{
		$last_diary_no = $last_no+1;
	}
	
	//die("$last_diary_no $last_no");

	
	if($env_rows["img_allow"]){
		$upfile_size=$_FILES["upfile"]["size"];
		$upfile_name=$_FILES["upfile"]["name"];
		$upfile=$_FILES["upfile"]["tmp_name"];
	}
		
	if($upfile_name != ""){
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
		$up_name = ucfirst($name);
		$newname = $up_name.$last_diary_no.".$ext";
		move_uploaded_file($upfile, $putdir.$upfile_name);
		rename($putdir.$upfile_name, $putdir.$newname);
		$sam_size = getimagesize($putdir.$newname);
		if ($sam_size[0] > $W || $sam_size[1] > $H) {
			thumb_create($putdir.$newname,$W,$H,$sam_dir);
		}
	}
		
		$body = str_replace("\r\n", "\r", $body);
		$body = str_replace("\r", "\n", $body);
		$title = htmlspecialchars($title);
		$body = htmlspecialchars($body);
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = time();
		$unix_time = time();
		
		$sql = "INSERT INTO `USER_DIARY` VALUES ('','$uid','$last_diary_no','$title','$body','$newname','$ip','$date')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
		    trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$sql = "replace INTO `LASTDATE_DIARY` VALUES ('$uid','$unix_time')";
		
		$result = $db->query($sql);
		if (DB::isError($result)) {
		    trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$txt = "$name さんの日記が更新されました。<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$txt .= "&gt;&gt; <A href='list/profile.php?name=$name'>日記を見る。</A>";
		//die($txt);
		add_news(0,0,$txt);
		
		//die();
		sub_msg("5","list/$ML_SCRIPT","書き込み終了","日記を書き込みました。自動的にトップページに戻ります");
		

}


function last_up_diary_time($uid){
	global $db;
	
	$wait_time = "5";
	
	$sql = "select * from `LASTDATE_DIARY` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$di_row = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$max_date = $di_row["date"];
	
	if($max_date){
	
		$local_date = time();

		$last_time = $local_date - $max_date;
		
		if($last_time > $wait_time){
			return true;
		}else{
			return false;
		}
		
	}else{
		return true;
	}
	

}



function res_diary(){
	global $db,$uid,$env_rows,$PLOF_SCRIPT;
	$u_uid = $uid;
	//$indexcolor = $env_rows["indexcolor"];

	$diary_name = $_POST["diary_name"];
	$diary_no = $_POST["diary_no"];
	$name = $_POST["name"];
	$body = $_POST["body"];
	$pass = $_POST["pass"];
	
	if($name == "" || $body == ""){
		sub_msg("","","あら","名前か本文が記入されてませんよん");
	}
	
	if(!$u_uid && !$pass){
		$pass = "0123";
	}
	
	$body = str_replace("\r\n", "\r", $body);
	$body = str_replace("\r", "\n", $body);
	$body = htmlspecialchars($body);
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = time();
	$unix_time = time();
	
    mt_srand(microtime()*100000);
	$sid = md5(uniqid(mt_rand(),1));
	
	$sql = "select * from USER_DATA where `name` = '$diary_name'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	if($main_rows == ""){
		sub_msg("","","そのアカウントは登録されていません","正しいアカウントをお使いください");
	}else{
		$uid = $main_rows["uid"];
	}
	
		$sql = "select uid,max(di_no_res) as max_di_no_res , di_no from `USER_DIARY_RES` where `uid` = '$uid' and `di_no` = '$diary_no' group by `uid`";
		//die("$sql");
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		if($diary_rows == ""){
			$diary_last_no = 0;
		}else{
			$diary_last_no = $diary_rows["max_di_no_res"];
			$diary_last_no = $diary_last_no + 1;
		}
		
		$sql = "INSERT INTO `USER_DIARY_RES` VALUES ('', '$uid','$sid','$diary_no', '$diary_last_no', '$name','$u_uid', '$body', '$pass', '$ip', '$date')";
	
		$result = $db->query($sql);
	
		if (DB::isError($result)) {
  		  trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$sql = "select * from `LASTDATE_DIARY` where `uid` = '$uid'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
		    trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$lastdate_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$lastdate_uid = $lastdate_rows["uid"];
		
		if($lastdate_id){
			$sql = "replace INTO `LASTDATE_DIARY` VALUES ('$lastdate_uid','$unix_time')";
		}else{
			$sql = "replace INTO `LASTDATE_DIARY` VALUES ('$lastdate_uid','$unix_time')";
		}
		
		$result = $db->query($sql);
		if (DB::isError($result)) {
		    trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$mail_head ="日記にレスがありました";
		$body = "$diary_name さんの$diary_no 番の日記の$diary_last_no 番目のレス\n".$body;
		
		$txt = "$diary_name さんの日記で返信がありました。<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$txt .= "&gt;&gt; <A href='list/profile.php?name=$diary_name'>日記を見る。</A>";
		//die($txt);
		add_news(0,0,$txt);
		
		wb_sendmail(0,$diary_name,$name,$mail_head,$body);
		
		sub_msg("5","list/$PLOF_SCRIPT?name=$diary_name","書き込み終了","レスを書き込みました。自動的にトップページに戻ります");
}



function adv_res(){
	global $db,$c_name,$uid,$env_rows,$ML_SCRIPT,$DIARY_SCRIPT,$putdir,$sam_dir,$image_type,$STYLE;
	
	$diary_name = $_GET["diary_name"];
	$diary_no = $_GET["diary_no"];
	
	if($uid){
		$sql = "select `broadband`,`diary_mode` from `PHP_USR_STYLE` where `uid` = '$uid'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$b_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$b_broadband = $b_rows["broadband"];
	}else{
		$b_broadband = $STYLE["broadband"];
		$b_diary_mode = $STYLE["diary_mode"];
	}
	
	$sql = "select * from USER_DATA where `name` = '$diary_name'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	if($main_rows == ""){
		sub_msg("","","そのアカウントは登録されていません","正しいアカウントをお使いください");
	}
	$user_uid = $main_rows["uid"];
	
	$sql = "select * from `USER_DIARY` where `uid` = '$user_uid' AND `no` = '$diary_no'";
	//die("$sql");
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	echo "<table width=100%><TR><TD>レスを行います</TD></TR></table>";
	echo "　($diary_name さんの$diary_no 番の日記)。<br><br>";
	
	echo "<table border='0' width='100%' cellpadding='2' cellspacing='1' class='forumline'>\n";

	while( $diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
		$diary_no = $diary_rows["no"];
		$diary_title = $diary_rows["title"];
		$diary_text = $diary_rows["text"];
		$diary_img = $diary_rows["img"];
		$diary_date = $diary_rows["date"];
		$mod_date = gmdate("Y/m/d (D) H:i:s", $diary_date+9*60*60);
		$diary_text = str_replace("\n", "<br>", $diary_text);

		echo "<TR class='color2'><TD align=left><B>●$diary_title</B>\n";
		echo "</TD><TD align=right>$mod_date($diary_no)</TD></TR>\n";

		echo "<TR><TD colspan=2>\n";
		if($diary_img != ""){
			$picsize = filesize($putdir.$diary_img);
			$picsize = intval($picsize / 1024);
			$pic_alt = $diary_img." $picsize"."[kb]";
			
			$pos = strrpos($diary_img,".");	//拡張子取得
			$sam_img_name = substr($diary_img,0,$pos);
			//$ext = strtolower($ext);//小文字化
			
				if($image_type == "0"){
					$sam_img_name = $sam_img_name.".png";
				}else{
					$sam_img_name = $sam_img_name.".jpg";
				}
			
				if($b_broadband == "1"){
					if (file_exists($sam_dir.$sam_img_name)){
						echo "<a href='$putdir$diary_img' target='_blank'>\n";
						echo "<img src='$sam_dir$sam_img_name' height=150 width=200 border=0 alt='$pic_alt'></a><BR>\n";
					}else{
						echo "<a href='$putdir$diary_img' target='_blank'>\n";
						echo "<img src='$putdir$diary_img' height=150 width=200 border=0 alt='$pic_alt'></a><BR>\n";
					}
				}else{
					echo "<br>　　<a href='$putdir$diary_img' target='_blank'>この日記には画像があります。$pic_alt</a><br><BR>\n";
				}
		}
		echo "$diary_text<HR>\n";
			
		$sql_r = "select * from `USER_DIARY_RES` where `uid` = '$user_uid' AND `di_no` = '$diary_no' order by `di_no_res`";
		//var_dump($sql_r);
		$d_result = $db->query($sql_r);
			if (DB::isError($d_result)) {
				trigger_error($d_result->getMessage(), E_USER_ERROR);
			}
		$diary_c = $d_result->numRows();
		if($diary_c != 0){
			while( $res_diary_rows = $d_result->fetchRow(DB_FETCHMODE_ASSOC) ){
				$res_name = $res_diary_rows["name"];
				$di_no_res = $res_diary_rows["di_no_res"];
				$di_sid= $res_diary_rows["sid"];
				$res_text = $res_diary_rows["text"];
				if($res_name == $c_name){
					$res_name = "<a href='$DIARY_SCRIPT?mode=edit_res&sid=$di_sid'>$res_name</a>";
				}

				$res_text = str_replace("\n", "<br>", $res_text);

				echo "<TABLE cellpadding=0 cellspacing=0 border=0 width=100%>
        <TBODY>
          <TR>
            <TD width=2% valign=top>$res_name&nbsp;&gt;&nbsp;</TD>
            <TD>$res_text</TD>
          </TR>
        </TBODY>
      </TABLE>";
				}
		}
	}
	
	echo "</table><br>";
	
	echo "<form method=post enctype='multipart/form-data' action=$DIARY_SCRIPT><input type=hidden name=diary_name value=$diary_name>";
	echo "<input type=hidden name=diary_no value=$diary_no>\n";
	echo "<input type=hidden name=mode value=res_diary>\n";
	echo "name<BR><input type=text name=name size=80 value='$c_name'><BR>\n";
	echo "body<BR><textarea name=body cols=80 rows=10></textarea><BR>\n";
	echo "<br>pass<BR><input type=password name=pass size=10><BR>*ログインしている場合は特に指定しなくても大丈夫です。<BR><br>\n";
	echo "<input type=submit value=レス></form>\n";
}


function modify_diary(){
	global $db,$uid,$c_name,$env_rows,$PLOF_SCRIPT,$DIARY_SCRIPT,$putdir,$sam_dir,$image_type;

	$no = $_POST["no"];
	if(!$no){
		$no = $_GET["no"];
	}
	$name = $c_name;
	
	if($no == ""){
		sub_msg("","","$name さんの日記($no)はありません","正しいnoをお使いください");
	}
	$mode = $_POST["mode"];
	$date = time();
	
		$sql = "select * from `USER_DIARY` where `uid` = '$uid' and `no` = '$no'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$diary_no = $diary_rows["no"];
		$diary_id = $diary_rows["id"];
		
	if($mode == "modify"){
	//die("modify");
		$title = $_POST["title"];
		$body = $_POST["body"];
		
		if($title == "" || $body == ""){
			sub_msg("","","あら","題名か本文が記入されてませんよん");
		}
		
		$sql = "select `uid`,`di_no`,count(di_no_res) from `USER_DIARY_RES` where `uid` = '$uid' AND `di_no` = '$diary_no' group by `uid`";
		//die("$sql");
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$di_c_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$res_count = $di_c_rows["count(di_no_res)"];
		
		if(!$_POST["del_img"]){
			$diary_img = $diary_rows["img"];
		}else{
			$diary_img_del = $diary_rows["img"];
			$pos = strrpos($diary_img_del,".");	//拡張子取得
			$sam_img_name = substr($diary_img_del,0,$pos);
			if($image_type == "0"){
				$sam_img_name = $sam_img_name.".png";
			}else{
				$sam_img_name = $sam_img_name.".jpg";
			}
			unlink($putdir.$diary_img_del);
			unlink($sam_dir.$sam_img_name);
		}
		
		

		
		$body = str_replace("\r\n", "\r", $body);
		$body = str_replace("\r", "\n", $body);
		$title = htmlspecialchars($title);
		$body = htmlspecialchars($body);
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = time();
		$unix_time = time();
		
		$sql = "replace INTO `USER_DIARY` VALUES ('$diary_id','$uid','$diary_no','$title','$body','$diary_img','$ip','$date')";
		//die($sql);
		$result = $db->query($sql);
		if (DB::isError($result)) {
		    trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		if($res_count){
			$sql = "select * from `USER_DIARY_RES` where `uid` = '$uid' and `di_no` = '$diary_no' order by `di_no_res`";
			$result = $db->query($sql);
			if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
			}
			//die("$sql");
			while( $res_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){

				$res_id = $res_rows["id"];
				$res_sid = $res_rows["sid"];
				$di_no_res = $res_rows["di_no_res"];
				$pass = $res_rows["pass"];
				$ip = $res_rows["ip"];
				$date = $res_rows["date"];
				
				$r_name = $_POST["r_name_$di_no_res"];
				$r_body = $_POST["r_body_$di_no_res"];
				$r_del = $_POST["res_del_$di_no_res"];
				
				$r_body = str_replace("\r\n", "\r", $r_body);
				$r_body = str_replace("\r", "\n", $r_body);
				$r_body = htmlspecialchars($r_body);
				
				if(!$res_id || !$r_name || !$r_body){
				echo "1>$res_id 2>$r_name 3>$r_body 4>$di_no_res";
					sub_msg("","","error function 1000","NO res_id NO r_nname NO r_body ");
				}
				
				if($r_del == 1){
					$sql = "delete from `USER_DIARY_RES` where `id` = '$res_id'";
				}else{
					$sql = "replace INTO `USER_DIARY_RES` VALUES ('$res_id','$uid','$res_sid', '$diary_no', '$di_no_res', '$r_name', '$u_uid' ,'$r_body', '$pass', '$ip', '$date')";
				}
				
		
		//die($sql);
		//var_dump($sql);
				$m_result = $db->query($sql);
				if (DB::isError($m_result)) {
				    //die ($m_result->getMessage());
					trigger_error($m_result->getMessage(), E_USER_ERROR);
				}
			}
		}

		$sql = "replace INTO `LASTDATE_DIARY` VALUES ('$uid','$unix_time')";
				
		$result = $db->query($sql);
		if (DB::isError($result)) {
		    trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		sub_msg("5","list/$PLOF_SCRIPT?name=$name","書き込み終了","日記($diary_no)を修正しました。自動的に戻ります");

	}elseif($mode == "del"){
		$diary_img_del = $diary_rows["img"];
		
		if($diary_img_del){
			$pos = strrpos($diary_img_del,".");	//拡張子取得
			$sam_img_name = substr($diary_img_del,0,$pos);
			if($image_type == "0"){
				$sam_img_name = $sam_img_name.".png";
			}else{
				$sam_img_name = $sam_img_name.".jpg";
			}
			unlink($putdir.$diary_img_del);
			unlink($sam_dir.$sam_img_name);
		}
	
		$sql = "delete from `USER_DIARY` where `id` = '$diary_id' AND `uid` = '$uid'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$sql = "select `di_no`,`id`,`uid` from `USER_DIARY_RES` where `uid` = '$uid' AND `di_no` = '$diary_no'";
		//die("$sql");
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		while( $di_c_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
			$del_res_id = $di_c_rows["id"];
			$sql = "delete from `USER_DIARY_RES` where `id` = '$del_res_id'";
			$d_result = $db->query($sql);
			if (DB::isError($d_result)) {
				trigger_error($d_result->getMessage(), E_USER_ERROR);
			}
		}
		
		sub_msg("5","list/$PLOF_SCRIPT?name=$name","削除完了","日記($diary_no)を削除しました。自動的に戻ります");
		
	}else{


		$diary_no = $diary_rows["no"];
		$diary_title = $diary_rows["title"];
		$diary_text = $diary_rows["text"];
		$diary_img = $diary_rows["img"];
		$diary_date = $diary_rows["date"];
		$diary_ip = $diary_rows["ip"];
		$diary_date = gmdate("Y/m/d (D) H:i:s", $diary_date+9*60*60);
		$last_diary_date = "$diary_date from $diary_ip";
		

	echo "　No. $user_no : '$name' さんの日記($diary_no)。<br>";
	echo "　Last Updated : $last_diary_date <br>";
	echo "<form method=post enctype='multipart/form-data' action=$DIARY_SCRIPT>";
	//echo "<input type=hidden name=mode value=edit_diary>";
	echo "<input type=hidden name=no value=$diary_no>\n";
	echo "title<BR><input type=text name=title size=80 value='$diary_title'><BR>\n";
	echo "body<BR><textarea name=body cols=80 rows=10>$diary_text</textarea><BR>\n";

	$sql_r = "select * from `USER_DIARY_RES` where `uid` = '$uid' AND `di_no` = '$diary_no' order by `di_no_res`";
	$d_result = $db->query($sql_r);
		if (DB::isError($d_result)) {
			trigger_error($d_result->getMessage(), E_USER_ERROR);
		}
	$diary_c = $d_result->numRows();
		if($diary_c != 0){
			//$rd_count = 0;
			echo "<br>res<br>";
			while( $res_diary_rows = $d_result->fetchRow(DB_FETCHMODE_ASSOC) ){
				$res_di_no_res = $res_diary_rows["di_no_res"];
				$res_name = $res_diary_rows["name"];
				$res_text = $res_diary_rows["text"];
				
				//if(strpos($res_text , "\n")){
				
				$res_text2 = str_replace("\n", "", $res_text);
				
				$res_text_count = mb_strwidth($res_text2);
				
				$res_text_count = intval($res_text_count / 40 );
				
				$res_text_count = $res_text_count + 3 ;
				
				
				if($res_text_count == 3){$res_text_count = 5;}
				
				
			echo "name<BR><input type=text name=r_name_$res_di_no_res size=10 value='$res_name'><BR>\n";
			echo "body<BR><textarea name=r_body_$res_di_no_res cols=80 rows=$res_text_count>$res_text</textarea><BR>\n";
			echo "このレスを削除<input type=checkbox  name=res_del_$res_di_no_res value=1><br><br>";
			//$rd_count++;
				}
			echo "<BR>";
	  }
	
	if($diary_img){echo "<input type=checkbox name=del_img value=delete>画像の消去<BR>\n";}
	echo "<select name=mode><option value=del>削除</option><option value=modify selected>修正</option></select>\n";
	echo "<input type=submit value=modify></form>\n";

	}

}


function my_res_edit(){
	global $db,$uid,$env_rows,$PLOF_SCRIPT,$DIARY_SCRIPT;
	
	//$indexcolor = $env_rows["indexcolor"];
	//$c_pass = $env_rows["c_pass"];
	
	//$di_no = $_POST["diary_no"];
	//if(!$di_no){
	//	$di_no = $_GET["diary_no"];
	//}
	
	//$di_no_res = $_POST["diary_res_no"];
	///if(!$di_no_res){
	//	$di_no_res = $_GET["diary_res_no"];
	//}
	
	//$name = $_POST["diary_name"];
	//if(!$name){
	//	$name = $_GET["diary_name"];
	//}
	$res_name = $_POST["name"];
	if(!$res_name){
		$res_name = $_GET["name"];
	}
	$res_pass = $_POST["res_pass"];
	if(!$res_pass){
		$res_pass = $_GET["res_pass"];
	}
	
	$sid = $_POST["sid"];
	if(!$sid){
		$sid = $_GET["sid"];
	}
	
	$mode = $_POST["modify_mode"];
	$date = time();
	
	$sql = "select * from `USER_DIARY_RES` where `sid` = '$sid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	$diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$diary_name = $diary_rows["name"];
	$u_uid = $diary_rows["u_uid"];
	$m_uid = $diary_rows["uid"];
	if(!$sid || !$chk){
		sub_msg("","","レスはありません","正しいレスをお使いください");
	}
	
	$sql = "select * from `USER_DATA` where `uid` = '$m_uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$m_name = $user_rows["name"];
	if(!$chk){
		sub_msg("","","$m_name さんは未登録です","正しい名前を選択してください");
	}

	
	if($mode == "modify" || $mode == "del"){
	//die("modify");
		$res_name = $_POST["name"];
		$res_body = $_POST["body"];
		
		if($res_name == "" || $res_body == ""){
			sub_msg("","","あら","名前か本文が記入されてませんよん");
		}
		
		if($u_uid == $uid){
			$sql = "select * from `USER_DIARY_RES` where `sid` = '$sid'";
		}else{
			$sql = "select * from `USER_DIARY_RES` where `sid` = '$sid' AND `pass` = password('$res_pass') and `name` = '$res_name'";
		}
		//die("$sql");
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		if($user_rows == ""){
			sub_msg("","","その名前・パスワードは正しくありません","正しい名前・パスワードをお使いください");
		}
		$res_id = $user_rows["id"];
		$res_di_no = $user_rows["di_no"];
		$res_di_no_res = $user_rows["di_no_res"];
		$res_pass = $user_rows["pass"];
		$u_uid = $user_rows["u_uid"];
		
		$res_body = str_replace("\r\n", "\r", $res_body);
		$res_body = str_replace("\r", "\n", $res_body);
		$res_name = htmlspecialchars($res_name);
		$res_body = htmlspecialchars($res_body);
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = time();
		$unix_time = time();
		
			if(!$res_name || !$res_body){
				echo "1>$res_id 2>$r_name ";
				sub_msg("","","error function 1000","NO r_text NO r_name  ");
			}
				
			if($mode == "del"){
				$sql = "delete from `USER_DIARY_RES` where `sid` = '$sid' AND `id` = '$res_id'";
			}else{
				$sql = "replace INTO `USER_DIARY_RES` VALUES ('$res_id','$uid','$sid', '$res_di_no', '$res_di_no_res', '$res_name','$u_uid', '$res_body', '$res_pass', '$ip', '$date')";
			}
			//die($sql);
			$result = $db->query($sql);
			if (DB::isError($result)) {
			    trigger_error($result->getMessage(), E_USER_ERROR);
			}
		
		$sql = "select * from `LASTDATE_DIARY` where `uid` = '$user_uid'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
		    trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$lastdate_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$lastdate_uid = $lastdate_rows["uid"];
		
		if($lastdate_id){
			$sql = "replace INTO `LASTDATE_DIARY` VALUES ('$lastdate_uid','$unix_time')";
		}else{
			$sql = "replace INTO `LASTDATE_DIARY` VALUES ('$lastdate_uid','$unix_time')";
		}
		
		$result = $db->query($sql);
		if (DB::isError($result)) {
		    trigger_error($result->getMessage(), E_USER_ERROR);
		}

		sub_msg("5","list/$PLOF_SCRIPT?name=$m_name","修正終了","レス($res_name さんの$res_di_no 番の日記)を修正しました。自動的に戻りますす");

	}else{


		$res_di_no = $diary_rows["di_no"];
		$res_di_no_res = $diary_rows["di_no_res"];
		$res_name = $diary_rows["name"];
		$res_text = $diary_rows["text"];
		$res_pass = $diary_rows["pass"];
		$res_date = $diary_rows["date"];
		$res_ip = $diary_rows["ip"];
		$res_date = gmdate("Y/m/d (D) H:i:s", $res_date+9*60*60);
		$last_diary_date = "$res_date from $res_ip";
		
	echo "　'$res_name' さんのレス($m_name さんの$res_di_no 番の日記)。<br>";
	echo "　Last Updated : $last_diary_date <br>";
	echo "<form method=post enctype='multipart/form-data' action=$DIARY_SCRIPT><input type=hidden name=diary_name value=$name>";
	echo "<input type=hidden name=sid value=$sid>\n";

	echo "name<BR><input type=text name=name size=80 value='$res_name'><BR>\n";
	echo "body<BR><textarea name=body cols=80 rows=10>$res_text</textarea><BR>\n";
	echo "<br>pass<BR><input type=password name=res_pass size=10 value=><BR><br>\n";
	echo "<select name=modify_mode><option value=del>削除</option><option value=modify selected>修正</option></select>\n";
	echo "<input type=submit value=modify></form>\n";

	}

}


?>