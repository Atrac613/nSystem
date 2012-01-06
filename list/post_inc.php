<?php


function show_post_all($user_uid,$user_name){
	global $db,$env_rows,$POST_SCRIPT,$mog_net_group,$mog_net_domain;
	
	$send_user_mail = $user_name."@".$mog_net_group.".".$mog_net_domain;
	
	$sql = "select * from `POST_IN` where `receive` = '$send_user_mail'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$post_row = $result->numRows();
	
	$time_id = time_id();
	
	echo "<HR><BR>";
	
	if($post_row == 0){
		echo "<table cellpading=0 width=100% class=forumline><TR class=\"table_title\"><TH>$user_name さんへの手紙はありません。</TH></TR></table><BR>";
	}else{
		$sql = "select * from `POST_IN` where `receive` = '$send_user_mail' and `chk` = '0' order by `date` desc";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$post_row = $result->numRows();
		if($post_row == 0){
			echo "<table cellpading=0 width=100% class=forumline><TR class=\"table_title\"><TH>$user_name さんへの新着メッセージはありません。</TH></TR></table><br>";
		}else{
		?>
<TABLE width=100% cellpading=0 class=forumline>
  <TBODY>
    <TR class="table_title">
      <TD width=20%><B>送信者</B></TD>
	  <TD width=4% rowspan="2"><B>読む</B></TD>
	  <TD width=4% rowspan="2"><B>削除</B></TD>
	  <TD width=8% rowspan="2"><B>送信日</B></TD>
    </TR>
	<TR class="table_title">
      <TD width=20%><B>件名</B></TD>
	</TR><?php
	$result = $db->query($sql);
	while($post_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$post_id = $post_rows["id"];
		$post_code = $post_rows["code"];
		$post_send = $post_rows["send"];
		$post_title = $post_rows["title"];
		$post_chk = $post_rows["chk"];
		$post_date = $post_rows["date"];
		$post_date = gmdate("y/m/d H:i:s", $post_date+9*60*60);
		
		if($post_chk == 0){
			$post_title = "<B>$post_title</B>";
		}
	
	echo "
	<form method=post enctype='multipart/form-data' action=$POST_SCRIPT>
	<input type=hidden name=post value=in>
	<input type=hidden name=id value=$post_id>
	<input type=hidden name=code value=$post_code>
    <TR>
      <TD class='row0'>$post_send</TD>
	  <TD rowspan=2 class='row0'><INPUT type=submit value=read name=mode></TD>
	  <TD rowspan=2 class='row0'><INPUT type=submit value=del name=mode></TD></FORM>
	  <TD rowspan=2 class='row0'>$post_date</TD>
    </TR>
	<TR>
      <TD class='row0'>&gt;$post_title</TD>
	</TR>
    <TR>
      <TD class='spaceRow' colspan='5' height='1'><IMG src='../img/spacer.gif' width='1' height='1'></TD>
    </TR>
	";
	
		}
	
	echo "
  </TBODY>
</TABLE></CENTER><br>
";
		}
	}
	?>
	<CENTER>
<TABLE cellpading=0 class=forumline>
  <TBODY>
    <TR class="table_title">
      <TH colspan=2>モグポストメニュー</TH>
    </TR>
	<form method=post enctype='multipart/form-data' action="<?php echo "$POST_SCRIPT?TUID=$time_id"; ?>"><input type=hidden name=mode value=in>
    <TR>
      <TD width=69>読んだ手紙</TD>
      <TD width=100 align=center><INPUT type=submit value="  クポ  "></TD></FORM>
    </TR>
		<form method=post enctype='multipart/form-data' action="<?php echo "$POST_SCRIPT?TUID=$time_id"; ?>"><input type=hidden name=mode value=out>
    <TR>
      <TD width=69>送った手紙</TD>
      <TD width=100 align=center><INPUT type=submit value="  クポ  "></TD></FORM>
    </TR>
	<form method=post enctype='multipart/form-data' action="<?php echo "$POST_SCRIPT?TUID=$time_id"; ?>"><input type=hidden name=mode value=new>
    <TR>
      <TD width=69>手紙を書く</TD>
      <TD width=100 align=center><INPUT type=submit value="  クポ  "></TD></FORM>
    </TR>
	<form method=post enctype='multipart/form-data' action="<?php echo "$POST_SCRIPT?TUID=$time_id"; ?>">
    <TR>
      <TD width=69>リロード</TD>
      <TD width=100 align=center><INPUT type=submit value="  クポ  "></TD></FORM>
    </TR>
  </TBODY>
</TABLE>
</CENTER><BR>
	<?php
}


