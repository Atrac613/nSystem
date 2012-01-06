<?php

function table_ultra_simple(){
	global $db,$uid,$env_rows,$INDEX,$JOBLIST,$RACELIST,$FACENAME,$PLOF_SCRIPT,$ML_SCRIPT;
	
	$env_anon_mode = $env_rows["anon_mode"];

	$sql = "select * from USER_DATA , USER_STA , USER_LEV , USER_IP where USER_DATA.uid = USER_STA.uid and USER_DATA.uid = USER_LEV.uid and  USER_DATA.uid = USER_IP.uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	echo "登録メンバー数:$members";
				//テーブルヘッド開始
                  ?><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY><?php
					
	//データチェック
	if($members == "0"){
		echo "<TR class=\"table_title\"><TH>ユーザー未登録</TH></TR>";
	}else{
                  ?><TR class="table_title">
                        <TH width="2%"><A href="<?php echo "$ML_SCRIPT?view=0&sort=0"; ?>"><?php echo "$INDEX[0]"; ?></A></TH>
                        <TH width="2%"><A href="<?php echo "$ML_SCRIPT?view=0&sort=4"; ?>"><?php echo "$INDEX[11]"; ?></A></TH>
                        <TH width="10%"><A href="<?php echo "$ML_SCRIPT?view=0&sort=2"; ?>"><?php echo "$INDEX[2]"; ?></A></TH>
                        <TH width="10%"><?php echo "$INDEX[7]"; ?></TH>
                        <TH><?php echo "$INDEX[9]"; ?></TH>
                        <TH width="5%"><A href="<?php echo "$ML_SCRIPT?view=0&sort=9"; ?>"><?php echo "$INDEX[8]"; ?></A></TH>
                      </TR>
					  <?php
					//sort
					$sql = sql_sort();
					$result = $db->query($sql);
					
					//データをwhileで回す
					while( $main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					
						$main_rows = convert_to_sjis($main_rows);
						
						$user_no = $main_rows["no"];
						$user_uid = $main_rows["uid"];
						$user_name = $main_rows["name"];
						$user_faceid = $main_rows["face"];
						$user_pass = $main_rows["pass"];
						//$user_class = $main_rows["class"];
						$user_race = $main_rows["race"];
						$user_anon = $main_rows["anon"];
						$user_mainjob = $main_rows["mainjob"];
						$user_supportjob = $main_rows["supportjob"];
						$user_comment = $main_rows['comment'];
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
						$user_lev[17] = $main_rows["lev17"];
	
						$user_date = $main_rows["date"];
						$date = gmdate("y/m/d", $user_date+9*60*60);
						//var_dump(strip_tags($user_comment));
						//$user_comment = mb_convert_encoding($user_comment, "SJIS" , "auto");
						$user_comment = htmlspecialchars(strip_tags($user_comment));
						//var_dump($user_comment);
						//自分の行だったときは行カラー変更
						if($uid == $user_uid){
							$row_c = 2;
						}else{
							$row_c = 1;
						}
						
						//クラスが未入力の場合は空白
						if($user_class == ""){
							$user_class="&nbsp";
						}
						
						//ジョブ関係
						if($user_anon == 0 || $env_anon_mode != 1){
							if($user_lev[$user_mainjob]<10){
								$main_level="0$user_lev[$user_mainjob]";
							} else {
								$main_level=$user_lev[$user_mainjob];
							}
	
							if($user_supportjob != 99){
								$mod_level=intval($user_lev[$user_mainjob]/2);
								
								if($mod_level==0){
									$mod_level=1;
								}
								
								if($mod_level>$user_lev[$user_supportjob]){
									$mod_level=$user_lev[$user_supportjob];
								}
								
								if($mod_level<10){
									$mod_level="0$mod_level";
								}
	
								$job_str = "$JOBLIST[$user_mainjob]$main_level/$JOBLIST[$user_supportjob]$mod_level";
	
							} else{
								$job_str = "$JOBLIST[$user_mainjob]$main_level";
							}
	
						}else{
							$job_str = "-";
						}
						
						//アイコンのALT
						$pos = strrpos($user_faceid,"b");
						if($pos){
							$user_face2 = substr($user_faceid,0,$pos);
							$user_face_name = "$FACENAME[$user_face2] B";
						}else{
							$user_face_name = "$FACENAME[$user_faceid] A";
						}
						
						$diary = time_chk_diary($user_uid);
						$bazaar_icon = chk_bazaar($user_uid);
						
						
						?>
                      <TR class="row<?php echo $row_c; ?>">
                        <TD align="center"><?php echo $user_no; ?></TD>
                        <TD align="center"><img src="../face/<?php echo $user_faceid; ?>.gif" alt="<?php echo "$RACELIST[$user_race] [$user_face_name]"; ?>"></TD>
                        <TD align="center"><A href="<?php echo"$PLOF_SCRIPT?name=$user_name"; ?>"><?php echo "$user_name</A>&nbsp;$diary$bazaar_icon"; ?></TD>
                        <TD align="center"><?php echo $job_str; ?></TD>
                        <TD><?php echo $user_comment; ?></TD>
                        <TD align="center"><?php echo $date; ?></TD>
                      </TR><?php
					
					}
	
	
	}
	
	?>
                    </TBODY>
                  </TABLE><?php


}

