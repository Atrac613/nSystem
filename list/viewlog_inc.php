<?php
/*
function load_env(){
	global $db;
	
  $sql = "select * from `MEMBER_LIST_ENV`";
  $result = $db->query($sql);
	if (DB::isError($result)) {
    	trigger_error($result->getMessage(), E_USER_ERROR);
	}
	$env_rows = $result->fetchRow(DB_FETCHMODE_ASSOC);
	return $env_rows;

}*/

function table_chk(){
	global $env_rows,$uid;
	
	
	$default_table = $env_rows["default_table"];
	$table = $_GET["view"];
	if(!$table){
		$table = $_POST["view"];
	}
	if($table == ""){
		$table = $default_table;
	}
	
	if($table=="0"){
		table_ultra_simple($uid);
	} elseif($table=="1") {
		table_simple($uid);
	} elseif($table=="2") {
		table_job($uid);
	} elseif($table=="3") {
		table_job($uid);
	} elseif($table=="4") {
		table_prod_item($uid);
	} elseif($table=="5") {
		table_prod_skill($uid);
	} elseif($table=="6") {
		table_stat($uid);
	} elseif($table=="7") {
		table_full($uid);
	} else {
		table_ultra_simple($uid);
	}
}

function log_sql_sort($uid){
	//global $sql;
	$sort = $_GET["sort"];
	if(!$sort){
		$sort = $_POST["sort"];
	}
	if(!$sort){
		$sort = "9";
	}
	
	$p_mode = $_POST["p_mode"];
	
	$p_limit = $_POST["p_limit"];
	if(!$p_limit){
		$p_limit = $_GET["p_limit"];
	}
	
	if(!$p_limit){
		$p_limit = "30";
	}
	
	$p_offset = $_POST["p_offset"];
	if(!$p_offset){
		$p_offset = $_GET["p_offset"];
	}
	if(!$p_offset){
		$p_offset = "0";
	}
	
	if($p_mode == " prev "){
		if($p_offset >= 0){
			$p_offset = $p_offset - $p_limit;
		}else{
			$p_offset = "0";
		}
	}elseif($p_mode == " next "){
		if($p_offset >= 0){
			$p_offset = $p_offset + $p_limit;
		}else{
			$p_offset = "0";
		}
	}else{
	
	}
	
	if($p_offset < 0){
		$p_offset = "0";
	}
	
	if($sort==""){$sort = 0;}
	if($sort > 6){$desc = " desc";}
	
	$sql = "select * from LOG_USER_DATA , LOG_USER_STA , LOG_USER_PLOF , LOG_USER_LEV , LOG_USER_PROD , LOG_USER_SKL , LOG_USER_IP where LOG_USER_DATA.id = LOG_USER_STA.id and LOG_USER_DATA.id = LOG_USER_PLOF.id and LOG_USER_DATA.id = LOG_USER_LEV.id and LOG_USER_DATA.id = LOG_USER_PROD.id AND LOG_USER_DATA.id = LOG_USER_SKL.id AND LOG_USER_DATA.id = LOG_USER_IP.id AND LOG_USER_DATA.uid = '$uid' order by ";
	
	$sort_a = array('no','class','name','race','face','size','relm','point','mrank','date','lev0', 'lev1', 'lev2', 'lev3', 'lev4', 'lev5', 'lev6', 'lev7', 'lev8', 'lev9', 'lev10','lev11', 'lev12', 'lev13', 'lev14', 'lev15', 'lev16', 'prod0', 'prod1', 'prod2', 'prod3', 'prod4', 'prod5', 'prod6','prod7', 'prod8', 'prod9', 'prod10', 'prod11', 'prod12', 'prod13', 'prod14', 'prod15', 'prod16','skl0','skl1','skl2','skl3','skl4','skl5','skl6','skl7','skl8','skl9','skl10','skl11','skl12','skl13','skl14','skl15','skl16','skl17','skl18','skl19','skl20','skl21','skl22','skl23','skl24','skl25','skl26','skl27','skl28','skl29','skl30','skl31','skl32','id');
	
	$sql = $sql.$sort_a[$sort].$desc." limit ".$p_offset." , ".$p_limit;
	//var_dump($sql);
	return $sql;

}

function time_chk_diary($uid){
	global $db;
	
	$diarygif = "diary.gif";
	$upgif = "up_s050.gif";
	
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
		
		$diary = "<img src='../img/$diarygif' width=15 height=15>";
		
		if( $days < 1 ){
			$diary.="<img src='../img/$upgif' width=13 heigh=9>";
		}
	}
	
	return $diary;
}

function chk_bazaar($uid){
	global $db;
	
	$bazaar = "bazaar.png";
	
	$sql = "select * from `BAZAAR` where `uid` = '$uid'";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		trigger_error($result->getMessage(), E_USER_ERROR);
	}
	
	$bazaar_rows = $result->numRows();
	
	
	if($bazaar_rows != 0){	
		$bazaar_icon = "<img src='../img/$bazaar' width=15 height=15>";
	}
	
	return $bazaar_icon;
}

?>