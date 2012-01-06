<?php
//ÉâÉCÉuÉâÉäåƒÇ—èoÇµ
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

function show_poll(){
	global $db;
	
	$id = intval($_GET["id"]);
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$question = $tmp_rows["question"];
		$multiple = $tmp_rows["multiple"];
		if($multiple){
			$multiple ="checkbox";
		}else{
			$multiple ="radio";
		}
	}else{
		$question = "ìäï[";
	}
	
	?>
                  <form method=post enctype='multipart/form-data' action=poll.php>
				  <input type=hidden name=id value=<?php echo "$id" ?>>
				  <TABLE width="100%">
                    <TBODY>
                      <TR>
                        <TD align="center" class="color4" valign="top" colspan="2"><?php echo "$question"; ?><BR>
						</TD>
                      </TR>
                      
						<?php
						if($chk){
							$sql = "select * from `PHP_POLL_OPTION` where `poll_id` = '$id' order by `option_id`";
							$result = $db->query($sql);
							if (DB::isError($result)) {
						    	trigger_error($result->getMessage(), E_USER_ERROR);
							}
							
							while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
								$option_id = $tmp_rows["option_id"];
								$option_text = $tmp_rows["option_text"];
								
								echo "<TR><TD width=\"2%\" class=\"color3\">";
								echo "<input type=\"$multiple\" name=\"$option_id\" value=\"1\" />";
								echo "</TD>";
								
								echo "<TD align=\"left\" class=\"color3\">";
								echo "$option_text";
								echo "</TD></TR>";
							}
						}else{
							echo "ìäï[Ç»Çµ";
						}
						?>
                      
                      <TR>
                        <TD class="color4" align="right" valign="top" colspan="2">
						<input type="submit" value="ìäï[" name="vote">
						<input type="submit" value="åãâ " name="result"></TD>
                      </TR>
					  </form>
                    </TBODY>
                  </TABLE>
	<?php
}

function all_poll_vote(){
	global $db;
	
	?>

<TABLE width="100%"  class="forumline">
  <TBODY>
    <TR class="table_title">
      <TD>çÏé“</TD>
      <TD>éøñ‚</TD>
      <TD>ìäï[é“êî</TD>
      <TD>ä˙å¿</TD>
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
			
			if($loc==1){
				$class="class=\"row0\"";
			}elseif($loc==2){
				$loc=0;
				$class="class=\"row0\"";
			}else{
				$class="class=\"row0\"";
			}
			
	echo "
    <TR>
      <TD $class>$poll_name2</TD>
      <TD $class>$question</TD>
      <TD $class>$voters</TD>
      <TD $class>$date</TD>
      <TD $class><a href=poll.php?mode=show_poll&id=$poll_id>ìäï[</a><BR><a href=poll.php?mode=result&id=$poll_id>åãâ </a><BR></TD>
    </TR>
	";
		$loc++;
		}
	}else{
		echo "<TR><TD colspan=5 class=\"row0\">No data</TD></TR>";
	}

?>
  </TBODY>
</TABLE>
	
	<?php

}

function poll_result($id){
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
		$end_time = $tmp_rows["end_time"];
		$voters = $tmp_rows["voters"];
		$description = $tmp_rows["description"];
		$description = str_replace("\n", "<br>", $description);
		$date = gmdate("Y-m-d H:i:s", $end_time+9*60*60);
	}