function table_simple(){
	global $db,$uid,$env_rows,$INDEX,$JOBLIST,$PLOF_SCRIPT,$ML_SCRIPT;
	
	$env_anon_mode = $env_rows["anon_mode"];

	$sql = "select * from USER_DATA , USER_STA , USER_LEV , USER_IP where USER_DATA.uid = USER_STA.uid and USER_DATA.uid = USER_LEV.uid and  USER_DATA.uid = USER_IP.uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	echo "登録メンバー数:$members";
				//テーブルヘッド開始
                  ?><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY><?php
					
	//データチェック
	if($members == "0"){
		echo "<TR class=\"table_title\"><TH>ユーザー未登録</TH></TR>";
	}else{
                  ?><TR class="table_title">
                        <TH width="2%"><A href="<?php echo "$ML_SCRIPT?view=1&sort=0"; ?>"><?php echo "$INDEX[0]"; ?></A></TH>
                        <TH width="2%"><A href="<?php echo "$ML_SCRIPT?view=1&sort=1"; ?>"><?php echo "$INDEX[1]"; ?></A></TH>
                        <TH width="10%"><A href="<?php echo "$ML_SCRIPT?view=1&sort=2"; ?>"><?php echo "$INDEX[2]"; ?></A></TH>
                        <TH width="10%"><?php echo "$INDEX[7]"; ?></TH>
                        <TH><?php echo "$INDEX[9]"; ?></TH>
                        <TH width="5%"><A href="<?php echo "$ML_SCRIPT?view=1&sort=9"; ?>"><?php echo "$INDEX[8]"; ?></A></TH>
                      </TR>
					  <?php
					//sort
					$sql = sql_sort();
					$result = $db->query($sql);
					
					//データをwhileで回す
					while( $main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					
						$main_rows = convert_to_sjis($main_rows);
					
						$user_no = $main_rows["no"];
						$user_uid = $main_rows["uid"];
						$user_name = $main_rows["name"];
						$user_faceid = $main_rows["face"];
						$user_pass = $main_rows["pass"];
						$user_class = $main_rows["class"];
						$user_race = $main_rows["race"];
						$user_anon = $main_rows["anon"];
						$user_mainjob = $main_rows["mainjob"];
						$user_supportjob = $main_rows["supportjob"];
						$user_comment = $main_rows["comment"];
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
						$user_lev[17] = $main_rows["lev17"];
	
						$user_date = $main_rows["date"];
						$date = gmdate("y/m/d", $user_date+9*60*60);
						
						$user_comment = htmlspecialchars($user_comment);
						
						//自分の行だったときは行カラー変更
						if($uid == $user_uid){
							$row_c = 2;
						}else{
							$row_c = 1;
						}
						
						//クラスが未入力の場合は空白
						if($user_class == ""){
							$user_class="&nbsp";
						}
						
						//ジョブ関係
						if($user_anon == 0 || $env_anon_mode != 1){
							if($user_lev[$user_mainjob]<10){
								$main_level="0$user_lev[$user_mainjob]";
							} else {
								$main_level=$user_lev[$user_mainjob];
							}
	
							if($user_supportjob != 99){
								$mod_level=intval($user_lev[$user_mainjob]/2);
								
								if($mod_level==0){
									$mod_level=1;
								}
								
								if($mod_level>$user_lev[$user_supportjob]){
									$mod_level=$user_lev[$user_supportjob];
								}
								
								if($mod_level<10){
									$mod_level="0$mod_level";
								}
	
								$job_str = "$JOBLIST[$user_mainjob]$main_level/$JOBLIST[$user_supportjob]$mod_level";
	
							} else{
								$job_str = "$JOBLIST[$user_mainjob]$main_level";
							}
	
						}else{
							$job_str = "-";
						}
						
						//アイコンのALT
						$pos = strrpos($user_faceid,"b");
						if($pos){
							$user_face2 = substr($user_faceid,0,$pos);
							$user_face_name = "$FACENAME[$user_face2] B";
						}else{
							$user_face_name = "$FACENAME[$user_faceid] A";
						}
						
						$diary = time_chk_diary($user_uid);
						$bazaar_icon = chk_bazaar($user_uid);
						
						
						?>
                      <TR>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo $user_no; ?></TD>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$user_class"; ?></TD>
                        <TD align="center" class="row<?php echo $row_c; ?>"><A href="<?php echo"$PLOF_SCRIPT?name=$user_name"; ?>"><?php echo "$user_name</A>&nbsp;$diary$bazaar_icon"; ?></TD>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo $job_str; ?></TD>
                        <TD class="row<?php echo $row_c; ?>"><?php echo $user_comment; ?></TD>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo $date; ?></TD>
                      </TR><?php
					
					}
	
	
	}
	
	?>
                    </TBODY>
                  </TABLE><?php


}

function table_icon(){

}

