<?php

function show_bazaar_form($uid,$user_name){
	global $db,$BAZ_SCRIPT,$c_name;
	
	$sql = "select * from `BAZAAR` where `uid` = '$uid' ORDER BY `date` DESC";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$bazaar_rows = $result->numRows();
	
	
	if($bazaar_rows == 0){	
		echo "<HR><table cellpading=0 width=100% class=forumline><TR class=\"table_title\"><TH>$user_name ����̃o�U�[�͂���܂���B</TH></TR></table>";
	}else{
	
	
			?><CENTER>
<TABLE width=100% cellpading=0 class="forumline">
  <TBODY>
    <TR class="table_title">
      <TD rowspan="2" width=2%><B>No.</B></TD>
      <TD><B>�A�C�e��</B></TD>
      <TD rowspan="2" width=15%><B>�l�i</B></TD>
      <TD width=10%><B>�o�i��</B></TD>
      <TD rowspan="2" width=10%><B>�o�i��</B></TD>
      <TD rowspan="2" width=22%><B>���D (���O/��)</B></TD>
    </TR>
    <TR class="table_title">
      <TD><B>�R�����g</B></TD>
      <TD width=10%><B>���D��</B></TD>
    </TR>
	<?php
		while($bazaar_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$bazaar_id = $bazaar_rows["id"];
		$bazaar_no = $bazaar_rows["no"];
		$bazaar_item = $bazaar_rows["item"];
		$bazaar_text = $bazaar_rows["text"];
		$bazaar_gil = $bazaar_rows["gil"];
		$bazaar_in = $bazaar_rows["item_in"];
		$bazaar_out = $bazaar_rows["item_out"];
		$bazaar_date = $bazaar_rows["date"];
		$bazaar_date = gmdate("y/m/d", $bazaar_date+9*60*60);
		
	echo "
	<form method=post enctype='multipart/form-data' action=$BAZ_SCRIPT>
	<input type=hidden name=mode value=bid_bazaar>
	<input type=hidden name=item value=$bazaar_id>
	<input type=hidden name=name value=$user_name>
    <TR>
      <TD rowspan='2' class='row0'>$bazaar_no</TD>
      <TD class='row0'>$bazaar_item</TD>
      <TD rowspan='2' class='row0'>$bazaar_gil G</TD>
      <TD class='row0'>$bazaar_out</TD>
      <TD rowspan='2' class='row0'>$bazaar_date</TD>
      <TD rowspan='2' class='row0'><INPUT type=text name=bid_user size=14 value=$c_name>
	  <INPUT size=2 type=text name=bid_in>
	  <INPUT type=submit value=submit></TD></form>
    </TR>
    <TR>
      <TD class='row0'>$bazaar_text</TD>
      <TD class='row0'>$bazaar_in</TD>
    </TR>";
	
		}
	
	?>
  </TBODY>
</TABLE>
</CENTER><br>
<?php

	}

}

function edit_bazaar_form($user_uid,$user_name){
	global $db,$BAZ_SCRIPT;
	
		$sql = "select * from `BAZAAR` where `uid` = '$user_uid' order by `no`";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$bazaar_rows = $result->numRows();
	
		if($bazaar_rows == 0){	
			echo "<HR><table cellpading=0 width=100% class=forumline><TR class=\"table_title\"><TH>$user_name ����̃o�U�[�͂���܂���B</TH></TR></table><br>";
		}else{
			?><CENTER>
<TABLE width=100% cellpading=0 class="forumline">
  <TBODY>
  
    <TR class="table_title">
      <TD rowspan="2" width=2%><B>No.</B></TD>
      <TD><B>�A�C�e��</B></TD>
      <TD rowspan="2" width=15%><B>�l�i</B></TD>
      <TD width=15%><B>�o�i��</B></TD>
      <TD rowspan="2" width=10%><B>�o�i��</B></TD>
      <TD rowspan="2" width=22%><B>���D (���O/��)</B></TD>
	  <TD rowspan="2" width=2%><B>�C��</B></TD>
	  <TD rowspan="2" width=2%><B>�폜</B></TD>
    </TR>
    <TR class="table_title">
      <TD><B>�R�����g</B></TD>
      <TD width=15%><B>���D��</B></TD>
    </TR><?php
	
		while($bazaar_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$bazaar_id = $bazaar_rows["id"];
		$bazaar_no = $bazaar_rows["no"];
		$bazaar_item = $bazaar_rows["item"];
		$bazaar_text = $bazaar_rows["text"];
		$bazaar_gil = $bazaar_rows["gil"];
		$bazaar_in = $bazaar_rows["item_in"];
		$bazaar_out = $bazaar_rows["item_out"];
		$bazaar_name = $bazaar_rows["name"];
		$bazaar_name = str_replace("\n", "<br>", $bazaar_name);
		$bazaar_date = $bazaar_rows["date"];
		$bazaar_date = gmdate("y/m/d", $bazaar_date+9*60*60);
		
	echo "
	<form method=post enctype='multipart/form-data' action=$BAZ_SCRIPT>
	<input type=hidden name=name value=$user_name>
	<input type=hidden name=id value=$bazaar_id>
    <TR>
      <TD rowspan='2' class='row0'>$bazaar_no</TD>
      <TD class='row0'>$bazaar_item</TD>
      <TD rowspan='2' class='row0'>$bazaar_gil G</TD>
      <TD class='row0'>$bazaar_out</TD>
      <TD rowspan='2' class='row0'>$bazaar_date</TD>
      <TD rowspan='2' class='row0'>$bazaar_name</TD>
	  <TD rowspan='2' class='row0'><INPUT type=submit value=modify name=mode></TD>
	  <TD rowspan='2' class='row0'><INPUT type=submit value=delete name=mode></TD></FORM>
    </TR>
    <TR>
      <TD class='row0'>$bazaar_text</TD>
      <TD class='row0'>$bazaar_in</TD>
    </TR>";
	
		}
	
	echo "
  </TBODY>
</TABLE></CENTER><br>
";

	}	
	?>
	<CENTER>
<TABLE width=100% cellpading=0 class="forumline">
  <TBODY>
    <TR class="table_title">
      <TD><B>�A�C�e��</B></TD>
      <TD><B>�R�����g</B></TD>
	  <TD><B>�l�i</B></TD>
      <TD><B>�o�i��</B></TD>
    </TR>
	<form method=post enctype='multipart/form-data' action=<?php echo $BAZ_SCRIPT; ?>>
	<input type=hidden name=name value=<?php echo $user_name;?>>
	<input type=hidden name=mode value=add_bazaar>
    <TR>
      <TD>
      <INPUT size=30 type=text name=item>
      </TD>
      <TD>
      <INPUT size=30 type=text name=msg>
      </TD>
      <TD>
      <INPUT size=10 type=text name=gil>
      </TD>
      <TD>
      <INPUT size=3 type=text name=out>
      </TD>
    </TR>
    <TR>
      <TD colspan=4>
      <INPUT type=submit value=add>
      </TD></FORM>
    </TR>
  </TBODY>
</TABLE>
</CENTER>
<?php

}

