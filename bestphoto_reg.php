<?php
//���C�u�����Ăяo��
require_once "db_setting.php";
require_once "php_inc.php";
require_once "right_menu_inc.php";
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

$f_name = $_POST["f_name"];
$f_msg = $_POST["f_msg"];
$f_mode = $_POST["f_mode"];
if($f_name && $f_msg && $f_mode == "submit"){
	$arrowext = array('jpg','jpeg','png');
	$W = 144;
	$H = 108;
	$WW = 512;
	$HH = 382;
	$image_type = 1;
	$limitk	= 3072;

function error_msg($txt1,$txt2){
	echo "$txt1 <BR>";
	die("$txt2 <BR>");
}

function thumb_create($src, $W, $H, $sam_dir){
	global $image_type;
  // �摜�̕��ƍ����ƃ^�C�v���擾
  $size = GetImageSize($src);
  //var_dump($size);
  switch ($size[2]) {
    case 1 : return false; break;
    case 2 : $im_in = ImageCreateFromJPEG($src); break;
    case 3 : $im_in = ImageCreateFromPNG($src);  break;
  }
  
  	if(!$im_in){
		$filename = substr($src, strrpos($src,"/")+1);
  		$filename = substr($filename, 0, strrpos($filename,"."));
  		if($image_type == 1){
  			copy($src, $sam_dir.$filename.".jpg");
  		}else{
  			copy($src, $sam_dir.$filename.".png");
  		}
		return false; break;
		//sub_msg("","","Jpeg�f�R�[�h�G���[","����Jpeg�t�@�C���͐���������܂���B");
	}
  
  // ���T�C�Y
  if ($size[0] > $W || $size[1] > $H) {
    $key_w = $W / $size[0];
    $key_h = $H / $size[1];
    ($key_w < $key_h) ? $keys = $key_w : $keys = $key_h;
    $out_w = $size[0] * $keys;
    $out_h = $size[1] * $keys;
  } else {
    $out_w = $size[0];
    $out_h = $size[1];
  }
  // �o�͉摜�i�T���l�C���j�̃C���[�W���쐬
  // $im_out = ImageCreate($out_w, $out_h);
  $im_out = ImageCreateTrueColor($out_w, $out_h);
  // ���摜���c���Ƃ� �R�s�[���܂��B
  ImageCopyResized($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  // �T���l�C���摜���u���E�U�ɏo�́A�ۑ�
  $filename = substr($src, strrpos($src,"/")+1);
  $filename = substr($filename, 0, strrpos($filename,"."));
  if($image_type == 1){
  	ImageJPEG($im_out, $sam_dir.$filename.".jpg");
  }else{
  	ImagePNG($im_out, $sam_dir.$filename.".png");
  }
  // �쐬�����C���[�W��j��
  ImageDestroy($im_in);
  ImageDestroy($im_out);
}

		$upfile_size=$_FILES["upfile"]["size"];
		$upfile_name=$_FILES["upfile"]["name"];
		$upfile=$_FILES["upfile"]["tmp_name"];
		
		if($upfile_name != ""){
		$pos = strrpos($upfile_name,".");	//�g���q�擾
		$ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		$ext = strtolower($ext);//��������
		if(!in_array($ext, $arrowext)){
			error_msg("�g���q�G���[","���̊g���q�t�@�C���̓A�b�v���[�h�ł��܂���");
		}
		$limitb = $limitk * 1024;
		if($limitb < $upfile_size){
		$nowsize = intval( $upfile_size /1024 );
			error_msg("�t�@�C���T�C�Y�G���[","�ő�A�b�v�e�ʂ�... $limitk kb �ł�<br>���݂̃t�@�C���T�C�Y��... $nowsize kb �ł�");
		}
		
		$newname = "bestphoto_".time().".$ext";
		move_uploaded_file($upfile, "bp/img/$upfile_name");
		rename("bp/img/$upfile_name", "bp/img/$newname");
		$sam_size = getimagesize("bp/img/$newname");
		if ($sam_size[0] > $W || $sam_size[1] > $H) {
			thumb_create("bp/img/$newname",$W,$H,"bp/imgs/");
		}
		$sam_size = getimagesize("bp/img/$newname");
		if ($sam_size[0] > $WW || $sam_size[1] > $HH) {
			thumb_create("bp/img/$newname",$WW,$HH,"bp/img/");
		}
		}

	if(!$f_msg && !$f_name){
		die("No form date");
	}
	$f_name = htmlspecialchars($f_name);
	$f_msg = htmlspecialchars($f_msg);

	$date = time();

	$sql = "REPLACE INTO `PHP_BESTPHOTO` VALUES ('', '0', '$f_name', '$f_msg', '$newname', '2','$date')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sta_msg = "�o�^���܂���!!";

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
            <TD class="color2" height="34" width="200">&nbsp;TOP</TD>
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
                  <TD colspan="2" width="422"></TD>
                  <TD rowspan="5" align="right" width="148" valign="top">
				<?php
					right_menu();
				?>
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422" valign="top"><?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?><BR>�摜���e</TD>
                </TR>

                <TR>
				<TD align="left" colspan="2" valign="top"><BR>
                  <TABLE>
  <TBODY>
    <TR>
      <TD>���O</TD>
      <TD>
      <FORM method="post" enctype="multipart/form-data" action="bestphoto_reg.php"><INPUT size="20" type="text" name="f_name" value="<?php echo $c_name; ?>">
      </TD>
    </TR>
    <TR>
      <TD>���b�Z�[�W</TD>
      <TD>
      <INPUT size="60" type="text" name="f_msg">
      </TD>
    </TR>
    <TR>
      <TD>�摜</TD>
      <TD><INPUT type="file" name="upfile"></TD>
    </TR>
    <TR>
      <TD>submit</TD>
      <TD>
      <INPUT type="submit" name="f_mode" value="submit">
      </TD>
    </TR></FORM>
  </TBODY>
</TABLE>
                <BR></TD></TR>
				<TR>
				<TD align="left" colspan="2" valign="top"><BR>
				*���e�摜�͖��T���j��0���ɃV�X�e���������I�ɒ��I���܂��B<BR>
				*�usubmit�v�������Ƒ��M����A�㕔�Ɂu�o�^���܂���!! �v�ƕ\�������܂ł��҂����������B�\�����ꂽ�ꍇ�͓o�^�����ł��B<BR>
				*���e�ł���摜�̎�ނ�<B>�ujpg , jpeg , png�v</B>�ł��B<BR>
				*�摜�͎����I�ɏk���g�傳��܂����Ȃ�ׂ�FinalFantasyXI�̃f�t�H���g��SS�T�C�Y�����g�����������B<BR><BR><HR width='420'><A href='javascript:history.back()'>�߂�</A>
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
