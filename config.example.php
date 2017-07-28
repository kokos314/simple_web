<?php
	define('D_SERVER', 'geek.adachsoft.com');
	define('D_SERVER_URL', 'http://'.D_SERVER.'/');
	define('D_EMAIL_ADMIN', '');
	define('D_EMAIL_CONTACT', '');
	define('D_PATH_CACHE', './cache/');
	define('D_PATH_IMG_TMP', './img/tmp/');
	define('D_PATH_IMG', './img/uploaded/');
	define('D_PATH_IMG_THUMB', './img/uploaded/thumb/');
	define('D_PATH_IMG_SOURCE', './img/source/');
	
	define('D_TIME_CACHE', 60*15); //w sekundach
	define('D_EXPIRATION_TIME_FORM', 60*60*5); //w sekundach
	define('D_DEFAULT_USER_LOGIN', 'default');
	define('D_DEFAULT_USER_PASSWORD', 'default');
	define('D_DEFAULT_MODULE', 'home');
	define('D_DEFAULT_ACTION', 'index');
	define('D_DEFAULT_LANGUAGE', 'EN');
	define('D_NAME_OF_THE_PORTAL', 'GEEK');
	define('D_NAME_OF_THE_PORTAL_URL', 'GEEK.ADACHSOFT.COM');
	define('D_TITLE_OF_THE_PORTAL', '');
	define('D_IMG_ALT', D_NAME_OF_THE_PORTAL_URL.' - '.D_TITLE_OF_THE_PORTAL);
	define('D_ROLE_USER', 4);
	define('D_USER_UNLOGGED', 2);
	define('D_DB_DATABASE', '');
	define('D_DB_USER', '');
	define('D_DB_PASSWORD', '');
	define('D_DB_HOST', '');
	define('D_DB_PORT', '');
	require_once '../inc/function/system.functions.php';
	
	
	
	pgsql::getInstance()->connect(D_DB_DATABASE, D_DB_USER, D_DB_PASSWORD, D_DB_HOST, D_DB_PORT);
	
	CreateSettings::CreateIf();
?>