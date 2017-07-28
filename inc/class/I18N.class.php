<?php

class I18N{
	//private static $short_code_array;
	private static $default_lang_id = 1;
	private static $true_short_code_from_domain = NULL;
	private static $trans_lang = array();
	private static $file_log;
	
	public static function SetFileLog($file_log){
		self::$file_log = $file_log;
	}
	
	public static function ReadFileLog(){
		if( self::$file_log!='' && file_exists(self::$file_log) ){
			return file_get_contents(self::$file_log);
		}
		
		return NULL;
	}
	
	public static function HtmlHeaderLang(){
		$sql = "SELECT *, lower(short_code) AS sc FROM languages";
		$res = pgsql::getInstance()->cq($sql);
		foreach( $res as $val_lang ):
			$url = $val_lang['languages_id']==self::$default_lang_id ?
				'http://'.D_SERVER.$_SERVER['REQUEST_URI'] :
				'http://'.$val_lang['sc'].'.'.D_SERVER.$_SERVER['REQUEST_URI'];
			echo "\t\t"; ?><link rel="alternate" hreflang="<?php echo $val_lang['sc']; ?>" href="<?php echo $url; ?>" /><?php
			echo "\r\n";
		endforeach;
		//if( self::GetLangId()==self::$default_lang_id ):
			$url = 'http://'.D_SERVER.$_SERVER['REQUEST_URI'];
			echo "\t\t";
			?><link rel="alternate" href="<?php echo $url; ?>" hreflang="x-default" /><?php
			echo "\r\n";
		//endif;
	}
	
	public static function RedirectIfDefault(){
		if( self::$true_short_code_from_domain=='pl' ){
			$url = 'http://'.D_SERVER.$_SERVER['REQUEST_URI'];
			// 301 Moved Permanently
			header("Location: $url", TRUE, 301);
			exit();
		}
	}
	
	public static function GetTrueShortCodeFromDomain(){
		return self::$true_short_code_from_domain;
	}
	
	public static function GetDefaultLangId(){
		return self::$default_lang_id;
	}
	
	public static function GetLangId(){
		return isset($_SESSION["LangId"]) ? $_SESSION["LangId"] : self::$default_lang_id;
	}
	
	public static function GetArrayOfShortCodes(){
		return array_flip($_SESSION['LangArrayOfShortCode']);
	}
	
	public static function GetShortCode(){
		return array_search(self::GetLangId(), $_SESSION['LangArrayOfShortCode']);
	}
	
	public static function HttpHeader(){
		$sc = self::GetShortCode();
		if( $sc!==FALSE ){
			header("Content-Language: $sc");
		}
	}
	
	public static function SetLangId($lang_id){
		$_SESSION["LangId"]=$lang_id;
	}
	
	public static function SetLangIdByShortCode($short_code){
		$short_code = strtolower($short_code);
		if( isset( $_SESSION['LangArrayOfShortCode'][$short_code] ) ){
			$_SESSION['LangId']=$_SESSION['LangArrayOfShortCode'][$short_code];
		}else{
			$_SESSION['LangId']=self::$default_lang_id;
		}
	}
	
	public static function SetLangFromDomain(){
		if( preg_match('/^([^\.]{2,3})\./i', $_SERVER['SERVER_NAME'], $m) ){
			self::SetLangIdByShortCode( $m[1] );
			$_SESSION['LangDomain'] = $_SERVER['SERVER_NAME'];
			self::$true_short_code_from_domain = strtolower($m[1]);
		}else{
			self::SetLangId(self::$default_lang_id);
		}
	}
	
	public static function GetArrayOfShortCodeIfNotSet(){
		if( !(isset( $_SESSION['LangArrayOfShortCode'] ) && 
				count($_SESSION['LangArrayOfShortCode']) > 0) ){
			self::GetArrayOfShortCode();
		}
	}
	
