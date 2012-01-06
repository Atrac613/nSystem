<?php


function load_env(){
	global $db;
	
  $sql = "select * from `MEMBER_LIST_ENV`";
  $result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$env_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	return $env_rows;

}

function table_chk(){
	global $env_rows;
	
	$default_table = $env_rows["default_table"];
	$info_title = $env_rows["info_title"];
	$info_body = $env_rows["info_body"];
	$info_body = str_replace("\n", "<br>", $info_body);
	
	$table = $_GET["view"];
	if($table == ""){
		$table = $default_table;
	}
	
	if($info_title){
		echo "<table border=\"0\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\" class=\"forumline\"><TR class=\"table_title\"><TH>$info_title</TH></TR><TR class=\"color2\"><TD>$info_body</TD></TR></table><BR>";
	}

	
	if($table=="0"){
		table_ultra_simple();
	} elseif($table=="1") {
		table_simple();
	} elseif($table=="2") {
		table_job();
	} elseif($table=="3") {
		table_job();
	} elseif($table=="4") {
		table_prod_item();
	} elseif($table=="5") {
		table_prod_skill();
	} elseif($table=="6") {
		table_stat();
	} elseif($table=="7") {
		table_full();
	} else {
		table_ultra_simple();
	}
}

function sql_sort(){
	//global $sql;
	$sort = $_GET["sort"];
	if($sort==""){$sort = 0;}
	if($sort > 6){$desc = " desc";}
	
	$sql = "select * from USER_DATA , USER_STA , USER_LEV , USER_PROD , USER_SKL , USER_IP where USER_DATA.uid = USER_STA.uid and USER_DATA.uid = USER_LEV.uid and USER_DATA.uid = USER_PROD.uid AND USER_DATA.uid = USER_SKL.uid AND USER_DATA.uid = USER_IP.uid order by ";
	
	$sort_a = array('no','class','name','race','face','size','relm','point','mrank','date','lev0', 'lev1', 'lev2', 'lev3', 'lev4', 'lev5', 'lev6', 'lev7', 'lev8', 'lev9', 'lev10','lev11', 'lev12', 'lev13', 'lev14', 'lev15', 'lev16', 'prod0', 'prod1', 'prod2', 'prod3', 'prod4', 'prod5', 'prod6','prod7', 'prod8', 'prod9', 'prod10', 'prod11', 'prod12', 'prod13', 'prod14', 'prod15', 'prod16','skl0','skl1','skl2','skl3','skl4','skl5','skl6','skl7','skl8','skl9','skl10','skl11','skl12','skl13','skl14','skl15','skl16','skl17','skl18','skl19','skl20','skl21','skl22','skl23','skl24','skl25','skl26','skl27','skl28','skl29','skl30','skl31','skl32');
	
	$sql = $sql.$sort_a[$sort].$desc;
	//echo "$sort $sql";
	return $sql;

}

function time_chk_diary($uid){
	global $db;
	
	$diarygif = "diary.gif";
	$upgif = "diary_up.gif";
	
	$sql = "select * from `LASTDATE_DIARY` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$di_row = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$max_date = $di_row["date"];
	
	if($max_date){
	
		$local_date = time();

		$days = intval(($local_date - $max_date) / 86400);
		
		$diary = "<img src='img/$diarygif' width=15 height=15>";
		
		if( $days < 1 ){
			$diary.="<img src='img/$upgif' width=13 heigh=9>";
		}
	}
	
	return $diary;
}

function chk_bazaar($uid){
	global $db;
	
	$bazaar = "bazaar.gif";
	
	$sql = "select * from `BAZAAR` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$bazaar_rows = $result->numRows();
	
	
	if($bazaar_rows != 0){	
		$bazaar_icon = "<img src='img/$bazaar' width=15 height=15>";
	}
	
	return $bazaar_icon;
}


?>