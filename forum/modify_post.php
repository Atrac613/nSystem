<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "../ffxi_common_lib.php";
require_once "forum_lib.php";
require_once "forum_upd_lib.php";
$db = db_init();

//page chk
page_mode();

/*
//各種設定
$arrowext = array('txt','lzh','zip','jpg','jpeg','png');
$arrowimgext = array('jpg','jpeg','png');
$W = 172;
$H = 129;
$image_type = 1;
$limitk	= 3072;
$limitb = $limitk * 1024;*/

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

$p = $_GET["p"];
if(!$p){
    $p = $_POST["p"];
}

$p = intval($p);

//auth
$sql = "select * from `FORUM_POSTS` WHERE `post_id` = '$p'";
$result = $db->query($sql);
$chk = $result->numRows();
if($chk){
    //
	if($uid){
		$sql = "select * from `FORUM_USERS` WHERE `post_id` = '$p'";
		$result = $db->query($sql);
		$topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$post_uid = $topic_rows["uid"];
		if($post_uid){
			if( $post_uid != $uid && !find_root($uid)){
				die("Access Denied");
			}
		}else{
			$fr_pass = $_POST["fr_pass"];
			$sql = "select * from `FORUM_USERS` WHERE `post_id` = '$p' AND `post_userpass` = password('$fr_pass')";
			$result = $db->query($sql);
			$chk = $result->numRows();
			if(!$chk){
				die("Access Denied");
			}
		}
	    
	}else{
		$fr_pass = $_POST["fr_pass"];
		$sql = "select * from `FORUM_USERS` WHERE `post_id` = '$p' AND `post_userpass` = password('$fr_pass')";
		$result = $db->query($sql);
		$chk = $result->numRows();
		if(!$chk){
			die("Access Denied");
		}
	}
}else{
	die("Access Denied");
}


$sid= $_POST["sid"];
if(!$sid){
    mt_srand(microtime()*100000);
	$sid = md5(uniqid(mt_rand(),1));
}

