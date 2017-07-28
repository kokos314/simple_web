<?php
	//include '503.php';die();
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	session_set_cookie_params(3600, '/');
	session_start();
	
	//------------------------------------
	@include './inc/arr_blacklist.inc.php';
	if( isset($arr_blacklist) ){
		$ip = $_SERVER['REMOTE_ADDR'];
		
		foreach( $arr_blacklist as $val ){
			if( preg_match("/^$val$/i", $ip, $matches) ){
				die();
			}
		}
		
	}
	//------------------------------------
	
	$start_time = microtime(true);
	require_once './config/config.php';
	srand(); 
	
	$stat_update = Statistics::Update();
	if( $stat_update ){
		die();
	}
	
	Ctrl::RedirectToNonWWW();
	I18N::GetArrayOfShortCodeIfNotSet();
	I18N::SetLangFromDomain();
	I18N::RedirectIfDefault();
	I18N::HttpHeader();
	I18N::Load();
	I18N::SetFileLog('./i18n_log.txt');
	Ctrl::GetValFromRequest();
	Ctrl::SetEscapeDefaultMethod('pgsql');
	Form::SetEventMessage('I18N::T');
	Theme::SetTheme('first');
	
	require_once 'config_ctrl.inc.php';
	
	$module = Ctrl::M();
	$action = Ctrl::A();
	
	$path_to_file = './modules/' . $module . '/' . $action.'.php';
	if( !file_exists($path_to_file) ){
		Statistics::Save('404');
		header("HTTP/1.0 404 Not Found");
		include_once '404.php';
		return;
	}
	if( !isset($_SESSION['FIRST_RUN'] ) ){
		$_SESSION['FIRST_RUN'] = time();
	}
	/*
	if( User::GetId()=='' ){
		User::SignIn(D_DEFAULT_USER_LOGIN, D_DEFAULT_USER_PASSWORD);
		User::SetData(true);
		Access::getInstance()->CreateArray(User::GetId());
	}
	if( !Access::getInstance()->CheckAccess(User::GetId(), $module, $action) ){
		if( $module=='user' && $action=='login' ){
			session_unset();
			session_destroy();
			die("No access: $module : $action" );
		}
		if( Ctrl::GV('debug')=='1' ){
			Debug::VarDumb($_SESSION);
		}else{
			if( Ctrl::IsGet() ){
				$url = base64_encode( "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
				Ctrl::Redirect('user', 'login', array('url'=>$url));
			}else{
				Ctrl::Redirect('user', 'login');
			}
		}
		die("No access: $module : $action");
	}*/
	
	$content_data = get_partial($path_to_file);
	
	$is_layout = Ctrl::IsLayout();
	if( $is_layout!==false && !$stat_update ) Statistics::Save();
	
	$param = array(
			'content_data' => $content_data,
			'module'=>$module,
			'action'=>$action,
	);
	
	if( $is_layout===false ){
		$html = $content_data;
	}else{
		$html = Ctrl::GetLayoutHTML($param);
	}
	
	$end_time = number_format( microtime(true) - $start_time, 4).'s';
	$mem_usage = Misc::human_size( memory_get_usage() );
	$mem_peak_usage = Misc::human_size( memory_get_peak_usage() );
	$page_size = Misc::human_size( strlen($html) );
	$cnt_included_files = count( get_included_files() );
	$html = str_replace('#time_load#', $end_time, $html);
	$html = str_replace('#mem_usage#', $mem_usage, $html);
	$html = str_replace('#mem_peak_usage#', $mem_peak_usage, $html);
	$html = str_replace('#page_size#', $page_size, $html);
	$html = str_replace('#cnt_included_files#', $cnt_included_files, $html);
	
	
	echo $html;
?>