<?php
//ライブラリ呼び出し
require_once "../db_setting.php";
require_once "../php_inc.php";
require_once "../ffxi_common_lib.php";
require_once "forum_lib.php";
require_once "../list/ml_common.php";
require_once "forum_upd_lib.php";
//require_once "../function/graphic_lib.php";
$db = db_init();

//page chk
page_mode();

/*
        //各種設定
        $arrowext = array('txt','lzh','zip','jpg','jpeg','png');
        $arrowimgext = array('jpg','jpeg','png');
	    $W = 172;
	    $H = 129;
	    $image_type = "1";
	    $limitk	=10240;
        $limitb = $limitk * 1024;
		*/

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

function send_to_maindb($sid){
    global $db,$uid,$t_pass;

	//一度登録したsidは無効
    $sql = "select * from `FORUM_POSTS` where `sid` = '$sid'";
    $result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
    $chk = $result->numRows();
    if($chk){
		die("sid error");
	}

    $sql = "select * from `FORUM_TMPDATA` where `sid` = '$sid'";
    $result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
    $chk = $result->numRows();
    if($chk){
	
		//session start
		if(!forum_session(0,$sid)){
			die("session error");
		}
		//var_dump(forum_session(0,$sid));
	
        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $f_file = $tmp_rows["file"];
        //data set...
        $f_name = $tmp_rows["name"];
        $f_mode = $tmp_rows["f_mode"];
        $f_val = $tmp_rows["f_val"];
        $f_mail = $tmp_rows["mail"];
        $f_url = $tmp_rows["url"];
        $f_face = $tmp_rows["face"];
        $f_subject = $tmp_rows["subject"];
        $f_msgbody = $tmp_rows["body"];
        $f_pass = $tmp_rows["pass"];
        $f_level = $tmp_rows["level"];
        $f_option1 = $tmp_rows["option1"];
		$f_status = $tmp_rows["status"];
        $f_time = $tmp_rows["time"];
        
        $f_ip = $_SERVER['REMOTE_ADDR'];
        
        $f_name = addslashes($f_name);
        $f_subject = addslashes($f_subject);
        $f_msgbody = addslashes($f_msgbody);
        $f_pass = addslashes($f_pass);
		
		if(!$f_subject){
			$f_subject ="無題";
		}
		
		if(!$f_msgbody){
			sub_msg("","","エラー","本文が未記入です");
		}
		
		if($uid){
			if($t_pass == $f_pass){
				$f_pass = "";
			}
		}else{
			$f_level = "";
			$f_option1 = "";
		}
        
        //forum_topic
        if($f_mode == "0"){
            //新規トピックの登録
            
            //FORUM_TOPICへの追加
            $sql = "REPLACE INTO `FORUM_TOPIC` VALUES ('', '$f_val', '$f_subject', '$f_name','0','0','$f_status','$f_level','$f_time','$f_time','$sid')";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            
            //追加したデータのtopic_id取得
            $sql = "select * from `FORUM_TOPIC` where `sid` = '$sid'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $topic_id = $tmp_rows["topic_id"];
            }else{
                trigger_error("unknown sid = $sid", E_USER_ERROR);
            }
            
            //FORUM_POSTSへの追加
            $sql = "REPLACE INTO `FORUM_POSTS` VALUES ('', '1', '$topic_id', '$f_val', '$f_time','$f_option1','$f_time','0','$f_level','$sid')";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            
            //追加したデータのpost_id取得
            $sql = "select * from `FORUM_POSTS` where `sid` = '$sid'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $post_id = $tmp_rows["post_id"];
            }else{
                trigger_error("unknown sid = $sid", E_USER_ERROR);
            }
            
            //FORUM_POSTS_TXTへの追加
            $sql = "REPLACE INTO `FORUM_POSTS_TXT` VALUES ('$post_id', '$f_subject', '$f_msgbody')";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            
            //認証モードだった場合そのデータを追加
            if($f_level == "2"){
                //topic_idの取得
                $sql = "select * from `FORUM_TMPDATA_AUTH` where `sid` = '$sid'";
                $result = $db->query($sql);
                $chk = $result->numRows();
                if($chk){
                    $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                    $f_uid = $tmp_rows["uid"];
                    
                    $sql = "REPLACE INTO `FORUM_AUTH_ACCESS` VALUES ('', '$post_id', '2','2','$f_uid')";
                    $result = $db->query($sql);
                    if (DB::isError($result)) {
            	        trigger_error($result->getMessage(), E_USER_ERROR);
                    }
                }else{
                      trigger_error('unknown sid = $sid', E_USER_ERROR);
                }
            }
            
            //FORUM_USERSへの追加
            $sql = "REPLACE INTO `FORUM_USERS` VALUES ('$post_id', '$uid','$f_name', password('$f_pass'), '$f_mail', '$f_url', '$f_file', '$f_face', '$f_ip')";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            
            //ルートテーブルとフォーラムルートのlast_time及びforum_topics及びforum_postsの更新
            $sql = "select * from `FORUM_FORUMS` where `forum_id` = '$f_val'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $forum_topics = $tmp_rows["forum_topics"];
                $forum_posts = $tmp_rows["forum_posts"];
                $cat_id = $tmp_rows["cat_id"];
                $forum_topics = $forum_topics + 1;
                $forum_posts = $forum_posts + 1;

                //update forum_topics & last_time &fourum_posts
                $sql ="UPDATE `FORUM_FORUMS` SET `forum_topics` = '$forum_topics' , `forum_posts` = '$forum_posts' , `last_time` = '$f_time' WHERE `forum_id` = '$f_val'";
				//var_dump($sql);
                $result = $db->query($sql);
                if (DB::isError($result)) {
            	    trigger_error($result->getMessage(), E_USER_ERROR);
                }

                //update last_time
                $sql ="UPDATE `FORUM_CATEGORIES` SET `last_time` = '$f_time' WHERE `cat_id` = '$cat_id'";
                $result = $db->query($sql);
                if (DB::isError($result)) {
            	    trigger_error($result->getMessage(), E_USER_ERROR);
                }
            }else{
                trigger_error('unknown forum_id = $f_val', E_USER_ERROR);
            }
			
			//ニュースの追加
			add_news('3',"$topic_id",'');
			
			$mail_head ="フォーラムで新規トピック!!";
			$body .= "タイトル : $f_subject\n\n";
			$body .= "$f_msgbody\n\n";
			
			//mail
			$sql = "select * from `PHP_USR_STYLE` WHERE `mail_forum` = '1'";
			$result = $db->query($sql);
			$chk = $result->numRows();
			//var_dump($chk);
			if($chk){
				while( $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					$mail_sendfor = $tmp_rows["mail_sendfor"];
					$mail_uid = $tmp_rows["uid"];
					
					$sql = "select * from `USER_DATA` WHERE `uid` = '$mail_uid'";
					$result2 = $db->query($sql);
					$chk = $result2->numRows();
					if($chk){
						$tmp_rows2 = $result2->fetchRow(DB_FETCHMODE_ASSOC);
						$user_name = $tmp_rows2["name"];
						wb_sendmail(2,$user_name,$f_name,$mail_head,$body);
					
					}
				}
			}
			
			sub_msg("3","forum/viewtopic.php?t=$topic_id","登録しました!!","リロードします。");
			
			
        }elseif($f_mode == "1"){
            //返信データの追加

            //forum_idの取得
            $forum_id = get_f($f_val);

            //thread_idの取得、及び+1
            $sql = "select `topic_id`,max(thread_id) from `FORUM_POSTS` where `topic_id` = '$f_val' group by `topic_id`";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $mt_id = $tmp_rows["max(thread_id)"];
                $mt_id = $mt_id + 1;
            }else{
                trigger_error('unknown f_val = $f_val', E_USER_ERROR);
            }
            
            //var_dump($sql);
            //var_dump($mt_id);
            //FORUM_POSTSへの追加
            $sql = "REPLACE INTO `FORUM_POSTS` VALUES ('', '$mt_id', '$f_val', '$forum_id', '$f_time','$f_option1','$f_time','0','$f_level','$sid')";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            
            //post_idの取得
            $sql = "select * from `FORUM_POSTS` where `sid` = '$sid'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $post_id = $tmp_rows["post_id"];
            }else{
                trigger_error('unknown sid = $sid', E_USER_ERROR);
            }

            //FORUM_POST_TXTへの追加
            $sql = "REPLACE INTO `FORUM_POSTS_TXT` VALUES ('$post_id', '$f_subject', '$f_msgbody')";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            
            //認証モードだった場合そのデータを追加
            if($f_level == "2"){
                //get topic_id
                $sql = "select * from `FORUM_TMPDATA_AUTH` where `sid` = '$sid'";
                $result = $db->query($sql);
                $chk = $result->numRows();
                if($chk){
                    $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                    $f_uid = $tmp_rows["uid"];

                    $sql = "REPLACE INTO `FORUM_AUTH_ACCESS` VALUES ('', '$post_id', '2','2','$f_uid')";
                    $result = $db->query($sql);
                    if (DB::isError($result)) {
            	        trigger_error($result->getMessage(), E_USER_ERROR);
                    }
                }else{
                      trigger_error("unknown sid = $sid", E_USER_ERROR);
                }
            }

            //ユーザーデータの追加
            $sql = "REPLACE INTO `FORUM_USERS` VALUES ('$post_id', '$uid','$f_name', password('$f_pass'), '$f_mail', '$f_url', '$f_file', '$f_face', '$f_ip')";
            $result = $db->query($sql);
            if (DB::isError($result)) {
            	trigger_error($result->getMessage(), E_USER_ERROR);
            }
            
            //トピックルートのlast_time及びtopic_repliesの更新
            $sql = "select * from `FORUM_TOPIC` where `topic_id` = '$f_val'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                $topic_replies = $tmp_rows["topic_replies"];
                $topic_replies = $topic_replies + 1;
                
                $sql ="UPDATE `FORUM_TOPIC` SET `topic_replies` = '$topic_replies' , `last_time` = '$f_time' WHERE `topic_id` = '$f_val'";
                $result = $db->query($sql);
                if (DB::isError($result)) {
            	    trigger_error($result->getMessage(), E_USER_ERROR);
                }
                
            }else{
                trigger_error("unknown f_val =$f_val", E_USER_ERROR);
            }
            
            //カテゴリールートとフォーラムルートのlast_time及びforum_topics及びforum_postsの更新
            $sql = "select * from `FORUM_FORUMS` where `forum_id` = '$forum_id'";
            $result = $db->query($sql);
            $chk = $result->numRows();
            if($chk){
                $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                //$forum_topics = $tmp_rows["forum_topics"];
                $forum_posts = $tmp_rows["forum_posts"];
                $cat_id = $tmp_rows["cat_id"];
                //$forum_topics = $forum_topics + 1;
                $forum_posts = $forum_posts + 1;

                //update forum_topics & last_time
                //$sql ="UPDATE `FORUM_FORUMS` SET `forum_topics` = '$forum_topics' , `forum_posts` = '$forum_posts' , `last_time` = '$f_time' WHERE `forum_id` = '$forum_id'";
                $sql ="UPDATE `FORUM_FORUMS` SET `forum_posts` = '$forum_posts' , `last_time` = '$f_time' WHERE `forum_id` = '$forum_id'";
                $result = $db->query($sql);
                if (DB::isError($result)) {
            	    trigger_error($result->getMessage(), E_USER_ERROR);
                }

                //update last_time
                $sql ="UPDATE `FORUM_CATEGORIES` SET `last_time` = '$f_time' WHERE `cat_id` = '$cat_id'";
                $result = $db->query($sql);
                if (DB::isError($result)) {
            	    trigger_error($result->getMessage(), E_USER_ERROR);
                }
            }else{
                trigger_error("unknown forum_id = $forum_id or f_val =$f_val", E_USER_ERROR);
            }
            //die("eplay_stop");
			//ニュースの追加
			add_news('4',"$post_id",'');
			
			//mail
			$mail_head ="フォーラムで返信がありました!!";
			$body .= "タイトル : $f_subject\n\n";
			$body .= "$f_msgbody\n\n";
			
			$sql = "select * from `PHP_USR_STYLE` WHERE `mail_forum` = '1'";
			$result = $db->query($sql);
			$chk = $result->numRows();
			//var_dump($chk);
			if($chk){
				while( $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					$mail_sendfor = $tmp_rows["mail_sendfor"];
					$mail_uid = $tmp_rows["uid"];
					
					$sql = "select * from `USER_DATA` WHERE `uid` = '$mail_uid'";
					$result2 = $db->query($sql);
					$chk = $result2->numRows();
					if($chk){
						$tmp_rows2 = $result2->fetchRow(DB_FETCHMODE_ASSOC);
						$user_name = $tmp_rows2["name"];
						wb_sendmail(2,$user_name,$f_name,$mail_head,$body);
					
					}
				}
			}
			
			sub_msg("3","forum/viewtopic.php?t=$f_val","登録しました!!","リロードします。");
			
        }else{
            trigger_error("unknown f_mode = $f_mode", E_USER_ERROR);
        }
        //die("reg_ok");
        //forum_posts
        //00
		

    }else{
        trigger_error("DB Error:sid=$sid failed", E_USER_ERROR);
    }
}

