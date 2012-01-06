<?

//default setting
//$PHP_CUR_PASS = "http://192.168.0.101/admin_work_area/program/html/debug/";

function time_id(){
	$time_id_md5 = md5(time());
	$time_id = substr($time_id_md5, 0, 15);

	return $time_id;
}

function db_init(){
	global $db_user,$db_pass,$db_host,$db_name,$db_type;
$dsn = "$db_type://$db_user:$db_pass@$db_host/$db_name";
$db = DB::connect($dsn, true);
	if (DB::isError($db)) {
		trigger_error($db->getMessage(), E_USER_ERROR);
	}
	
	$sql = "SET NAMES sjis";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sql = "set character set sjis";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sql = "SET SESSION old_passwords = 1";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	return $db;
}

function db_init2(){
	$db_user = "user";
	$db_pass = "pass";
	$db_host = "localhost";
	$db_name = "system";
	$db_type = "mysql";
	
	$dsn = "$db_type://$db_user:$db_pass@$db_host/$db_name";
	$db2 = DB::connect($dsn, true);
	if (DB::isError($db2)) {
		trigger_error($db2->getMessage(), E_USER_ERROR);
	}
	
	return $db2;
}

function db_init_manual($db_name){
	$db_user = "user";
	$db_pass = "pass";
	$db_host = "localhost";
	//$db_name = "system";
	$db_type = "mysql";
	
	$dsn = "$db_type://$db_user:$db_pass@$db_host/$db_name";
	$dbm = DB::connect($dsn, true);
	if (DB::isError($dbm)) {
		trigger_error($dbm->getMessage(), E_USER_ERROR);
	}
	
	return $dbm;
}
/*
function bestphoto(){
	global $db;
	?>
                  <TABLE>
                    <TBODY>
                      <TR>
                        <TD align="center" class="color4" valign="top">今週の一枚<BR>

						<?php
						

	$sql = "select * from `PHP_BESTPHOTO`";
	$result = $db->query($sql);
	$chk = $result->numRows();
		if($chk){
			$sql = "select * from `PHP_BESTPHOTO` where `sta` = '0'";
			$result = $db->query($sql);
			$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			if($user_rows){
				//$sel_id = $user_rows["sel_id"];
				$img = $user_rows["img"];
				
				echo "<a href=javascript:mode('new')><IMG src='bp/imgs/$img' width='144' height='108' border='0' alt ='NEW!!'></a>";
					
			}else{
				echo "<a href=javascript:mode('new')><IMG src='bp/imgs/no_photo.jpg' width='144' height='108' border='0' alt='No Photo'></a>";
			}
			
		}else{
			echo "<a href=javascript:mode('new')><IMG src='bp/imgs/no_photo.jpg' width='144' height='108' border='0' alt='No Photo'></a>";
		}
		?>
						</TD>
                      </TR>
                      <TR>
                        <TD class="color4" align="right" valign="top"><a href=javascript:list('open')>ギャラリー</a> or <a href="bestphoto_reg.php">応募</a></TD>
                      </TR>
                    </TBODY>
                  </TABLE>
				  <?php

}
*/