	public static function GetArrayOfShortCode(){
		$_SESSION['LangArrayOfShortCode'] = array();
		
		$sql = "SELECT *, lower( short_code ) AS sc FROM languages";
		$res = pgsql::getInstance()->cq( $sql );
		
		foreach( $res as $val ){
			$_SESSION['LangArrayOfShortCode'][$val['sc']] = $val['languages_id'];
		}
	}

	public static function T($name_key){
		if( $name_key=='' ) return $name_key;
		$lang_id = I18N::GetLangId();
		
		if( self::$default_lang_id==$lang_id ){
			$str = isset(self::$trans_lang[$lang_id][$name_key]) ? 
				self::$trans_lang[$lang_id][$name_key] : 
				$name_key;
		}else{
			/*$str = isset(self::$trans_lang[$lang_id][$name_key]) ? 
				self::$trans_lang[$lang_id][$name_key] : 
				"[$name_key]";*/
			if( isset(self::$trans_lang[$lang_id][$name_key]) ){
				$str = self::$trans_lang[$lang_id][$name_key];
			}else{
				$str = "[$name_key]";
				if( self::$file_log!='' ){
					$fp = fopen(self::$file_log, 'a');
					fwrite($fp, $name_key."\r\n");
					fclose($fp);
				}
			}
			
		}
		
		return $str;
		/*if( $name_key=='' ) return $name_key;
		$lang_id = I18N::GetLangId();
		$ckey = "I18N".$lang_id.$name_key;
		$name_key = pg_escape_string($name_key);
		
		$ct = cache::cache_read( $ckey );
		if( $ct!==false ){
			return $ct;
		}
		
		$sql = "SELECT * FROM translations WHERE name_key='$name_key' AND languages_id=$lang_id";
		pgsql::getInstance()->query( $sql );
		$res = pgsql::getInstance()->fetch_a();
		$str = $res===FALSE ? "[$name_key]" : $res['name_val'];
		
		return cache::cache_str($ckey, $str);*/
	}
	
	private static function Param($str, $param=array()){
		if( count($param)==0 ) return $str;
		return str_replace(array_keys($param), array_values($param), $str);
	}

	public static function TP($name_key, $param=array()){
		return self::Param(self::T($name_key), $param);
	}
	
	public static function ET($name_key){
		echo I18N::T($name_key);
	}
	
	public static function ETP($name_key, $param=array()){
		echo self::TP($name_key, $param);
	}
	
	public static function IMG($name_key, $ext='png'){
		return $name_key . '_' . self::GetShortCode() . '.' . $ext;
	}
	
	public static function EIMG($name_key, $ext='png'){
		echo self::IMG($name_key, $ext);
	}
	
	public static function CheckFileLang($lang_id){
		$file_name = D_PATH_CACHE . '_translations_lang_'.$lang_id.'.cache';
		return file_exists($file_name);
	}
	
	public static function CreateByLang($lang_id){
		$sql = "SELECT * FROM translations WHERE languages_id=$lang_id";
		$res = pgsql::getInstance()->qa($sql);
		$arr = array();
		foreach( $res as $key=>$val ){
			$arr[$val['name_key']]=$val['name_val'];
		}
		
		$file_name = D_PATH_CACHE . '_translations_lang_'.$lang_id.'.cache';
		$str = serialize($arr);
		self::$trans_lang[$lang_id] = $arr;
		
		$fp = fopen($file_name, 'w');
		fwrite($fp, $str);
		fclose($fp);
	}
	
	public static function LoadByLang($lang_id){
		if( self::CheckFileLang($lang_id) ){
			$file_name = D_PATH_CACHE . '_translations_lang_'.$lang_id.'.cache';
			$arr = unserialize( file_get_contents($file_name) );
			self::$trans_lang[$lang_id] = $arr;
			return TRUE;
		}
		return FALSE;
	}
	
	public static function Load(){
		$lang_id = I18N::GetLangId();
		
		if( !self::LoadByLang($lang_id) ){ //Brak pliku
			self::CreateByLang( $lang_id );
		}
	}
	 
}
?>