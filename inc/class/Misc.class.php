<?php

class Misc
{
	public static function PathRemoveDot($path){
		return substr($path,0,1)=='.' ? substr($path,1) : $path;
	}
	
	public static function NameForUrl($title){
		return preg_replace(
				array('/\./i', '/\-/i', '/\&/i', '/\//i', '/\,/i', '/\s+/i'), 
				array('',      ' ',     '',      '-',     '',      '-'), 
				$title);
	}

	public static function MakeInsertFromRequestParam($table_name, $param)
	{
		$i = 1;
		$cnt = count($param);
		$sql = "INSERT INTO $table_name (";
		foreach( $param as $key=>$val ){
			$sql .= "\"$key\"";
			if( $i < $cnt ){
				$sql .=	 ', ';
			}
			$i++;
		}
		$sql .= ")VALUES(";
		
		$i = 1;
		foreach( $param as $key=>$val ){
			$sql .= self::GetValueOrNull( $val );
			if( $i < $cnt ){
				$sql .=	 ', ';
			}
			$i++;
		}
		$sql .= ")";
		
		return $sql;
	}
	
	public static function MakeUpdateFromArray($table_name, $param, $f_id)
	{
		$i = 1;
		$cnt = count($param);
		$sql = "UPDATE $table_name SET ";
		foreach( $param as $key=>$val ){
			$sql .= "\"$key\"=".self::GetValueOrNull( $val );
			if( $i < $cnt ){
				$sql .=	 ', ';//echo "[$val]$sql<br>\r\n";
			}
			$i++;
		}
		$sep = is_int($param[$f_id]) ? '' : "'";
		$sql .= " WHERE $f_id=$sep$param[$f_id]$sep";
		return $sql;
	}
	
	public static function GetValueOrNull($val, $sep='\'')
	{
		if( is_int( $val ) ){
			return  $val;
		}elseif( is_bool($val) ) {
			return  $val===true ? 'true' : 'false';
		}
		
		return $val = $val=='' ? 'NULL' : $sep.pg_escape_string( $val ).$sep;
	}
	
	public static function GetBoolFromStr($val_str){
		if( strtolower( $val_str )=='true' || 
				strtolower( $val_str )=='t'|| 
				strtolower( $val_str )=='on'|| 
				strtolower( $val_str )=='yes'|| 
				strtolower( $val_str )=='y'|| 
				strtolower( $val_str )=='1'){
			return true;
		}
		
		return false;
	}
	
	public static function GetDate($str_date, $format_date='Y-m-d H:i:s')
	{
		if( is_null( $str_date ) ) return NULL;
		$uts = strtotime( $str_date );
 		return date( $format_date, $uts );  
	}
	
	public static function GetUnixTimeStamp($str_date)
	{
		if( is_null( $str_date ) ) return NULL;
		$uts = strtotime( $str_date );
 		return $uts;  
	}
	
	public static function FormatBytes($bytes, $precision = 2) 
	{
	    $units = array('B', 'KB', 'MB', 'GB', 'TB');
	  
	    $bytes = max($bytes, 0);
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	    $pow = min($pow, count($units) - 1);
	  
	    $bytes /= pow(1024, $pow);
	  
	    return round($bytes, $precision) . ' ' . $units[$pow];
	} 
	
	
	/*public static function GetLastModuleName() 
	{
		$c = sfContext::getInstance()->getActionStack()->getSize();
	    $e = sfContext::getInstance()->getActionStack()->getEntry($c-2);
		return $e==NULL ? NULL : $e->getModuleName();
	}*/
	
	public static function GetVal($arr, $idx, $def=NULL) 
	{
		return is_array( $arr ) && isset( $arr[$idx] ) ? $arr[$idx] : $def; 
	}
	
	public static function human_size($a_bytes, $sep=' ')
	{
	    if ($a_bytes < 1024) {
	        return $a_bytes .$sep.'b';
	    } elseif ($a_bytes < 1048576) {
	        return round($a_bytes / 1024, 2) .$sep.'Kb';
	    } elseif ($a_bytes < 1073741824) {
	        return round($a_bytes / 1048576, 2) . $sep.'Mb';
	    } elseif ($a_bytes < 1099511627776) {
	        return round($a_bytes / 1073741824, 2) . $sep.'Gb';
	    } elseif ($a_bytes < 1125899906842624) {
	        return round($a_bytes / 1099511627776, 2) .$sep.'Tb';
	    } elseif ($a_bytes < 1152921504606846976) {
	        return round($a_bytes / 1125899906842624, 2) .$sep.'Pb';
	    } elseif ($a_bytes < 1180591620717411303424) {
	        return round($a_bytes / 1152921504606846976, 2) .$sep.'Eb';
	    } elseif ($a_bytes < 1208925819614629174706176) {
	        return round($a_bytes / 1180591620717411303424, 2) .$sep.'Zb';
	    } else {
	        return round($a_bytes / 1208925819614629174706176, 2) .$sep.'Yb';
	    }
	}
}

?>