function user_chk(){
	global $db_name,$db;
	
	$wbcookie= $_COOKIE["$db_name"];
	
	if($wbcookie != ""){
		list($c_name,$c_session_id)=explode(",",$wbcookie);

		$sql = "select * from `USER_DATA` where `name` = '$c_name'";
		$result = $db->query($sql);
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$uid = $user_rows["uid"];
		//var_dump($uid);
		
		if($uid){
			$sql = "select * from `USER_SESSION_ID` where `uid` = '$uid'";
			$result = $db->query($sql);
			$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			$session_id = $user_rows["session_id"];
			
			if($session_id == $c_session_id){
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
		
	}else{
		return false;
	}
}


function sub_menu($uid){
    global $PHP_CUR_PASS;

	if(usr_level($uid,1)){
		echo "<TR><TD><BR>　サブメニュー<BR></TD></TR>";
	}

	if(usr_level($uid,4)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."list/user.php'>冒険記録</a><BR></TD></TR>";
	}
	if(usr_level($uid,4)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."list/diary.php'>日記登録</a><BR></TD></TR>";
	}
	if(usr_level($uid,4)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."list/bazaar.php?mode=edit_bazaar'>バザー管理</a><BR></TD></TR>";
	}
	if(usr_level($uid,4)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."list/post.php'>モグポスト</a><BR></TD></TR>";
	}
	if(usr_level($uid,3)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."modify_poll.php'>投票管理</a><BR></TD></TR>";
	}
	if(usr_level($uid,1)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."usr_style.php'>スタイル設定</a><BR></TD></TR>";
	}
	if(usr_level($uid,5)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."usr_news.php'>ニュース編集</a><BR></TD></TR>";
	}
	
	
	if(usr_level($uid,9)){
		echo "<TR><TD><BR>　- Root Tool -<BR></TD></TR>";
	}
	if(usr_level($uid,9)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."bestphoto_setting.php'>ベストフォト設定</a><BR></TD></TR>";
	}
	if(usr_level($uid,9)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."user_permission.php'>ユーザー権限</a><BR></TD></TR>";
	}
	if(usr_level($uid,9)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."list/list_setting.php'>リスト設定</a><BR></TD></TR>";
	}
	if(usr_level($uid,9)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."site_theme.php'>サイトテーマ</a><BR></TD></TR>";
	}
	if(usr_level($uid,9)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."site_setting.php'>サイト設定</a><BR></TD></TR>";
	}
	if(usr_level($uid,9)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."right_menu.php'>右メニュー設定</a><BR></TD></TR>";
	}
	if(usr_level($uid,9)){
		echo "<TR><TD>　+ <a href='".$PHP_CUR_PASS."modify_user.php'>ユーザー編集</a><BR></TD></TR>";
	}
}

function usr_level($uid,$level){
	global $db;
	
	switch($level){
		case '1':
		$sql = "select `site` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
		
		case '2':
		$sql = "select `link` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
		
		case '3':
		$sql = "select `poll` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
		
		case '4':
		$sql = "select `guild` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
		
		case '5':
		$sql = "select `news` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
		
		case '6':
		$sql = "select `album` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
  
  		case '7':
		$sql = "select `forum` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
		
  		case '8':
		$sql = "select `content` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
		
  		case '9':
		$sql = "select `root` from `PHP_USR_LEVEL` where `uid` = '$uid'";
		break;
		
		default:
		return false;
		
	}
	
	
	$result = $db->query($sql);
	$user_rows = $result->fetchRow();

		if($user_rows[0]){
			return true;
		}else{
			return false;
		}
		
}

function main_menu(){
    global $PHP_CUR_PASS;

    echo "
                <TR>
                  <TD valign=\"middle\">Menu</TD>
                </TR>
                <TR>
                  <TD>　・<A href='".$PHP_CUR_PASS."index_pc.php'>トップ</A></TD>
                </TR>
                <TR>
                  <TD>　・<A href='".$PHP_CUR_PASS."readme.php'>はじめに</A></TD>
                </TR>
                <TR>
                  <TD>　・<A href='".$PHP_CUR_PASS."list/memberlist.php'>メンバーリスト</A></TD>
                </TR>
                <TR>
                  <TD>　・<A href='".$PHP_CUR_PASS."album.php'>アルバム</A></TD>
                </TR>
                <TR>
                  <TD>　・<A href='".$PHP_CUR_PASS."poll.php'>投票</A></TD>
                </TR>
                <TR>
                  <TD>　・<A href='".$PHP_CUR_PASS."forum/forum.php'>掲示板</A></TD>
                </TR>
                <TR>
                  <TD>　・<A href='".$PHP_CUR_PASS."links.php'>リンク</A></TD>
                </TR>
                <TR>
                  <TD></TD>
                </TR>
                <TR>
                  <TD>　・<A href='".$PHP_CUR_PASS."manual.php'>マニュアル</A></TD>
                </TR>
    ";

}

function login_form($uid){
	global $db,$name,$pass,$PHP_CUR_PASS;
	//require_once "list/memberlist_inc.php";
	
				  if(!$uid){
				  	$name = "name";
					$pass = "pass";
					$login_msg ="ログインしていません";
					$type = "password";

					echo'<FORM method=post enctype=multipart/form-data action="'.$PHP_CUR_PASS.'login.php">';
				  echo "<FONT CLASS='tx12'>*$login_msg</FONT><BR>";
				  echo "<INPUT size=15 type=text name=name value=$name><BR>";
                  echo "<INPUT size=15 type=$type name=pass value=$pass><BR>";
                  echo '<INPUT type="submit" value="login">';
				  echo '</FORM>';
				  

				//$env_rows = load_env();
  $sql = "select * from `MEMBER_LIST_ENV`";
  $result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$env_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	
				if($env_rows["reg_mode"]){
				  echo'<FORM method=post enctype=multipart/form-data action="'.$PHP_CUR_PASS.'mail_reg.php">';
				  //echo "メンバーの方は登録してください。";
				  echo '<INPUT type="submit" value="新規登録">';
				  echo '</form>';
				  echo '<a href="'.$PHP_CUR_PASS.'forgot_passwd.php">パスワード忘れ?</a>';
				}
				  
				  }else{
				  	$login_msg ="ログイン済みです";
				  	//$type = "password";
					echo'<FORM method=post enctype=multipart/form-data action="'.$PHP_CUR_PASS.'login.php">';
					echo "<FONT CLASS='tx12'>*$login_msg</FONT><BR>";
					echo '<INPUT type="submit" value="logout">';
					echo '</FORM>';

				  }

                  
}


function sub_msg($reload,$reload_url,$mode_str,$str){
	global $db,$uid,$name,$PHP_CUR_PASS,$db_name,$STYLE;

	if($reload){
		$reload_str ="<META HTTP-EQUIV='Refresh' CONTENT='$reload;URL=$PHP_CUR_PASS$reload_url'>";
	}

	//if($mode){
	//	$mode_str ="";
	//}else{
	//	$mode_str ="";
	//}
	
	
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<?php echo $reload_str; ?>
<TITLE><?php echo "$STYLE[site_name]"; ?></TITLE>
<?php echo "$STYLE[css]"; ?>
<SCRIPT language="JavaScript" src="<?php echo $PHP_CUR_PASS; ?>popup.js"></SCRIPT>
</HEAD>
<BODY>
<TABLE height="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD width="8" class="color3" background="<?php echo $PHP_CUR_PASS; ?>img/<?php echo "$STYLE[img_left]"; ?>" rowspan="2"><IMG src="/img/spacer.gif" width="8" height="1"></TD>
      <TD width="750" valign="top">
	  <?php echo "$STYLE[topimage]"; ?>
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="row_title" height="34"><IMG src="<?php echo $PHP_CUR_PASS; ?>img/spacer.gif" width="8" height="1"></TD>
            <TD class="row_title" height="34" width="131">FINAL FANTASY XI<BR>
            <?php echo "$STYLE[site_name]"; ?></TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10" class="color2">&nbsp;</TD>
            <TD class="color2" height="34" width="200">&nbsp;システムメッセージ</TD>
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
            <TD class="color2"><IMG src="<?php echo $PHP_CUR_PASS; ?>img/spacer.gif" width="8" height="1"></TD>
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
                <TD rowspan="5" align="right" width="10" valign="top"><BR>
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422">
                  <BR><?php echo $mode_str; ?></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
				  <blockquote>
				  <?php echo $str; ?>
				  </blockquote>
				<BR><HR><A href='javascript:history.back()'>戻る</A><BR>
				  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422"></TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
				 </TD>
                </TR>
              </TBODY>
            </TABLE>
            </TD>
          </TR>
        </TBODY>
      </TABLE>
      </TD>
      <TD width="25" class="color3" background="<?php echo $PHP_CUR_PASS; ?>img/<?php echo "$STYLE[img_right]"; ?>" rowspan="2"><IMG src="/img/spacer.gif" width="25" height="1"></TD>
      <TD class="color3" rowspan="2"></TD>
    </TR>
    <TR>
      <TD height="34">
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="color2" height="34"><IMG src="<?php echo $PHP_CUR_PASS; ?>img/spacer.gif" width="8" height="1"></TD>
            <TD class="color2" height="34" width="131">&nbsp;</TD>
            <TD class="color6" width="5">&nbsp;</TD>
            <TD width="10">&nbsp;</TD>
            <TD height="34" width="596" colspan="2" class="color2">
            <?php copyright(); ?></TD>
          </TR>
        </TBODY>
      </TABLE>
      </TD>
    </TR>
  </TBODY>
</TABLE>
</BODY>

<?php
die("</HTML>");

}



function wb_sendmail($send_mode,$send_me,$send_user,$mail_head,$main_msg){
	global $db,$env_rows,$db_name,$JOBLIST;
	//require_once "list/ml_common.php";
	//die("ok");
	$env_anon_mode = $env_rows["anon_mode"];
	$guild_name = $db_name;
	
	$sql = "select `mail_mode`,`mail_master` from `PHP_DEFAULT_STYLE`";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$DEF_STYLE = $result->fetchRow(DB_FETCHMODE_ASSOC);
	//var_dump($DEF_STYLE);
	$mlfr=$DEF_STYLE["mail_master"];
	if($DEF_STYLE["mail_mode"] == 1){
	
	$sql = "select `uid` from `USER_DATA` where `name` = '$send_me'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$SM_uid = $user_rows["uid"];
	
	$sql = "select * from PHP_USR_STYLE where `uid` = '$SM_uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$mail_mode = $main_rows["mail_list"];
	$mail_forum = $main_rows["mail_forum"];
	$mlto = $main_rows["mail_sendfor"];

	$sql = "select `uid` from `USER_DATA` where `name` = '$send_user'";
	
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$SU_uid = $user_rows["uid"];
	
	$sql = "select * from USER_STA,USER_LEV where USER_STA.uid = '$SU_uid' and USER_LEV.uid = '$SU_uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	
	if($main_rows){
	
		
		$user_name = $main_rows["name"];
		$user_class = $main_rows["class"];
		$user_comment = $main_rows["comment"];
		$user_anon = $main_rows["anon"];
		$user_mainjob = $main_rows["mainjob"];
		$user_supportjob = $main_rows["supportjob"];
		$user_lev[0] = $main_rows["lev0"];
		$user_lev[1] = $main_rows["lev1"];
		$user_lev[2] = $main_rows["lev2"];
		$user_lev[3] = $main_rows["lev3"];
		$user_lev[4] = $main_rows["lev4"];
		$user_lev[5] = $main_rows["lev5"];
		$user_lev[6] = $main_rows["lev6"];
		$user_lev[7] = $main_rows["lev7"];
		$user_lev[8] = $main_rows["lev8"];
		$user_lev[9] = $main_rows["lev9"];
		$user_lev[10] = $main_rows["lev10"];
		$user_lev[11] = $main_rows["lev11"];
		$user_lev[12] = $main_rows["lev12"];
		$user_lev[13] = $main_rows["lev13"];
		$user_lev[14] = $main_rows["lev14"];
		$user_lev[15] = $main_rows["lev15"];
		$user_lev[16] = $main_rows["lev16"];

		if($user_anon == 0 || $env_anon_mode != 1){

			if($user_lev[$user_mainjob]<10){
				$main_level="0$user_lev[$user_mainjob]";
			} else {
				$main_level=$user_lev[$user_mainjob];
			}

			if($user_supportjob != 99){
				$mod_level=intval($user_lev[$user_mainjob]/2);
				if($mod_level==0){$mod_level=1;}
				if($mod_level>$user_lev[$user_supportjob]){$mod_level=$user_lev[$user_supportjob];}
				if($mod_level<10){
					$mod_level="0$mod_level";
				}
				$user_job = "$JOBLIST[$user_mainjob]$main_level/$JOBLIST[$user_supportjob]$mod_level";
			} else{
				$user_job = "$JOBLIST[$user_mainjob]$main_level";
			}
		}else{
			$user_job = "-";
		}
	}else{
		$user_class = "none";
		$user_job = "none";
		$user_comment = "メンバーリストに登録していません。";
	}
	
	//var_dump($send_mode);
	if($send_mode == 2){
		if(!$mail_forum){
			$mail_mode = 3;
		}
	}elseif($send_mode == 1 && $mail_mode == 0){
		$mail_mode = 3;
	}elseif($send_mode == 0 && $mail_mode == 1){
		$mail_mode = 3;
	}

	//var_dump($mail_mode);
	if($mail_mode != 3){
		if($mlto){
		
		$mail_msg = substr("__ $mail_head ______________________________________________________",0,70)."\n\n";
		$mail_msg .= "Sender : $send_user さん\n";
		$mail_msg .= "$main_msg\n\n";
		$mail_msg .= "__ User_Info ______________________________________________________\n";
		$mail_msg .= "Name      : $send_user\n";
		$mail_msg .= "Class     : $user_class\n";
		$mail_msg .= "Job       : $user_job\n";
		$mail_msg .= "Comment   : $user_comment\n";
		$mail_msg .= "___________________________________________________________________\n";
		$mail_msg .= "\n";
		
		$mlsb ="[$guild_name -Mail Delivery-]";

		if(!$mlto or !$mlsb or !$mail_msg or !$mlfr){
			trigger_error("$mlfr", E_USER_ERROR);
		}
		
		mb_language('Japanese');
		mb_internal_encoding("SJIS");
		$rcd = mb_send_mail($mlto, $mlsb, $mail_msg, "FROM: $mlfr\nContent-Type: text/plain;\n charset=iso-2022-jp");
		//var_dump($rcd);

		}
	}
	
	}

}

function add_news($area,$val,$data){
	global $db;
	//area 0 ... $dataを追加
	//area 1 ... カテゴリー追加
	//area 2 ... フォーラム追加
	//area 3 ... トピック追加
	//area 4 ... ポストを追加。レス
	//area 5 ... list 
	//var_dump($area);
	//var_dump($val);
	$max_news = "30";
	//var_dump($data);
	$data = addslashes($data);
	//var_dump($data);
	$time = time();	
	
	$sql = "REPLACE INTO `PHP_SITE_NEWS` VALUES ('','$area','$val','$data','$time')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$sql = "select * from `PHP_SITE_NEWS`";
	$result = $db->query($sql);
	$chk = $result->numRows();
	if($chk > $max_news){
		$sql = "select min(`id`) from `PHP_SITE_NEWS` ";
		$result = $db->query($sql);
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$min_id = $tmp_rows["min(`id`)"];
		
	    $sql = "delete from `PHP_SITE_NEWS` where `id` = '$min_id'";
	    $result = $db->query($sql);
	    if (DB::isError($result)) {
	         trigger_error($result->getMessage(), E_USER_ERROR);
	    }
		
	}
	
}


function make_clickable($text){

	// pad it with a space so we can match things at the start of the 1st line.
	$ret = ' ' . $text;

	// matches an "xxxx://yyyy" URL at the start of a line, or after a space. 
	// xxxx can only be alpha characters. 
	// yyyy is anything up to the first space, newline, comma, double quote or < 
	$ret = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret); 

	// matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing 
	// Must contain at least 2 dots. xxxx contains either alphanum, or "-" 
	// zzzz is optional.. will contain everything up to the first space, newline, 
	// comma, double quote or <. 
	//$ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret); 
	
	$ret = ereg_replace("(https?|ftp|news)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)","<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",$ret);

	// matches an email@domain type address at the start of a line, or after a space.
	// Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
	$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

	// Remove our padding..
	$ret = substr($ret, 1);

	return($ret);
}

function make_clickable2($text){

	// pad it with a space so we can match things at the start of the 1st line.
	$ret = ' ' . $text;

	// matches an "xxxx://yyyy" URL at the start of a line, or after a space. 
	// xxxx can only be alpha characters. 
	// yyyy is anything up to the first space, newline, comma, double quote or < 
	$ret = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret); 

	// matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing 
	// Must contain at least 2 dots. xxxx contains either alphanum, or "-" 
	// zzzz is optional.. will contain everything up to the first space, newline, 
	// comma, double quote or <. 
	$ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret); 
	
	//$ret = ereg_replace("(https?|ftp|news)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)","<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",$ret);

	// matches an email@domain type address at the start of a line, or after a space.
	// Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
	$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

	// Remove our padding..
	$ret = substr($ret, 1);

	return($ret);
}

