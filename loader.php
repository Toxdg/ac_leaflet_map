<?php

 function load_modules($dir){
	//$dir = 'functions';
	 $dir = __DIR__.$dir;
	$files = scandir($dir);
	$extensions = array("php");
	$tab_plikow = array();
	foreach ($files as $filename) {
		$filepath = $dir.'/'.$filename;
		if(is_file($filepath)) {
			$ext = getFileExtension($filename);			
			if (in_array($ext,$extensions)) {
				include($dir.'/'.$filename);
			}
		}
	}
}

function getFileExtension($filename){
	$path_info = pathinfo($filename);
	return $path_info['extension'];
}