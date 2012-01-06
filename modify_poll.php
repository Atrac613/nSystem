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
	if(!usr_level($uid,3)){
		die("Access Denied");
	}
}

function chk_poll_id($id){
	global $db;
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		return true;
	}else{
		return false;
	}

}

function modify_poll($id){
	global $db;
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$poll_uid = $tmp_rows["uid"];
		$multiple = $tmp_rows["multiple"];
		$description = $tmp_rows["description"];
		$question = $tmp_rows["question"];
		$mail_status = $tmp_rows["mail_status"];
		$end_time = $tmp_rows["end_time"];
		
		$date = gmdate("Y-m-d H:i:s", $end_time+9*60*60);
		
		$sql = "select * from `USER_DATA` where `uid` = '$poll_uid'";
				
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$poll_name = $tmp_rows["name"];
	}
?>

				<form method=post enctype='multipart/form-data' action=modify_poll.php>
				<INPUT type=hidden name=poll_id value=<?php echo "$id"; ?>>
<TABLE width="100%">
  <TBODY>
    <TR>
      <TD>作者</TD>
      <TD><?php echo "$poll_name"; ?></TD>
    </TR>
    <TR>
      <TD>質問</TD>
      <TD><INPUT size="40" type="text" name="question" value="<?php echo "$question"; ?>"></TD>
    </TR>
    <TR>
      <TD>説明</TD>
      <TD><TEXTAREA name="desc" rows="4" cols="40"><?php echo "$description"; ?></TEXTAREA></TD>
    </TR>
    <TR>
      <TD>期限</TD>
      <TD><INPUT size=30 type=text name=end_time value="<?php echo "$date"; ?>"></TD>
    </TR>
    <TR>
      <TD>複数選択可</TD>
      <TD><input type='checkbox' name='multiple' value='1' <?php if($multiple){echo "checked"; } ?> /></TD>
    </TR>
    <TR>
      <TD>期限終了時にメール送信</TD>
      <TD><input type='checkbox' name='end_mail' value='1' <?php if($mail_status){echo "checked"; } ?> /></TD>
    </TR>
    <TR>
      <TD valign="top">選択肢</TD>
      <TD>
	<?php
	
		$sql = "select * from `PHP_POLL_OPTION` where `poll_id` = '$id' order by `option_id`";
		$result = $db->query($sql);
		if (DB::isError($result)) {
	    	trigger_error($result->getMessage(), E_USER_ERROR);
		}
		//$loc =0;
		while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$option_id = $tmp_rows["option_id"];
			$option_text = $tmp_rows["option_text"];

			echo "<INPUT size=\"40\" type=\"text\" name=\"option_text[]\" value=\"$option_text\"><br>";
			//$loc++;
		}
		echo "<a href=modify_poll.php?mode=addmore&id=$id>選択肢を追加</a>";
		//for($i=0 ; $i<(10-$loc) ; $i++){
		//	echo '<INPUT size=40 type=text name=option_text[] value""><BR>';
		//}
	
	?>
	</td>
	</tr>
	
    <TR>
      <TD></TD>
      <TD><input type=submit value=修正 name=regist></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>

<?php

}

function more_vote($id){
	global $db;
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$question = $tmp_rows["question"];
	}

?>
<form method=post enctype='multipart/form-data' action=modify_poll.php>
<INPUT type=hidden name=poll_id value=<?php echo "$id"; ?>>
<TABLE width="100%">
  <TBODY>
    <TR>
      <TD colspan="2">選択肢を追加する</TD>
    </TR>
    <TR>
      <TD>質問</TD>
      <TD><?php echo "$question"; ?></TD>
    </TR>
    <TR>
      <TD valign="top">選択肢</TD>
      <TD>
	  <?php
		for($i=0 ; $i<10 ; $i++){
			echo '<INPUT size=40 type=text name=option_text[] value""><BR>';
		}
		?>
	  </TD>
    </TR>
    <TR>
      <TD colspan="2"><input type=submit value=登録 name=addmore></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>

<?php

}


function delete_form($id){
	global $db;
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$question = $tmp_rows["question"];
	}

?>
<form method=post enctype='multipart/form-data' action=modify_poll.php>
<INPUT type=hidden name=poll_id value=<?php echo "$id"; ?>>
<TABLE width="100%">
  <TBODY>
    <TR>
      <TD colspan="2">下記の投票を削除します</TD>
    </TR>
    <TR>
      <TD width="10%">質問</TD>
      <TD><?php echo "$question"; ?></TD>
    </TR>
    <TR>
      <TD>&nbsp;</TD>
      <TD>&nbsp;</TD>
    </TR>
    <TR>
      <TD colspan="2"><input type=submit value=削除 name=delete></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>

<?php

}