function copyright(){
	global $microtime_start,$PHP_CUR_PASS;
	$db2 = db_init2();

	$sql = "select * from `nSystem_UPD` order by `id` desc";
	
	$result = $db2->query($sql);
	
	$chk = $result->numRows();
	if($chk){
		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$rev = $user_rows["rev"];
	}
?>

Copyright (C) 2004 envision. All Rights Reserved.<BR>
Copyright (C) 2004 SQUARE ENIX CO., LTD. All Rights Reserved.

<?php

	$end_time = getmicrotime() -$microtime_start;
	echo "<BR><a href='".$PHP_CUR_PASS."system.php'>nSystem</a> Rev:$rev ";
	printf ("<B>Running time : %f seconds. </B>",$end_time);

}

function page_mode(){
	global $db;
	
	//table 
	//id
	//page_name
	//page_id
	//status
	
	//0=ok
	//1=lock
	
	//$microtime_start = getmicrotime();
	
	//$page_name = basename($_SERVER["PHP_SELF"]);
	
	$page_name = strstr($_SERVER["PHP_SELF"], '/');
	$pos =strpos($page_name,'/');
	$page_name = substr($page_name,$pos+1 );
	
	//var_dump($page_name);
	//die();
	$sql = "select * from `PHP_PM` where `page_name` = '$page_name'";
	$result = $db->query($sql);
	$chk = $result->numRows();
	if($chk){
		$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$status = $tmp_rows["status"];
		if($status){
			die("System halted.");
		}else{
			return true;
		}
	}else{
		$sql = "REPLACE INTO `PHP_PM` VALUES ('', '$page_name', '')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		
		return true;
	}

}

