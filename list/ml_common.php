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
$FACENAME = array('���[�O��','�G�A�n���g','���t�@�[��','�A�����I','�W���[','�x�C�h','�A�U�O�o','�o�[�g����','�G���P�C','�~�A','�����K�i','�G���E�B��','�L��','�A�f��','�V�F�����C','�G�s�I','�A�E���X','���e�[��','�����Q��','���s�G','������','�V�����I��','�{���A��','�A�W���m�[','�G�e���~�I��','�G���G��','�A���~�[�h','�G���[��','�}������','�I�y��','���m�G','�A�y�V�A','���F�C��','�p���`���~���`��','�g���\�����[�\��','�A���o�I���o','�N���h�O���[�h','�p�C�^�}�C�^','�W���}�J�\�}�J','�������I�[����','�v���_�p�S�b�p','���[����','�p����','�m�b�s�s','���R�R','�J�^����','�A�W���W��','�|����','�R�b�g�g','�I�s','�y��','�V���~','�X�[','���[��','�L����','�~�I','�\�C','�x�A�N���[','�N���C�W�[�A�[��','�{�[���C�[�^�[','�X�l�[�N�A�C','�h�����K','�W�[�h�t','�O�����h�E','�K�����[�h');
$RACELIST = array('Hume��','Hume��','Elvaan��','Elvaan��','Tarutaru��','Tarutaru��','Mithra��','Galka��');
$RELMLIST= array("Sand'Oria",'Bastok','Windurst');
$SIZELIST = array('S','M','L');
$COLOR = array('#ECFFEC','#E2FED4','#DBFCBB','#CEFBA2','#C4F98A','#BAF871','#B0F658','#A6F540','#9CF327','#91F10E');
$JOB_MAX=18;
$PROD_MAX=9;


$PLODLIST= array('�b��','�ٖD','�B��','�؍H','����','�v�׍H','���H','�ނ�','����');
$SKILLLIST= array('�i��','�Z��','�Ў茕','���茕','�Ў蕀','���蕀','���芙','���葄','�Ў蓁','���蓁','�Ў螞','���螞','�|�p','�ˌ�','����','�K�[�h','���','��','�󂯗���','�_�����@','�񕜖��@','�������@','��̖��@','���얂�@','�Í����@','�̏�','���y��','�Ǌy��','�E�p','�������@');

$putdir = "./img/";
$sam_dir = "./imgs/";

$arrowext = array('jpg','png','gif');
$limitk	= 3072;		//�A�b�v���[�h�����iKB �L���o�C�g�j

$W = 200;
$H = 150;
$image_type = "1";//0=png 1=jpg

?>