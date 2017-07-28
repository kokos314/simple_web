<?php
class cache{
	private static $tmp_key;
	private static $tmp_prefix;
	
	public static function get_file_name($key, $prefix=''){
		return D_PATH_CACHE . $prefix . md5($key).'.cache';
	}
	
	public static function cache_html_start($key, $prefix=''){
		$b = self::cache_exists($key, $prefix);
		if( !$b ){
			self::$tmp_key = $key;
			self::$tmp_prefix = $prefix;
			ob_start();
		}
		return $b;
	}
	
	public static function cache_html_end(){
		$html = ob_get_clean();
		self::cache_str(self::$tmp_key, $html, FALSE, self::$tmp_prefix);
		return $html;
	}
	
	public static function cache_str($key, $str, $clear=FALSE, $prefix=''){
		$file_name = self::get_file_name($key, $prefix);
		
		if( self::cache_exists($key, $prefix) ){
			return file_get_contents( $file_name );
		}else{
			$fp = fopen($file_name, 'w');
			fwrite($fp, $str);
			fclose($fp);
			
			return $str;
		}
	}
	
	public static function cache_read($key, $prefix=''){
		if( self::cache_exists($key, $prefix) ){
			$file_name = self::get_file_name($key, $prefix);
			return file_get_contents( $file_name );
		}
		
		return  false;
	}
	
	public static function cache_clear($key, $prefix=''){
		$file_name = self::get_file_name($key, $prefix);
		
		if( file_exists( $file_name ) ){
			unlink($file_name);
		}
	}
	
	public static function cache_exists($key, $prefix=''){
		$file_name = self::get_file_name($key, $prefix);
		return ( file_exists( $file_name ) && (time() - filemtime($file_name) <= D_TIME_CACHE) );
	}
	
	public static function cache_clear_all($prefix=''){
		$arr = array();
		$d = dir(D_PATH_CACHE);
		while (false !== ($entry = $d->read())) {
			$fn = D_PATH_CACHE . $entry;
			
			if( is_file($fn) && preg_match("/^$prefix.*\.cache$/", $entry ) ){
				$arr[] = $fn;
				unlink( $fn );
			}
			
		}
		$d->close();
		
		return $arr;
	}
}