function post_in($user_name,$user_uid){
	global $db,$POST_SCRIPT,$mog_net_group,$mog_net_domain;
	
	$time_id = time_id();
	
	$send_user_mail = $user_name."@".$mog_net_group.".".$mog_net_domain;
	
	$sql = "select * from `POST_IN` where `receive` = '$send_user_mail' order by `date` desc";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
			echo "<CENTER>";


	$post_rows = $result->numRows();
	if($post_rows == 0){
		echo "<table cellpading=0 width=100% class=forumline><TR class=\"table_title\"><TH>$user_name さんへの手紙はありません。</TH></TR>";
	}else{
		?>
<TABLE width=100% cellpading=0 class=forumline>
  <TBODY>
    <TR class="table_title">
      <TD width=20%><B>送信者</B></TD>
	  <TD width=4% rowspan="2"><B>読む</B></TD>
	  <TD width=4% rowspan="2"><B>削除</B></TD>
	  <TD width=8% rowspan="2"><B>送信日</B></TD>
    </TR>
	<TR class="table_title">
      <TD width=20%><B>件名</B></TD>
	</TR><?php
	
		while($post_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$post_id = $post_rows["id"];
			$post_code = $post_rows["code"];
			$post_send = $post_rows["send"];
			$post_title = $post_rows["title"];
			$post_chk = $post_rows["chk"];
			$post_date = $post_rows["date"];
			$post_date = gmdate("y/m/d H:i:s", $post_date+9*60*60);
		
			if($post_chk == 0){
				$post_title = "<B>$post_title</B>";
			}
			
	echo "
	<form method=post enctype='multipart/form-data' action=$POST_SCRIPT>
	<input type=hidden name=post value=in>
	<input type=hidden name=id value=$post_id>
	<input type=hidden name=code value=$post_code>
    <TR>
      <TD class='row0'>$post_send</TD>
	  <TD rowspan=2 class='row0'><INPUT type=submit value=read name=mode></TD>
	  <TD rowspan=2 class='row0'><INPUT type=submit value=del name=mode></TD></FORM>
	  <TD rowspan=2 class='row0'>$post_date</TD>
    </TR>
	<TR>
      <TD class='row0'>&gt;$post_title</TD>
	</TR>
    <TR>
      <TD class='spaceRow' colspan='5' height='1'><IMG src='../img/spacer.gif' width='1' height='1'></TD>
    </TR>
	";
	
		}
	
	}
	
	echo "
  </TBODY>
</TABLE></CENTER><br>
<form method=post enctype='multipart/form-data' action=$POST_SCRIPT?TUID=$time_id>
<INPUT type=submit value=戻る></FORM>
";
}

function post_out($user_name,$uid){
	global $db,$POST_SCRIPT,$mog_net_group,$mog_net_domain;
	
	$time_id = time_id();
	
	$send_user_mail = $user_name."@".$mog_net_group.".".$mog_net_domain;
	
	$sql = "select * from `POST_OUT` where `send` = '$send_user_mail' order by `date` desc";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
			echo "<CENTER>";

	$post_rows = $result->numRows();
	if($post_rows == 0){
		echo "<table cellpading=0 width=100% class=forumline><TR class=\"table_title\"><TH>送った手紙はありません。</TH></TR>";
	}else{
	
		?>
<TABLE width=100% cellpading=0 class=forumline>
  <TBODY>
    <TR class="table_title">
      <TD width=20%><B>送信先</B></TD>

	  <TD width=5% rowspan="2"><B>送信確認</B></TD>
	  <TD width=4% rowspan="2"><B>読む</B></TD>
	  <TD width=4% rowspan="2"><B>削除</B></TD>
	  <TD width=8% rowspan="2"><B>送信日</B></TD>
    </TR>
	<TR class="table_title">
      <TD width=20%><B>件名</B></TD>
	</TR><?php
	
	while($post_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$post_id = $post_rows["id"];
		$post_code = $post_rows["code"];
		$post_receive = $post_rows["receive"];
		$post_title = $post_rows["title"];
		$post_chk = $post_rows["chk"];
		$post_u_chk = $post_rows["u_chk"];
		$post_date = $post_rows["date"];
		$post_date = gmdate("y/m/d H:i:s", $post_date+9*60*60);
		
		if($post_u_chk == 0){
			$post_u_chk = "-";
		}elseif($post_u_chk == 1){
			$post_u_chk = "??";
		}else{
			$post_u_chk = "read";
		}
		
	echo "
	<form method=post enctype='multipart/form-data' action=$POST_SCRIPT>
	<input type=hidden name=post value=out>
	<input type=hidden name=id value=$post_id>
	<input type=hidden name=code value=$post_code>
    <TR>
      <TD class='row0'>$post_receive</TD>

	  <TD rowspan=2 class='row0'>$post_u_chk</TD>
	  <TD rowspan=2 class='row0'><INPUT type=submit value=read name=mode></TD>
	  <TD rowspan=2 class='row0'><INPUT type=submit value=del name=mode></TD></FORM>
	  <TD rowspan=2 class='row0'>$post_date</TD>
    </TR>
	<TR>
      <TD class='row0'>&gt;$post_title</TD>
	</TR>
    <TR>
      <TD class='spaceRow' colspan='5' height='1'><IMG src='../img/spacer.gif' width='1' height='1'></TD>
    </TR>
	";
	
		}
	}
	echo "
  </TBODY>
</TABLE></CENTER><br>
<form method=post enctype='multipart/form-data' action=$POST_SCRIPT?TUID=$time_id>
<INPUT type=submit value=戻る></FORM>
";
}