?>
<TABLE width="100%">
  <TBODY>
    <TR>
      <TH colspan="2" class="color4"><?php echo "$question"; ?></TH>
    </TR>
    <TR>
      <TD colspan="2" align="right" class="color3"><?php echo "$date"; ?>Ç…èIóπÇµÇ‹Ç∑</TD>
    </TR>
	<?php
  
		$sql = "select * from `PHP_POLL_OPTION` where `poll_id` = '$id' order by `option_id`";
		$result = $db->query($sql);
		if (DB::isError($result)) {
	    	trigger_error($result->getMessage(), E_USER_ERROR);
		}
		//$total = $result->numRows();
		$loc=0;
		while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$option_id = $tmp_rows["option_id"];
			$option_text = $tmp_rows["option_text"];
			$option_count = $tmp_rows["option_count"];
			if($voters){
				$per = 100 * $option_count / $voters;
			}else{
				$per =0;
			}
			
			if($loc==1){
				$class="class=\"row0\"";
			}elseif($loc==2){
				$loc=0;
				$class="class=\"row0\"";
			}else{
				$class="class=\"row0\"";
			}
			
			//var_dump($per);
			$width = intval($per)*2;
			$percent = sprintf(" %d %% (%d)", $per, $option_count);

			echo "<TR><TD width=\"25%\" $class>$option_text</TD>";
			echo "<TD $class><img src='./img/grey.gif' height='14' width='".$width."'> ".$percent."</TD></TR>";
			$loc++;
		}
	?>
	<TR>
	<TD valign="top"></TD>
	<TD>&nbsp;</TD>
    </TR>
	<TR>
	<TD valign="top">ê‡ñæ</TD>
	<TD><?php echo "$description"; ?></TD>
    </TR>
	<TR>
	<TD colspan="2">&nbsp;</TD>
    </TR>
	<TR>
      <TD colspan="2">ìäï[é“êîÅF<?php echo "$voters"; ?><BR><a href=poll.php?mode=show_poll&id=<?php echo "$id"; ?> >ìäï[</a></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR><a href=poll.php?>ëSÉäÉXÉg</a></TD>
    </TR>
  </TBODY>
</TABLE>
<?php

}


$mode =$_GET["mode"];
if(!$mode){
	$mode =$_POST["mode"];
}
if($mode == "result" || $mode == "show_poll" || $mode =="vote" || $_POST["result"]){
	$id = intval($_POST["id"]);
	if(!$id){
		$id = intval($_GET["id"]);
	}
	if(!chk_poll_id($id)){
		sub_msg("5","poll.php","ÉGÉâÅ[","ÇªÇÃidÇÕñ¢ìoò^Ç≈Ç∑");
	}
}

function vote_poll($option_id){
	global $db;

	$st_time = time();

	$sql = "select * from `PHP_POLL_OPTION` where `option_id` = '$option_id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$poll_id = $tmp_rows["poll_id"];
	$option_count = $tmp_rows["option_count"];
	$option_count++;
	
	$chk = $_POST["$option_id"];
	
	$sql = "select * from `PHP_POLL_DESC` where `poll_id` = '$poll_id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$voters = $tmp_rows["voters"];
	$end_time= $tmp_rows["end_time"];

	$voters++;
	
	if($chk){
		
		if(time() < $end_time){
		
			$sql = "UPDATE `PHP_POLL_OPTION` SET `option_count` = '$option_count' WHERE `option_id` = '$option_id'";
			$result_row = $db->query($sql);
				if (DB::isError($result_row)) {
				trigger_error($result_row->getMessage(), E_USER_ERROR);
			}
			
			$sql = "UPDATE `PHP_POLL_DESC` SET `voters` = '$voters' WHERE `poll_id` = '$poll_id'";
			$result_row = $db->query($sql);
				if (DB::isError($result_row)) {
				trigger_error($result_row->getMessage(), E_USER_ERROR);
			}
		
		}else{
			sub_msg("","","ÉGÉâÅ[","ÇªÇÃìäï[ÇÕÇ∑Ç≈Ç…èIóπÇµÇƒÇ¢Ç‹Ç∑");
		}
	}

}

//ÉÅÉCÉìÉãÅ[É`Éì
$vote = $_POST["vote"];
if($vote){
	
	$poll_id = $_POST["id"];

	$sql = "select * from `PHP_POLL_OPTION` where `poll_id` = '$poll_id'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	   	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	if($chk){
	
		while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
			$option_id= $tmp_rows["option_id"];

			vote_poll($option_id);
		}
		sub_msg("5","poll.php?mode=result&id=$poll_id","ìoò^ê¨å˜","é©ìÆìIÇ…ÉgÉbÉvÉyÅ[ÉWÇ…ñﬂÇËÇ‹Ç∑");
	
	}else{
		sub_msg("5","poll.php","ÉGÉâÅ[","ÇªÇÃidÇÕñ¢ìoò^Ç≈Ç∑");
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
            <TD class="color2" height="34" width="200">&nbsp;ìäï[</TD>
            <TD class="color2" align="right" height="34" width="396">
			<?php
			
			if($name){
				echo "ÇÊÇ§Ç±ÇªÅA$name Ç≥ÇÒ";
			}else{
				echo "ÇÊÇ§Ç±ÇªÅAÉQÉXÉgÇ≥ÇÒ";
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
                  <TD colspan="2" width="570" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
				
				<?php
				$mode =$_GET["mode"];
				if(!$mode){
					$mode =$_POST["mode"];
				}

				if($mode == "result" || $_POST["result"]){
					$id = intval($_GET["id"]);
					if(!$id){
						$id = intval($_POST["id"]);
					}
					poll_result($id);
				}elseif($mode == "show_poll"){
					show_poll();
				}else{
					all_poll_vote();
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
				<BR><HR width='420'><A href='javascript:history.back()'>ñﬂÇÈ</A><BR><BR><BR>
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