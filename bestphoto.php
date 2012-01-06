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
	$sql = "select * from `USER_DATA` where `name` = '$c_name'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$uid = $user_rows["uid"];
}

$STYLE = load_style(0,0);

function new_photo(){
	global $db,$name,$msg,$sel_id,$date;
	$sql = "select * from `PHP_BESTPHOTO`";
	$result = $db->query($sql);
	$chk = $result->numRows();
		if($chk){
			$sql = "select * from `PHP_BESTPHOTO` where `sta` = '0'";
			$result = $db->query($sql);
			$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			if($user_rows){
				$sel_id = $user_rows["sel_id"];
				$name = $user_rows["name"];
				$msg = $user_rows["msg"];
				$img = $user_rows["img"];
				$date = $user_rows["date"];
				$date = gmdate("y/m/d (D) H:i", $date+9*60*60);
				
				echo "<IMG src='bp/img/$img' width='512' height='382' border='0' alt='NEW!!'>";
					
			}else{
				$sql = "select * from `PHP_BESTPHOTO` where `sta` = '1' order by `sel_id`";
				$result = $db->query($sql);
				$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
				if($user_rows){
					$sel_id = $user_rows["sel_id"];
					$name = $user_rows["name"];
					$msg = $user_rows["msg"];
					$img = $user_rows["img"];
					$date = $user_rows["date"];
					$date = gmdate("y/m/d (D) H:i", $date+9*60*60);
					
					echo "<IMG src='bp/img/$img' width='512' height='382' border='0' alt='OLD'>";
					
				}else{
					echo "<IMG src='bp/img/no_photo.jpg' width='512' height='382' border='0' alt='No Photo'>";
				}
			}
			
		}else{
			echo "<IMG src='bp/img/no_photo.jpg' width='512' height='382' border='0' alt='No Photo'>";
		}

}

function change_photo($mode){
	global $db,$name,$msg,$sel_id,$date;

	$sql = "select * from `PHP_BESTPHOTO` where `sta` = '1' or `sta` = '0'";
	$result = $db->query($sql);
	$chk = $result->numRows();
		if($chk){
		$id = $_GET["id"];
		if($id < 1){
			$id = 1;
		}
		
			if($mode == "prev"){
				$sql = "select * from `PHP_BESTPHOTO` where `sel_id` = '$id'";
				$result = $db->query($sql);
				if (DB::isError($result)) {
    				trigger_error($result->getMessage(), E_USER_ERROR);
				}
				
				$chk = $result->numRows();
				if(!$chk){
					$prev = $chk - 1;
				}else{
					$prev = $id;
				}
				
				if($prev < 1){
					$prev = 1;
				}
				
				$sql = "select * from `PHP_BESTPHOTO` where `sel_id` = '$prev'";
				$result = $db->query($sql);
				if (DB::isError($result)) {
    				trigger_error($result->getMessage(), E_USER_ERROR);
				}
			}else{
				$sql = "select * from `PHP_BESTPHOTO` where `sel_id` = '$id'";
				$result = $db->query($sql);
				if (DB::isError($result)) {
    				trigger_error($result->getMessage(), E_USER_ERROR);
				}
				
				$chk = $result->numRows();
				if(!$chk){
					$id = 1;
				}
				
				
				$sql = "select * from `PHP_BESTPHOTO` where `sel_id` = '$id'";
				$result = $db->query($sql);
				if (DB::isError($result)) {
    				trigger_error($result->getMessage(), E_USER_ERROR);
				}
			}
			
			$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			if(!$user_rows){
				sub_msg("","","エラー","システムが矛盾を検出しました。<br />このセッションを終了しました。");
			}
			$sel_id = $user_rows["sel_id"];
			$name = $user_rows["name"];
			$msg = $user_rows["msg"];
			$img = $user_rows["img"];
			$date = $user_rows["date"];
			$date = gmdate("y/m/d (D) H:i", $date+9*60*60);
			echo "<IMG src='bp/img/$img' width='512' height='382' border='0' alt ='Old date sel_id=$sel_id id=$id'>";
		
		}else{
			$name = "Atrac";
			$msg = "投稿写真がないよ～";
			$date = "???";
			echo "<IMG src='bp/img/no_photo.jpg' width='512' height='382' border='0' alt='No Photo'>";
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
<CENTER>
<TABLE>
  <TBODY>
    <TR>
      <TD>
						<?php
						
						$mode = $_GET["mode"];
						if($mode == "prev"){
							change_photo($mode);
						}elseif($mode == "next"){
							change_photo($mode);
						}else{
							new_photo();
						}
							
						?>
	  </TD>
    </TR>
    <TR>
      <TD align="center">
	  <?php
	  
	  if(!$msg){
	  	$msg="どんどん投稿してクポ～!!";
	  }
	  if(!$name){
	  	$name="Moogle";
	  }
	  
	  echo "$msg<BR>
      投稿者：$name さん&nbsp;&nbsp; $date</TD>";
	  ?>
    </TR>
    <TR>
      <TD align="center">
      <TABLE width="100%">
        <TBODY>
          <TR>
			<?php
			$id = $_GET["id"];
			if(!$id){
				$id = $sel_id;
			}
			//var_dump($id);
			
				$sql = "select * from `PHP_BESTPHOTO` where `sta` = '1' or `sta` = '0'";
				$result = $db->query($sql);
				$chk_all = $result->numRows();
				//var_dump($chk_all);
				
				$prev = $id - 1;
				//var_dump($prev);
				if($prev < 0){
					$prev = $chk_all -1;
				}
				//var_dump($prev);
				$next = $id + 1;
				
				//staが正常か調べる
				$sql = "select * from `PHP_BESTPHOTO` where `sel_id` = '$prev'";
				$result = $db->query($sql);
				$chk = $result->numRows();
				//var_dump($chk);
				//var_dump($prev);
				if(!$chk){
					$prev = 1;
				}
				//var_dump($prev);
				$sql = "select * from `PHP_BESTPHOTO` where `sel_id` = '$next'";
				$result = $db->query($sql);
				$chk = $result->numRows();
				if(!$chk){
					$next = $chk_all;
				}
				
			echo "<TD class=\"color4\"><a href='bestphoto.php?mode=prev&id=$prev'>Prev</a></TD>
            <TD align='center' class=\"color4\"><a href='bestphoto.php?mode=new'>New</a></TD>
            <TD align='right' class=\"color4\"><a href='bestphoto.php?mode=next&id=$next'>Next</a></TD>";
			
			?>
          </TR>
        </TBODY>
      </TABLE>
      </TD>
    </TR>
  </TBODY>
</TABLE>
<BR><a href="JavaScript:close('mode')">CLOSE</a>
</CENTER>
</BODY>
</HTML>