function new_vote_form(){
$date = gmdate("Y-m-d H:i:s", time()+9*60*60);
?>
<form method=post enctype='multipart/form-data' action=modify_poll.php>
<INPUT type=hidden name=poll_id value=<?php echo "$id"; ?>>
<TABLE width="100%">
  <TBODY>
    <TR>
      <TD>質問</TD>
      <TD><INPUT size="40" type="text" name="question" value""></TD>
    </TR>
    <TR>
      <TD>説明</TD>
      <TD><TEXTAREA name="desc" rows="4" cols="40"></TEXTAREA></TD>
    </TR>
    <TR>
      <TD>期限</TD>
      <TD><INPUT size=30 type=text name=end_time value="<?php echo "$date"; ?>"></TD>
    </TR>
    <TR>
      <TD>複数選択可</TD>
      <TD><input type='checkbox' name='multiple' value='1' /></TD>
    </TR>
    <TR>
      <TD>期限終了時にメール送信</TD>
      <TD><input type='checkbox' name='end_mail' value='1' /></TD>
    </TR>
    <TR>
      <TD valign="top">選択肢</TD>
      <TD>
	  <?php
	  for($i=0 ; $i<10 ; $i++){
	  	echo '<INPUT size=40 type=text name=option_text[] value""><BR>';
	  }
	  ?>
	  </TD>
    </TR>
    <TR>
      <TD></TD>
      <TD><input type=submit value=登録 name=regist></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
<?php

}

function all_poll(){
	global $db;
	
	?>
	<form method=post enctype='multipart/form-data' action=modify_poll.php>
<TABLE width=100% cellpading=0 class="forumline">
  <TBODY>
    <TR class="table_title">
      <TD>作者</TD>
      <TD>質問</TD>
      <TD>投票者数</TD>
      <TD>期限</TD>
      <TD></TD>
    </TR>
	
	
<?php

	$sql = "select * from `PHP_POLL_DESC` order by `poll_id` desc";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		$loc=0;
		while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			if($loc == 2){
				$loc=0;
			}
			$poll_id = $tmp_rows["poll_id"];
			$poll_uid = $tmp_rows["uid"];
			$multiple = $tmp_rows["multiple"];
			$end_time = $tmp_rows["end_time"];
			$question = $tmp_rows["question"];
			$voters = $tmp_rows["voters"];
		
			$date = gmdate("Y-m-d H:i:s", $end_time+9*60*60);
		
			$sql = "select * from `USER_DATA` where `uid` = '$poll_uid'";
				
			$result2 = $db->query($sql);
			if (DB::isError($result2)) {
				trigger_error($result2->getMessage(), E_USER_ERROR);
			}
			$tmp_rows2 = $result2->fetchRow(DB_FETCHMODE_ASSOC);
			$poll_name2 = $tmp_rows2["name"];
			if($loc == 1){
				$class = "class=\"row0\"";
			}else{
				$class = "class=\"row0\"";
			}
	echo "
    <TR>
      <TD $class>$poll_name2</TD>
      <TD $class>$question</TD>
      <TD $class>$voters</TD>
      <TD $class>$date</TD>
      <TD $class><a href=modify_poll.php?mode=modify&id=$poll_id>編集</a><BR><a href=modify_poll.php?mode=delete&id=$poll_id>削除</a><BR><a href=poll.php?mode=result&id=$poll_id>結果</a><BR></TD>
    </TR>
	";
		$loc++;
		}
	}else{
		echo "<TR><TD colspan=5 class=\"row0\">No data</TD></TR>";
	}

?>
    <TR>
      <TD colspan="5" class="row0"><input type=hidden name=mode value=new_vote><input type=submit value=投票の追加></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
	
	<?php

}

//var_dump(count(register::getTimeZoneList()));
//$timezone = register::getTimeZoneList();
//var_dump($timezone);
//die();




$mode =$_GET["mode"];
if(!$mode){
	$mode =$_POST["mode"];
}
if($mode =="modify"){
	$id = intval($_GET["id"]);
	if(!chk_poll_id($id)){
		sub_msg("5","modify_poll.php","エラー","そのidは未登録です");
	}
}elseif($mode == "addmore"){
	$id = intval($_GET["id"]);
	if(!chk_poll_id($id)){
		sub_msg("5","modify_poll.php","エラー","そのidは未登録です");
	}
}elseif($mode == "delete"){
	$id = intval($_GET["id"]);
	if(!chk_poll_id($id)){
		sub_msg("5","modify_poll.php","エラー","そのidは未登録です");
	}
}else{
	
}

