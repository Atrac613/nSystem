<?php
//���C�u�����Ăяo��
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "ml_common.php";
require_once "memberlist_inc.php";

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

$STYLE = load_style(3,0);

if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,9)){
		die("Access Denied");
	}
}

$modify = $_POST["modify"];
if($modify){

$arrowext = array('gif');
$limitk	= 3072;		//�A�b�v���[�h�����iKB �L���o�C�g�j
$putdir = "./img/";

	$upfile_size=$_FILES["uf_diary"]["size"];
	$upfile_name=$_FILES["uf_diary"]["name"];
	$upfile=$_FILES["uf_diary"]["tmp_name"];
			
	if($upfile_name != ""){
		$newname = "diary.gif";
		$pos = strrpos($upfile_name,".");	//�g���q�擾
		$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		$ext = strtolower($ext);//��������
		if(!in_array($ext, $arrowext)){
			sub_msg("","","�g���q�G���[","���̊g���q�t�@�C���̓A�b�v���[�h�ł��܂���");
		}
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
		$nowsize = intval( $upfile_size /1024 );
			sub_msg("","","�t�@�C���T�C�Y�G���[","�ő�A�b�v�e�ʂ�... $limitk kb �ł�<br>���݂̃t�@�C���T�C�Y��... $nowsize kb �ł�");
		}

		move_uploaded_file($upfile, $putdir.$upfile_name);
		rename($putdir.$upfile_name, $putdir.$newname);
	}
	
	$upfile_size=$_FILES["uf_diary_up"]["size"];
	$upfile_name=$_FILES["uf_diary_up"]["name"];
	$upfile=$_FILES["uf_diary_up"]["tmp_name"];
	if($upfile_name != ""){
		$newname = "diary_up.gif";
		$pos = strrpos($upfile_name,".");	//�g���q�擾
		$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		$ext = strtolower($ext);//��������
		if(!in_array($ext, $arrowext)){
			sub_msg("","","�g���q�G���[","���̊g���q�t�@�C���̓A�b�v���[�h�ł��܂���");
		}
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
		$nowsize = intval( $upfile_size /1024 );
			sub_msg("","","�t�@�C���T�C�Y�G���[","�ő�A�b�v�e�ʂ�... $limitk kb �ł�<br>���݂̃t�@�C���T�C�Y��... $nowsize kb �ł�");
		}

		move_uploaded_file($upfile, $putdir.$upfile_name);
		rename($putdir.$upfile_name, $putdir.$newname);
	}
	
	$upfile_size=$_FILES["uf_bazaar"]["size"];
	$upfile_name=$_FILES["uf_bazaar"]["name"];
	$upfile=$_FILES["uf_bazaar"]["tmp_name"];
	if($upfile_name != ""){
		$newname = "bazaar.gif";
		$pos = strrpos($upfile_name,".");	//�g���q�擾
		$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		$ext = strtolower($ext);//��������
		if(!in_array($ext, $arrowext)){
			sub_msg("","","�g���q�G���[","���̊g���q�t�@�C���̓A�b�v���[�h�ł��܂���");
		}
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
		$nowsize = intval( $upfile_size /1024 );
			sub_msg("","","�t�@�C���T�C�Y�G���[","�ő�A�b�v�e�ʂ�... $limitk kb �ł�<br>���݂̃t�@�C���T�C�Y��... $nowsize kb �ł�");
		}

		move_uploaded_file($upfile, $putdir.$upfile_name);
		rename($putdir.$upfile_name, $putdir.$newname);
	}
	
	$default_table = intval($_POST["default_table"]);
	$diary_res = intval($_POST["diary_res"]);
	$show_max = intval($_POST["show_max"]);
	$reg_mode = intval($_POST["reg_mode"]);
	$reg_pass = $_POST["reg_pass"];
	$class_edit = intval($_POST["class_edit"]);
	$anon_mode = intval($_POST["anon_mode"]);
	$img_allow = intval($_POST["img_allow"]);
	$oekaki_mode = intval($_POST["oekaki_mode"]);
	$info_title = $_POST["info_title"];
	$info_body = $_POST["info_body"];
	
	$info_body = str_replace("\r\n", "\r", $info_body);
	$info_body = str_replace("\r", "\n", $info_body);
	
	$sql = "replace INTO `MEMBER_LIST_ENV` VALUES ('1','$default_table','$diary_res','$show_max','$reg_mode','$reg_pass','$class_edit','$anon_mode','$img_allow','$oekaki_mode','$info_title','$info_body')";
				
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sta_msg = "�o�^���܂���!!";
}

	$env_rows = load_env();

	$diary_res = $env_rows["diary_res"];
	$show_max = $env_rows["show_max"];

	$reg_mode = $env_rows["reg_mode"];
	$reg_pass = $env_rows["reg_pass"];
	
	$class_edit = $env_rows["class_edit"];
	$anon_mode = $env_rows["anon_mode"];
	
	$default_table = $env_rows["default_table"];
	$img_allow = $env_rows["img_allow"];
	
	$oekaki_mode = $env_rows["oekaki_mode"];
	
	$info_title = $env_rows["info_title"];
	$info_body = $env_rows["info_body"];

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
            <TD class="color2" height="34" width="200">&nbsp;Root Tool</TD>
            <TD class="color2" align="right" height="34" width="396">
			<?php
			
			if($name){
				echo "�悤�����A$name ����";
			}else{
				echo "�悤�����A�Q�X�g����";
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
                  <TD colspan="2" width="422" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR></TD>
                </TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
<form method=post enctype='multipart/form-data' action=list_setting.php>
<TABLE width="100%" cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD colspan="2">�����o�[���X�g�ݒ�</TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD>���L�摜<IMG src="img/diary.gif" width=15 height=15></TD>
      <TD><input type='file' name='uf_diary'></TD>
    </TR>
    <TR>
      <TD>���LUP�摜<IMG src="img/diary_up.gif" width=13 heigh=9></TD>
      <TD><input type='file' name='uf_diary_up'></TD>
    </TR>
    <TR>
      <TD>�o�U�[�摜<IMG src="img/bazaar.gif" width=15 heigh=15></TD>
      <TD><input type='file' name='uf_bazaar'></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD>�f�t�H���g�e�[�u��</TD>
      <TD><SELECT name="default_table"><?php
for($i=0;$i<count($MENU);$i++){
	if($i == $default_table){
		echo "<option value=$i selected>$MENU[$i]</option>\n";
	}else{
		echo "<option value=$i>$MENU[$i]</option>\n";
	}
}
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>���L�ւ̃��X</TD>
      <TD><SELECT name="diary_res"><?php
	  
		if($diary_res){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>����</option>";
		echo "<option value=1 $sel1>����</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>���L�ő�\����</TD>
      <TD><input type=text name="show_max" size=3 value="<?php echo "$show_max"; ?>"></TD>
    </TR>
    <TR>
      <TD colspan="2"><BR></TD>
    </TR>
    <TR>
      <TD colspan="2"></TD>
    </TR>
    <TR>
      <TD>�o�^���[�h</TD>
      <TD><SELECT name="reg_mode"><?php
	  	
		$sel0="";
		$sel1="";
		$sel2="";
		
		if($reg_mode == 1){
			$sel1 = "selected";
		}elseif($reg_mode == 2){
			$sel2 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>�o�^�s��</option>";
		echo "<option value=1 $sel1>�o�^����</option>";
		echo "<option value=2 $sel2>�o�^����(�v�p�X���[�h)</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>�o�^���p�X���[�h</TD>
      <TD><input type=text name="reg_pass" size=10 value="<?php echo "$reg_pass"; ?>"></TD>
    </TR>
    <TR>
      <TD>�N���X�̕ҏW</TD>
      <TD><SELECT name="class_edit"><?php
	  
		$sel0="";
		$sel1="";
		$sel2="";
		
		if($class_edit){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>����</option>";
		echo "<option value=1 $sel1>����</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>�A�m����</TD>
      <TD><SELECT name="anon_mode"><?php
		$sel0="";
		$sel1="";
		$sel2="";
		if($anon_mode){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>����</option>";
		echo "<option value=1 $sel1>����</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>���L�̉摜��</TD>
      <TD><SELECT name="img_allow"><?php
		$sel0="";
		$sel1="";
		$sel2="";
		if($img_allow){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>����</option>";
		echo "<option value=1 $sel1>����</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>���G������</TD>
      <TD><SELECT name="oekaki_mode"><?php
		$sel0="";
		$sel1="";
		$sel2="";
		if($oekaki_mode){
			$sel1 = "selected";
		}else{
			$sel0 = "selected";
		}
	  	echo "<option value=0 $sel0>����</option>";
		echo "<option value=1 $sel1>����</option>";
	  ?></SELECT></TD>
    </TR>
    <TR>
      <TD>�C���t�H���[�V����<BR>(�^�C�g��)</TD>
      <TD><input type=text name="info_title" size=20 value="<?php echo "$info_title"; ?>"></TD>
    </TR>
    <TR>
      <TD>�C���t�H���[�V����<BR>(�{��)</TD>
      <TD><TEXTAREA name="info_body" rows=5 cols=40><?php echo "$info_body"; ?></TEXTAREA></TD>
    </TR>
    <TR>
      <TD><input type=submit value=Modify name=modify></TD>
      <TD></TD>
    </TR>
	</form>
  </TBODY>
</TABLE>
<BR>
				</TD></TR><TR><TD colspan="2" width="422" valign="top"></TD></TR>
                <TR>
				<TD align="left" colspan="2" valign="top">
                </TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				*�uModify�v�������Ƒ��M����A�㕔�Ɂu�o�^���܂���!! �v�ƕ\�������܂ł��҂����������B�\�����ꂽ�ꍇ�͓o�^�����ł��B<BR><BR>
				*�C���t�H���[�V�����̓^�C�g������͂��邱�ƂŗL���ɂȂ�܂��B
				<BR><BR>
				*���L�摜�A���LUP�摜�A�o�U�[�摜��<b>.gif</b>�̂݃A�b�v�ł��܂��B�T�C�Y�̓T���v�����������������B
				
				<BR>
				<BR><HR width='420'><A href='javascript:history.back()'>�߂�</A><BR><BR><BR>
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