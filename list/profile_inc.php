<?php

function chk_bazaar2($user_uid,$user_name){
	global $db,$env_rows,$BAZ_SCRIPT;
	
	//$indexcolor = $env_rows["indexcolor"];
	$time_id = time_id();
	
	$sql = "select * from `BAZAAR` where `uid` = '$user_uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$bazaar_rows = $result->numRows();
	
	
	if($bazaar_rows == 0){	
		echo '<HR><table border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline"><TR class="table_title"><TH>'.$user_name.' さんのバザーはありません。</TH></TR></table>';
	}else{
		echo '<HR><table border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline"><TR class="table_title"><TH><a href="'.$BAZ_SCRIPT.'?mode=show_bazaar&name='.$user_name.'&TUID='.$time_id.'">'.$user_name.' さんはバザーを開いています。</a></TH></TR></table>';
	}
}


function show_diary($user_uid,$user_name){
	global $db,$uid,$env_rows,$putdir,$sam_dir,$image_type,$PLOF_SCRIPT,$DIARY_SCRIPT,$c_name,$STYLE;
	
	$showmax = $env_rows["show_max"];
	$diaryres = $env_rows["diary_res"];

	$sql = "select * from `USER_DIARY` where `uid` = '$user_uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$mod_id = $result->numRows();
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$offset = $_GET["offset"];
	if($offset == ""){
	$offset = "0";
	}
	
	if($mod_id == ""){	
		echo "<HR><table cellpading=0 width=100% class=forumline><TR class=\"table_title\"><TH>$user_name さんの日記はありません。</TH></TR></table>";
	}else{
		$sql = "select * from `USER_DATA` where `uid` = '$user_uid'";
				
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$mod_id2 = $result->numRows();
		$rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$user_id = $rows["id"];

			if($uid){
				
				if($mod_id2){
					$sql = "select `broadband`,`diary_mode` from `PHP_USR_STYLE` where `uid` = '$uid'";
					
					$result = $db->query($sql);
					if (DB::isError($result)) {
						trigger_error($result->getMessage(), E_USER_ERROR);
					}
					$b_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
					$b_broadband = $b_rows["broadband"];
					$b_diary_mode = $b_rows["diary_mode"];
					//var_dump($b_broadband);
				}
			}else{
				$b_broadband = $STYLE["broadband"];
				$b_diary_mode = $STYLE["diary_mode"];
			}
			
		$sql = "select * from `USER_DIARY` WHERE `uid` = '$user_uid' order by id desc limit $offset , $showmax";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		echo "<HR><table  border='0' width='100%' cellpadding='2' cellspacing='1' class='forumline'><TR class=\"table_title\"><TH>$user_name さんの日記。($showmax 件表示）";
		echo "</TH></TR></table><BR>\n";
		echo "<table  border='0' width='100%' cellpadding='2' cellspacing='1' class='forumline'>\n";
		
		while( $diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
			$diary_no = $diary_rows["no"];
			$diary_title = $diary_rows["title"];
			$diary_text = $diary_rows["text"];
			$diary_img = $diary_rows["img"];
			$diary_date = $diary_rows["date"];
			$mod_date = gmdate("Y/m/d (D) H:i:s", $diary_date+9*60*60);
			$diary_text = str_replace("\n", "<br>", $diary_text);
			$diary_text = make_clickable($diary_text);
			$new_news = new_news($diary_date);
			
			if($user_uid == $uid){
				echo "<TR class=\"table_title\"><TD align=left><B>●$diary_title $new_news ";
				echo "<a href='$DIARY_SCRIPT?mode=edit_diary&no=$diary_no'>[修正・削除]</a></B></TD>\n";
				echo "<TD align=right>$mod_date($diary_no)</TD></TR>";
			} else {
				echo "<TR class=\"table_title\"><TD align=left><B>●$diary_title $new_news</B>\n";
				echo "</TD><TD align=right>$mod_date($diary_no)</TD></TR>\n";
			}
			echo "<form method=post enctype='multipart/form-data' action=$DIARY_SCRIPT><TR><TD colspan=2>\n";
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
			
				if($b_broadband == "0"){
					echo "<br>　　<a href='$putdir$diary_img' target='_blank'>この日記には画像があります。$pic_alt</a><br><BR>\n";
				}else{
					if (file_exists($sam_dir.$sam_img_name)){
						echo "<a href='$putdir$diary_img' target='_blank'>\n";
						echo "<img src='$sam_dir$sam_img_name' height=150 width=200 border=0 alt='$pic_alt'></a><BR>\n";
					}else{
						echo "<a href='$putdir$diary_img' target='_blank'>\n";
						echo "<img src='$putdir$diary_img' height=150 width=200 border=0 alt='$pic_alt'></a><BR>\n";
					}
				}
			}
			echo "$diary_text<HR>\n";
			
			$sql_r = "select * from `USER_DIARY_RES` where `uid` = '$user_uid' AND `di_no` = '$diary_no' order by `di_no_res`";
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
					$date = $res_diary_rows["date"];
					if($res_name == $c_name){
						$res_name = "<a href='$DIARY_SCRIPT?mode=edit_res&sid=$di_sid'>$res_name</a>";
					}
					$res_text = str_replace("\n", "<br>", $res_text);
					$res_text = make_clickable2($res_text);
					$new_news = new_news($date);
					$res_text .= "&nbsp;$new_news"; 
					//$res_text2= "http://www.google.com";
					//var_dump(make_clickable($res_text2));
					
					//$res_name = "$res_name &gt;";
				echo "<TABLE cellpadding=0 cellspacing=0 border=0 width=100%>
        <TBODY>
          <TR>
            <TD width=2% valign=top>$res_name&nbsp;&gt;&nbsp;</TD>
            <TD>$res_text</TD>
          </TR>
        </TBODY>
      </TABLE>";
				//echo "<br>";
				}
			echo "<HR>";
			}
			
			if($diaryres != 0){
				if($b_diary_mode != 1){
					echo "名前<input type=text size=10 name=name value='$c_name'>\n";
					echo "パス<input type=password size=10 name=pass value=''>\n";
					echo "<input type=text size=80 name=body><input type=hidden name=mode value=res_diary>\n";
					echo "<input type=hidden name=diary_name value='$user_name'><input type=hidden name=diary_no value=$diary_no>\n";
					echo "<input type=submit value='レス'>\n";
				}
				if($b_diary_mode == 2 || $b_diary_mode == 1){
					echo " <a href='$DIARY_SCRIPT?mode=adv_res&diary_name=$user_name&diary_no=$diary_no'>この日記にレス</a>";
				}
			}
			echo "<hr></TD></TR></form>";
			
		}
		if($offset != 0){
		$prev = $offset - $showmax;
		$before="<a href='$PLOF_SCRIPT?mode=prof&name=$user_name&offset=$prev'>前のページ</a> <a href='$PLOF_SCRIPT?mode=prof&name=$user_name&offset=0'>&lt;TOP&gt;</a>";
		}
		//var_dump($mod_id);
		if($offset <= $mod_id){
		$next = $offset + $showmax;
			if($next > $mod_id){
				$after = "";
			}else{
				$after="<a href='$PLOF_SCRIPT?mode=prof&name=$user_name&offset=$next'>次のページ</a>";
			}
		}
		
		$sql = "select * from `USER_DIARY` WHERE `uid` = '$user_uid'";
		$result = $db->query($sql);
		$count = $result->numRows();
		$local_count =0;
		$page_links = "&nbsp;";
		while( $diary_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
			
			if(((($local_count) % ($showmax)) == 0)){
				$local_count2= $local_count / $showmax;
				$local_count2++;
				if($offset == $local_count){
					$page_links .= "<b>";
				}
				$page_links .="<a href='$PLOF_SCRIPT?mode=prof&name=$user_name&offset=$local_count'>$local_count2</a>";
				if($offset == $local_count){
					$page_links .= "</b>&nbsp;";
				}else{
					$page_links .= "&nbsp;";
				}
			}
			$local_count +=1;
		}
		//var_dump($count);

		echo "<TR><TD colspan=2> </TD></TR>";
		echo "<TR><TD colspan=2>$before $after || $page_links</TD></TR>";
		
		echo "</table>";
		
	}
}


?>