function new_news($time){
	global $db,$uid;
	
	if($uid){
		$sql = "select * from `PHP_USR_STYLE` where `uid` = '$uid'";
		$result = $db->query($sql);
		$chk = $result->numRows();
		if($chk){
			$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			$show_limit_time = $user_rows["limit_day"];
			$max_news = $user_rows["max_news"];
			if(!$show_limit_time){
				$show_limit_time = "1";
			}
		}else{
			$max_news = "5";
			$show_limit_time = "1";
		}
	
	}else{
		$max_news = "5";
		$show_limit_time = "1";
	}

	$limit_time_h = $show_limit_time * 24;
	
	if($time + 86400*$show_limit_time > time()){
		$new_news = "<font color='red'>new!!</font>";
		return $new_news;
	}else{
		return false;
	}
	
}

function find_oracle($uid){

	$db2 = db_init2();
	
	$sql = "select * from `ORACLE_USER` where `uid` = '$uid'";
	$result = $db2->query($sql);
	$chk = $result->numRows();
	if($chk){
		return true;
	}else{
		return false;
	}
}

function find_root($uid){
	global $db;
	
	$sql = "select * from `PHP_USR_LEVEL` where `uid` = '$uid' and `root` = '1'";
	$result = $db->query($sql);
	$chk = $result->numRows();
	if($chk){
		return true;
	}else{
		return false;
	}
}

