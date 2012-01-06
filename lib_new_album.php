<?php

 		if($upfile_name1){
            $pos = strrpos($upfile_name1,".");	//拡張子取得
            $ext = substr($upfile_name1,$pos+1,strlen($upfile_name1)-$pos);
            $ext = strtolower($ext);//小文字化
            if(!in_array($ext, $arrowext)){
			    die("その拡張子ファイルはアップロードできません");
            }

            if($limitb < $upfile_size1){
                $nowsize = intval( $upfile_size1 /1024 );
                die("最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
            }
            // ディレクトリ一覧取得、ソート
            $img_dir = "./album/$a_session_id/img/";
            $sam_dir = "./album/$a_session_id/imgs/";
            $ext1 = ".+\.png$|.+\.jpe?g$";
            $d = dir($img_dir);
              while ($ent = $d->read()) {
                if (eregi($ext1, $ent)) {
                   $files[] = $ent;
                }
              }
            $d->close();
            // ソート
            if($files){
                rsort($files);
                $pos1 = strrpos($files[0],"_");
                $pos2 = strrpos($files[0],".");
                $id = substr($files[0],$pos1+1,$pos2-$pos1-1);
                intval($id);
                $id++;
                if($id<10){$id="0$id";}
                if($id<100){$id="0$id";}
                if($id<1000){$id="0$id";}
                if($id>=10000){die("error: buffer over flow");}

            }else{
                $id = "0000";
            }

            $newname = $name."_".$id.".$ext";
			$newname_r = $name."_".$id;
			
            move_uploaded_file("$upfile1", "$img_dir$upfile_name1");
            rename("$img_dir$upfile_name1", "$img_dir$newname");
            $sam_size = getimagesize("$img_dir$newname");
            if ($sam_size[0] > $W || $sam_size[1] > $H) {
               thumb_create("$img_dir$newname",$W,$H,"$sam_dir");
            }else{
               copy("$img_dir$newname","$sam_img$newname");
            }
            
            //comment
            $comment1 = $_POST["comment1"];
            if($comment1){
                $comment1 = htmlspecialchars($comment1);
               	$sql = "REPLACE INTO `PHP_ALBUM_COMMENT` VALUES ('', '$uid', '$img_dir$newname_r', '$comment1')";
                $result = $db->query($sql);
                if (DB::isError($result)) {
                   trigger_error($result->getMessage(), E_USER_ERROR);
                }
            }
		}

 		if($upfile_name2){
            $pos = strrpos($upfile_name2,".");	//拡張子取得
            $ext = substr($upfile_name2,$pos+1,strlen($upfile_name2)-$pos);
            $ext = strtolower($ext);//小文字化
            if(!in_array($ext, $arrowext)){
			    die("その拡張子ファイルはアップロードできません");
            }

            if($limitb < $upfile_size2){
                $nowsize = intval( $upfile_size2 /1024 );
                die("最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
            }
            // ディレクトリ一覧取得、ソート
            $img_dir = "./album/$a_session_id/img/";
            $sam_dir = "./album/$a_session_id/imgs/";
            $ext1 = ".+\.png$|.+\.jpe?g$";
            $d = dir($img_dir);
              while ($ent = $d->read()) {
                if (eregi($ext1, $ent)) {
                   $files[] = $ent;
                }
              }
            $d->close();
            // ソート
            if($files){
                rsort($files);
                $pos1 = strrpos($files[0],"_");
                $pos2 = strrpos($files[0],".");
                $id = substr($files[0],$pos1+1,$pos2-$pos1-1);
                intval($id);
                $id++;
                if($id<10){$id="0$id";}
                if($id<100){$id="0$id";}
                if($id<1000){$id="0$id";}
                if($id>=10000){die("error: buffer over flow");}
            }else{
                $id = "0";
            }

            $newname = $name."_".$id.".$ext";
			$newname_r = $name."_".$id;
            move_uploaded_file("$upfile2", "$img_dir$upfile_name2");
            rename("$img_dir$upfile_name2", "$img_dir$newname");
            $sam_size = getimagesize("$img_dir$newname");
            if ($sam_size[0] > $W || $sam_size[1] > $H) {
               thumb_create("$img_dir$newname",$W,$H,"$sam_dir");
            }else{
               copy("$img_dir$newname","$sam_img$newname");
            }

            //comment
            $comment2 = $_POST["comment2"];
            if($comment2){
                $comment2 = htmlspecialchars($comment2);
               	$sql = "REPLACE INTO `PHP_ALBUM_COMMENT` VALUES ('', '$uid', '$img_dir$newname_r', '$comment2')";
                $result = $db->query($sql);
                if (DB::isError($result)) {
                   trigger_error($result->getMessage(), E_USER_ERROR);
                }
            }
		}
  
  		if($upfile_name3){
            $pos = strrpos($upfile_name3,".");	//拡張子取得
            $ext = substr($upfile_name3,$pos+1,strlen($upfile_name3)-$pos);
            $ext = strtolower($ext);//小文字化
            if(!in_array($ext, $arrowext)){
			    die("その拡張子ファイルはアップロードできません");
            }

            if($limitb < $upfile_size3){
                $nowsize = intval( $upfile_size3 /1024 );
                die("最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
            }
            // ディレクトリ一覧取得、ソート
            $img_dir = "./album/$a_session_id/img/";
            $sam_dir = "./album/$a_session_id/imgs/";
            $ext1 = ".+\.png$|.+\.jpe?g$";
            $d = dir($img_dir);
              while ($ent = $d->read()) {
                if (eregi($ext1, $ent)) {
                   $files[] = $ent;
                }
              }
            $d->close();
            // ソート
            if($files){
                rsort($files);
                $pos1 = strrpos($files[0],"_");
                $pos2 = strrpos($files[0],".");
                $id = substr($files[0],$pos1+1,$pos2-$pos1-1);
                intval($id);
                $id++;
                if($id<10){$id="0$id";}
                if($id<100){$id="0$id";}
                if($id<1000){$id="0$id";}
                if($id>=10000){die("error: buffer over flow");}
            }else{
                $id = "0";
            }

            $newname = $name."_".$id.".$ext";
			$newname_r = $name."_".$id;
            move_uploaded_file("$upfile3", "$img_dir$upfile_name3");
            rename("$img_dir$upfile_name3", "$img_dir$newname");
            $sam_size = getimagesize("$img_dir$newname");
            if ($sam_size[0] > $W || $sam_size[1] > $H) {
               thumb_create("$img_dir$newname",$W,$H,"$sam_dir");
            }else{
               copy("$img_dir$newname","$sam_img$newname");
            }

            //comment
            $comment3 = $_POST["comment3"];
            if($comment3){
                $comment3 = htmlspecialchars($comment3);
               	$sql = "REPLACE INTO `PHP_ALBUM_COMMENT` VALUES ('', '$uid', '$img_dir$newname_r', '$comment3')";
                $result = $db->query($sql);
                if (DB::isError($result)) {
                   trigger_error($result->getMessage(), E_USER_ERROR);
                }
            }
		}
  
 		if($upfile_name4){
            $pos = strrpos($upfile_name4,".");	//拡張子取得
            $ext = substr($upfile_name4,$pos+1,strlen($upfile_name4)-$pos);
            $ext = strtolower($ext);//小文字化
            if(!in_array($ext, $arrowext)){
			    die("その拡張子ファイルはアップロードできません");
            }

            if($limitb < $upfile_size4){
                $nowsize = intval( $upfile_size4 /1024 );
                die("最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
            }
            // ディレクトリ一覧取得、ソート
            $img_dir = "./album/$a_session_id/img/";
            $sam_dir = "./album/$a_session_id/imgs/";
            $ext1 = ".+\.png$|.+\.jpe?g$";
            $d = dir($img_dir);
              while ($ent = $d->read()) {
                if (eregi($ext1, $ent)) {
                   $files[] = $ent;
                }
              }
            $d->close();
            // ソート
            if($files){
                rsort($files);
                $pos1 = strrpos($files[0],"_");
                $pos2 = strrpos($files[0],".");
                $id = substr($files[0],$pos1+1,$pos2-$pos1-1);
                intval($id);
                $id++;
                if($id<10){$id="0$id";}
                if($id<100){$id="0$id";}
                if($id<1000){$id="0$id";}
                if($id>=10000){die("error: buffer over flow");}
            }else{
                $id = "0";
            }

            $newname = $name."_".$id.".$ext";
			$newname_r = $name."_".$id;
            move_uploaded_file("$upfile4", "$img_dir$upfile_name4");
            rename("$img_dir$upfile_name4", "$img_dir$newname");
            $sam_size = getimagesize("$img_dir$newname");
            if ($sam_size[0] > $W || $sam_size[1] > $H) {
               thumb_create("$img_dir$newname",$W,$H,"$sam_dir");
            }else{
               copy("$img_dir$newname","$sam_img$newname");
            }

            //comment
            $comment4 = $_POST["comment4"];
            if($comment4){
                $comment4 = htmlspecialchars($comment4);
               	$sql = "REPLACE INTO `PHP_ALBUM_COMMENT` VALUES ('', '$uid', '$img_dir$newname_r', '$comment4')";
                $result = $db->query($sql);
                if (DB::isError($result)) {
                   trigger_error($result->getMessage(), E_USER_ERROR);
                }
            }
		}
  
 		if($upfile_name5){
            $pos = strrpos($upfile_name5,".");	//拡張子取得
            $ext = substr($upfile_name5,$pos+1,strlen($upfile_name5)-$pos);
            $ext = strtolower($ext);//小文字化
            if(!in_array($ext, $arrowext)){
			    die("その拡張子ファイルはアップロードできません");
            }

            if($limitb < $upfile_size5){
                $nowsize = intval( $upfile_size5 /1024 );
                die("最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
            }
            // ディレクトリ一覧取得、ソート
            $img_dir = "./album/$a_session_id/img/";
            $sam_dir = "./album/$a_session_id/imgs/";
            $ext1 = ".+\.png$|.+\.jpe?g$";
            $d = dir($img_dir);
              while ($ent = $d->read()) {
                if (eregi($ext1, $ent)) {
                   $files[] = $ent;
                }
              }
            $d->close();
            // ソート
            if($files){
                rsort($files);
                $pos1 = strrpos($files[0],"_");
                $pos2 = strrpos($files[0],".");
                $id = substr($files[0],$pos1+1,$pos2-$pos1-1);
                intval($id);
                $id++;
                if($id<10){$id="0$id";}
                if($id<100){$id="0$id";}
                if($id<1000){$id="0$id";}
                if($id>=10000){die("error: buffer over flow");}
            }else{
                $id = "0";
            }

            $newname = $name."_".$id.".$ext";
			$newname_r = $name."_".$id;
            move_uploaded_file("$upfile5", "$img_dir$upfile_name5");
            rename("$img_dir$upfile_name5", "$img_dir$newname");
            $sam_size = getimagesize("$img_dir$newname");
            if ($sam_size[0] > $W || $sam_size[1] > $H) {
               thumb_create("$img_dir$newname",$W,$H,"$sam_dir");
            }else{
               copy("$img_dir$newname","$sam_img$newname");
            }

            //comment
            $comment5 = $_POST["comment5"];
            if($comment5){
                $comment5 = htmlspecialchars($comment5);
               	$sql = "REPLACE INTO `PHP_ALBUM_COMMENT` VALUES ('', '$uid', '$img_dir$newname_r', '$comment5')";
                $result = $db->query($sql);
                if (DB::isError($result)) {
                   trigger_error($result->getMessage(), E_USER_ERROR);
                }
            }
		}

?>
