<?php


class pgsql
{
	private $conn;
	private $result;
	private $cny_query = 0;
	private $arr_query = array();
	
//----------------------------------SINGLETON---------------------------------------------
	private static $instance;
	private function __construct() {} 	// Blokujemy domyślny konstruktor publiczny
	private function __clone(){} 		//Uniemozliwia utworzenie kopii obiektu
 
	public static function getInstance()
	{
		return ($i = &self::$instance) ? $i : $i = new self;
	}	
	
//-------------------------------------------------------------------------------
	private function error($str_error)
	{
		die( "<b>$str_error</b> ".pg_last_error($this->conn) );
	}
//-------------------------------------------------------------------------------	
 	public function connect($dbname, $login, $password, $host='localhost', $port=5432) 
 	{
 		$this->cny_query=0;
 		$this->arr_query = array();
 		$this->conn = pg_connect("host=$host port=$port dbname=$dbname user=$login password=$password")
      	or $this->error( 'Connect error: ' );
		return true;
 	}
//------------------------------------------------------------------------------- 	
 	public function qa( $sql, $param=array() ){
 		$this->query($sql, $param);
 		return $this->get_array();
 	}
//------------------------------------------------------------------------------- 	
 	public function qf( $sql, $param=array() ){
 		$this->query($sql, $param);
 		return $this->fetch_a();
 	}
//------------------------------------------------------------------------------- 	
 	public function query( $sql, $param=array() ){
 		$this->cny_query++;
 		$this->arr_query[] = $sql;
 		
 		if( count( $param )==0 ){
 			$this->result = pg_query( $this->conn, $sql);
 		}else{
 			$this->result = pg_query_params( $this->conn, $sql, $param);
 		}
 		
 		if( $this->result===false ) $this->error( $sql );
 		return $this->result;
 	}
//-------------------------------------------------------------------------------
 	public function fetch_a()
 	{
 		return pg_fetch_assoc($this->result);
 	}
//-------------------------------------------------------------------------------
 	public function fetch_one($key)
 	{
 		$row = $this->fetch_a();
 		return isset($row[$key]) ? $row[$key] : false;
 	}
//-------------------------------------------------------------------------------
 	public function fa()
 	{
 		return pg_fetch_assoc($this->result);
 	}
//-------------------------------------------------------------------------------
 	public function get_array()
 	{
 		$i=0;
 		$row = array();
		while ( $res = pg_fetch_assoc($this->result) ) $row[$i++]=$res; 
 		return $row; 
 	}
//-------------------------------------------------------------------------------
 	public function get_seq($seq_name)
 	{
 		//pg_get_serial_sequence
 		$sql="SELECT nextval('$seq_name'::regclass) as nv";
 		pgsql::getInstance()->query($sql);
 		$row = pgsql::getInstance()->fetch_a();
 		return $row['nv'];
 	}	
//-------------------------------------------------------------------------------
 	public function cq($sql, $prefix='') //get cached array
 	{
 		if( cache::cache_exists( $sql, $prefix ) ){
 			return unserialize( cache::cache_read( $sql, $prefix ) );
 		}
 		
 		pgsql::getInstance()->query($sql);
 		$res = pgsql::getInstance()->get_array();
 		cache::cache_str($sql, serialize($res), false, $prefix );
 		
 		return $res;
 	}
//-------------------------------------------------------------------------------
 	public function cache_exists($sql, $prefix='') //get cached one row
 	{
 		return cache::cache_exists( $sql, $prefix );
 	}
//-------------------------------------------------------------------------------
 	public function cq_one($sql, $prefix='') //get cached one row
 	{
 		if( cache::cache_exists( $sql, $prefix ) ){
 			return unserialize( cache::cache_read( $sql, $prefix ) );
 		}
 			
 		pgsql::getInstance()->query($sql);
 		$res = pgsql::getInstance()->fetch_a();
 		cache::cache_str($sql, serialize($res), false, $prefix );
 			
 		return $res;
 	}
//-------------------------------------------------------------------------------
 	public function get_data()
 	{
 		if( pg_num_rows($this->result)>1 ){
 			$i=0;
			while ( $res = pg_fetch_assoc($this->result) ) $row[$i++]=$res; 
 		}else{
 			$row = pg_fetch_assoc($this->result);
 		}
 		return $row; 
 	} 	
//-------------------------------------------------------------------------------
 	public function get_cnt_query()
 	{
 		return $this->cny_query;
 	}
//-------------------------------------------------------------------------------
 	public function get_array_query()
 	{
 		return $this->arr_query;
 	}
}

?>