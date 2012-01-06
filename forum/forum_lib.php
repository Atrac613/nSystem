<?php
//forum_lib.php

//$tから$fを引く
function get_f($t){
    global $db;

$sql = "select * from `FORUM_TOPIC` WHERE `topic_id` = '$t'";
$result = $db->query($sql);
$chk = $result->numRows();
     if($chk){
         $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
         $f= $user_rows["forum_id"];
         return $f;
     }else{
         //die("not found f");
		 return false;
     }
}

function get_c($f){
    global $db;

$sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$f'";
$result = $db->query($sql);
$chk = $result->numRows();
     if($chk){
         $user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
         $c= $user_rows["cat_id"];
         return $c;
     }else{
         //die("not found f");
		 return false;
     }
}

//写真の縮小
//grafic_lib.phpみたいな感じにしたほうが？
function thumb_create($src, $W, $H, $sam_dir){
	global $image_type;
	//die($sam_dir);
    //var_dump($src);
    //var_dump($sam_dir);
  // 画像の幅と高さとタイプを取得
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
		//sub_msg("","","Jpegデコードエラー","そのJpegファイルは正しくありません。");
	}
  
  // リサイズ
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
  // 出力画像（サムネイル）のイメージを作成
  // $im_out = ImageCreate($out_w, $out_h);
  $im_out = ImageCreateTrueColor($out_w, $out_h);
  // 元画像を縦横とも コピーします。
  ImageCopyResized($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  // サムネイル画像をブラウザに出力、保存
  $filename = substr($src, strrpos($src,"/")+1);
  $filename = substr($filename, 0, strrpos($filename,"."));
  $filename = $filename."_s";
  if($image_type == 1){
  	ImageJPEG($im_out, $sam_dir.$filename.".jpg");
  }else{
  	ImagePNG($im_out, $sam_dir.$filename.".png");
  }
  // 作成したイメージを破棄
  ImageDestroy($im_in);
  ImageDestroy($im_out);
}


//FORUM_TOPICとFORUM_FORUMSのアップカウント
//topic_replies及びforum_posts
//topic_repliesはそのトピックのFORUM_FORUMS・forum_postsの合計
//forum_postsはそのフォーラムの返信数
function posts_count_up($p){
    //FORUM_FORUMS,forum_postsは$fでFORUM_POSTSをサーチした結果POSTSですべてのFORUM_ID
    //FORUM_TOPIC,topic_repliesは$tでFORUM_POSTSをサーチした結果

}

//FORUM_TOPICとFORUM_FORUMSのダウンカウント
//topic_replies及びforum_posts
function posts_count_down($p){
    //
}

//FORUM_FORUMSのアップカウント
//フォーラムすべてのトピックの数しかもthread_id=1
//forum_topics
function topics_count_up($p){
    //$ｔでFORUM_POSTSをサーチ
}

//FORUM_FORUMSのアップカウント
//トピックの数
//forum_topics
function topics_count_down($p){
    //
}