//メインルーチン
$regist = $_POST["regist"];
if($regist){

	$poll_id = $_POST["poll_id"];
	$desc = $_POST["desc"];
	$option_text = $_POST["option_text"];
	$multiple = $_POST["multiple"];
	$end_time = $_POST["end_time"];
	$question = $_POST["question"];
	$end_mail = $_POST["end_mail"];
	$st_time = time();

	$end_time = formatTimestamp($end_time);

	$desc = str_replace("\r\n", "\r", $desc);
	$desc = str_replace("\r", "\n", $desc);
	$desc = htmlspecialchars($desc);
	
	$question = htmlspecialchars($question);
	if(!$question){
		sub_msg("","","エラー","質問が未記入です");
	}
	
	if(!$desc){
		$desc = "なし";
	}
	
	if($_POST["poll_id"]){
		$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$poll_id'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$voters = $tmp_rows["voters"];
		$st_time = $tmp_rows["start_time"];
	}
	
	$sql = "replace INTO `PHP_POLL_DESC` VALUES ('$poll_id','$uid', '$question', '$desc', '$st_time', '$end_time', '$voters', '$multiple' ,'$end_mail')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "select * from `PHP_POLL_DESC` where `start_time` = '$st_time' and `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$poll_id = $tmp_rows["poll_id"];
	$voters = $tmp_rows["voters"];
	$chk = $result->numRows();
	
	if($_POST["poll_id"]){
		
		$sql = "select * from `PHP_POLL_OPTION` where `poll_id` = '$poll_id' order by `option_id`";
		$result = $db->query($sql);
		if (DB::isError($result)) {
	    	trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$loc=0;
		while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$option_id = $tmp_rows["option_id"];
			$option_count = $tmp_rows["option_count"];
			
			$option = $option_text["$loc"];
			
			if($option){
				$option = htmlspecialchars($option);
				$sql = "replace INTO `PHP_POLL_OPTION` VALUES ('$option_id','$poll_id', '$option', '$option_count')";
			}else{
				$voters = $voters-$option_count;
				$sql = "UPDATE `PHP_POLL_DESC` SET `voters` = '$voters' WHERE `poll_id` = '$poll_id'";
				$result_row = $db->query($sql);
				if (DB::isError($result_row)) {
					trigger_error($result_row->getMessage(), E_USER_ERROR);
				}
				
				$sql = "delete from `PHP_POLL_OPTION` where `option_id` = '$option_id'";
			}
			
			$result_row = $db->query($sql);
			if (DB::isError($result_row)) {
				trigger_error($result_row->getMessage(), E_USER_ERROR);
			}
			
			$loc++;
		}
		
		
	}else{
		for($i=0;$i<count($option_text);$i++){
			if($option_text["$i"]){
				$option = $option_text["$i"];
				
				$option = htmlspecialchars($option);
				
				$sql = "replace INTO `PHP_POLL_OPTION` VALUES ('','$poll_id', '$option', '')";
				$result = $db->query($sql);
				if (DB::isError($result)) {
					trigger_error($result->getMessage(), E_USER_ERROR);
				}
			}
		}
		add_news('7',"$poll_id","$c_name");
	}
	
	sub_msg("5","modify_poll.php","登録成功","自動的にトップページに戻ります");
}

$addmore = $_POST["addmore"];
if($addmore){

	$poll_id = $_POST["poll_id"];
	$option_text = $_POST["option_text"];
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$poll_id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$poll_id = $tmp_rows["poll_id"];
	
	if(!chk_poll_id($poll_id)){
		sub_msg("5","modify_poll.php","エラー","そのidは未登録です");
	}
	
	if($poll_id){
		
	for($i=0;$i<count($option_text);$i++){
		if($option_text["$i"]){
			$option = $option_text["$i"];
			
			$option = htmlspecialchars($option);
			
			$sql = "replace INTO `PHP_POLL_OPTION` VALUES ('','$poll_id', '$option', '')";
			$result = $db->query($sql);
			if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
			}
		}
	}
		
		sub_msg("5","modify_poll.php","登録成功","自動的にトップページに戻ります");
	}

}

$delete= $_POST["delete"];
if($delete){
	$poll_id = $_POST["poll_id"];
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$poll_id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$poll_id = $tmp_rows["poll_id"];
	
	if(!chk_poll_id($poll_id)){
		sub_msg("5","modify_poll.php","エラー","そのidは未登録です");
	}
	
	$sql = "delete from `PHP_POLL_DESC` where `poll_id` = '$poll_id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "select * from `PHP_POLL_OPTION` where `poll_id` = '$poll_id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
		$option_id = $tmp_rows["option_id"];
		
		$sql = "delete from `PHP_POLL_OPTION` where `option_id` = '$option_id'";
		$result_del = $db->query($sql);
		if (DB::isError($result_del)) {
	    	trigger_error($result_del->getMessage(), E_USER_ERROR);
		}
	}
	
	sub_msg("5","modify_poll.php","削除成功","自動的にトップページに戻ります");

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
            <TD class="color2" height="34" width="200">&nbsp;投票管理</TD>
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
                  <TD colspan="2" width="570"></TD>
                  <TD rowspan="5" align="right" width="10" valign="top"></TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422" valign="top">
				  <?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
				<?php
				$mode =$_GET["mode"];
				if(!$mode){
					$mode =$_POST["mode"];
				}
				if($mode =="modify"){
					$id = intval($_GET["id"]);
					modify_poll($id);
				}elseif($mode == "new_vote"){
					new_vote_form();
				}elseif($mode == "addmore"){
					$id = intval($_GET["id"]);
					more_vote($id);
				}elseif($mode == "delete"){
					$id = intval($_GET["id"]);
					delete_form($id);
				}else{
					all_poll();
				}
				
				?>
				
				</TD></TR><TR><TD colspan="2" width="422" valign="top"></TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				<BR><BR>
				
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