function table_job(){
	global $db,$uid,$env_rows,$INDEX,$JOBLIST,$COLOR,$PLOF_SCRIPT,$ML_SCRIPT,$JOB_MAX;
	
	$env_anon_mode = $env_rows["anon_mode"];
	$table = $_GET["view"];

	$sql = "select * from USER_DATA , USER_STA , USER_LEV , USER_IP where USER_DATA.uid = USER_STA.uid and USER_DATA.uid = USER_LEV.uid and  USER_DATA.uid = USER_IP.uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	echo "登録メンバー数:$members";
				//テーブルヘッド開始
                  ?><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" >
                    <TBODY><?php
					
	//データチェック
	if($members == "0"){
		echo "<TR class=\"table_title\"><TH>ユーザー未登録</TH></TR>";
	}else{
	echo "<td align=right><table cellpadding=0 cellspacing=0 border=0>";
	if($table==3){
		echo "<tr CLASS=tx12>";
		for($i=0;$i<10;$i++){
		$ls=$i*10;
		$le=$i*10+11;
		echo "<td CLASS='color$i' nowrap>Lv$ls-$le</td><td>&nbsp;&nbsp;</td>\n";
		}
		echo "<td CLASS='color9' nowrap0>Lv100～</td>\n";
		echo "</tr>";
	} else {
		echo "<tr><td class=row2 nowrap>&nbsp;main job&nbsp;</td>\n";
		echo "<td>&nbsp;&nbsp;</td><td class=row1 nowrap>&nbsp;support job&nbsp;</td></tr>\n";
	}
                  ?></td></TBODY></TABLE></TBODY></TABLE><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY><TR class="table_title">
                        <TH width="2%" rowspan=3><A href="<?php echo "$ML_SCRIPT?view=$table&sort=0"; ?>"><?php echo "$INDEX[0]"; ?></A></TH>
                        <TH width="10%" rowspan=3><A href="<?php echo "$ML_SCRIPT?view=$table&sort=2"; ?>"><?php echo "$INDEX[2]"; ?></A></TH>
                        <TH width="10%" rowspan=3><?php echo "$INDEX[7]"; ?></TH>
						<?php
						
							for ($j=0;$j<8;$j++){
								$jn=$j+10;
								echo "<TH width=3%><a href='$ML_SCRIPT?view=$table&sort=$jn'>$JOBLIST[$j]</a></TH>";
							}
						
						?>
						
                        <TH width="5%" rowspan=3><A href="<?php echo "$ML_SCRIPT?view=$table&sort=9"; ?>"><?php echo "$INDEX[8]"; ?></A></TH>
                      </TR>
					  <TR class="table_title">
						<?php
						
							for ($j=8;$j<16;$j++){
								$jn=$j+10;
								echo "<TH width=3%><a href='$ML_SCRIPT?view=$table&sort=$jn'>$JOBLIST[$j]</a></TH>";
							}
						
						?>
					  </TR>
					  <TR class="table_title">
						<?php
						
							for ($j=16;$j<18;$j++){
								$jn=$j+10;
								echo "<TH width=3%><a href='$ML_SCRIPT?view=$table&sort=$jn'>$JOBLIST[$j]</a></TH>";
							}
						
						?>
					  <TH></TH>
					  <TH></TH>
					  <TH></TH>
					  <TH></TH>
					  <TH></TH>
					  <TH></TH>
					  </TR>
					  <?php
					  //die();
					//sort
					$sql = sql_sort();
					$result = $db->query($sql);
					
					//データをwhileで回す
					while( $main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					
						$main_rows = convert_to_sjis($main_rows);
					
						$user_no = $main_rows["no"];
						$user_uid = $main_rows["uid"];
						$user_name = $main_rows["name"];
						$user_faceid = $main_rows["face"];
						$user_pass = $main_rows["pass"];
						//$user_class = $main_rows["class"];
						$user_race = $main_rows["race"];
						$user_anon = $main_rows["anon"];
						$user_mainjob = $main_rows["mainjob"];
						$user_supportjob = $main_rows["supportjob"];
						$user_comment = $main_rows["comment"];
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
						$user_lev[17] = $main_rows["lev17"];
	
						$user_date = $main_rows["date"];
						$date = gmdate("y/m/d", $user_date+9*60*60);
						
						$user_comment = htmlspecialchars($user_comment);
						
						//自分の行だったときは行カラー変更
						if($uid == $user_uid){
							$row_c = 2;
						}else{
							$row_c = 1;
						}
						
						//クラスが未入力の場合は空白
						//if($user_class == ""){
						//	$user_class="&nbsp";
						//}
						
						//ジョブ関係
						if($user_anon == 0 || $env_anon_mode != 1){
							if($user_lev[$user_mainjob]<10){
								$main_level="0$user_lev[$user_mainjob]";
							} else {
								$main_level=$user_lev[$user_mainjob];
							}
	
							if($user_supportjob != 99){
								$mod_level=intval($user_lev[$user_mainjob]/2);
								
								if($mod_level==0){
									$mod_level=1;
								}
								
								if($mod_level>$user_lev[$user_supportjob]){
									$mod_level=$user_lev[$user_supportjob];
								}
								
								if($mod_level<10){
									$mod_level="0$mod_level";
								}
	
								$job_str = "$JOBLIST[$user_mainjob]$main_level/$JOBLIST[$user_supportjob]$mod_level";
	
							} else{
								$job_str = "$JOBLIST[$user_mainjob]$main_level";
							}
	
						}else{
							$job_str = "-";
						}
						
						//アイコンのALT
						$pos = strrpos($user_faceid,"b");
						if($pos){
							$user_face2 = substr($user_faceid,0,$pos);
							$user_face_name = "$FACENAME[$user_face2] B";
						}else{
							$user_face_name = "$FACENAME[$user_faceid] A";
						}
						
						$diary = time_chk_diary($user_uid);
						$bazaar_icon = chk_bazaar($user_uid);
						
						
						?>
                      <TR>
                        <TD rowspan=3 align="center" class="row<?php echo $row_c; ?>"><?php echo $user_no; ?></TD>
                        <TD rowspan=3 align="center" class="row<?php echo $row_c; ?>"><A href="<?php echo"$PLOF_SCRIPT?name=$user_name"; ?>"><?php echo "$user_name</A>&nbsp;$diary$bazaar_icon"; ?></TD>
                        <TD rowspan=3 align="center" class="row<?php echo $row_c; ?>"><?php echo $job_str; ?></TD>
						<?php
						
							for ($j=0;$j<8;$j++){
								$bg="";
								
								if($user_anon == 0 || $env_anon_mode != 1){
								
								if($_GET["view"] ==3){
									if($user_lev[$j] >= 100){
										$bg=" class='color9'";
									}else{
										$bg_tmp = intval($user_lev[$j]/10);
										$bg=" class='color$bg_tmp'";
									}
			
								} else {
									if($j == $user_mainjob){$bg=" class=row2";}
									if($j == $user_supportjob){$bg=" class=row1";}
									if(!$bg){$bg=" class=row1";}
								}
		
								echo "<TD$bg>$user_lev[$j]</TD>";
								
								}else{
									echo "<TD class=row1>-</TD>";
								}
							}
						
						?>
                        <TD rowspan=3 align="center" class="row<?php echo $row_c; ?>"><?php echo $date; ?></TD>
                      </TR>
					  <TR>
					  <?php
							for ($j=8;$j<16;$j++){
								$bg="";

								if($user_anon == 0 || $env_anon_mode != 1){
								
								if($_GET["view"] ==3){
									if($user_lev[$j] >= 100){
										$bg=" class='color9'";
									}else{
										$bg_tmp = intval($user_lev[$j]/10);
										$bg=" class='color$bg_tmp'";
									}
			
								} else {
									if($j == $user_mainjob){$bg=" class=row2";}
									if($j == $user_supportjob){$bg=" class=row1";}
									if(!$bg){$bg=" class=row1";}
								}
		
								echo "<TD$bg>$user_lev[$j]</TD>";
								
								}else{
									echo "<TD class=row1>-</TD>";
								}
							}
								
							
							
							
					  ?>
					  
					  </TR>
					  <TR>
					  <?php
							for ($j=16;$j<18;$j++){
								$bg="";

								if($user_anon == 0 || $env_anon_mode != 1){
								
								if($_GET["view"] ==3){
									if($user_lev[$j] >= 100){
										$bg=" class='color9'";
									}else{
										$bg_tmp = intval($user_lev[$j]/10);
										$bg=" class='color$bg_tmp'";
									}
			
								} else {
									if($j == $user_mainjob){$bg=" class=row2";}
									if($j == $user_supportjob){$bg=" class=row1";}
									if(!$bg){$bg=" class=row1";}
								}
		
								echo "<TD$bg>$user_lev[$j]</TD>";
								
								}else{
									echo "<TD class=row1>-</TD>";
								}
							}
								
							echo "<TD class='color0'></TD>";
							echo "<TD class='color0'></TD>";
							echo "<TD class='color0'></TD>";
							echo "<TD class='color0'></TD>";
							echo "<TD class='color0'></TD>";
							echo "<TD class='color0'></TD>";
							
							
					  ?>
					  
					  </TR>
					  
					  <?php
					
					}
	
	
	}
	
	?>
                    </TBODY>
                  </TABLE><?php


}