function form_post($user_name,$uid){
	global $POST_SCRIPT,$mog_net_group,$mog_net_domain;
	
	$receive = $_POST["send_for"];
	
	if($_POST["re_title"]){
		$re_title = "Re:";
		$re_title .= $_POST["re_title"];
	}
	
	$name = $_GET["name"];
	if(isset($name)){
		$receive = $name."@".$mog_net_group.".".$mog_net_domain;
	}
	
	$receive = strtolower($receive);
	
	$time_id = time_id();
	
	$send_user_mail = $user_name."@".$mog_net_group.".".$mog_net_domain;
	$send_user_mail = strtolower($send_user_mail);
	
		echo"
		<CENTER>
<TABLE width=100% cellpading=0 class=tbl>
  <TBODY>
	<form method=post enctype='multipart/form-data' action=$POST_SCRIPT>
	<TR>
      <TD colspan=2>手紙を送る</TD>
    </TR>
    <TR>
      <TD width=68>送信者：</TD>
      <TD>$send_user_mail</TD>
    </TR>
    <TR>
      <TD width=68>宛先：</TD>
      <TD><input type=text name=receive size=40 value='$receive'></TD>
    </TR>
    <TR>
      <TD width=68>タイトル：</TD>
      <TD><input type=text name=title size=50 value='$re_title'></TD>
    </TR>
    <TR>
      <TD width=68>本文：</TD>
      <TD><TEXTAREA rows=14 cols=50 name=text></TEXTAREA></TD>
    </TR>
    <TR>
      <TD>開封確認：</TD>
      <TD>
      <SELECT name=u_chk>
        <OPTION value=0 selected>しない</OPTION>
        <OPTION value=1>する</OPTION>
      </SELECT>
      </TD>
    </TR>
    <TR>
      <TD width=68>クポ</TD>
      <TD><INPUT type=submit value=send name=mode></TD></FORM>
    </TR>
  </TBODY>
</TABLE>
</CENTER><BR>
<form method=post enctype='multipart/form-data' action=$POST_SCRIPT?TUID=$time_id>
<INPUT type=submit value=戻る></FORM>
";

}