function modify_bazaar_form($user_uid,$user_name){
	global $db,$BAZ_SCRIPT;

	$id = $_POST["id"];
	$sql = "select * from `BAZAAR` where `id` = '$id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$bazaar_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$bazaar_id = $bazaar_rows["id"];
	$bazaar_no = $bazaar_rows["no"];
	$bazaar_item = $bazaar_rows["item"];
	$bazaar_text = $bazaar_rows["text"];
	$bazaar_gil = $bazaar_rows["gil"];
	$bazaar_in = $bazaar_rows["item_in"];
	$bazaar_out = $bazaar_rows["item_out"];
	$bazaar_name = $bazaar_rows["name"];
	//$bazaar_name = str_replace("\n", "<br>", $bazaar_name);
	$bazaar_date = $bazaar_rows["date"];
	$bazaar_date = gmdate("y/m/d", $bazaar_date+9*60*60);

	?>
	<CENTER>
<TABLE width=100% cellpading=0 class="forumline">
  <TBODY>
    <TR class="table_title">
      <TD rowspan="2" width=2%><B>No.</B></TD>
      <TD><B>�A�C�e��</B></TD>
      <TD rowspan="2" width=15%><B>�l�i</B></TD>
      <TD width=15%><B>�o�i��</B></TD>
      <TD rowspan="2" width=10%><B>�o�i��</B></TD>
      <TD rowspan="2" width=22%><B>���D (���O/��)</B></TD>
    </TR>
    <TR class="table_title">
      <TD><B>�R�����g</B></TD>
      <TD width=15%><B>���D��</B></TD>
    </TR>
	<?php 
	echo "<form method=post enctype='multipart/form-data' action=$BAZ_SCRIPT>
	<input type=hidden name=mode value=modify_bazaar>
	<input type=hidden name=name value=$user_name>
	<input type=hidden name=id value=$bazaar_id>
    <TR>
      <TD rowspan='2' class='row0'>$bazaar_no</TD>
      <TD class='row0'><INPUT size=30 type=text name=item value=$bazaar_item></TD>
      <TD rowspan='2' class='row0'><INPUT size=10 type=text name=gil value=$bazaar_gil></TD>
      <TD class='row0'><INPUT size=3 type=text name=out value=$bazaar_out></TD>
      <TD rowspan='2' class='row0'>$bazaar_date</TD>
      <TD rowspan='2' class='row0'><TEXTAREA rows=5 cols=15 name=names>$bazaar_name</TEXTAREA></TD>
    </TR>
    <TR>
      <TD class='row0'><INPUT size=30 type=text name=msg value=$bazaar_text></TD>
      <TD class='row0'><INPUT size=3 type=text name=in value=$bazaar_in></TD>
    </TR>
    <TR>
      <TD>
      <INPUT type=submit value=modify>
      </TD></FORM>
      <TD colspan=7></TD>
    </TR>
  </TBODY>
</TABLE>
</CENTER><br>
";
}