//global val


$f_mode= $_GET["mode"];
//var_dump($f_mode);
if($f_mode == "new_topic"){
    $f_mode = 0;
    $f= intval($_GET["f"]);
	
	//ロック
 		//ロック
		$c = get_c($f);
		if(!rock_status("c",$c)){
			sub_msg("5","forum/forum.php","このカテゴリーはロック状態です","リロードします。");
		}else{
			if(!rock_status("f",$f)){
				sub_msg("5","forum/forum.php","このフォーラムはロック状態です","リロードします。");
			}
		}
}elseif($f_mode == "reply"){
    $f_mode = 1;
    $t= intval($_GET["t"]);
    //sqlで$f
    $f = get_f($t);
	
 		//ロック
	$c = get_c($f);
	if(!rock_status("c",$c)){
		sub_msg("5","forum/forum.php","このカテゴリーはロック状態です","リロードします。");
	}else{
		if(!rock_status("f",$f)){
			sub_msg("5","forum/forum.php","このフォーラムはロック状態です","リロードします。");
		}else{
			if(!rock_status("t",$t)){
				//die("topic rock");
				sub_msg("5","forum/forum.php","このトピックはロック状態です","リロードします。");
			}
		}
	}
	
}elseif($f_mode == "quote"){
    $f_mode = 1;
    $p= intval($_GET["p"]);
    
    $sql = "select * from `FORUM_POSTS` where `post_id` = '$p'";
    $result = $db->query($sql);
    $chk = $result->numRows();
    if($chk){
        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);

        $t = $tmp_rows["topic_id"];
        $f = $tmp_rows["forum_id"];
		
 		//ロック
		$c = get_c($f);
		if(!rock_status("c",$c)){
			sub_msg("5","forum/forum.php","このカテゴリーはロック状態です","リロードします。");
		}else{
			if(!rock_status("f",$f)){
				sub_msg("5","forum/forum.php","このフォーラムはロック状態です","リロードします。");
			}else{
				if(!rock_status("t",$t)){
					//die("topic rock");
					sub_msg("5","forum/forum.php","このトピックはロック状態です","リロードします。");
				}
			}
		}

        $sql = "select * from `FORUM_POSTS_TXT` where `post_id` = '$p'";
        $result = $db->query($sql);
        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
		
        //認証関係スタート
        if(auth_read($p)){
		
        $f_subject = $tmp_rows["post_subject"];
        $f_msgbody = $tmp_rows["post_text"];
        //var_dump($tmp_rows);
        
        //正規化
        $f_msgbody0 = str_replace("\r\n", "\r", $f_msgbody);
        $f_msgbody0 = str_replace("\r", "\n", $f_msgbody0);
        
        $f_msgbody1 = str_replace("\n", "\n&gt;", $f_msgbody0);
        $f_msgbody1 = "&gt;".$f_msgbody1;

        //var_dump($f_msgbody1);
        
        $f_msgbody0 = htmlspecialchars($f_msgbody0);
        $f_msgbody0 = str_replace("\n", "<br>", $f_msgbody0);
        
        $f_subject = htmlspecialchars($f_subject);
        $f_subject1 = "Re:".$f_subject;
		
		}
        
    }else{
        trigger_error("unknown p = $p", E_USER_ERROR);
    }
    //sqlで$f
    //$f = get_f($t);
}elseif($_POST["submit"] == "Submit"){
    $f_mode = $_POST["f_mode"];
    //f_modeは0が新規1が返信
    if($f_mode == "1"){
        $t= intval($_POST["t"]);
		$f = get_f($t);
        $f_val= intval($_POST["t"]);
        if($t == ""){
            //die("No topic_id");
			sub_msg("5","forum/forum.php","そのトピックは存在しません","リロードします。");
		}
        if($f == ""){
            //die("No forum_id");
			sub_msg("5","forum/forum.php","そのフォーラムは存在しません","リロードします。");
        }
        //$f_val= get_f($t);
        //sqlで$f
		//ロック
		$c = get_c($f);
		if(!rock_status("c",$c)){
			sub_msg("5","forum/forum.php","このカテゴリーはロック状態です","リロードします。");
		}else{
			if(!rock_status("f",$f)){
				sub_msg("5","forum/forum.php","このフォーラムはロック状態です","リロードします。");
			}else{
				if(!rock_status("t",$t)){
					//die("topic rock");
					sub_msg("5","forum/forum.php","このトピックはロック状態です","リロードします。");
				}
			}
		}
		
    }else{
        $f= intval($_POST["f"]);
        $f_val= intval($_POST["f"]);
        if($f == ""){
            //die("No forum_id");
			sub_msg("5","forum/forum.php","そのフォーラムは存在しません","リロードします。");
        }
		//ロック
		//var_dump(rock_status("f",$f));
		if(!rock_status("f",$f)){
			//die("forum rock");
			sub_msg("5","forum/forum.php","そのフォーラムは存在しません","リロードします。");
		}
	}

    //一度tmpへ
    //アップロードファイル
    $tmp_file = $_POST["tmp_file"];
    if(!$tmp_file){
        $upfile_size=$_FILES["upfile"]["size"];
	    $upfile_name=$_FILES["upfile"]["name"];
	    $upfile=$_FILES["upfile"]["tmp_name"];

        if($upfile_name){

        $pos = strrpos($upfile_name,".");	//拡張子取得
        $ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
        $ext = strtolower($ext);//小文字化
        //die($ext);
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
        //die($sid_name);
        move_uploaded_file($upfile, "dat/tmp/$newname");

        if(in_array($ext, $arrowimgext)){
            $sam_size = getimagesize("dat/tmp/$newname");

            if ($sam_size[0] > $W || $sam_size[1] > $H) {
               thumb_create("dat/tmp/$newname",$W,$H,"dat/tmp/");
            }else{
               $sam_name = "$sid_name"."_s.$ext";
			   //die($sam_name);
               copy("dat/tmp/$newname","dat/tmp/$sam_name");
            }
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
    $f_option1 = $_POST["option1"];
    $f_time = time();
    //var_dump($f_body);

    //$f_name = htmlspecialchars($f_name);
    //$f_mail = htmlspecialchars($f_mail);
    //$f_url = htmlspecialchars($f_url);
    //$f_subject = htmlspecialchars($f_subject);
    //$f_option1 = htmlspecialchars($f_option1);

    $sql = "REPLACE INTO `FORUM_TMPDATA` VALUES ('$sid','$f_mode','$f_val', '$f_name', '$f_mail', '$f_url', '$f_face', '$f_subject', '$f_body', '$upfile_name', '$f_pass', '$f_level', '$f_option1','$f_status', '$f_time')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
 
    //var_dump("tmp file ok!!");
    
    //メッセージレベルチェック
    //f_levelは0はノーマル1はゲスト拒否、２指定
    if($f_level == "2"){
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
    }
    
    //メッセージレベルチェック2

    if($f_level == "2"){
        if($msg_level){
            send_to_maindb($sid);
        }
    }else{
        send_to_maindb($sid);
    }

}elseif($_POST["preview"] == "Preview"){
    $f_mode = $_POST["f_mode"];
    if($f_mode == "1"){
        $t= intval($_POST["t"]);
        $f_val= intval($_POST["t"]);
        if($t == ""){
            //die("No topic_id");
			sub_msg("5","forum/forum.php","そのトピックは存在しません","リロードします。");
        }

        //sqlで$f
        $f = get_f($t);
        //$f_val = $f;
    }else{
        $f= intval($_POST["f"]);
        $f_val= intval($_POST["f"]);
        if($f == ""){
            //die("No forum_id");
			sub_msg("5","forum/forum.php","そのフォーラムは存在しません","リロードします。");
        }
    }

    //アップロードファイル
 	$upfile_size=$_FILES["upfile"]["size"];
	$upfile_name=$_FILES["upfile"]["name"];
	$upfile=$_FILES["upfile"]["tmp_name"];

    if($upfile_name){

    //各種設定
	/*
    $arrowext = array('txt','lzh','zip','jpg','jpeg','png');
    $arrowimgext = array('jpg','jpeg','png');
	$W = 172;
	$H = 129;
	$image_type = 1;
	$limitk	= 3072;
    $limitb = $limitk * 1024;
	*/

    $pos = strrpos($upfile_name,".");	//拡張子取得
    $ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
    $ext = strtolower($ext);//小文字化
	//var_dump($arrowext);
	//die($ext);
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
		//die($sam_name);
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
    $f_option1 = $_POST["option1"];
    $f_time = time();
    //var_dump($f_body);
    
    //$f_name = htmlspecialchars($f_name);
    //$f_mail = htmlspecialchars($f_mail);
    //$f_url = htmlspecialchars($f_url);
    //$f_subject = htmlspecialchars($f_subject);
    //$f_option1 = htmlspecialchars($f_option1);
	
	
    
    $sql = "REPLACE INTO `FORUM_TMPDATA` VALUES ('$sid','$f_mode','$f_val', '$f_name', '$f_mail', '$f_url', '$f_face', '$f_subject', '$f_body', '$upfile_name', '$f_pass', '$f_level', '$f_option1','$f_status','$f_time')";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
 
    //メッセージレベルチェック
    if($f_level == "2"){
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
    }
    
}elseif($_POST["selected"] == "Select"){
    $f_mode = $_POST["f_mode"];
    if($f_mode == "1"){
        $t= intval($_POST["t"]);
        //sqlで$f
        $f = get_f($t);
    }else{
        $f= intval($_POST["f"]);
    }
    //var_dump($t);
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
    die("No post mode specified");
}

//dump forum
$sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$f'";
//var_dump($sql);
$result = $db->query($sql);
$chk = $result->numRows();
if($chk){
    $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $cat_id = $user_rows["cat_id"];
    $forum_name = $user_rows["forum_name"];
	$forum_master = $user_rows["forum_master"];
	$make_time = $user_rows["make_time"];
	$make_date = strftime('%D' , $make_time);
	
	
	//認証関係
	if(!auth_read_cf("c",$cat_id)){
		//die("error auth");
		sub_msg("3","forum/forum.php","Error A-001","リロードします。");
	}else{	
		if(!auth_read_cf("f",$f)){
			//die("error auth");
			sub_msg("3","forum/forum.php","Error A-002","リロードします。");
		}
	}

    $sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$cat_id'";
    $result = $db->query($sql);
    $chk = $result->numRows();
    if($chk){
        $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $cat_name = $user_rows["cat_title"];
    }
}else{
    $forum_name = "none";
	$forum_master = "none";
	$make_time = "none";
}

//set forum_title
if($f_mode == 0){
    $f_mode_str = "新規トピック作成";
}else{
    $f_mode_str = "返信";
}

//make sid
$sid= $_POST["sid"];
if($sid){
    // read tmp
    $sql = "select * from `FORUM_TMPDATA` where `sid` = '$sid'";
    $result = $db->query($sql);
    $chk = $result->numRows();
    if($chk){
        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        $upfile_name = $tmp_rows["file"];
                              //data set...
        $f_name = $tmp_rows["name"];
        $f_mail = $tmp_rows["mail"];
        $f_url = $tmp_rows["url"];
        $f_face = $tmp_rows["face"];
        $f_subject = $tmp_rows["subject"];
        $f_msgbody = $tmp_rows["body"];
        $f_pass = $tmp_rows["pass"];
        $f_level = $tmp_rows["level"];
        $f_option1 = $tmp_rows["option1"];
        $f_time = $tmp_rows["time"];
        $f_status = $tmp_rows["status"];

        //正規化
        $f_msgbody0 = str_replace("\r\n", "\r", $f_msgbody);
        $f_msgbody0 = str_replace("\r", "\n", $f_msgbody0);
		if(!$f_option1){
	        $f_msgbody0 = htmlspecialchars($f_msgbody0);
        }
		$f_msgbody0 = str_replace("\n", "<br>", $f_msgbody0);
		$f_msgbody0 = make_clickable($f_msgbody0);
        $f_name = htmlspecialchars($f_name);

        $f_subject0 = htmlspecialchars($f_subject);

        //date
        $date = gmdate("Y/m/d (D) H:i", $f_time+9*60*60);
        
        //options
        if($f_level == "2"){
            $chk_level2 = "selected";
        }elseif($f_level == "1"){
            $chk_level1 = "selected";
        }else{
            $chk_level0 = "selected";
        }
		
        //status
        if($f_status == "1"){
            $chk_status1 = "selected";
        }else{
            $chk_status0 = "selected";
        }
        
        if($f_option1){
            $chk_option1 = "checked";
        }
    }
}else{
    mt_srand(microtime()*100000);
	$sid = md5(uniqid(mt_rand(),1));
	
	if($uid){
		$f_name = $c_name;
		
		$sql = "select * from `USER_STA` where `uid` = '$uid'";
	    $result = $db->query($sql);
	    $chk = $result->numRows();
	    if($chk){
	        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	        $f_face = $tmp_rows["face"];
		}
		
		$sql = "select * from `USER_PLOF` where `uid` = '$uid'";
	    $result = $db->query($sql);
	    $chk = $result->numRows();
	    if($chk){
	        $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	        $f_mail = $tmp_rows["mail"];
			$f_url = $tmp_rows["url"];
		}
	}
}

if($_GET["mode"] == "quote"){
    $f_msgbody = $f_msgbody1;
    $f_subject = $f_subject1;
    //var_dump($f);
    //var_dump($t);
    //var_dump($f_msgbody);
    //var_dump($f_msgbody1);
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
                  <TD colspan="2" width="570"></TD>
                <TD rowspan="5" align="right" width="10" valign="top"><BR>
                  </TD>
                </TR>
                <TR>
                  <TD colspan="2" width="422">
                  <BR>フォーラム -<?php echo "$f_mode_str"; ?>-
                  </TD>
                </TR>
                <TR>
                  <TD align="left" colspan="2" valign="top"><BR>
                  <?php
                    echo "$cat_name >> $forum_name >> $f_mode_str";
                  ?>
                  <?php
                  if($_POST["preview"] == "Preview" || $f_level == "2" && $sid){

                  echo '
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH colspan="3" align="center">プレビュー</TH>
                      </TR>
                      <TR>
                        <TD width="100" align="center" class="row1"><S>削除</S> <S>編集</S></TD>
                        <TD width="540" class="row1">[??] '.$f_subject0.'</TD>
                        <TD width="90" align="center" class="row1"><S>引用返信</S></TD>
                      </TR>
                      <TR>
                        <TD rowspan="2" class="row1" valign="top" align="center"><BR><img src=../face/'.$f_face.'.gif width=30 height=30 border=0><br>'.$f_name.'</TD>
                        <TD colspan="2" class="row1" valign="top">'.$f_msgbody0.'<BR><BR>';

                        if($upfile_name){
                           echo "添付ファイル($upfile_name)";
                       }

                        echo '</TD>
                      </TR>
                      <TR>
                        <TD align="right" colspan="2" class="row1">['.$date.']</TD>
                      </TR>
                      <TR>
                        <TD align="center" class="row1">TOP</TD>
                        <TD colspan="2" class="row1">PROFILE PM MAIL URL</TD>
                      </TR>
                    </TBODY>
                  </TABLE><BR>';
                  }
                  
                  if($f_level == "2" && $sid){

                  echo '
                  <FORM method=post enctype=multipart/form-data action="posting.php">
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH align="center" colspan="2">拒否ユーザー指定</TH>
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
                        <TD class="color2" colspan="2"><INPUT type="hidden" name="f_mode" value="'.$f_mode.'">';
                        if($f_mode == "0"){
                            echo '<INPUT type="hidden" name="f" value="'.$f.'">\n';
                        }else{
                            echo '<INPUT type="hidden" name="t" value="'.$t.'">\n';
                        }
                        echo '<INPUT type="hidden" name="sid" value="'.$sid.'"><INPUT type="submit" name="selected" value="Select"></TD>
                      </TR>
                      </FORM>
                    </TBODY>
                  </TABLE>';
                  }
               if($_POST["sid"]){
                  if(!$f_name || !$f_subject || !$f_msgbody || $msg_level){
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
                        if(!$f_name){
                            echo "・名前が記入されていないか短すぎます。<BR><BR>";
                        }
                        if(!$f_subject){
                            echo "・タイトルが記入されていないか短すぎます。<BR><BR>";
                        }
                        if(!$f_msgbody){
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
                }
                  
                  ?>
                  
                  <FORM method=post enctype=multipart/form-data action='posting.php'>
                  <TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                      <TR class="table_title">
                        <TH colspan="2"><?php echo "$f_mode_str"; ?></TH>
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
                        <INPUT size="40" type="text" name="mail" value="<?php echo "$f_mail"; ?>">
                        </TD>
                      </TR>
                      <TR>
                        <TD>url</TD>
                        <TD>
                        <INPUT size="40" type="text" name="url" value="<?php echo "$f_url"; ?>">
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
		if($f_face == $i_tmp2){
			echo "<option value=$i>$facename[$i] A</option>\n";
			echo "<option selected value=".$i."b>$facename[$i] B</option>\n";
		}elseif($f_face == $i){
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
                        <TEXTAREA rows="15" cols="65" name="body"><?php echo "$f_msgbody"; ?></TEXTAREA>
                        </TD>
                      </TR>
                      <TR>
                        <TD>file</TD>
                        <TD>
                        <?php
                          if($upfile_name){
                              echo $upfile_name.'&nbsp;<INPUT type="hidden" name="tmp_file" value="1"><input type="checkbox" name="del" value="1" />削除';
                          }else{
                              echo '<INPUT type="file" name="upfile">';
                          }
                        ?>
                        </TD>
                      </TR>
                      <TR>
                        <TD>pass</TD>
                        <TD>
                        <INPUT size="40" type="password" name="pass" value="<?php echo "$f_pass"; ?>">
                        </TD>
                      </TR>
					  
					  <?php
                      if($uid){
					  echo '
					  <TR>
                        <TD>レベル</TD>
                        <TD><SELECT name=level>
                        <option value=0 '.$chk_level0.'>ノーマル</option>
                        <option value=1 '.$chk_level1.'>ゲスト拒否</option>
                        <option value=2 '.$chk_level2.'>指定</option>
                        </select></TD>
                      </TR>';
					  }
					  
					  
					  if($f_mode == 0){
					  echo '
                      <TR>
                        <TD>ロック</TD>
                        <TD><SELECT name=status>
                        <option value=0 '.$chk_status0.'>ロック解除</option>
                        <option value=1 '.$chk_status1.'>ロック</option>
                        </select></TD>
                      </TR>';
					  }

                      if($uid){
					  echo '
                      <TR>
                        <TD>option</TD>
                        <TD><input type="checkbox" name="option1" value="1" '.$chk_option1.'>HTMLコード有効
                        </TD>
                      </TR>';
					  }
					  ?>
                      <TR>
                        <TD colspan="2" align="center">
                        <INPUT type="hidden" name="f_mode" value="<?php echo "$f_mode"; ?>">
                        <?php
                        if($f_mode == "0"){
                            echo '<INPUT type="hidden" name="f" value="'.$f.'">';
                        }else{
                            echo '<INPUT type="hidden" name="t" value="'.$t.'">';
                        }
                        ?>
                        <INPUT type="hidden" name="sid" value="<?php echo "$sid"; ?>">
                        <INPUT type="submit" name="preview" value="Preview">&nbsp;<INPUT type="submit" name="submit" value="Submit">
                        </TD>
                      </TR>
                      </FORM>
                    </TBODY>
                  </TABLE>
                  <?php
                  if($f_mode == "1" && $t){
                  echo'<BR><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
                    <TR class="table_title"><TH colspan="3">Preview</TH></TR>';
					$sql = "select * from `FORUM_TOPIC` WHERE `topic_id` = '$t'";
					$result = $db->query($sql);
					$chk = $result->numRows();
					if($chk){
                             echo'
                      <TR class="table_title">
                        <TH width="150" height="26">投稿者</TH>
                        <TH colspan="2">メッセージ</TH>
                      </TR>';

                            $sql = "select * from `FORUM_POSTS` WHERE `topic_id` = '$t' order by `post_id`";
                            $forum_result = $db->query($sql);
                            $chk = $forum_result->numRows();
                            if($chk){
                                $row_c = 1;
                                while( $forum_rows = $forum_result->fetchRow(DB_FETCHMODE_ASSOC) ){
                                    //td_cor
                                    if($row_c == 3){
                                        $row_c = 1;
                                    }

                                    $post_id = $forum_rows["post_id"];
									
                                    //認証関係スタート
                                    //var_dump(auth_read($post_id));
                                    if(auth_read($post_id)){
									
                                    $thread_id = $forum_rows["thread_id"];
                                    $topic_id = $forum_rows["topic_id"];
                                    $post_time = $forum_rows["post_time"];
                                    $enable_spcode = $forum_rows["enable_spcode"];
                                    $post_edit_time = $forum_rows["post_edit_time"];
                                    $post_edit_count = $forum_rows["post_edit_count"];
                                    $auth_mode = $forum_rows["auth_mode"];
                                    $date = gmdate("y/m/d (D) H:i", $post_edit_time+9*60*60);

                                    $sql = "select * from `FORUM_POSTS_TXT` WHERE `post_id` = '$post_id'";
                                    //var_dump($sql);
                                    $post_result = $db->query($sql);
                                    $chk = $post_result->numRows();

                                    if($chk){
                                        $post_rows = $post_result->fetchRow(DB_FETCHMODE_ASSOC);
                                        $post_subject = $post_rows["post_subject"];
                                        $post_text = $post_rows["post_text"];
                                        //$post_text = str_replace("\n", "<br>", $post_text);
										
								        $f_msgbody0 = str_replace("\r\n", "\r", $post_text);
								        $f_msgbody0 = str_replace("\r", "\n", $f_msgbody0);
								        $f_msgbody0 = htmlspecialchars($f_msgbody0);
								        $f_msgbody0 = str_replace("\n", "<br>", $f_msgbody0);
										
										$post_subject = htmlspecialchars($post_subject);
                                    }else{
                                        die("Fatal Errror $sql");
                                    }

                                    $sql = "select * from `FORUM_USERS` WHERE `post_id` = '$post_id'";
                                    $post_usr_result = $db->query($sql);
                                    $chk = $post_usr_result->numRows();

                                    if($chk){
                                        $post_rows = $post_usr_result->fetchRow(DB_FETCHMODE_ASSOC);
                                        $post_username = $post_rows["post_username"];
                                        $post_userpass = $post_rows["post_userpass"];
                                        $post_mail = $post_rows["mail"];
                                        $post_url = $post_rows["url"];
                                        $post_file = $post_rows["file"];
                                        $post_face = $post_rows["face"];
                                    }else{
                                        die("Fatal Errror $sql");
                                    }



                                echo '
                      <TR>
                        <TD width="100" align="center" class="row'.$row_c.'">';
                        if($c_name == $post_username){
                            echo '<S>削除</S> <S>編集</S>';
                        }
                        echo '</TD>
                        <TD width="540" class="row'.$row_c.'">['.$thread_id.'] '.$post_subject.'</TD>
                        <TD width="90" align="center" class="row'.$row_c.'"><A href="posting.php?mode=quote&p='.$post_id.'">引用返信</A></TD>
                      </TR>
                      <TR>
                        <TD rowspan="2" class="row'.$row_c.'" valign="top" align="center"><BR><img src=../face/'.$post_face.'.gif width=30 height=30 border=0><br>'.$post_username.'</TD>
                        <TD colspan="2" class="row'.$row_c.'" valign="top">'.$f_msgbody0.'<BR><BR>';

                        if($post_file){
                           echo "添付ファイル($post_file)";
                       }

                        echo '</TD>
                      </TR>
                      <TR>
                        <TD align="right" colspan="2" class="row'.$row_c.'">['.$date.']</TD>
                      </TR>
                      <TR>
                        <TD align="center" class="row'.$row_c.'">TOP</TD>
                        <TD colspan="2" class="row'.$row_c.'">';
                        if($c_name == $post_username){
                            echo 'PROFILE PM ';
                        }
                        if($post_mail){
                            echo '<A href="mailto:'.$post_mail.'">MAIL</A> ';
                        }
                        if($post_url){
                            echo '<A href="'.$post_url.'">URL</A>';
                        }
                        echo '
                        </TD>
                      </TR>
                      <TR>
                        <TD class="spaceRow" colspan="3" height="1"><IMG src="../img/spacer.gif" alt="" width="1" height="1"></TD>
                      </TR>
                      ';
                                $row_c = $row_c + 1;
                                     }//認証関係終了
                                }
					         }
                    }else{
                        echo '
                      <TR>
                        <TD colspan="2" background="img/cellpic1.gif" height="28">Information</TD>
                      </TR>
                      <TR>
                        <TD height="6">そのトピックは存在していません。</TD>
                      </TR>';
                    }
                    echo '
                    </TBODY>
                  </TABLE>';
                  }
                  ?>
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