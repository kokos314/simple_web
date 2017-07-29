<?php
	$GLOBALS['layout']=false;
	
	//Theme::GetTheme();
	
	$arr = array();
	$d = dir('css/theme/first/');
	while (false !== ($entry = $d->read())) {
		$path = 'css/theme/first/'.$entry;
		
		if( is_file($path) && pathinfo($path, PATHINFO_EXTENSION)=='css' ){
			//echo $entry."<br>\n";
			$arr[]=$path;
		}
	}
	$d->close();
	
	header("Content-Type: text/css");
	
	foreach ($arr as $val){
		$str = file_get_contents($val);
		echo $str;
		echo "/* $val */\r\n";
	}
?>