//dump forum
$sql = "select * from `FORUM_POSTS` WHERE `post_id` = '$p'";
$result = $db->query($sql);
$chk = $result->numRows();
if($chk){
    if($_POST["modify"] == "Modify"){
        $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $thread_id = $topic_rows["thread_id"];
        $topic_id = $topic_rows["topic_id"];
        $forum_id = $topic_rows["forum_id"];
		
 		//ロック
		$c = get_c($forum_id);
		if(!rock_status("c",$c)){
			sub_msg("5","forum/forum.php","このカテゴリーはロック状態です","リロードします。");
		}else{
			if(!rock_status("f",$forum_id)){
				sub_msg("5","forum/forum.php","このフォーラムはロック状態です","リロードします。");
			}
		}
		
		if($thread_id != 1){
			if(!rock_status("t",$topic_id)){
				die("topic rock");
			}
		}
		
        $post_time = $topic_rows["post_time"];
        $post_edit_count = $topic_rows["post_edit_count"];
        $post_edit_count = $post_edit_count + 1;
        
	    //insert preview
	    $sid = $_POST["sid"];
	    $f_name = $_POST["name"];
	    $f_mail = $_POST["mail"];
	    $f_url = $_POST["url"];
	    $face = $_POST["face"];
	    $f_subject = $_POST["subject"];
	    $post_text = $_POST["body"];
	    //$f_file = $_POST["file"];
	    $post_userpass = $_POST["pass"];
	    $auth_mode = $_POST["level"];
	    $enable_spcode = $_POST["option1"];
		$p_status = $_POST["status"];
	    $f_time = time();
	    //var_dump($f_body);

		if($uid){
			if($t_pass == $post_userpass){
				$post_userpass = "";
			}
		}

	    //$f_name = htmlspecialchars($f_name);
	    //$post_mail = htmlspecialchars($f_mail);
	    //$post_url = htmlspecialchars($f_url);
	    //$f_subject = htmlspecialchars($f_subject);
	    //$f_option1 = htmlspecialchars($f_option1);
	    
        //正規化
       // $f_msgbody0 = str_replace("\r\n", "\r", $post_text);
        //$f_msgbody0 = str_replace("\r", "\n", $f_msgbody0);
        //$f_msgbody0 = htmlspecialchars($f_msgbody0);
       // $f_msgbody0 = str_replace("\n", "<br>", $f_msgbody0);
    
	    $f_ip = $_SERVER['REMOTE_ADDR'];
	    $date = gmdate("Y/m/d (D) H:i:s", $post_time+9*60*60);
    
    
    
	    //メッセージレベルチェック
	    //f_levelは0はノーマル1はゲスト拒否、２指定
	    if($auth_mode == "2"){
	        $sql = "select * from `FORUM_TMPDATA_AUTH` where `sid` = '$sid'";
	        $result = $db->query($sql);
	        $chk = $result->numRows();
	        if($chk){
	            $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	            $tmp_auth_uid = $tmp_rows["uid"];
	            if($tmp_auth_uid){
	                $msg_level = "1";
	            }
	        }
	    }else{
	        $msg_level = "1";
	    }
	
	    //tmp_authに入ってるか？
		//入っている場合は登録。それ以外は無視でauthフォームの表示
	    if($msg_level){
	        //写真
		    //アップロードファイル
		 	$upfile_size=$_FILES["upfile"]["size"];
			$upfile_name=$_FILES["upfile"]["name"];
			$upfile=$_FILES["upfile"]["tmp_name"];

		    if($upfile_name){
			    $pos = strrpos($upfile_name,".");	//拡張子取得
			    $ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
			    $ext = strtolower($ext);//小文字化
			    if(!in_array($ext, $arrowext)){
					//die("その拡張子ファイルはアップロードできません");
					sub_msg("","","アップロードエラー","その拡張子ファイルはアップロードできません");
			    }
	
			    if($limitb < $upfile_size){
			        $nowsize = intval( $upfile_size /1024 );
			        //die("最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
					sub_msg("","","アップロードエラー","最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
			    }
	
			    $sid_name = $_POST["sid"];
			    $newname = "$sid_name.$ext";
			    move_uploaded_file($upfile, "dat/tmp/$newname");

			    if(in_array($ext, $arrowimgext)){
			        $sam_size = getimagesize("dat/tmp/$newname");
			        $sam_name = "$sid_name"."_s.$ext";
			        if ($sam_size[0] > $W || $sam_size[1] > $H) {
			            thumb_create("dat/tmp/$newname",$W,$H,"dat/tmp/");
			        }else{
			            copy("dat/tmp/$newname","dat/tmp/$sam_name");
			        }
			    }

		    }else{
			    //read dat
			    $sid = $_POST["sid"];
			    $sql = "select * from `FORUM_TMPDATA` where `sid` = '$sid'";
			    $result = $db->query($sql);
			    $chk = $result->numRows();
			    if($chk){
			        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			        $upfile_name = $tmp_rows["file"];
			    }else{
				    $sql = "select * from `FORUM_USERS` where `post_id` = '$p'";
				    $result = $db->query($sql);
				    $chk = $result->numRows();
				    if($chk){
				        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
				        $upfile_name = $tmp_rows["file"];
				    }
			    }
		    }

		    //del dat
		    $del_dat = $_POST["del"];
		    if($del_dat){
		        $upfile_name = "";
		    }
			

		    if($upfile_name){

		        //拡張子取得
		        $pos = strrpos($upfile_name,".");	//拡張子取得
		        $ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		        $ext = strtolower($ext);//小文字化
        
		        //イメージネーム作成
		        $sid = $_POST["sid"];
		        $img_name = "$sid.$ext";
	        
		        //コピー開始
		        copy("dat/tmp/$img_name","dat/$img_name");
        
		        //縮小画像コピー
		        $sam_size = getimagesize("dat/$img_name");
		        if ($sam_size[0] > $W || $sam_size[1] > $H) {
		           if($image_type == "1"){
		                 $sam_name=$sid."_s.jpg";
		           }else{
		                 $sam_name=$sid."_s.png";
		           }
		           copy("dat/tmp/$sam_name","dat/$sam_name");
		        }

		    }
			
		
	        //FORUM_POSTSへの追加
	        $sql = "REPLACE INTO `FORUM_POSTS` VALUES ('$p', '$thread_id', '$topic_id', '$forum_id', '$post_time','$enable_spcode','$f_time','$post_edit_count','$auth_mode','$sid')";
	        $result = $db->query($sql);
	        if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
	        }
	
	        //FORUM_POST_TXTへの追加
	        $sql = "REPLACE INTO `FORUM_POSTS_TXT` VALUES ('$p', '$f_subject', '$post_text')";
	        $result = $db->query($sql);
	        if (DB::isError($result)) {
	        	trigger_error($result->getMessage(), E_USER_ERROR);
	        }
			
			//thread_idが1だった場合はFORUM_TOPICも更新
			if($thread_id == 1){
	            $sql = "select * from `FORUM_TOPIC` where `topic_id` = '$topic_id'";
	            $result = $db->query($sql);
	            $chk = $result->numRows();
	            if($chk){
	                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	                $t_forum_id = $tmp_rows["forum_id"];
					$t_topic_master = $tmp_rows["topic_master"];
					$t_topic_replies = $tmp_rows["topic_replies"];
					$t_auth_edit = $tmp_rows["auth_edit"];
					$t_auth_mode = $tmp_rows["auth_mode"];
					$t_make_time = $tmp_rows["make_time"];
					$t_last_time = $tmp_rows["last_time"];
					$t_sid = $tmp_rows["sid"];
	            }else{
	                trigger_error("unknown topic_id = $topic_id", E_USER_ERROR);
	            }
			
	            $sql = "REPLACE INTO `FORUM_TOPIC` VALUES ('$topic_id', '$t_forum_id', '$f_subject', '$t_topic_master','$t_topic_replies','$t_auth_edit','$p_status','$auth_mode','$t_make_time','$t_last_time','$t_sid')";
	            //var_dump($sql);
				$result = $db->query($sql);
	            if (DB::isError($result)) {
	            	trigger_error($result->getMessage(), E_USER_ERROR);
	            }
			}
			
            
	        //認証モードだった場合そのデータを追加
	        if($auth_mode == "2"){
				//topic_idの取得
	            $sql = "select * from `FORUM_TMPDATA_AUTH` where `sid` = '$sid'";
	            $result = $db->query($sql);
	            $chk = $result->numRows();
	            if($chk){
					$tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	                $f_uid = $tmp_rows["uid"];
                    	
	                $sql = "select * from `FORUM_AUTH_ACCESS` where `auth_id` = '$p' AND `auth_area` = '2'";
	                $result = $db->query($sql);
	                $chk = $result->numRows();
	                if($chk){
						$a_id = $tmp_rows["id"];
	                    $sql = "REPLACE INTO `FORUM_AUTH_ACCESS` VALUES ('$a_id', '$p', '2','2','$f_uid')";
	                    $result = $db->query($sql);
	                    if (DB::isError($result)) {
							trigger_error($result->getMessage(), E_USER_ERROR);
	                    }
	                }else{
						$sql = "REPLACE INTO `FORUM_AUTH_ACCESS` VALUES ('', '$p', '2','2','$f_uid')";
	                    $result = $db->query($sql);
						if (DB::isError($result)) {
							trigger_error($result->getMessage(), E_USER_ERROR);
	                    }
	                }
	            }else{
	                trigger_error("unknown sid = $sid", E_USER_ERROR);
	            }
	        }

	        //FORUM_USERSへの追加
	        $sql = "REPLACE INTO `FORUM_USERS` VALUES ('$p', '$uid','$f_name', password('$post_userpass'), '$post_mail', '$post_url', '$upfile_name', '$face', '$f_ip')";
	        $result = $db->query($sql);
	        if (DB::isError($result)) {
	        	trigger_error($result->getMessage(), E_USER_ERROR);
	        }
            
	        //FORUMSの最終更新時刻更新
	        $sql = "select * from `FORUM_FORUMS` where `forum_id` = '$forum_id'";
	        $result = $db->query($sql);
	        $chk = $result->numRows();
	        if($chk){
	            $sql ="UPDATE `FORUM_FORUMS` SET `last_time` = '$f_time' WHERE `forum_id` = '$forum_id'";
	            $result = $db->query($sql);
	            if (DB::isError($result)) {
	        	    trigger_error($result->getMessage(), E_USER_ERROR);
	            }
	
	        }else{
	            trigger_error("unknown forum_id =$forum_id", E_USER_ERROR);
	        }
            
	        //TOPICの最終更新時刻更新
	        $sql = "select * from `FORUM_TOPIC` where `topic_id` = '$topic_id'";
	        $result = $db->query($sql);
	        $chk = $result->numRows();
	        if($chk){
	            $sql ="UPDATE `FORUM_TOPIC` SET `last_time` = '$f_time' WHERE `topic_id` = '$topic_id'";
	            $result = $db->query($sql);
	            if (DB::isError($result)) {
	        	    trigger_error($result->getMessage(), E_USER_ERROR);
	            }
	        }else{
	            trigger_error("unknown topic_id =$topic_id", E_USER_ERROR);
	        }
	        //die("modify_ok!!");
			sub_msg("3","forum/viewtopic.php?t=$topic_id","登録しました!!","リロードします");
	     }//tmp_authに入ってるか？終了
		     
    }elseif($_POST["del"] == "Del"){
        $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $topic_id = $topic_rows["topic_id"];
        $forum_id = $topic_rows["forum_id"];
        $thread_id = $topic_rows["thread_id"];
        $local_count = 0;
        $time = time();
		
 		//ロック
		$c = get_c($forum_id);
		if(!rock_status("c",$c)){
			sub_msg("5","forum/forum.php","このカテゴリーはロック状態です","リロードします。");
		}else{
			if(!rock_status("f",$forum_id)){
				sub_msg("5","forum/forum.php","このフォーラムはロック状態です","リロードします。");
			}
		}
		
		if($thread_id != 1){
			if(!rock_status("t",$topic_id)){
				sub_msg("5","forum/forum.php","このトピックはロック状態です","リロードします。");
			}
		}
        
        if($thread_id == "1"){
            //トピック削除
            $sql = "delete from `FORUM_TOPIC` where `topic_id` = '$topic_id'";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            //$local_count += 1;

            //ポスト削除
            $sql = "select * from `FORUM_POSTS` WHERE `topic_id` = '$topic_id'";
            $posts_result = $db->query($sql);
            $chk_post = $posts_result->numRows();
            var_dump($chk_post);
            var_dump($sql);
            if($chk_post){
				while( $topic_rows = $posts_result->fetchRow(DB_FETCHMODE_ASSOC) ){
	                $post_id = $topic_rows["post_id"];
	            
	                $sql = "delete from `FORUM_POSTS` where `post_id` = '$p'";
	                $result = $db->query($sql);
	                if (DB::isError($result)) {
	                    trigger_error($result->getMessage(), E_USER_ERROR);
	                }
            
	                $sql = "delete from `FORUM_POSTS_TXT` where `post_id` = '$p'";
	                $result = $db->query($sql);
	                if (DB::isError($result)) {
	                    trigger_error($result->getMessage(), E_USER_ERROR);
	                }
            
	                $sql = "delete from `FORUM_USERS` where `post_id` = '$p'";
	                $result = $db->query($sql);
	                if (DB::isError($result)) {
	                    trigger_error($result->getMessage(), E_USER_ERROR);
	                }
				}
            }else{
                trigger_error("unknown forum_id =$forum_id", E_USER_ERROR);
            }

            //FORUMSのforum_topics及びforum_postsのダウンカウント
            $sql = "select * from `FORUM_FORUMS` where `forum_id` = '$forum_id'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $forum_topics = $tmp_rows["forum_topics"];
                $forum_posts = $tmp_rows["forum_posts"];
                $forum_topics = $forum_topics - 1;
                $forum_posts = $forum_posts - $chk_post;

                $sql ="UPDATE `FORUM_FORUMS` SET `forum_topics` = '$forum_topics' , `forum_posts` = '$forum_posts' , `last_time` = '$time' WHERE `forum_id` = '$forum_id'";
                $result = $db->query($sql);
                if (DB::isError($result)) {
            	    trigger_error($result->getMessage(), E_USER_ERROR);
                }

            }else{
                trigger_error("unknown forum_id =$forum_id", E_USER_ERROR);
            }
            
        
        }else{
            $sql = "delete from `FORUM_POSTS` where `post_id` = '$p'";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }

            $sql = "delete from `FORUM_POSTS_TXT` where `post_id` = '$p'";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }

            $sql = "delete from `FORUM_USERS` where `post_id` = '$p'";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }

            //FORUMSのforum_postsのダウンカウント
            $sql = "select * from `FORUM_FORUMS` where `forum_id` = '$forum_id'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $forum_posts = $tmp_rows["forum_posts"];
                $forum_posts = $forum_posts - 1;

                $sql ="UPDATE `FORUM_FORUMS` SET `forum_posts` = '$forum_posts' , `last_time` = '$time' WHERE `forum_id` = '$forum_id'";
                $result = $db->query($sql);
                if (DB::isError($result)) {
            	    trigger_error($result->getMessage(), E_USER_ERROR);
                }

            }else{
                trigger_error("unknown forum_id =$forum_id", E_USER_ERROR);
            }
            
            //TOPICのforum_postsのダウンカウント
            $sql = "select * from `FORUM_TOPIC` where `topic_id` = '$topic_id'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $topic_replies = $tmp_rows["topic_replies"];
                $topic_replies = $topic_replies - 1;

                $sql ="UPDATE `FORUM_TOPIC` SET `topic_replies` = '$topic_replies' , `last_time` = '$time' WHERE `topic_id` = '$topic_id'";
                $result = $db->query($sql);
                if (DB::isError($result)) {
            	    trigger_error($result->getMessage(), E_USER_ERROR);
                }

            }else{
                trigger_error("unknown topic_id =$topic_id", E_USER_ERROR);
            }
        }
        //die("del_ok!!");
		sub_msg("3","forum/viewforum.php?f=$forum_id","削除しました!!","リロードします。");
		
    }elseif($_POST["preview"] == "Preview"){
        //写真
	    //アップロードファイル
		$upfile_size=$_FILES["upfile"]["size"];
		$upfile_name=$_FILES["upfile"]["name"];
		$upfile=$_FILES["upfile"]["tmp_name"];

	    if($upfile_name){

		    $pos = strrpos($upfile_name,".");	//拡張子取得
		    $ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
		    $ext = strtolower($ext);//小文字化
		    if(!in_array($ext, $arrowext)){
				//die("その拡張子ファイルはアップロードできません");
				sub_msg("","","アップロードエラー","その拡張子ファイルはアップロードできません");
		    }

		    if($limitb < $upfile_size){
		        $nowsize = intval( $upfile_size /1024 );
		        //die("最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
				sub_msg("","","アップロードエラー","最大アップ容量は... $limitk kb です。現在のファイルサイズは... $nowsize kb です");
		    }

		    $sid_name = $_POST["sid"];
		    $newname = "$sid_name.$ext";
		    move_uploaded_file($upfile, "dat/tmp/$newname");
			//die($newname);
		    if(in_array($ext, $arrowimgext)){
		        $sam_size = getimagesize("dat/tmp/$newname");
		        $sam_name = "$sid_name"."_s.$ext";
				
		        if ($sam_size[0] > $W || $sam_size[1] > $H) {
		            thumb_create("dat/tmp/$newname",$W,$H,"dat/tmp/");
					//die("the");
		        }else{
		            copy("dat/tmp/$newname","dat/tmp/$sam_name");
		        }
		    }

	    }else{
		    //read dat
		    $sid = $_POST["sid"];
		    $sql = "select * from `FORUM_TMPDATA` where `sid` = '$sid'";
		    $result = $db->query($sql);
		    $chk = $result->numRows();
		    if($chk){
		        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		        $upfile_name = $tmp_rows["file"];
		    }else{
			    $sql = "select * from `FORUM_USERS` where `post_id` = '$p'";
			    $result = $db->query($sql);
			    $chk = $result->numRows();
			    if($chk){
			        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			        $upfile_name = $tmp_rows["file"];
			    }
		    }
	    }

	    //del dat
	    $del_dat = $_POST["del"];
	    if($del_dat){
	        $upfile_name = "";
	    }
    
	    //insert preview
	    $sid = $_POST["sid"];
	    $f_name = $_POST["name"];
	    $f_mail = $_POST["mail"];
	    $f_url = $_POST["url"];
	    $f_face = $_POST["face"];
	    $f_subject = $_POST["subject"];
	    $f_body = $_POST["body"];
	    //$f_file = $_POST["file"];
	    $f_pass = $_POST["pass"];
	    $f_level = $_POST["level"];
		$f_status = $_POST["status"];
	    $f_option1 = $_POST["option1"];
	    $f_time = time();

	    //$f_name = htmlspecialchars($f_name);
	    //$f_mail = htmlspecialchars($f_mail);
	    //$f_url = htmlspecialchars($f_url);
	    //$f_subject = htmlspecialchars($f_subject);
	    //$f_option1 = htmlspecialchars($f_option1);

	    $sql = "REPLACE INTO `FORUM_TMPDATA` VALUES ('$sid','$f_mode','$f_val', '$f_name', '$f_mail', '$f_url', '$f_face', '$f_subject', '$f_body', '$upfile_name', '$f_pass', '$f_level', '$f_option1', '$f_status','$f_time')";
		$result = $db->query($sql);
		if (DB::isError($result)) {
			trigger_error($result->getMessage(), E_USER_ERROR);
		}
    
    
	}elseif($_POST["selected"] == "Select"){
		$sql = "select * from `USER_DATA`";
		$result = $db->query($sql);
		$chk = $result->numRows();
		if($chk){
            if($_POST["selected_guest"]){
                $str_uid = "guest,";
            }
			while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
			$f_usr_name = $user_rows["name"];
            $f_chk_usrs = $_POST["selected_$f_usr_name"];
                if($f_chk_usrs){
                    $f_usr_uid = $user_rows["uid"];
                    $str_uid .= "$f_usr_uid".",";
                }
            }
	        //if($str_uid){
	        $sid = $_POST["sid"];
	        $sql = "REPLACE INTO `FORUM_TMPDATA_AUTH` VALUES ('$sid', '$str_uid')";
            //var_dump($sql);
            $result = $db->query($sql);
			if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
            }
            if($str_uid){
                $msg_level = "1";
            }
            //}
        }
    }else{
        $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $thread_id = $topic_rows["thread_id"];
        $topic_id = $topic_rows["topic_id"];
        $forum_id = $topic_rows["forum_id"];
		
 		//ロック
		$c = get_c($forum_id);
		if(!rock_status("c",$c)){
			sub_msg("5","forum/forum.php","このカテゴリーはロック状態です","リロードします。");
		}else{
			if(!rock_status("f",$forum_id)){
				sub_msg("5","forum/forum.php","このフォーラムはロック状態です","リロードします。");
			}
		}
		
		if($thread_id != 1){
			if(!rock_status("t",$topic_id)){
				//die("topic rock");
				sub_msg("5","forum/forum.php","このトピックはロック状態です","リロードします。");
			}
		}
		
        $post_time = $topic_rows["post_time"];
        $enable_spcode = $topic_rows["enable_spcode"];
        $post_edit_time = $topic_rows["post_edit_time"];
        $post_edit_count = $topic_rows["post_edit_count"];
        $auth_mode = $topic_rows["auth_mode"];
        //$sid = $topic_rows["sid"];
        
        $sql = "select * from `FORUM_POSTS_TXT` WHERE `post_id` = '$p'";
        $result = $db->query($sql);
        $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $post_subject = $topic_rows["post_subject"];
        $post_text = $topic_rows["post_text"];
        
        $sql = "select * from `FORUM_USERS` WHERE `post_id` = '$p'";
        $result = $db->query($sql);
        $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $post_username = $topic_rows["post_username"];
        $post_userpass = $topic_rows["post_userpass"];
        $post_mail = $topic_rows["mail"];
        $post_url = $topic_rows["url"];
        $up_file = $topic_rows["file"];
        $face = $topic_rows["face"];
        $ip = $topic_rows["ip"];
		
		if($thread_id == 1){
        	$sql = "select * from `FORUM_TOPIC` WHERE `topic_id` = '$topic_id'";
        	$result = $db->query($sql);
        	$topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        	$f_status = $topic_rows["topic_status"];
		}


        //正規化
        $f_msgbody0 = str_replace("\r\n", "\r", $post_text);
        $f_msgbody0 = str_replace("\r", "\n", $f_msgbody0);
        $f_msgbody0 = htmlspecialchars($f_msgbody0);
        $f_msgbody0 = str_replace("\n", "<br>", $f_msgbody0);
        $f_name = htmlspecialchars($post_username);
        $f_subject = htmlspecialchars($post_subject);

        //date
        $date = gmdate("Y/m/d (D) H:i:s", $post_time+9*60*60);
        
        if($auth_mode == "2"){
             $sql = "select * from `FORUM_AUTH_ACCESS` WHERE `auth_id` = '$p' AND `auth_area` = '2'";

             $result = $db->query($sql);
             $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
             $auth_usr = $topic_rows["auth_usr"];

             $sql = "REPLACE INTO `FORUM_TMPDATA_AUTH` VALUES ('$sid', '$auth_usr')";
             //var_dump($sql);
	         $result = $db->query($sql);
             if (DB::isError($result)) {
             	trigger_error($result->getMessage(), E_USER_ERROR);
             }
        }
        
        
    }
}else{
    die("unknown topic $t");
}