function load_style($page,$mode){
	global $db,$uid,$PHP_CUR_PASS;
	
	if($uid){
	
		$sql = "select * from `PHP_DEFAULT_STYLE` where `id` = '1'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$DEF_STYLE = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$site_name = $DEF_STYLE["site_name"];
	
		$sql = "select * from `PHP_USR_STYLE` where `uid` = '$uid'";
		$result = $db->query($sql);
		$USR_STYLE = $result->fetchRow(DB_FETCHMODE_ASSOC);
		extract($USR_STYLE);
		//var_dump($USR_STYLE);
		
		$sql = "select * from `PHP_SITE_THEME` where `id` = '$site_theme'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$SIT_THEME = $result->fetchRow(DB_FETCHMODE_ASSOC);
		extract($SIT_THEME);
	
	}else{
	
		$sql = "select * from `PHP_DEFAULT_STYLE` where `id` = '1'";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$DEF_STYLE = $result->fetchRow(DB_FETCHMODE_ASSOC);
		extract($DEF_STYLE);
	
		$sql = "select * from `PHP_SITE_THEME` where `id` = '$site_theme'";
		//var_dump($sql);
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
		$SIT_THEME = $result->fetchRow(DB_FETCHMODE_ASSOC);
		extract($SIT_THEME);
	}

	//css
	if($fo_size == "0"){
		$css = "<LINK rel='stylesheet' href='".$PHP_CUR_PASS."css/theme".$theme_def."_def.css' type='text/css'>";
	}elseif($fo_size == "1"){
		$css = "<LINK rel='stylesheet' href='".$PHP_CUR_PASS."css/theme".$theme_small."_small.css' type='text/css'>";
	}elseif($fo_size == "2"){
		$css = "<LINK rel='stylesheet' href='".$PHP_CUR_PASS."css/theme".$theme_small."_small.css' type='text/css'>";
	}else{
		$css = "<LINK rel='stylesheet' href='".$PHP_CUR_PASS."css/theme0_def.css' type='text/css'>";
	}

	//topimage
	$imgs = $SIT_THEME["img_$page"];
	if($sc_size == "0"){
		if($imgs == "127"){
			$img_link = mt_rand(0,$img_rdn);
			$topimage = "<IMG src='".$PHP_CUR_PASS."img/topimage".$img_link.".jpg' width='750' height='200' border='0'><BR>";
		}else{
			$topimage = "<IMG src='".$PHP_CUR_PASS."img/topimage".$imgs.".jpg' width='750' height='200' border='0'><BR>";
		}
	}
		
	//img_left
	$img_left = "img_left".$img_left.".gif";
	
	//img_right
	$img_right = "img_right".$img_right.".gif";
		
		
	//BBS
	if($mode){
		//celpic0
		$cellpic0 = "cellpic".$forum_cellpic0.".gif";
	
		//celpic1
		$cellpic1 = "cellpic".$forum_cellpic1.".gif";
	
		//post
		$post = "post".$forum_post.".gif";
	
		//reply
		$reply = "reply".$forum_reply.".gif";
	
		//sta0
		$sta0 = "sta".$forum_sta0.".gif";
	
		//sta1
		$sta1 = "sta".$forum_sta1.".gif";
	
		//sta2
		$sta2 = "sta".$forum_sta2.".gif";
		
		//sta3
		$sta3 = "sta".$forum_sta3.".gif";
	}

	$STYLE = compact('site_name','topimage','css','img_left','img_right','limit_day');
	if($mode){
		$BBS = compact('cellpic0','cellpic1','post','reply','sta0','sta1','sta2','sta3','max_news','broadband','diary_mode','$mail_mode','$mail_master');
		$STYLE = $STYLE + $BBS;
	}
	/*
	if($mode==1){
		$OTH = compact('max_news','broadband','dairy_mode');
		$STYLE = $STYLE + $OTH;
	}
	*/
	
	return $STYLE;

}

function formatTimestamp($time) {
	//setlocale (LC_TIME, $locale);
    ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $time, $datetime);
    $datetime = mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]);

    return($datetime);
}

function insert_safe($str) 
{
    if (get_magic_quotes_gpc() == 1) {
        $str = strip_tags(trim($str));
        return $str;
    } else {
        $str = addslashes(strip_tags(trim($str)));
        return $str;
    }
}

function convert_to_sjis($str) 
{
	//global $_POST;
	mb_language('Japanese');
    //if ($cfg['enable_unicode'] == 'on') {
        if (function_exists('mb_convert_encoding')) {
            if (is_array($str)) {
			    while (list ($key, $val) = each ($str)) {
			    	$str[$key] = mb_convert_encoding($val, 'SJIS', "auto");
					//var_dump($str[$key]);
    			}
            }
        }
		
		return $str;
    //}
}

?>