function table_prod_item(){
	global $db,$uid,$env_rows,$INDEX,$COLOR,$PLOF_SCRIPT,$ML_SCRIPT,$PROD_MAX,$PLODLIST;
	
	$env_anon_mode = $env_rows["anon_mode"];
	$table = $_GET["view"];

	$sql = "select * from USER_DATA , USER_STA , USER_PROD , USER_IP where USER_DATA.uid = USER_STA.uid and USER_DATA.uid = USER_PROD.uid and  USER_DATA.uid = USER_IP.uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	echo "登録メンバー数:$members";
				//テーブルヘッド開始
                  ?><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" >
                    <TBODY><?php
					
	//データチェック
	if($members == "0"){
		echo "<TR class=\"table_title\"><TH>ユーザー未登録</TH></TR>";
	}else{
	echo "<td align=right><table cellpadding=0 cellspacing=0 border=0>";

		echo "<tr CLASS=tx12>";
		for($i=0;$i<10;$i++){
		$ls=$i*10;
		$le=$i*10+11;
		echo "<td class='color$i' nowrap>$ls-$le</td><td>&nbsp;&nbsp;</td>\n";
		}
		echo "<td class='color9' nowrap>100～</td>\n";
		echo "</tr>";
                  ?></td></TBODY></TABLE></TBODY></TABLE><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY><TR class="table_title">
                        <TH width="2%"><A href="<?php echo "$ML_SCRIPT?view=4&sort=0"; ?>"><?php echo "$INDEX[0]"; ?></A></TH>
                        <TH width="10%"><A href="<?php echo "$ML_SCRIPT?view=4&sort=2"; ?>"><?php echo "$INDEX[2]"; ?></A></TH>
						<?php
						
							for ($p=0;$p<$PROD_MAX;$p++){
								$pn=$p+27;
								echo "<TH width=4%><a href='$ML_SCRIPT?view=4&sort=$pn'>$PLODLIST[$p]</a></TH>";
							}
						
						?>
						
                        <TH width="5%"><A href="<?php echo "$ML_SCRIPT?view=4&sort=9"; ?>"><?php echo "$INDEX[8]"; ?></A></TH>
                      </TR>
					  <?php
					  //die();
					//sort
					$sql = sql_sort();
					$result = $db->query($sql);
					
					//データをwhileで回す
					while( $main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					
						$main_rows = convert_to_sjis($main_rows);
					
						$user_no = $main_rows["no"];
						$user_uid = $main_rows["uid"];
						$user_name = $main_rows["name"];
						$user_faceid = $main_rows["face"];
						$user_pass = $main_rows["pass"];
						//$user_class = $main_rows["class"];
						$user_race = $main_rows["race"];
						$user_anon = $main_rows["anon"];
						$user_mainjob = $main_rows["mainjob"];
						$user_supportjob = $main_rows["supportjob"];
						$user_comment = $main_rows["comment"];
						$user_prod[0] = $main_rows["prod0"];
						$user_prod[1] = $main_rows["prod1"];
						$user_prod[2] = $main_rows["prod2"];
						$user_prod[3] = $main_rows["prod3"];
						$user_prod[4] = $main_rows["prod4"];
						$user_prod[5] = $main_rows["prod5"];
						$user_prod[6] = $main_rows["prod6"];
						$user_prod[7] = $main_rows["prod7"];
						$user_prod[8] = $main_rows["prod8"];
						$user_prod[9] = $main_rows["prod9"];
						$user_prod[10] = $main_rows["prod10"];
						$user_prod[11] = $main_rows["prod11"];
						$user_prod[12] = $main_rows["prod12"];
						$user_prod[13] = $main_rows["prod13"];
						$user_prod[14] = $main_rows["prod14"];
						$user_prod[15] = $main_rows["prod15"];
						$user_prod[16] = $main_rows["prod16"];

						$user_date = $main_rows["date"];
						$date = gmdate("y/m/d", $user_date+9*60*60);
						
						$user_comment = htmlspecialchars($user_comment);
						
						//自分の行だったときは行カラー変更
						if($uid == $user_uid){
							$row_c = 2;
						}else{
							$row_c = 1;
						}
						
						//クラスが未入力の場合は空白
						//if($user_class == ""){
						//	$user_class="&nbsp";
						//}
						
						//アイコンのALT
						$pos = strrpos($user_faceid,"b");
						if($pos){
							$user_face2 = substr($user_faceid,0,$pos);
							$user_face_name = "$FACENAME[$user_face2] B";
						}else{
							$user_face_name = "$FACENAME[$user_faceid] A";
						}
						
						$diary = time_chk_diary($user_uid);
						$bazaar_icon = chk_bazaar($user_uid);
						
						
						?>
                      <TR>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo $user_no; ?></TD>
                        <TD align="center" class="row<?php echo $row_c; ?>"><A href="<?php echo"$PLOF_SCRIPT?name=$user_name"; ?>"><?php echo "$user_name</A>&nbsp;$diary$bazaar_icon"; ?></TD>
						<?php
						
	
							for ($p=0;$p<$PROD_MAX;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_prod[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_prod[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_prod[$p]){$user_prod[$p] = "-";}
								echo "<TD$bg>$user_prod[$p]</TD>";
								
								}else{
									echo "<TD class=row1>-</TD>";
								}
							}
						
						?>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo $date; ?></TD>
                      </TR>
					  
					  
					  <?php
					
					}
	
	
	}
	
	?>
                    </TBODY>
                  </TABLE><?php


}