function send_post($user_name){
	global $db,$mog_net_group,$mog_net_domain,$POST_SCRIPT;
	
	$db2 = db_init2();
	
	$send = $user_name."@".$mog_net_group.".".$mog_net_domain;
	$send = strtolower($send);
	$receive = $_POST["receive"];
	$receive = strtolower($receive);
	$title = $_POST["title"];
	$text = $_POST["text"];
	$u_chk = $_POST["u_chk"];

	if(!$receive || !$title || !$text){
		sub_msg("","","クポ・・","記入もれがあるクポ");
	}
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = time();
	
	$time_id = time_id();
	$code = md5($time_id);
	
	$time_id = time_id();
	$code2 = md5($time_id);
	
	$text = str_replace("\r\n", "\r", $text);
	$text = str_replace("\r", "\n", $text);
	$title = htmlspecialchars($title);
	$text = htmlspecialchars($text);
	
	$f_pos = strrpos($receive,"@");
	$f_receive = substr($receive,0,$f_pos);
	$pos = strrpos($receive,".");
	$e_receive = substr($receive,$pos+1,strlen($receive) - $pos);
	$count_receive = strlen($receive);
	$count_f_receive = strlen($f_receive) + 1;
	$count_e_receive = strlen($e_receive) + 1;
	$center_count = $count_receive - $count_f_receive - $count_e_receive;
	$c_receive = substr($receive,$f_pos+1,$center_count);
	
	if($e_receive != $mog_net_domain){
		sub_msg("","","error -mail-","モグネット以外へは送信できません");
	}
	
	//if($c_receive != $mog_net_group){
	//	sub_msg("","","クポ・・","モグネットのグループに '$c_receive' はないクポよ");
	//}
	
	$sql = "select * from `linkshell` where `ls_name` = '$c_receive'";
	$result = $db2->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$chk = $result->numRows();
	
	if(!$chk){
		sub_msg("","","クポ・・","モグネットのグループに '$c_receive' はないクポよ");
	}
	
	$post_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$ls_name = $post_rows["ls_name"];
	
	$dbm = db_init_manual($ls_name);
	
	$sql = "select * from `USER_DATA` where `name` = '$f_receive'";
	$result = $dbm->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$post_rows = $result->numRows();
	if($post_rows == 0){
		sub_msg("","","クポ・・","宛先 '$receive' が見つからなかったクポ");
	}
	
	$sql = "INSERT INTO `POST_OUT` VALUES ('','$code','$send','$receive','$title','$text','0','$u_chk','$date','$ip')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "INSERT INTO `POST_IN` VALUES ('','$code2','$send','$receive','$title','$text','0','$u_chk','$date','$ip')";
	$result = $dbm->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	sub_msg("3","list/$POST_SCRIPT","Moogle","送ったクポ～♪");
	
}


function user_chk_post(){
	global $db;
	
	$msg_read = $_POST["msg_read"];
	
	if($msg_read == "yes"){
	
	$id = $_POST["id"];
	$code = $_POST["code"];
	
	$sql = "select * from `POST_IN` where `id` = '$id' and `code` = '$code'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$chk = $result->numRows();
	if(!$chk){
		sub_msg("","","error","id not found");
	}else{
		$post_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$post_id = $post_rows["id"];
		$sql = "UPDATE `POST_IN` SET `u_chk` = '3' WHERE `id` = '$post_id'";
		
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
	}
	
	$sql = "select * from `POST_OUT` where `id` = '$id'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$chk = $result->numRows();
	if($chk){
		$post_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$post_id = $post_rows["id"];
		$sql = "UPDATE `POST_OUT` SET `u_chk` = '3' WHERE `id` = '$post_id'";
		
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	}
	
	}

}


function show_post_msg(){
	global $db,$POST_SCRIPT,$c_name,$mog_net_group,$mog_net_domain;
	
	$id = $_POST["id"];
	$user_name = $c_name;
	$mode_post = $_POST["post"];
	$code = $_POST["code"];
	
	$send_user_mail = $user_name."@".$mog_net_group.".".$mog_net_domain;
	
	$time_id = time_id();
	
	if($mode_post == "in"){
		$sql = "select * from `POST_IN` where `id` = '$id' and `code` = '$code'";
	}else{
		$sql = "select * from `POST_OUT` where `id` = '$id' and `code` = '$code'";
	}
	
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$post_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	if(!$post_rows){
		sub_msg("","","error","id not found");
	}else{
		$post_id = $post_rows["id"];
		$post_code = $post_rows["code"];
		$post_send = $post_rows["send"];
		$post_receive = $post_rows["receive"];
		$post_title = $post_rows["title"];
		$post_text = $post_rows["text"];
		$post_chk = $post_rows["chk"];
		$post_u_chk = $post_rows["u_chk"];
		$post_date = $post_rows["date"];
		$post_ip = $post_rows["ip"];
		$post_date2 = gmdate("y/m/d H:i:s", $post_date+9*60*60);
		
		
		if($mode_post == "in" && $post_u_chk == 1){
			user_chk_post_form($post_send,$post_id,$post_code);
		}
		
		if($mode_post == "in"){
			$from_or_send = "から";
			$post_send_name = $post_send;
			
		}else{
			$from_or_send = "へ";
			$post_send_name = $post_receive;
		}
		
		if($mode_post == "in" && $post_chk == 0){
		
			$post_title = addslashes($post_title);
			$post_text = addslashes($post_text);
			
			$sql = "REPLACE INTO `POST_IN` VALUES ('$post_id','$post_code','$post_send','$post_receive','$post_title','$post_text','1','$post_u_chk','$post_date','$post_ip')";
			
			$result = $db->query($sql);
			
			if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
			}
		}
		
		$post_text = str_replace("\n", "<br>", $post_text);
		
		echo"
		<CENTER>
		
<TABLE width=100% cellpading=0 class=forumline>
  <TBODY>
	<TR class=\"table_title\">
      <TD colspan=2>$post_send_name さん".$from_or_send."の手紙</TD>
    </TR>
    <TR>
      <TD width=15% class=row2>送信者：</TD>
      <TD class=row0>$post_send</TD>
    </TR>
    <TR>
      <TD width=15% class=row2>宛先：</TD>
      <TD class=row0>$post_receive</TD>
    </TR>
    <TR>
      <TD width=15% class=row2>タイトル：</TD>
      <TD class=row0>$post_title</TD>
    </TR>
    <TR>
      <TD width=15% class=row2>本文：</TD>
      <TD class=row0>$post_text<BR><BR>[$post_date2]</TD>
    </TR>
	<form method=post enctype='multipart/form-data' action=$POST_SCRIPT><input type=hidden name=mode value=new>
	<input type=hidden name=send_for value=$post_send><input type=hidden name=re_title value=$post_title>
    <TR>
      <TD width=15% class=row2>クポ(返信)</TD>
      <TD class=row0><INPUT type=submit value=submit></TD></FORM>
    </TR>
  </TBODY>
</TABLE>

</CENTER>
<form method=post enctype='multipart/form-data' action=$POST_SCRIPT?TUID=$time_id>
<INPUT type=submit value=戻る></FORM>
";
	}
}

