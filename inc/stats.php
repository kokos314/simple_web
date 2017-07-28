<?php


	$bots_array = array(
		'bot',
		'spider',
		'Agent',
		'indexer',
		'crawler',
		'wget',
		'analyzer',
		'java',
		'curl',
		'slurp',
		'python',
		'ruby',
		'apache',
		'scrapy',
		'robozilla',
		'wordPress',
		'archiver',
	);

	$str_bots = join('|', $bots_array);
	if( preg_match( "/($str_bots)/i", $_SERVER['HTTP_USER_AGENT'] ) && !preg_match( '/(google)/i', $_SERVER['HTTP_USER_AGENT'] ) ){
	    $GLOBALS['adsens_banner']=FALSE;
	}
	
	
	
	$_cnt_v='-1';
	/*$_cnt_v='1';
	$_cnt_v = isset($_COOKIE['cnt_v']) ? $_COOKIE['cnt_v'] : $_cnt_v;
	
	if( !setcookie("cnt_v", $_cnt_v+1, time()+(3600*24*180)) ){
		$_cnt_v='-1';		
	}*/
	
	
	
	$_SERVER['HTTP_REFERER'] = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
	
	$version_app = isset($_GET['v']) ? $_GET['v'] : NULL;
	$refid = isset($_GET['refid']) ? $_GET['refid'] : NULL; 
  	/*$fp = fopen('data.txt', 'a');
	fwrite($fp, 
		date("Y-m-d H:i:s") . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_SERVER['HTTP_USER_AGENT'] . "\t" . 
		$_SERVER['HTTP_REFERER'] . "\t" . $refid . "\t" . $_cnt_v . "\t" . $_SERVER['HTTP_HOST'] .
		"\t" . $module . "\t" . $action . "\t" . $_SERVER['SCRIPT_URI'] . '?'. $_SERVER['QUERY_STRING'] .
		"\t" . $version_app .
		"\r\n" );
	fclose($fp);*/
	
	
	//echo $path_to_file;
//###########################################
	if( !(isset( $GLOBALS['stat_off'] ) && $GLOBALS['stat_off']===TRUE) ){
		$ip=$_SERVER['REMOTE_ADDR'];
		$user_agent = $_SERVER['HTTP_USER_AGENT']!='' ? "'".pg_escape_string( $_SERVER['HTTP_USER_AGENT'] )."'" : 'NULL';
		$country_code='NULL';
		$access_url=$_SERVER['SCRIPT_URI'] . '?'. $_SERVER['QUERY_STRING'];
		
		$http_referer = $_SERVER['HTTP_REFERER']!='' ? "'".pg_escape_string( $_SERVER['HTTP_REFERER'] )."'" : 'NULL';
		$refid = $refid!='' ? "'".pg_escape_string( $refid )."'" : 'NULL';
	    $cnt = $_cnt_v;
	    $http_host = $_SERVER['HTTP_HOST']!='' ? "'".pg_escape_string( $_SERVER['HTTP_HOST'] )."'" : 'NULL';
	    $stat_module = $module!='' ? "'".pg_escape_string( $module )."'" : 'NULL';
	    $stat_action = $action!='' ? "'".pg_escape_string( $action )."'" : 'NULL';
	    $access_url = $access_url!='' ? "'".pg_escape_string( $access_url )."'" : 'NULL';
	    $version_app = $version_app!='' ? "'".pg_escape_string( $version_app )."'" : 'NULL';
		
		$sql = "
				INSERT INTO developer.statistic(date_insert, ip, user_agent, http_referer, refid, cnt, country_code, http_host,
					module, action, access_url, version_app
				)
		    	VALUES (now(), '$ip', $user_agent, $http_referer, $refid, $cnt, $country_code, $http_host,
		    		$stat_module, $stat_action, $access_url, $version_app
		    	);
			";
		pgsql::getInstance()->query($sql);	
	}
	//var_dump($GLOBALS['stat_off']); 
	
	
//###########################################


?>