function table_prod_skill(){
	global $db,$uid,$env_rows,$INDEX,$COLOR,$PLOF_SCRIPT,$ML_SCRIPT,$SKILLLIST;
	
	$env_anon_mode = $env_rows["anon_mode"];
	$table = $_GET["view"];

	$sql = "select * from USER_DATA , USER_STA , USER_PROD , USER_IP where USER_DATA.uid = USER_STA.uid and USER_DATA.uid = USER_PROD.uid and  USER_DATA.uid = USER_IP.uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	echo "登録メンバー数:$members";
				//テーブルヘッド開始
                  ?><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" >
                    <TBODY><?php
					
	//データチェック
	if($members == "0"){
		echo "<TR class=\"table_title\"><TH>ユーザー未登録</TH></TR>";
	}else{
	echo "<td align=right><table cellpadding=0 cellspacing=0 border=0>";

		echo "<tr CLASS=tx12>";
		for($i=0;$i<10;$i++){
		$ls=$i*10;
		$le=$i*10+11;
		echo "<td class='color$i' nowrap>$ls-$le</td><td>&nbsp;&nbsp;</td>\n";
		}
		echo "<td class='color9' nowrap>100～</td>\n";
		echo "</tr>";
                  ?></td></TBODY></TABLE></TBODY></TABLE><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY><TR class="table_title">
                        <TH width="2%" rowspan=4><A href="<?php echo "$ML_SCRIPT?view=4&sort=0"; ?>"><?php echo "$INDEX[0]"; ?></A></TH>
                        <TH width="10%" rowspan=4><A href="<?php echo "$ML_SCRIPT?view=4&sort=2"; ?>"><?php echo "$INDEX[2]"; ?></A></TH>
						<?php
						
							for ($p=0;$p<8;$p++){
								$pn=$p+36;
								echo "<TH width=5% class=tx10><a href='$ML_SCRIPT?view=5&sort=$pn'>$SKILLLIST[$p]</a></TH>";
							}
						
						?>
						
                        <TH width="5%" rowspan=4><A href="<?php echo "$ML_SCRIPT?view=4&sort=9"; ?>"><?php echo "$INDEX[8]"; ?></A></TH>
                      </TR>
					  <TR class="table_title">
						<?php
						
							for ($p=8;$p<16;$p++){
								$pn=$p+36;
								echo "<TH width=5% class=tx10><a href='$ML_SCRIPT?view=5&sort=$pn'>$SKILLLIST[$p]</a></TH>";
							}
						
						?>
					  </TR>
					  <TR class="table_title">
						<?php
							for ($p=16;$p<24;$p++){
								$pn=$p+36;
								echo "<TH width=5% class=tx10><a href='$ML_SCRIPT?view=5&sort=$pn'>$SKILLLIST[$p]</a></TH>";
							}
						
						?>
					  </TR>
					  <TR class="table_title">
						<?php
							for ($p=24;$p<30;$p++){
								$pn=$p+36;
								echo "<TH width=5% class=tx10><a href='$ML_SCRIPT?view=5&sort=$pn'>$SKILLLIST[$p]</a></TH>";
							}
						
						?>
						<TH></TH><TH></TH>
					  </TR>
					  <?php
					  //die();
					//sort
					$sql = sql_sort();
					$result = $db->query($sql);
					
					//データをwhileで回す
					while( $main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					
						$main_rows = convert_to_sjis($main_rows);
					
						$user_no = $main_rows["no"];
						$user_uid = $main_rows["uid"];
						$user_name = $main_rows["name"];
						$user_faceid = $main_rows["face"];
						$user_pass = $main_rows["pass"];
						//$user_class = $main_rows["class"];
						$user_race = $main_rows["race"];
						$user_anon = $main_rows["anon"];
						
						$user_skl[0] = $main_rows["skl0"];
						$user_skl[1] = $main_rows["skl1"];
						$user_skl[2] = $main_rows["skl2"];
						$user_skl[3] = $main_rows["skl3"];
						$user_skl[4] = $main_rows["skl4"];
						$user_skl[5] = $main_rows["skl5"];
						$user_skl[6] = $main_rows["skl6"];
						$user_skl[7] = $main_rows["skl7"];
						$user_skl[8] = $main_rows["skl8"];
						$user_skl[9] = $main_rows["skl9"];
						$user_skl[10]= $main_rows["skl10"];
						$user_skl[11] = $main_rows["skl11"];
						$user_skl[12] = $main_rows["skl12"];
						$user_skl[13] = $main_rows["skl13"];
						$user_skl[14] = $main_rows["skl14"];
						$user_skl[15] = $main_rows["skl15"];
						$user_skl[16] = $main_rows["skl16"];
						$user_skl[17] = $main_rows["skl17"];
						$user_skl[18] = $main_rows["skl18"];
						$user_skl[19] = $main_rows["skl19"];
						$user_skl[20] = $main_rows["skl20"];
						$user_skl[21] = $main_rows["skl21"];
						$user_skl[22] = $main_rows["skl22"];
						$user_skl[23] = $main_rows["skl23"];
						$user_skl[24] = $main_rows["skl24"];
						$user_skl[25] = $main_rows["skl25"];
						$user_skl[26] = $main_rows["skl26"];
						$user_skl[27] = $main_rows["skl27"];
						$user_skl[28] = $main_rows["skl28"];
						$user_skl[29] = $main_rows["skl29"];
						$user_skl[30] = $main_rows["skl30"];
						$user_skl[31] = $main_rows["skl31"];
						$user_skl[32] = $main_rows["skl32"];

						$user_date = $main_rows["date"];
						$date = gmdate("y/m/d", $user_date+9*60*60);
						
						$user_comment = htmlspecialchars($user_comment);
						
						//自分の行だったときは行カラー変更
						if($uid == $user_uid){
							$row_c = 2;
						}else{
							$row_c = 1;
						}
						
						//クラスが未入力の場合は空白
						//if($user_class == ""){
						//	$user_class="&nbsp";
						//}
						
						//アイコンのALT
						$pos = strrpos($user_faceid,"b");
						if($pos){
							$user_face2 = substr($user_faceid,0,$pos);
							$user_face_name = "$FACENAME[$user_face2] B";
						}else{
							$user_face_name = "$FACENAME[$user_faceid] A";
						}
						
						$diary = time_chk_diary($user_uid);
						$bazaar_icon = chk_bazaar($user_uid);
						
						
						?>
                      <TR class=tx10>
                        <TD rowspan=4 align="center" class="row<?php echo $row_c; ?>"><?php echo $user_no; ?></TD>
                        <TD rowspan=4 align="center" class="row<?php echo $row_c; ?>"><A href="<?php echo"$PLOF_SCRIPT?name=$user_name"; ?>"><?php echo "$user_name</A>&nbsp;$diary$bazaar_icon"; ?></TD>
						<?php
						
	
							for ($p=0;$p<8;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_skl[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_skl[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_skl[$p]){$user_skl[$p] = "-";}
								echo "<TD$bg>$user_skl[$p]</TD>";
								
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						
						?>
                        <TD rowspan=4 align="center" class="row<?php echo $row_c; ?>"><?php echo $date; ?></TD>
                      </TR>
					  <TR class=tx10>
						<?php
							for ($p=8;$p<16;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_skl[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_skl[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_skl[$p]){$user_skl[$p] = "-";}
								echo "<TD$bg class=tx10>$user_skl[$p]</TD>";
								
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						
						?>
					  </TR>
					  <TR class=tx10>
						<?php
							for ($p=16;$p<24;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_skl[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_skl[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_skl[$p]){$user_skl[$p] = "-";}
								echo "<TD$bg class=tx10>$user_skl[$p]</TD>";
								
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						
						?>
					  </TR>
					  
					  <TR class=tx10>
						<?php
							for ($p=24;$p<30;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_skl[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_skl[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_skl[$p]){$user_skl[$p] = "-";}
								echo "<TD$bg class=tx10>$user_skl[$p]</TD>";
								
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
							echo "<TD class='color0'></TD><TD class='color0'></TD>";
						?>
						
					  </TR>
					  <?php
					
					}
	
	
	}
	
	?>
                    </TBODY>
                  </TABLE><?php


}

function table_stat(){
	global $db,$uid,$env_rows,$INDEX,$FACENAME,$RELMLIST,$RACELIST,$SIZELIST,$PLOF_SCRIPT,$ML_SCRIPT;
	
	$env_anon_mode = $env_rows["anon_mode"];

	$sql = "select * from USER_DATA , USER_STA , USER_LEV , USER_IP where USER_DATA.uid = USER_STA.uid and USER_DATA.uid = USER_LEV.uid and  USER_DATA.uid = USER_IP.uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	echo "登録メンバー数:$members";
				//テーブルヘッド開始
                  ?><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY><?php
					
	//データチェック
	if($members == "0"){
		echo "<TR class=\"table_title\"><TH>ユーザー未登録</TH></TR>";
	}else{
                  ?><TR class="table_title">
                        <TH width="2%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=0"; ?>"><?php echo "$INDEX[0]"; ?></A></TH>
                        <TH width="10%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=2"; ?>"><?php echo "$INDEX[2]"; ?></A></TH>
                        <TH width="10%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=6"; ?>"><?php echo "$INDEX[4]"; ?></A></TH>
                        <TH width="10%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=3"; ?>"><?php echo "$INDEX[3]"; ?></A></TH>
						<TH width="10%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=4"; ?>"><?php echo "$INDEX[11]"; ?></A></TH>
						<TH width="3%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=7"; ?>"><?php echo "$INDEX[10]"; ?></A></TH>
						<TH width="3%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=8"; ?>"><?php echo "$INDEX[6]"; ?></A></TH>
						<TH width="10%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=2"; ?>"><?php echo "$INDEX[5]"; ?></A></TH>
                        <TH width="5%"><A href="<?php echo "$ML_SCRIPT?view=6&sort=9"; ?>"><?php echo "$INDEX[8]"; ?></A></TH>
                      </TR>
					  <?php
					  //die();
					//sort
					$sql = sql_sort();
					$result = $db->query($sql);
					
					//データをwhileで回す
					while( $main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					
						$main_rows = convert_to_sjis($main_rows);
					
						$user_no = $main_rows["no"];
						$user_uid = $main_rows["uid"];
						$user_name = $main_rows["name"];
						$user_faceid = $main_rows["face"];
						$user_pass = $main_rows["pass"];
						$user_class = $main_rows["class"];
						$user_race = $main_rows["race"];
						$user_anon = $main_rows["anon"];
						//$user_mainjob = $main_rows["mainjob"];
						//$user_supportjob = $main_rows["supportjob"];
						//$user_comment = $main_rows["comment"];
						
						$user_race = $main_rows["race"];
						$user_face = $main_rows["face"];
						$user_size = $main_rows["size"];
						$user_relm = $main_rows["relm"];
						$user_point = $main_rows["point"];
						$user_mrank = $main_rows["mrank"];
	
						$user_date = $main_rows["date"];
						$date = gmdate("y/m/d", $user_date+9*60*60);
						
						$user_comment = htmlspecialchars($user_comment);
						
						//自分の行だったときは行カラー変更
						if($uid == $user_uid){
							$row_c = 2;
						}else{
							$row_c = 1;
						}
						
						
						
						//アイコンのALT
						$pos = strrpos($user_faceid,"b");
						if($pos){
							$user_face2 = substr($user_faceid,0,$pos);
							$user_face_name = "$FACENAME[$user_face2] B";
						}else{
							$user_face_name = "$FACENAME[$user_faceid] A";
						}
						
						$diary = time_chk_diary($user_uid);
						$bazaar_icon = chk_bazaar($user_uid);
						
						
						?>
                      <TR>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo $user_no; ?></TD>

                        <TD align="center" class="row<?php echo $row_c; ?>"><A href="<?php echo"$PLOF_SCRIPT?name=$user_name"; ?>"><?php echo "$user_name</A>&nbsp;$diary$bazaar_icon"; ?></TD>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$RELMLIST[$user_relm]"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$RACELIST[$user_race]"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$user_face_name"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$SIZELIST[$user_size]"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$user_mrank"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$user_point"; ?></TD>
                        <TD align="center" class="row<?php echo $row_c; ?>"><?php echo $date; ?></TD>
                      </TR><?php
					
					}
	
	
	}
	
	?>
                    </TBODY>
                  </TABLE><?php


}

function table_full(){
	global $db,$uid,$env_rows,$INDEX,$COLOR,$PLOF_SCRIPT,$ML_SCRIPT,$SKILLLIST,$PROD_MAX,$PLODLIST,$FACENAME,$RELMLIST,$RACELIST,$SIZELIST,$JOBLIST;
	
	$env_anon_mode = $env_rows["anon_mode"];
	$table = $_GET["view"];

	$sql = "select * from USER_DATA , USER_STA , USER_PROD , USER_IP where USER_DATA.uid = USER_STA.uid and USER_DATA.uid = USER_PROD.uid and  USER_DATA.uid = USER_IP.uid";
	$result = $db->query($sql);
	if (DB::isError($result)) {
	    trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$members = $result->numRows();
	echo "登録メンバー数:$members";
				//テーブルヘッド開始
                  ?><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" >
                    <TBODY><?php
					
	//データチェック
	if($members == "0"){
		echo "<TR class=\"table_title\"><TH>ユーザー未登録</TH></TR>";
	}else{
	echo "<td align=right><table cellpadding=0 cellspacing=0 border=0>";

		echo "<tr CLASS=tx12>";
		for($i=0;$i<10;$i++){
		$ls=$i*10;
		$le=$i*10+11;
		echo "<td class='color$i' nowrap>$ls-$le</td><td>&nbsp;&nbsp;</td>\n";
		}
		echo "<td class='color9' nowrap>100～</td>\n";
		echo "</tr>";
                  ?></td></TBODY></TABLE></TBODY></TABLE><TABLE border="0" width="100%" cellpadding="2" cellspacing="1" class="forumline">
                    <TBODY>
					
					<TR class="list_10">
						<!-- no -->
                        <TH width="2%" rowspan=9><A href="<?php echo "$ML_SCRIPT?view=7&sort=0"; ?>"><?php echo "$INDEX[0]"; ?></A></TH>
						
						<!-- name -->
                        <TH width="10%" rowspan=4><A href="<?php echo "$ML_SCRIPT?view=7&sort=2"; ?>"><?php echo "$INDEX[2]"; ?></A></TH>
						
						<?php
						//JOB col1
							for ($j=0;$j<9;$j++){
								$jn=$j+10;
								echo "<TH width=3%><a href='$ML_SCRIPT?view=7&sort=$jn'>$JOBLIST[$j]</a></TH>";
							}
						?>
						
						
						
					</TR>
					
					
					<TR class="list_10">
						<?php
						//JOB col2
							for ($j=9;$j<18;$j++){
								$jn=$j+10;
								echo "<TH width=3%><a href='$ML_SCRIPT?view=7&sort=$jn'>$JOBLIST[$j]</a></TH>";
							}
						?>
					</TR>
					
					
					<TR class="list_10">
						<?php
						//item
							for ($p=0;$p<$PROD_MAX;$p++){
								$pn=$p+27;
								echo "<TH width=4%><a href='$ML_SCRIPT?view=7&sort=$pn'>$PLODLIST[$p]</a></TH>";
							}
						
						?>
					</TR>
					
					
					<TR class="list_10">
						<?php
							//skill col1
							for ($p=0;$p<9;$p++){
								$pn=$p+36;
								echo "<TH width=6%><a href='$ML_SCRIPT?view=7&sort=$pn'>$SKILLLIST[$p]</a></TH>";
							}
						
						?>
					</TR>
					
					
					<TR class="list_10">
						<!-- job_Str -->
						<TH width="10%" rowspan=2><?php echo "$INDEX[7]"; ?></TH>
						<?php
							//skill col2
							for ($p=9;$p<18;$p++){
								$pn=$p+36;
								echo "<TH width=6%><a href='$ML_SCRIPT?view=7&sort=$pn'>$SKILLLIST[$p]</a></TH>";
							}
						?>
					</TR>
					
					
					<TR class="list_10">
						<?php
							//skill col3
							for ($p=18;$p<27;$p++){
								$pn=$p+36;
								echo "<TH width=6%><a href='$ML_SCRIPT?view=7&sort=$pn'>$SKILLLIST[$p]</a></TH>";
							}
						
						?>
					</TR>
					
					
					<TR class="list_10">
						<!-- class -->
					  <TH width="10%" rowspan=2><A href="<?php echo "$ML_SCRIPT?view=7&sort=1"; ?>"><?php echo "$INDEX[1]"; ?></A></TH>
						<?php
							//skill col4
							for ($p=27;$p<30;$p++){
								$pn=$p+36;
								echo "<TH width=6%><a href='$ML_SCRIPT?view=7&sort=$pn'>$SKILLLIST[$p]</a></TH>";
							}
						
						?>
						<TH></TH><TH></TH><TH></TH><TH></TH><TH></TH><TH></TH>
					</TR>
					
					<TR class="list_10">
						<TH width="3%"><A href="<?php echo "$ML_SCRIPT?view=7&sort=6"; ?>"><?php echo "$INDEX[4]"; ?></A></TH>
                        <TH width="3%"><A href="<?php echo "$ML_SCRIPT?view=7&sort=3"; ?>"><?php echo "$INDEX[3]"; ?></A></TH>
						<TH width="3%"><A href="<?php echo "$ML_SCRIPT?view=7&sort=4"; ?>"><?php echo "$INDEX[11]"; ?></A></TH>
						<TH width="3%"><A href="<?php echo "$ML_SCRIPT?view=7&sort=7"; ?>"><?php echo "$INDEX[10]"; ?></A></TH>
						<TH width="3%"><A href="<?php echo "$ML_SCRIPT?view=7&sort=8"; ?>"><?php echo "$INDEX[6]"; ?></A></TH>
						<TH width="3%"><A href="<?php echo "$ML_SCRIPT?view=7&sort=2"; ?>"><?php echo "$INDEX[5]"; ?></A></TH>
						<TH></TH><TH></TH><TH></TH>
					</TR>
					
					<TR class="list_10">
						<TD colspan="10"><B>[<A href="<?php echo "$ML_SCRIPT?view=7&sort=9"; ?>"><?php echo "$INDEX[8]"; ?></A>] <?php echo "$INDEX[9]"; ?></B></TD>
					</TR>
					<TR>
                        <TD class="spaceRow" colspan="11" height="1"><IMG src="../img/spacer.gif" alt="" width="1" height="1"></TD>
                      </TR>
					
					  <?php
					  //die();
					//sort
					$sql = sql_sort();
					$result = $db->query($sql);
					
					//データをwhileで回す
					while( $main_rows = $result->fetchRow(DB_FETCHMODE_ASSOC) ){
					
						$main_rows = convert_to_sjis($main_rows);
					
						$user_no = $main_rows["no"];
						$user_uid = $main_rows["uid"];
						$user_name = $main_rows["name"];
						$user_faceid = $main_rows["face"];
						$user_pass = $main_rows["pass"];
						$user_class = $main_rows["class"];
						$user_race = $main_rows["race"];
						$user_anon = $main_rows["anon"];
						
						$user_mainjob = $main_rows["mainjob"];
						$user_supportjob = $main_rows["supportjob"];
						$user_comment = $main_rows["comment"];
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
						$user_lev[17] = $main_rows["lev17"];
						
						$user_skl[0] = $main_rows["skl0"];
						$user_skl[1] = $main_rows["skl1"];
						$user_skl[2] = $main_rows["skl2"];
						$user_skl[3] = $main_rows["skl3"];
						$user_skl[4] = $main_rows["skl4"];
						$user_skl[5] = $main_rows["skl5"];
						$user_skl[6] = $main_rows["skl6"];
						$user_skl[7] = $main_rows["skl7"];
						$user_skl[8] = $main_rows["skl8"];
						$user_skl[9] = $main_rows["skl9"];
						$user_skl[10]= $main_rows["skl10"];
						$user_skl[11] = $main_rows["skl11"];
						$user_skl[12] = $main_rows["skl12"];
						$user_skl[13] = $main_rows["skl13"];
						$user_skl[14] = $main_rows["skl14"];
						$user_skl[15] = $main_rows["skl15"];
						$user_skl[16] = $main_rows["skl16"];
						$user_skl[17] = $main_rows["skl17"];
						$user_skl[18] = $main_rows["skl18"];
						$user_skl[19] = $main_rows["skl19"];
						$user_skl[20] = $main_rows["skl20"];
						$user_skl[21] = $main_rows["skl21"];
						$user_skl[22] = $main_rows["skl22"];
						$user_skl[23] = $main_rows["skl23"];
						$user_skl[24] = $main_rows["skl24"];
						$user_skl[25] = $main_rows["skl25"];
						$user_skl[26] = $main_rows["skl26"];
						$user_skl[27] = $main_rows["skl27"];
						$user_skl[28] = $main_rows["skl28"];
						$user_skl[29] = $main_rows["skl29"];
						$user_skl[30] = $main_rows["skl30"];
						$user_skl[31] = $main_rows["skl31"];
						$user_skl[32] = $main_rows["skl32"];
						
						$user_prod[0] = $main_rows["prod0"];
						$user_prod[1] = $main_rows["prod1"];
						$user_prod[2] = $main_rows["prod2"];
						$user_prod[3] = $main_rows["prod3"];
						$user_prod[4] = $main_rows["prod4"];
						$user_prod[5] = $main_rows["prod5"];
						$user_prod[6] = $main_rows["prod6"];
						$user_prod[7] = $main_rows["prod7"];
						$user_prod[8] = $main_rows["prod8"];
						$user_prod[9] = $main_rows["prod9"];
						$user_prod[10] = $main_rows["prod10"];
						$user_prod[11] = $main_rows["prod11"];
						$user_prod[12] = $main_rows["prod12"];
						$user_prod[13] = $main_rows["prod13"];
						$user_prod[14] = $main_rows["prod14"];
						$user_prod[15] = $main_rows["prod15"];
						$user_prod[16] = $main_rows["prod16"];

						$user_race = $main_rows["race"];
						$user_face = $main_rows["face"];
						$user_size = $main_rows["size"];
						$user_relm = $main_rows["relm"];
						$user_point = $main_rows["point"];
						$user_mrank = $main_rows["mrank"];

						$user_date = $main_rows["date"];
						$date = gmdate("y/m/d", $user_date+9*60*60);
						
						$user_comment = htmlspecialchars($user_comment);
						
						//自分の行だったときは行カラー変更
						if($uid == $user_uid){
							$row_c = 2;
						}else{
							$row_c = 1;
						}
						
						//クラスが未入力の場合は空白
						if($user_class == ""){
							$user_class="&nbsp";
						}
						
						//ジョブ関係
						if($user_anon == 0 || $env_anon_mode != 1){
							if($user_lev[$user_mainjob]<10){
								$main_level="0$user_lev[$user_mainjob]";
							} else {
								$main_level=$user_lev[$user_mainjob];
							}
	
							if($user_supportjob != 99){
								$mod_level=intval($user_lev[$user_mainjob]/2);
								
								if($mod_level==0){
									$mod_level=1;
								}
								
								if($mod_level>$user_lev[$user_supportjob]){
									$mod_level=$user_lev[$user_supportjob];
								}
								
								if($mod_level<10){
									$mod_level="0$mod_level";
								}
	
								$job_str = "$JOBLIST[$user_mainjob]$main_level/$JOBLIST[$user_supportjob]$mod_level";
	
							} else{
								$job_str = "$JOBLIST[$user_mainjob]$main_level";
							}
	
						}else{
							$job_str = "-";
						}
						
						//アイコンのALT
						$pos = strrpos($user_faceid,"b");
						if($pos){
							$user_face2 = substr($user_faceid,0,$pos);
							$user_face_name = "$FACENAME[$user_face2] B";
						}else{
							$user_face_name = "$FACENAME[$user_faceid] A";
						}
						
						$diary = time_chk_diary($user_uid);
						$bazaar_icon = chk_bazaar($user_uid);
						
						?>
						
											
					<TR class="list_10">
						<!-- no -->
                        <TD rowspan=9 align="center" class="row<?php echo $row_c; ?>"><?php echo $user_no; ?></TD>
						
						<!-- name -->
                        <TD rowspan=4 align="center" class="row<?php echo $row_c; ?>"><A href="<?php echo"$PLOF_SCRIPT?name=$user_name"; ?>"><?php echo "$user_name</A>&nbsp;$diary$bazaar_icon"; ?></TD>
						
						<?php
						//JOB col1
							for ($j=0;$j<9;$j++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								
									if($user_lev[$j] >= 100){
										$bg=" class='color9'";
									}else{
										$bg_tmp = intval($user_lev[$j]/10);
										$bg=" class='color$bg_tmp'";
									}
		
								echo "<TD$bg>$user_lev[$j]</TD>";
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						?>
						
						
						
					</TR>
					
					
					<TR class="list_10">
						<?php
						//JOB col2
							for ($j=9;$j<18;$j++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
									if($user_lev[$j] >= 100){
										$bg=" class='color9'";
									}else{
										$bg_tmp = intval($user_lev[$j]/10);
										$bg=" class='color$bg_tmp'";
									}
		
								echo "<TD$bg>$user_lev[$j]</TD>";
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						?>
					</TR>
					
					
					<TR class="list_10">
						<?php
						//item
							for ($p=0;$p<$PROD_MAX;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_prod[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_prod[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_prod[$p]){$user_prod[$p] = "-";}
								echo "<TD$bg>$user_prod[$p]</TD>";
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						
						?>
					</TR>
					
					
					<TR class="list_10">
						<?php
							//skill col1
							for ($p=0;$p<9;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_skl[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_skl[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_skl[$p]){$user_skl[$p] = "-";}
								echo "<TD$bg>$user_skl[$p]</TD>";
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						
						?>
					</TR>
					
					
					<TR class="list_10">
						<!-- job_Str -->
						<TD rowspan=2 align="center" class="row<?php echo $row_c; ?>"><?php echo $job_str; ?></TD>
						<?php
							//skill col2
							for ($p=9;$p<18;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_skl[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_skl[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_skl[$p]){$user_skl[$p] = "-";}
								echo "<TD$bg>$user_skl[$p]</TD>";
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						?>
					</TR>
					
					
					<TR class="list_10">
						<?php
							//skill col3
							for ($p=18;$p<27;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_skl[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_skl[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_skl[$p]){$user_skl[$p] = "-";}
								echo "<TD$bg>$user_skl[$p]</TD>";
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						
						?>
					</TR>
					
					
					<TR class="list_10">
						<!-- class -->
					  <TD rowspan=2 align="center" class="row<?php echo $row_c; ?>"><?php echo"$user_class"; ?></TD>
						<?php
							//skill col4
							for ($p=27;$p<30;$p++){
								if($user_anon == 0 || $env_anon_mode != 1){
								$bg="";
								if($user_skl[$p] >= 100){
									$bg=" class='color9'";
								}else{
									$bg_tmp = intval($user_skl[$p]/10);
									$bg=" class='color$bg_tmp'";
								}
								if(!$user_skl[$p]){$user_skl[$p] = "-";}
								echo "<TD$bg>$user_skl[$p]</TD>";
								}else{
									echo "<TD class='color0'>-</TD>";
								}
							}
						
						?>
						<TD></TD><TD></TD><TD></TD><TD></TD><TD></TD><TD></TD>
					</TR>
					
					<TR class="list_10">
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$RELMLIST[$user_relm]"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$RACELIST[$user_race]"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$user_face_name"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$SIZELIST[$user_size]"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$user_mrank"; ?></TD>
						<TD align="center" class="row<?php echo $row_c; ?>"><?php echo"$user_point"; ?></TD>
						<TD></TD><TD></TD><TD></TD>
					</TR>
					
					<TR class="list_10">
						<TD colspan="10" class="row<?php echo $row_c; ?>">[<?php echo $date; ?>] <?php echo "$user_comment"; ?></TD>
					</TR>
					<TR>
                        <TD class="spaceRow" colspan="11" height="1"><IMG src="../img/spacer.gif" alt="" width="1" height="1"></TD>
                      </TR>
					  <?php
					
					}
	
	
	}
	
	?>
                    </TBODY>
                  </TABLE><?php


}


?>