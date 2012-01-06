<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "forum_lib.php";
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

$STYLE = load_style(4,1);

	    $W = 172;
	    $H = 129;
	    $image_type = "1";

//大本の認証
if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,9)){
		die("Access Denied");
	}
}


//global val
$c= intval($_GET["c"]);

//2回目の認証
$sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$c'";
$result = $db->query($sql);
$chk = $result->numRows();
if($chk){
	if(!auth_edit_c($c)){
		//die("edit error");
		sub_msg("5","forum/forum.php","このカテゴリーは編集できません","リロードします。");
	}else{
	    $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    $cat_title = $user_rows["cat_title"];
	}
}else{
	die("no categories");
}

$f_chk= intval($_GET["chk"]);
if($f_chk == 1){

$sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$c'";
$cat_result = $db->query($sql);
$chk = $cat_result->numRows();
//var_dump($chk,$c);
if($chk){
	while($cat_rows = $cat_result->fetchRow(DB_FETCHMODE_ASSOC)){
		$f = $cat_rows["forum_id"];
		
		$sql = "select * from `FORUM_TOPIC` WHERE `forum_id` = '$f'";
		$topic_result = $db->query($sql);
		$topic_chk = $topic_result->numRows();
		if($topic_chk){
			while($topic_rows = $topic_result->fetchRow(DB_FETCHMODE_ASSOC)){
				//トピック検索&削除
				$topic_id = $topic_rows["topic_id"];
			
				//検索したtopic_idでpostを削除
				$sql = "select * from `FORUM_POSTS` WHERE `topic_id` = '$topic_id'";
				$posts_result = $db->query($sql);
				$posts_chk = $posts_result->numRows();
				if($posts_chk){
					while($posts_rows = $posts_result->fetchRow(DB_FETCHMODE_ASSOC)){
						$post_id = $posts_rows["post_id"];
						$sid = $posts_rows["sid"];
					
					    $sql = "delete from `FORUM_POSTS` where `post_id` = '$post_id'";
					    $del_posts_result = $db->query($sql);
					    if (DB::isError($del_posts_result)) {
					         trigger_error($del_posts_result->getMessage(), E_USER_ERROR);
					    }
					
					    $sql = "delete from `FORUM_POSTS_TXT` where `post_id` = '$post_id'";
					    $del_posts_txt_result = $db->query($sql);
					    if (DB::isError($del_posts_txt_result)) {
					         trigger_error($del_posts_txt_result->getMessage(), E_USER_ERROR);
					    }
					
						//検索したpost_idで
						$sql = "select * from `FORUM_USERS` WHERE `post_id` = '$post_id'";
						$users_result = $db->query($sql);
						$users_rows = $users_result->fetchRow(DB_FETCHMODE_ASSOC);
						$file = $users_rows["file"];
						if($file){
							if($image_type == 1){
								$del_file ="dat/$sid".".jpg";
							}else{
								$del_file ="dat/$sid".".png";
							}
							
							$sam_size = getimagesize("$del_file");
							unlink($del_file);
						
					        //縮小画像削除
					        if ($sam_size[0] > $W || $sam_size[1] > $H) {
								//$pos = strrpos($file,".");
        						//$file_s = substr($file,0,$pos);
								
					           if($image_type == "1"){
				                 $del_file ="dat/$sid"."_s.jpg";
					           }else{
				                 $del_file ="dat/$sid"."_s.png";
					           }
					           unlink($del_file);
					        }
						}
					
					    $sql = "delete from `FORUM_USERS` where `post_id` = '$post_id'";
					    $del_users_result = $db->query($sql);
					    if (DB::isError($del_users_result)) {
					         trigger_error($del_users_result->getMessage(), E_USER_ERROR);
					    }
					}
				

				}
			
				$posts_chk2 = $posts_chk2 + $posts_chk;
		    	$sql = "delete from `FORUM_TOPIC` where `topic_id` = '$topic_id'";
		    	$del_topic_result = $db->query($sql);
		    	if (DB::isError($del_topic_result)) {
		    	     trigger_error($del_topic_result->getMessage(), E_USER_ERROR);
		    	}
			}
		

		}

		$sql = "delete from `FORUM_FORUMS` where `forum_id` = '$f'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
	
	}
	
	$sql = "delete from `FORUM_CATEGORIES` where `cat_id` = '$c'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	//die("$topic_chk & $posts_chk2 & delete ok");
	sub_msg("3","forum/forum.php","カテゴリーを削除しました!!","リロードします。");
	
}
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<TITLE><?php echo "$STYLE[site_name]"; ?></TITLE>
<?php echo "$STYLE[css]"; ?>
<?php

$local_count = $_POST["local_count"];
if($local_count == 1){
    $local_count = 2;
}else{
    $local_count = 1;
}


?>

</HEAD>
<BODY>
<TABLE height="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD width="8" class="color3" background="../img/<?php echo "$STYLE[img_left]"; ?>" rowspan="2"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
      <TD width="750" valign="top">
	  <?php echo "$STYLE[topimage]"; ?>
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="row_title" height="34"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
            <TD class="row_title" height="34" width="131">FINAL FANTASY XI<BR>
            <?php echo "$STYLE[site_name]"; ?></TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10" class="color2">&nbsp;</TD>
            <TD class="color2" height="34" width="200">&nbsp;Forum</TD>
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
            <TD class="color2"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
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
                  <TD colspan="2" width="422">
                  <BR>カテゴリーの削除</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
				  下記のカテゴリー内のデータをすべて削除します。<BR><BR>
				  <?php
				  
				  echo "削除対象フォーラム：&nbsp;<B>$cat_title</B>";
				  
				  ?>
				  <BR><BR>
				  よろしいですか?<BR><BR>
				  <A href='delete_category.php?c=<?php echo "$c"; ?>&chk=1'>はい</A>　/　<A href='forum_reg.php'>いいえ</A>
				  <BR><BR><BR>
				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"><HR><BR></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
                  <A href='javascript:history.back()'>戻る</A>
				 </TD>
                </TR>
              </TBODY>
            </TABLE>
            </TD>
          </TR>
        </TBODY>
      </TABLE>
      </TD>
      <TD width="25" class="color3" background="../img/<?php echo "$STYLE[img_right]"; ?>" rowspan="2"><IMG src="../img/spacer.gif" width="25" height="1"></TD>
      <TD class="color3" rowspan="2"></TD>
    </TR>
    <TR>
      <TD height="34">
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="color2" height="34"><IMG src="../img/spacer.gif" width="8" height="1"></TD>
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