if($sid){
    // read tmp
    $sql = "select * from `FORUM_TMPDATA` where `sid` = '$sid'";
    $result = $db->query($sql);
    $chk = $result->numRows();
    if($chk){

        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $up_file = $tmp_rows["file"];
        $f_name = $tmp_rows["name"];
        $post_mail = $tmp_rows["mail"];
        $post_url = $tmp_rows["url"];
        $face = $tmp_rows["face"];
        $f_subject = $tmp_rows["subject"];
        $post_text = $tmp_rows["body"];
        $post_userpass = $tmp_rows["pass"];
        $auth_mode = $tmp_rows["level"];
        $enable_spcode = $tmp_rows["option1"];
		$f_status = $tmp_rows["status"];
        $post_time = $tmp_rows["time"];
        //$f_name = $_POST["name"];

        //正規化
        $f_msgbody0 = str_replace("\r\n", "\r", $post_text);
        $f_msgbody0 = str_replace("\r", "\n", $f_msgbody0);
		if(!$enable_spcode){
	        $f_msgbody0 = htmlspecialchars($f_msgbody0);
        }
		$f_msgbody0 = str_replace("\n", "<br>", $f_msgbody0);
		$f_msgbody0 = make_clickable($f_msgbody0);
		
        $f_name = htmlspecialchars($f_name);
        $f_subject = htmlspecialchars($f_subject);

        //date
        $date = gmdate("Y/m/d (D) H:i:s", $post_time+9*60*60);
        
        $sql = "select * from `FORUM_POSTS` where `post_id` = '$p'";
        $result = $db->query($sql);
        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $thread_id = $tmp_rows["thread_id"];
        
    }
}else{
    trigger_error("unknown sid= $sid", E_USER_ERROR);
}