function user_chk_post_form($post_send,$post_id,$post_code){
	global $db,$POST_SCRIPT;
	
echo "
<TABLE width=100% cellpading=0 class=tbl>
  <TBODY>
    <TR>
      <TD>Moogle &gt; $post_send さんは手紙を読んだか知りたいみたいクポ</TD>
    </TR>
	<form method=post enctype='multipart/form-data' action=$POST_SCRIPT>
	<input type=hidden name=mode value=read>
	<input type=hidden name=post value=in>
	<input type=hidden name=id value=$post_id>
	<input type=hidden name=code value=$post_code>
    <TR>
      <TD><INPUT type=submit value=yes name=msg_read>　<INPUT type=submit value=no name=msg_read></TD></FORM>
    </TR>
  </TBODY>
</TABLE><BR>

";

}



function del_post($user_name,$user_uid){
	global $db,$POST_SCRIPT,$env_rows;

	$post_id = $_POST["id"];
	$mode_post = $_POST["post"];
	$post_code = $_POST["code"];
	$del_mode = $_POST["del_mode"];
	
	$time_id = time_id();
	
	if($del_mode == "yes"){
		if($mode_post == "out"){
			$sql = "delete from `POST_OUT` where `id` = '$post_id' and `code` = '$post_code'";
		}else{
			$sql = "delete from `POST_IN` where `id` = '$post_id' and `code` = '$post_code'";
		}
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		//echo "Moogle &gt; 捨てたクポ<BR><a href=$POST_SCRIPT?TUID=$time_id>モグポストへ戻る</a><BR><BR>";
		sub_msg("5","list/$POST_SCRIPT?TUID=$time_id","モグポスト","捨てたクポ…<BR>自動で戻るクポ");
		
	}elseif($del_mode == "no"){
		if($mode_post == "out"){
			post_out($user_name,$user_pass);
		}else{
			post_in($user_name,$user_pass);
		}
	}else{
echo "
<TABLE width=100% cellpading=0 class=tbl>
  <TBODY>
    <TR>
      <TD>Moogle &gt; 本当に捨ててもいいクポ？</TD>
    </TR>
	<form method=post enctype='multipart/form-data' action=$POST_SCRIPT>
	<input type=hidden name=mode value=del>
	<input type=hidden name=post value=$mode_post>
	<input type=hidden name=id value=$post_id>
	<input type=hidden name=code value=$post_code>
    <TR>
      <TD><INPUT type=submit value=yes name=del_mode>　<INPUT type=submit value=no name=del_mode></TD></FORM>
    </TR>
  </TBODY>
</TABLE><BR>
<form method=post enctype='multipart/form-data' action=$POST_SCRIPT?TUID=$time_id>
<INPUT type=submit value=戻る></FORM>
";
	}
	


}

?>