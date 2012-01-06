<?php
//���C�u�����Ăяo��
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

$STYLE = load_style(6,0);

if(!$uid){
	die("Authorization Required");
}else{
	if(!usr_level($uid,6)){
		die("Access Denied");
	}
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

$f_mode = $_POST["f_mode"];
$a_session_id= $_POST["session_id"];
$a_title= $_POST["a_title"];
//var_dump($a_session_id);
if(!$a_title){
    $a_title ="���@�i�E�f�B�[���ʐ^�W";
}
if(!$a_session_id){
    mt_srand(microtime()*100000);
	$uid_md5 = md5(uniqid(mt_rand(),1));
	$a_session_id = substr($uid_md5, 0, 15);
}
if($_POST["session_id"]){
    $sta_msg = "���̃A���o���͂܂��ǉ��o�^�ł��܂��B";
}

if($f_mode =="submit" && $uid){

    $sql = "select * from `PHP_ALBUM` where `session_id` = '$a_session_id'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$a_uid = $user_rows["uid"];
 
    if($a_uid){
       if($a_uid != $uid){
          die("Access Denied");
       }
    }

    //�e��ݒ�
    $arrowext = array('jpg','jpeg','png');
	$W = 172;
	$H = 129;
	$image_type = 1;
	$limitk	= 3072;
    $limitb = $limitk * 1024;
 
    //�A�b�v���[�h�t�@�C��
 	$upfile_size1=$_FILES["upfile1"]["size"];
	$upfile_name1=$_FILES["upfile1"]["name"];
	$upfile1=$_FILES["upfile1"]["tmp_name"];

  	$upfile_size2=$_FILES["upfile2"]["size"];
	$upfile_name2=$_FILES["upfile2"]["name"];
	$upfile2=$_FILES["upfile2"]["tmp_name"];
 
 	$upfile_size3=$_FILES["upfile3"]["size"];
	$upfile_name3=$_FILES["upfile3"]["name"];
	$upfile3=$_FILES["upfile3"]["tmp_name"];

  	$upfile_size4=$_FILES["upfile4"]["size"];
	$upfile_name4=$_FILES["upfile4"]["name"];
	$upfile4=$_FILES["upfile4"]["tmp_name"];
 
  	$upfile_size5=$_FILES["upfile5"]["size"];
	$upfile_name5=$_FILES["upfile5"]["name"];
	$upfile5=$_FILES["upfile5"]["tmp_name"];
 
    if($upfile_name1 || $upfile_name2 || $upfile_name3 || $upfile_name4 || $upfile_name5){
       //if(!is_dir("./album/$name")){
       //    mkdir("./album/$name",0777);
      // }
       if(!is_dir("./album/$a_session_id")){
           mkdir("./album/$a_session_id",0777);
		   add_news('6',"","$name");
       }
       if(!is_dir("./album/$a_session_id/img")){
           mkdir("./album/$a_session_id/img",0777);
       }
       if(!is_dir("./album/$a_session_id/imgs")){
           mkdir("./album/$a_session_id/imgs",0777);
       }
       
       require_once "lib_new_album.php";

    $a_title = htmlspecialchars($a_title);
   	$date = time();
	$sql = "REPLACE INTO `PHP_ALBUM` VALUES ('$a_session_id', '$uid', '$name', '$a_title', '$date')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}

	$sta_msg = "�o�^���܂���!!<BR>���̃A���o���͂܂��ǉ��o�^�ł��܂��B";
       
       
    }
 }
 if($_POST["session_id"]){
    $sql = "select * from `PHP_ALBUM` where `session_id` = '$a_session_id'";
	$result = $db->query($sql);
	$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $a_name = $user_rows["name"];
    if($a_name){
        $img_dir = "./album/$a_session_id/img/";
        $ext = ".+\.png$|.+\.jpe?g$";
        $img_counter = 0;
        $d = dir($img_dir);
           while ($ent = $d->read()) {
               if (eregi($ext, $ent)) {
                   $files[] = $ent;
                  $img_counter++;
               }

           }
        $d->close();
        $sta_msg .= "<BR>���� $img_counter ���o�^�ς݂ł��`";
        if($files){
            rsort($files);
            $pos1 = strrpos($files[0],"_");
            $pos2 = strrpos($files[0],".");
            $id = substr($files[0],$pos1+1,$pos2-$pos1-1);
            intval($id);
            if($id >= 9999){
                $sta_msg = "���̃A���o���͒ǉ��o�^�ł��܂���B";
            }
        }
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
            <TD class="color2" height="34" width="200">&nbsp;�A���o��</TD>
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
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422">
                  <?php if($sta_msg){echo "<BR><B>$sta_msg </B><BR>";} ?>
                  <BR>���@�i�E�f�B�[���`���ʐ^</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <FORM method=post enctype=multipart/form-data action='new_album.php'>
<TABLE cellpadding="0" cellspacing="0">
  <TBODY>
    <TR>
      <TD>�A���o���^�C�g��&nbsp;</TD>
      <TD>
      <INPUT size="40" type="text" name="a_title" value="<?php echo "$a_title"; ?>">
      </TD>
    </TR>
    <TR>
      <TD>�R�����g/�摜1</TD>
      <TD><INPUT size="25" type="text" name="comment1">&nbsp;<INPUT type="file" name="upfile1"></TD>
    </TR>
    <TR>
      <TD>�R�����g/�摜2</TD>
      <TD><INPUT size="25" type="text" name="comment2">&nbsp;<INPUT type="file" name="upfile2"></TD>
    </TR>
    <TR>
      <TD>�R�����g/�摜3</TD>
      <TD><INPUT size="25" type="text" name="comment3">&nbsp;<INPUT type="file" name="upfile3"></TD>
    </TR>
    <TR>
      <TD>�R�����g/�摜4</TD>
      <TD><INPUT size="25" type="text" name="comment4">&nbsp;<INPUT type="file" name="upfile4"></TD>
    </TR>
    <TR>
      <TD>�R�����g/�摜5</TD>
      <TD><INPUT size="25" type="text" name="comment5">&nbsp;<INPUT type="file" name="upfile5"></TD>
    </TR>
  </TBODY>
</TABLE>
<INPUT type="submit" name="f_mode" value="submit"><input type="hidden" name="session_id" value="<?php echo "$a_session_id"; ?>"></FORM>
               <P>*���̓��e��5���܂œo�^�ł��܂��B<BR>
                  *�R�����g�͏ȗ��ł��܂��B<BR>
                  *���e�ł���摜�̎�ނ�<B>�ujpg , jpeg , png�v</B>�ł��B<BR>
                  *�usubmit�v�������Ƒ��M����A�㕔�Ɂu�o�^���܂���!! �v�ƕ\�������܂ł��҂����������B�\�����ꂽ�ꍇ�͓o�^�����ł��B<BR>
               </P>
				<BR></TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422">
                  <?php
                    if($uid){
                      echo "<TABLE cellpadding='0' cellspacing='0'>
                             <TBODY>
                                <TR>
                                 <TD>
                                   <FORM method=post enctype=multipart/form-data action='new_album.php'>
                                   <INPUT type='submit' value='�V�K�쐬'></FORM>
                                 </TD>
                                 <TD>
                                 &nbsp;
                                 </TD>";
                                 if($_POST["session_id"]){
                                     echo "
                                 <TD>
                                   <FORM method=post enctype=multipart/form-data action='mod_album.php'>
                                   <input type=\"hidden\" name=\"session_id\" value=\"$a_session_id\">
                                   <INPUT type='submit' value='�ҏW'></FORM>
                                 </TD>
                                 ";
                             }
                                 echo "
                                </TR>
                             </TBODY>
                            </TABLE>";
                    }
                  ?>
                  </TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2">
                  <HR width='420'><A href='javascript:history.back()'>�߂�</A><BR><BR><BR>
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