//status
   if($f_status == "1"){
      $chk_status1 = "selected";
   }else{
      $chk_status0 = "selected";
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
      <TD width="8" class="color3" background="../img/<?php echo "$STYLE[img_left]"; ?>" rowspan="2"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
      <TD width="750" valign="top">
	  <?php echo "$STYLE[topimage]"; ?>
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="row_title" height="34"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
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
            <TD class="color2"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
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
                  <BR>フォーラム -トピック編集-</TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <?php
                  
                  echo '
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH colspan="3">プレビュー</TH>
                      </TR>
                      <TR>
                        <TD width="100" align="center" class="row1"><S>削除</S> <S>編集</S></TD>
                        <TD width="540" class="row1">['.$thread_id.'] '.$f_subject.'</TD>
                        <TD width="90" align="center" class="row1"><S>引用返信</S></TD>
                      </TR>
                      <TR>
                        <TD rowspan="2" class="row1" valign="top" align="center"><BR><img src=../face/'.$face.'.gif width=30 height=30 border=0><br>'.$f_name.'</TD>
                        <TD colspan="2" class="row1" valign="top">'.$f_msgbody0.'<BR><BR>';

                        if($up_file){
                           echo "添付ファイル($up_file)";
                       }

                        echo '</TD>
                      </TR>
                      <TR>
                        <TD align="right" colspan="2" class="row1">['.$date.']</TD>
                      </TR>
                      <TR>
                        <TD align="center" class="row1">TOP</TD>
                        <TD colspan="2" class="row1">PROFILE PM ';

                        if($post_mail){
                            echo '<A href="mailto:'.$post_mail.'">MAIL</A> ';
                        }
                        if($post_url){
                            echo '<A href="'.$post_url.'">URL</A>';
                        }

                        echo '</TD>
                      </TR>
                    </TBODY>
                  </TABLE><BR>';
                  
                  echo '
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR>
                        <TH class="caution" colspan="2">レポート</TH>
                      </TR>
                      <TR>
                        <TD class="color2" colspan="2">';
                        
                        if(!$post_time || !$post_edit_time || !$post_edit_count || !$ip){
                            //
                        $sql = "select * from `FORUM_POSTS` WHERE `post_id` = '$p'";
                        $result = $db->query($sql);
                        $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                        $post_time = $topic_rows["post_time"];
                        $post_edit_time = $topic_rows["post_edit_time"];
                        $post_edit_count = $topic_rows["post_edit_count"];
                        
                        $sql = "select * from `FORUM_USERS` WHERE `post_id` = '$p'";
                        $result = $db->query($sql);
                        $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                        $ip = $topic_rows["ip"];
                        }
                        $date1 = gmdate("Y/m/d (D) H:i:s", $post_time+9*60*60);
                        $date2 = gmdate("Y/m/d (D) H:i:s", $post_edit_time+9*60*60);
                        
                        echo "<B>投稿時間：$date1<BR>";
                        echo "最終編集時間：$date2<BR>";
                        echo "編集回数：$post_edit_count<BR>";
                        echo "利用端末：$ip <BR></B>";

                        echo '</TD>
                      </TR>
                    </TBODY>
                  </TABLE><BR>';

                   
                  if($auth_mode == "2" && $sid){

                  echo '
                  <FORM method=post enctype=multipart/form-data action="modify_post.php">
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH colspan="2">拒否ユーザー指定</TH>
                      </TR>
                                              ';
					$sql = "select * from `USER_DATA`";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
                        $sql = "select * from `FORUM_TMPDATA_AUTH` where `sid` = '$sid'";
                        $result_tmp = $db->query($sql);
                        $chk = $result_tmp->numRows();
                        if($chk){
                            $tmp_rows = $result_tmp->fetchRow(DB_FETCHMODE_ASSOC);
                            $tmp_uid = $tmp_rows["uid"];
                            //var_dump($tmp_uid);
                            $tmp_uid = rtrim($tmp_uid,",");
                            $tmp_uid_result = split(",",$tmp_uid);
                            //var_dump($tmp_uid_result);
                            if($tmp_uid){
                                $msg_level = "1";
                            }else{
                                $msg_level = "0";
                            }
                        }
                        echo "<TR><TD align='center' width='25' class='row1'><input type='checkbox' name='selected_guest' value='1' checked></TD>
                        <TD width='532' class='row1'>ゲスト</TD></TR>";
						while( $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$f_usr_name = $user_rows["name"];
                            $f_usr_uid = $user_rows["uid"];
                            if($chk){
                            if(in_array("$f_usr_uid",$tmp_uid_result)){
                                $chk_auth = "checked";
                            }else{
                                $chk_auth = "";
                            }
                            }

							echo "<TR><TD align='center' width='25' class='row1'><input type='checkbox' name='selected_".$f_usr_name,"' value='1' ".$chk_auth."></TD>
                        <TD width='532' class='row1'>$f_usr_name</TD></TR>";
						}
					}else{
						echo "<TR><TD>ユーザーは登録されていません。</TD></TR>";
					}

                      echo '
                      <TR>
                        <TD class="color2" colspan="2">';
                        echo '<INPUT type="hidden" name="sid" value="'.$sid.'">
                        <INPUT type="hidden" name="p" value="'.$p.'">
                        <INPUT type="submit" name="selected" value="Select"></TD>
                      </TR>
                      </FORM>
                    </TBODY>
                  </TABLE>';
                  }
                  
                  if($thread_id == "1" || !$f_name || !$f_subject || !$post_text || $msg_level){
                  //var_dump($msg_level);
                  echo '
                  <BR>
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR>
                        <TH class="caution" colspan="2">レポート</TH>
                      </TR>
                      <TR>
                        <TD class="color2" colspan="2">';
                        
                        if($thread_id == "1"){
                            echo "・このメッセージを削除するとこのトピックに関連する全てのメッセージが削除されます。<BR><BR>";
                        }
                        if(!$f_name){
                            echo "・名前が記入されていないか短すぎます。<BR><BR>";
                        }
                        if(!$f_subject){
                            echo "・タイトルが記入されていないか短すぎます。<BR><BR>";
                        }
                        if(!$post_text){
                            echo "・メッセージが記入されていないか短すぎます。<BR><BR>";
                        }
                        if($msg_level == "1"){
                            echo "・このメッセージには読み書き拒否モードが指定されています。";
                        }else{
                              if($msg_level == "0"){
                                  echo "・このメッセージには読み書き拒否モードが指定されていますが、ユーザーが指定されていません。<BR><BR>";
                              }
                        }
                        
                        echo '</TD>
                      </TR>
                    </TBODY>
                  </TABLE>';
                  }
                  
                   
                   ?>
                   
                  <FORM method=post enctype=multipart/form-data action='modify_post.php'>
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH colspan="2">編集・削除</TH>
                      </TR>
                      <TR>
                        <TD>name</TD>
                        <TD>
                        <INPUT size="25" type="text" name="name" value="<?php echo "$f_name"; ?>">
                        </TD>
                      </TR>
                      <TR>
                        <TD>mail</TD>
                        <TD>
                        <INPUT size="40" type="text" name="mail" value="<?php echo "$post_mail"; ?>">
                        </TD>
                      </TR>
                      <TR>
                        <TD>url</TD>
                        <TD>
                        <INPUT size="40" type="text" name="url" value="<?php echo "$post_url"; ?>">
                        </TD>
                      </TR>
                      <TR>
                        <TD>face</TD>
                        <?php
                         echo "<TD><SELECT name=face>";
