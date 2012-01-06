<?php

function show_poll($id){
	global $db;
	
	//$id = intval($_GET["id"]);
	
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
		$question = "“Š•[";
	}
	
	?>
                  <form method=post enctype='multipart/form-data' action=poll.php>
				  <input type=hidden name=id value=<?php echo "$id" ?>>
				  <TABLE width="150">
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
								
								echo "<TD align=\"left\"  class=\"color3\">";
								echo "$option_text";
								echo "</TD></TR>";
							}
						}else{
							echo "<TR><TD width=\"2%\" class=\"color3\">“Š•[‚È‚µ</TD></TR>";
						}
						?>
                      <TR>
                        <TD class="color4" align="right" valign="top" colspan="2">
						<input type="submit" value="“Š•[" name="vote">
						<input type="submit" value="Œ‹‰Ê" name="result"></TD>
                      </TR>
					  </form>
                      <TR>
                        <TD colspan="2"><A href='javascript:history.back()'>–ß‚é</A>
						</TD>
                      </TR>
                    </TBODY>
                  </TABLE>
	<?php
}

function poll_list(){
	global $db;
	
	$time = time();
	$sql = "select * from `PHP_POLL_DESC` where `end_time` > '$time' order by `poll_id` desc";
	$result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$chk = $result->numRows();
	
	?>
                  <form method=post enctype='multipart/form-data' action=poll.php>
				  <TABLE width="150">
                    <TBODY>
                      <TR>
                        <TD align="center" class="color4" valign="top">“Š•[Žó•t’†ƒŠƒXƒg<BR>
						</TD>
                      </TR>
                      <TR>
					  <TD class="color3"><BR>
						<?php
						if($chk){
							$time = time();
							$sql = "select * from `PHP_POLL_DESC` where `end_time` > '$time' order by `poll_id` desc";
							$result = $db->query($sql);
							if (DB::isError($result)) {
						    	trigger_error($result->getMessage(), E_USER_ERROR);
							}
							
							while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
								$poll_id = $tmp_rows["poll_id"];
								$question = $tmp_rows["question"];
								
								//echo "<TD>";
								echo "<a href=\"?mode=show_poll&id=$poll_id\">$question</a><BR>";
								//echo "</TD>";
							}
						}else{
							echo "“Š•[‚È‚µ<BR>";
						}
						?>
						<BR>
						</TD>
                      </TR>
                      <TR>
                        <TD class="color4" align="right" valign="top">
						<input type="submit" value="‘S•”‚Ý‚é" name="all"></TD>
                      </TR>
					  </form>
                    </TBODY>
                  </TABLE>
	<?php
}

function poll_main(){
	global $db;
	$mode = $_GET["mode"];
	
	$id = intval($_GET["id"]);
	
	if($mode == "show_poll"){
		show_poll($id);
	}else{
		$time = time();
		$sql = "select * from `PHP_POLL_DESC` where `end_time` > '$time' order by `poll_id` desc";
		$result = $db->query($sql);
		if (DB::isError($result)) {
		   	trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$id = $tmp_rows["poll_id"];
		$chk = $result->numRows();
		//var_dump($chk);
		if($chk == 1){
			show_poll($id);
		}else{
			poll_list();
		}
	}

}

?>