//認証関係
function auth_read($p){
    global $db,$c_name,$uid;
	
	if(find_oracle($uid)){
		return true;
	}
	
    $sql = "select * from `FORUM_POSTS` WHERE `post_id` = '$p'";
    $result = $db->query($sql);
    $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $auth_mode = $topic_rows["auth_mode"];
	$thread_id = $topic_rows["thread_id"];
	$topic_id = $topic_rows["topic_id"];
    //var_dump($uid);
	
	if($thread_id != "1"){
		//thread_idが1以外の時は1のポストの認証モードを調べる。
    	$sql = "select * from `FORUM_POSTS` WHERE `thread_id` = '1' AND `topic_id` = '$topic_id'";
    	$result = $db->query($sql);
    	$topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
    	$root_auth_mode = $topic_rows["auth_mode"];
    	if($root_auth_mode != "0"){
        	if($root_auth_mode == "1"){
        	    if(!$c_name){
        	        return false;
        	    }else{
        	        return true;
        	    }
        	}elseif($root_auth_mode == "2"){
        	    if(!$c_name || !$uid){
        	        return false;
        	    }else{
        	        $sql = "select * from `FORUM_AUTH_ACCESS` where `auth_id` = '$p' AND `auth_area` = '2'";
        	        $result = $db->query($sql);
        	        $chk = $result->numRows();
        	        if($chk){
        	            $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
        	            $tmp_uid = $tmp_rows["auth_usr"];
        	            $tmp_uid = rtrim($tmp_uid,",");
        	            $tmp_uid_result = split(",",$tmp_uid);
        	            if(in_array("$uid",$tmp_uid_result)){
        	                return false;
        	            }else{
        	                return true;
        	            }
        	        }else{
        	            return false;
        	        }
        	    }
        	}else{
        	    return false;
        	}
    	}else{
    	    return true;
    	}
	}
    
    if($auth_mode != "0"){
        if($auth_mode == "1"){
            if(!$c_name){
                return false;
            }else{
                return true;
            }
        }elseif($auth_mode == "2"){
            if(!$c_name || !$uid){
                return false;
            }else{
                $sql = "select * from `FORUM_AUTH_ACCESS` where `auth_id` = '$p' AND `auth_area` = '2'";
                $result = $db->query($sql);
                $chk = $result->numRows();
                if($chk){
                    $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                    $tmp_uid = $tmp_rows["auth_usr"];
                    $tmp_uid = rtrim($tmp_uid,",");
                    $tmp_uid_result = split(",",$tmp_uid);
                    if(in_array("$uid",$tmp_uid_result)){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }else{
        return true;
    }
}

//認証関係
function auth_read_t($t){
    global $db,$c_name,$uid;
	
	if(find_oracle($uid)){
		return true;
	}
	
    $sql = "select * from `FORUM_POSTS` WHERE `topic_id` = '$t' AND `thread_id` = '1'";
    $result = $db->query($sql);
    $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $auth_mode = $topic_rows["auth_mode"];
    //var_dump($uid);
    
    if($auth_mode != "0"){
        if($auth_mode == "1"){
            if(!$c_name){
                return false;
            }else{
                return true;
            }
        }elseif($auth_mode == "2"){
            if(!$c_name || !$uid){
                return false;
            }else{
                $sql = "select * from `FORUM_AUTH_ACCESS` where `auth_id` = '$p' AND `auth_area` = '2'";
                $result = $db->query($sql);
                $chk = $result->numRows();
                if($chk){
                    $tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
                    $tmp_uid = $tmp_rows["auth_usr"];
                    $tmp_uid = rtrim($tmp_uid,",");
                    $tmp_uid_result = split(",",$tmp_uid);
                    if(in_array("$uid",$tmp_uid_result)){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }else{
        return true;
    }
}


//認証関係
function auth_read_cf($mode,$val){
    global $db,$c_name,$uid;
	
	if(find_oracle($uid)){
		return true;
	}
	
	if($mode == "c"){
		//カテゴリー
		
	    $sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$val'";
	    $result = $db->query($sql);
	    $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    $auth_mode = $topic_rows["auth_mode"];
	    if($auth_mode != "0"){
			if($auth_mode == "1"){
	            if(!$c_name){
	                return false;
	            }else{
	                return true;
	            }
	        }elseif($auth_mode == "2"){
	            if(!$c_name || !$uid){
	                return false;
	            }else{
	                $sql = "select * from `FORUM_AUTH_ACCESS` where `auth_id` = '$val' AND `auth_area` = '0'";
	                $result = $db->query($sql);
	                $chk = $result->numRows();
	                if($chk){
	                    while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
							$auth_mode = $tmp_rows["auth_mode"];
							//var_dump($auth_mode);
							if($auth_mode == "2"){
	                    		$tmp_uid = $tmp_rows["auth_usr"];
	                    		$tmp_uid = rtrim($tmp_uid,",");
	                    		$tmp_uid_result = split(",",$tmp_uid);
	                    		if(in_array("$uid",$tmp_uid_result)){
	                    		    return false;
	                    		}else{
	                    		    return true;
	                    		}
							}
						}
	                }else{
	                    return false;
	                }
	            }
			}else{
	            return false;
	        }
	    }else{
	        return true;
	    }
		
	}elseif($mode == "f"){
		//トピック
	    $sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$val'";
	    $result = $db->query($sql);
	    $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    $auth_mode = $topic_rows["auth_mode"];
	    if($auth_mode != "0"){
			if($auth_mode == "1"){
	            if(!$c_name){
	                return false;
	            }else{
	                return true;
	            }
	        }elseif($auth_mode == "2"){
	            if(!$c_name || !$uid){
	                return false;
	            }else{
	                $sql = "select * from `FORUM_AUTH_ACCESS` where `auth_id` = '$val' AND `auth_area` = '1'";
	                $result = $db->query($sql);
	                $chk = $result->numRows();
	                if($chk){
	                    while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC)){
							$auth_mode = $tmp_rows["auth_mode"];
							//var_dump($auth_mode);
							if($auth_mode == "2"){
								$tmp_uid = $tmp_rows["auth_usr"];
	                    		$tmp_uid = rtrim($tmp_uid,",");
	                    		$tmp_uid_result = split(",",$tmp_uid);
	                    		if(in_array("$uid",$tmp_uid_result)){
	                        		return false;
	                    		}else{
	                    		    return true;
	                    		}
							}
						}
	                }else{
	                    return false;
	                }
	            }
			}else{
	            return false;
	        }
	    }else{
	        return true;
	    }
		
	}else{
		return false;
	}
}


//認証関係
function rock_status($mode,$val){
    global $db,$c_name,$uid;
	
	if(find_oracle($uid)){
		return true;
	}
	
	if($mode == "c"){
		//カテゴリー
		
	    $sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$val'";
	    $result = $db->query($sql);
	    $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    $status = $topic_rows["cat_status"];
	    if($status != "0"){
			return false;
	    }else{
	        return true;
	    }
		
	}elseif($mode == "f"){
		//トピック
	    $sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$val'";
		//var_dump($sql);
	    $result = $db->query($sql);
	    $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    $status = $topic_rows["forum_status"];
		//var_dump($status);
	    if($status != "0"){
			return false;
	    }else{
	        return true;
	    }
	}elseif($mode == "t"){
		//トピック
	    $sql = "select * from `FORUM_TOPIC` WHERE `topic_id` = '$val'";
	    $result = $db->query($sql);
	    $topic_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    $status = $topic_rows["topic_status"];
	    if($status != "0"){
			return false;
	    }else{
	        return true;
	    }
	}else{
		return false;
	}
}

function auth_edit_c($c){
	global $db,$uid,$c_name;

	if(find_oracle($uid)){
		return true;
	}

	$sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$c'";
	$cat_result = $db->query($sql);
	$chk = $cat_result->numRows();
	if($chk){
		$cat_rows = $cat_result->fetchRow(DB_FETCHMODE_ASSOC);
		$auth_edit = $cat_rows["auth_edit"];
		
	    if($auth_edit == "0"){
			$cat_master = $cat_rows["cat_master"];
			//var_dump($uid);
			//var_dump($forum_master);
			if($uid != $cat_master){
				return false;
			}else{
				return true;
			}
	    }else{
			if($auth_edit == "1"){
	            if(!$c_name){
	                return false;
	            }else{
	                return true;
	            }
	        }elseif($auth_edit == "2"){
	                $sql = "select * from `PHP_USR_LEVEL` where `root` = '1'";
	                $result = $db->query($sql);
	                $chk = $result->numRows();
					//var_dump($chk);
	                if($chk){
						while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$auth_uid = $tmp_rows["uid"];
							if($auth_uid == $uid){
								return true;
							}else{
								return false;
							}
						}
	                }else{
	                    return false;
	                }
				
			}elseif($auth_edit == "3"){
	            if(!$c_name || !$uid){
	                return false;
	            }else{
	                $sql = "select * from `FORUM_AUTH_ACCESS` where `auth_id` = '$c' AND `auth_area` = '0'";
	                $result = $db->query($sql);
	                $chk = $result->numRows();
					//var_dump($chk);
	                if($chk){
						while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$auth_mode = $tmp_rows["auth_mode"];
							//var_dump($auth_mode);
							if($auth_mode == "1"){
	                    		$tmp_uid = $tmp_rows["auth_usr"];
	                    		$tmp_uid = rtrim($tmp_uid,",");
	                    		$tmp_uid_result = split(",",$tmp_uid);
	                    		if(in_array("$uid",$tmp_uid_result)){
	                    		    return true;
	                    		}else{
	                    		    return false;
	                    		}
							}
						}
	                }else{
	                    return false;
	                }
	            }
			}else{
	            return false;
	        }
	    }
	}else{
		die("no sid");
	}


}


function auth_edit($f){
	global $db,$uid,$c_name;
	
	if(find_oracle($uid)){
		return true;
	}
	
	$sql = "select * from `FORUM_FORUMS` WHERE `forum_id` = '$f'";
	$forum_result = $db->query($sql);
	$chk = $forum_result->numRows();
	if($chk){
		$forum_rows = $forum_result->fetchRow(DB_FETCHMODE_ASSOC);
		$auth_edit = $forum_rows["auth_edit"];
		$cat_id = $forum_rows["cat_id"];

		//2回目の認証
		$sql = "select * from `FORUM_CATEGORIES` WHERE `cat_id` = '$cat_id'";
		$result = $db->query($sql);
		$chk = $result->numRows();
		if($chk){
			if(!auth_edit_c($cat_id)){
				//die("edit error");
				sub_msg("5","forum/forum.php","このカテゴリーは編集できません","リロードします。");
			}else{
	    		$user_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	    		$cat_title = $user_rows["cat_title"];
			}
		}else{
			die("no categories");
		}
		
	    if($auth_edit == "0"){
			$forum_master = $forum_rows["forum_master"];
			//var_dump($uid);
			//var_dump($forum_master);
			if($uid != $forum_master){
				return false;
			}else{
				return true;
			}
	    }else{
			if($auth_edit == "1"){
	            if(!$c_name){
	                return false;
	            }else{
	                return true;
	            }
	        }elseif($auth_edit == "2"){
	                $sql = "select * from `PHP_USR_LEVEL` where `root` = '1'";
	                $result = $db->query($sql);
	                $chk = $result->numRows();
					//var_dump($chk);
	                if($chk){
						while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$auth_uid = $tmp_rows["uid"];
							if($auth_uid == $uid){
								return true;
							}else{
								return false;
							}
						}
	                }else{
	                    return false;
	                }
				
			}elseif($auth_edit == "3"){
	            if(!$c_name || !$uid){
	                return false;
	            }else{
	                $sql = "select * from `FORUM_AUTH_ACCESS` where `auth_id` = '$f' AND `auth_area` = '1'";
	                $result = $db->query($sql);
	                $chk = $result->numRows();
					//var_dump($chk);
	                if($chk){
						while($tmp_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
							$auth_mode = $tmp_rows["auth_mode"];
							//var_dump($auth_mode);
							if($auth_mode == "1"){
	                    		$tmp_uid = $tmp_rows["auth_usr"];
	                    		$tmp_uid = rtrim($tmp_uid,",");
	                    		$tmp_uid_result = split(",",$tmp_uid);
	                    		if(in_array("$uid",$tmp_uid_result)){
	                    		    return true;
	                    		}else{
	                    		    return false;
	                    		}
							}
						}
	                }else{
	                    return false;
	                }
	            }
			}else{
	            return false;
	        }
	    }
	}else{
		die("no sid");
	}
}


function forum_session($mode,$sid){
	global $db;
	
	$save_time = "15";
	
	if($mode == 0){
		//開始
		$sql = "select * from `FORUM_SESSION` WHERE `sid` = '$sid'";
		$result = $db->query($sql);
		$chk = $result->numRows();
		if($chk){
			$ses_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
			$ses_time = $ses_rows["time"];
			$now_time = time();
			$rock_time = $now_time - $ses_time;
			//var_dump($rock_time);
			if($rock_time < $save_time){
				//stop
				return false;
			}else{
			$time =time();
		    //mt_srand(microtime()*100000);
			//$sid = md5(uniqid(mt_rand(),1));
			
		    $sql = "REPLACE INTO `FORUM_SESSION` VALUES ('$sid','$time')";
			$result = $db->query($sql);
			if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
			}
			
			return true;
			}
		}else{
			$time =time();
		    //mt_srand(microtime()*100000);
			//$sid = md5(uniqid(mt_rand(),1));
			
		    $sql = "REPLACE INTO `FORUM_SESSION` VALUES ('$sid','$time')";
			$result = $db->query($sql);
			if (DB::isError($result)) {
				trigger_error($result->getMessage(), E_USER_ERROR);
			}
			
			return true;
		}
	}elseif($mode ==1){
		//廃棄
	    $sql = "delete from `FORUM_SESSION` where `sid` = '$sid'";
	    $result = $db->query($sql);
	    if (DB::isError($result)) {
	         trigger_error($result->getMessage(), E_USER_ERROR);
	    }
		return true;
	}else{
		return false;
	}
}

function find_user($user){
	global $db;
	
	$sql = "select * from `USER_DATA` WHERE `name` = '$user'";
	$forum_result = $db->query($sql);
	$chk = $forum_result->numRows();
	if($chk){
		return true;
	}else{
		return false;
	}

}



?>
