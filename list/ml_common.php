<?php

$ML_SCRIPT = "memberlist.php";
$PLOF_SCRIPT = "profile.php";
$USR_SCRIPT = "user.php";
$DIARY_SCRIPT= "diary.php";
$BAZ_SCRIPT= "bazaar.php";
$POST_SCRIPT= "post.php";

require_once "../mognet_common.php";

$INDEX = array('No.','class','name','race','realm','point','m_rank','job','date','comment','size','face','IP');
$MENU = array('Default','Simple','Job','Job(Lv)','Skill(1)','Skill(2)','Status','Full');
$JOBLIST= array('WAR','MNK','WHM','BLM','RDM','THF','PLD','DRK','RNG','BRD','BST','DRG','SMN','NIN','SAM','BLU','COR','PUP');
$FACENAME = array('モーグリ','エアハルト','ラファール','アレリオ','ジロー','ベイド','アザグバ','バートラム','エンケイ','ミア','モルガナ','エルウィン','キラ','アデラ','シェンメイ','エピオ','アウリス','ロテール','レンゲル','ルピエ','ロモン','シヴリオン','ボリアン','アジュノー','エテルミオン','エリエル','アルミード','エリーヴ','マリヨン','オペル','メノエ','アペシア','ヴェイラ','パンチャミンチャ','トンソルモーソル','アロバオロバ','クリドグリード','パイタマイタ','ジャマカソマカ','ヨランオーラン','プルダパゴッパ','メーリリ','パルル','ノッピピ','モココ','カタルル','アジョジョ','ポレレ','コットト','オピ','ペル','シャミ','スー','ユーン','キャル','ミオ','ソイ','ベアクロー','クレイジーアーム','ボーンイーター','スネークアイ','ドランガ','ジードフ','グランドウ','ガルムード');
$RACELIST = array('Hume♂','Hume♀','Elvaan♂','Elvaan♀','Tarutaru♂','Tarutaru♀','Mithra♀','Galka♂');
$RELMLIST= array("Sand'Oria",'Bastok','Windurst');
$SIZELIST = array('S','M','L');
$COLOR = array('#ECFFEC','#E2FED4','#DBFCBB','#CEFBA2','#C4F98A','#BAF871','#B0F658','#A6F540','#9CF327','#91F10E');
$JOB_MAX=18;
$PROD_MAX=9;


$PLODLIST= array('鍛冶','裁縫','錬金','木工','彫金','革細工','骨工','釣り','調理');
$SKILLLIST= array('格闘','短剣','片手剣','両手剣','片手斧','両手斧','両手鎌','両手槍','片手刀','両手刀','片手棍','両手棍','弓術','射撃','投擲','ガード','回避','盾','受け流し','神聖魔法','回復魔法','強化魔法','弱体魔法','精霊魔法','暗黒魔法','歌唱','弦楽器','管楽器','忍術','召喚魔法');

$putdir = "./img/";
$sam_dir = "./imgs/";

$arrowext = array('jpg','png','gif');
$limitk	= 3072;		//アップロード制限（KB キロバイト）

$W = 200;
$H = 150;
$image_type = "1";//0=png 1=jpg

?>