function bid_bazaar($user_name){
	global $db,$BAZ_SCRIPT;
	
	$b_id = $_POST["item"];
	$b_name = $_POST["bid_user"];
	$b_in = $_POST["bid_in"];
		
	if(!$b_id || !$b_in || !$b_name){
		sub_msg("","","i/o error","no or name or in empty");
	}
		
	$sql = "select * from `BAZAAR` where `id` = '$b_id'";
		
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$bazaar_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$bazaar_id = $bazaar_rows["id"];
	$bazaar_in = $bazaar_rows["item_in"];
	$bazaar_name = $bazaar_rows["name"];
	$bazaar_item = $bazaar_rows["item"];
	$bazaar_name .="$b_name($b_in)\n";

	$bazaar_in = $bazaar_in + $b_in;

	$sql = "UPDATE `BAZAAR` SET `name` = '$bazaar_name' , `item_in` = '$bazaar_in' WHERE `id` = '$bazaar_id'";
		
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
		
	$mail_head ="�o�U�[�ɓ��D������܂���";
	$body = "$b_name ����$bazaar_item ��$b_in ���D���܂����B\n".$body;
		
	wb_sendmail(1,$user_name,$b_name,$mail_head,$body);
		
	sub_msg("5","list/$BAZ_SCRIPT?name=$user_name","���D���������܂����B","�����I�Ƀg�b�v�y�[�W�ɖ߂�܂�");

}

function modify_bazaar($user_uid,$user_name){
	global $db,$uid,$BAZ_SCRIPT;
	
	$mode = $_POST["mode"];

	if($mode == "add_bazaar"){
	
		$item = $_POST["item"];
		$msg = $_POST["msg"];
		$gil = $_POST["gil"];
		$in = $_POST["in"];
		$out = $_POST["out"];
		if(!$item){
			sub_msg("","","�G���[","���L�����ڂ�����܂��B");
		}
		$item = htmlspecialchars($item);
		$msg = htmlspecialchars($msg);
		$date = time();
		
		$sql = "select max(no),`name`,`uid` from `BAZAAR` where `uid` = '$uid' group by `name`,`uid`";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$bazaar_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$bazaar_max_no = $bazaar_rows["max(no)"];
		
		if(!$id){
			if(!$bazaar_max_no){
				$bazaar_max_no = 1;
			}else{
				$bazaar_max_no = $bazaar_max_no + 1;
			}
		}else{
			$bazaar_name = $bazaar_rows["name"];
			$bazaar_max_no = $no;
		}
		
		$sql = "INSERT INTO `BAZAAR` VALUES ('','$uid' ,'$bazaar_max_no' ,'$item' ,'$msg' ,'$gil' ,'$out' ,'$in' ,'$bazaar_name','$date' )";
		//die("$sql");
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		$txt = "$user_name ����̃o�U�[���X�V����܂�!!<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$txt .= "&gt;&gt; <A href='list/$BAZ_SCRIPT?name=$user_name'>�o�U�[������B</A>";
		//die($txt);
		add_news(0,0,$txt);
		
		sub_msg("5","list/$BAZ_SCRIPT?mode=edit_bazaar","�o�U�[��ǉ��E�C�����܂����B","�����I�Ƀg�b�v�y�[�W�ɖ߂�܂�");

	}elseif($mode == "modify_bazaar"){
		$id = $_POST["id"];
		$item = $_POST["item"];
		$msg = $_POST["msg"];
		$gil = $_POST["gil"];
		$in = $_POST["in"];
		$out = $_POST["out"];
		$names = $_POST["names"];
		$names= str_replace("\r\n", "\r", $names);
		$names = str_replace("\r", "\n", $names);
		
		$sql = "select * from `BAZAAR` where `id` = '$id'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$bazaar_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		if(!$bazaar_rows){
			sub_msg("","","i/o error","empty");
		}
		
		$bazaar_id = $bazaar_rows["id"];
		$bazaar_uid = $bazaar_rows["uid"];
		$bazaar_no = $bazaar_rows["no"];
		$date = time();
		
		$sql = "REPLACE INTO `BAZAAR` VALUES ('$bazaar_id' ,'$bazaar_uid','$bazaar_no' ,'$item' ,'$msg' ,'$gil' ,'$out' ,'$in' ,'$names','$date' )";
		
		//die("$sql");
		
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}

		sub_msg("5","list/$BAZ_SCRIPT?mode=edit_bazaar","�o�U�[��ǉ��E�C�����܂����B","�����I�Ƀg�b�v�y�[�W�ɖ߂�܂�");

	}elseif($mode == "delete"){
		$id = $_POST["id"];
		$sql = "delete from `BAZAAR` where `id` = '$id'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		sub_msg("5","list/$BAZ_SCRIPT?mode=edit_bazaar","�A�C�e���폜����","�����I�Ƀg�b�v�y�[�W�ɖ߂�܂�");

	}else{
		sub_msg("","","�G���[","���[�h���ݒ�");
	}


}


?>