<?php

$dir="../";

function fol($folder){
	global $dir;
	$folder = $dir."$folder";
	if(file_exists($folder)){
		echo "$folder is alive.<BR>";
		
		$val1 = fileowner($folder);
		$val2 = filegroup($folder);
		$val3 = fileperms($folder);
		
		echo "owner = $val1 , group = $val2 <BR>";
		printf("perms = %o <BR>",$val3);
		
		if($val3 != 16895){
			//$val4 = chmod($folder,0777);
			echo "<B>!! chmod($folder) !!</B><BR>";
		}
		
	}else{
		echo "$folder is die.<BR>";
	}
	echo "<BR>";
}


fol("album");

//fol("bp");
fol("bp/img");
fol("bp/imgs");

fol("css");

//fol("forum/");
fol("forum/dat");
fol("forum/dat/tmp");

fol("img");

//fol("list");
fol("list/img");
fol("list/imgs");






?>