for($i=0;$i<count($facename);$i++){
$i_tmp2 = $i.'b';
	if($i == 0){
		echo "<option value=$i>$facename[$i]</option>\n";
		echo "<optgroup label=$racelist[0]>";
	}else{
		if($face == $i_tmp2){
			echo "<option value=$i>$facename[$i] A</option>\n";
			echo "<option selected value=".$i."b>$facename[$i] B</option>\n";
		}elseif($face == $i){
			echo "<option selected value=$i>$facename[$i] A</option>\n";
			echo "<option value=".$i."b>$facename[$i] B</option>\n";
		}else{
		echo "<option value=$i>$facename[$i] A</option>\n";
		echo "<option value=".$i."b>$facename[$i] B</option>\n";
		}
		if($i==8 || $i==16 || $i==24 || $i==32 || $i==40 || $i==48 || $i==56){
		$i_tmp = $i /8;
		echo "<optgroup label=$racelist[$i_tmp]>";
		}
	}
}
                         echo "</select></TD>";
                        ?>
                      </TR>
                      <TR>
                        <TD>subject</TD>
                        <TD>
                        <INPUT size="40" type="text" name="subject" value="<?php echo "$f_subject"; ?>">
                        </TD>
                      </TR>
                      <TR>
                        <TD>msg</TD>
                        <TD>
                        <TEXTAREA rows="15" cols="65" name="body"><?php echo "$post_text"; ?></TEXTAREA>
                        </TD>
                      </TR>
                      <TR>
                        <TD>file</TD>
                        <TD>
                        <?php
                          if($up_file){
                              echo $up_file.'&nbsp;<INPUT type="hidden" name="tmp_file" value="1"><input type="checkbox" name="del" value="1" />削除';
                          }else{
                              echo '<INPUT type="file" name="upfile">';
                          }
                        ?>
                        </TD>
                      </TR>
                      <TR>
                        <TD>pass</TD>
                        <TD>
                        <INPUT size="40" type="text" name="pass" value="<?php echo "$post_userpass"; ?>">
                        </TD>
                      </TR>
                      <TR>
                        <TD>レベル</TD>
                        <TD><SELECT name=level>
                        <option value=0 <?php if($auth_mode == "0"){ echo 'selected="selected"'; }?>>ノーマル</option>
                        <option value=1 <?php if($auth_mode == "1"){ echo 'selected="selected"'; }?>>ゲスト拒否</option>
                        <option value=2 <?php if($auth_mode == "2"){ echo 'selected="selected"'; }?>>指定</option>
                        </select></TD>
                      </TR>
					  
					  <?php
					  if($thread_id == 1){
					  echo '
                      <TR>
                        <TD>ロック</TD>
                        <TD><SELECT name=status>
                        <option value=0 '.$chk_status0.'>ロック解除</option>
                        <option value=1 '.$chk_status1.'>ロック</option>
                        </select></TD>
                      </TR>';
					  }
					  ?>
					  
                      <TR>
                        <TD>option</TD>
                        <TD><input type="checkbox" name="option1" value="1" <?php if($enable_spcode == "1"){ echo 'checked'; }?>>HTMLコード有効
                        </TD>
                      </TR>
                      <TR>
                        <TD colspan="2" align="center">
                        <INPUT type="hidden" name="sid" value="<?php echo "$sid"; ?>">
                        <INPUT type="hidden" name="p" value="<?php echo "$p"; ?>">
                        <INPUT type="submit" name="preview" value="Preview">&nbsp;&nbsp;&nbsp;<INPUT type="submit" name="modify" value="Modify">&nbsp;&nbsp;&nbsp;<INPUT type="submit" name="del" value="Del">
                        </TD>
                      </TR>
                      </FORM>
                    </TBODY>
                  </TABLE>
                   
                   <HR><A href='javascript:history.back()'>戻る</A><BR><BR><BR>
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
      <TD width="25" class="color3" background="../img/<?php echo "$STYLE[img_right]"; ?>" rowspan="2"><IMG src="..//img/spacer.gif" width="25" height="1"></TD>
      <TD class="color3" rowspan="2"></TD>
    </TR>
    <TR>
      <TD height="34">
      <TABLE cellpadding="0" cellspacing="0">
        <TBODY>
          <TR>
            <TD class="color2" height="34"><IMG src="..//img/spacer.gif" width="8" height="1"></TD>
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