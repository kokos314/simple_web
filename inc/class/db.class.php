<?php

class db{
	private $pdo;
	private $result;
	private $cny_query = 0;
	private $arr_query = array();
	
//----------------------------------SINGLETON---------------------------------------------
	private static $instance;
	private function __construct() {} 	// Blokujemy domyślny konstruktor publiczny
	private function __clone(){} 		//Uniemozliwia utworzenie kopii obiektu
 
	public static function getInstance(){
		return ($i = &self::$instance) ? $i : $i = new self;
	}	
//-------------------------------------------------------------------------------
	
	public static function MakeInsertFromRequestParam($table_name, $param, $sep=""){
		$i = 1;
		$cnt = count($param);
		$sql = "INSERT INTO $table_name (";
		foreach( $param as $key=>$val ){
			$sql .= $sep.$key.$sep;
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
	
	public static function MakeUpdateFromArray($table_name, $param, $f_id, $sep=""){
		$i = 1;
		$cnt = count($param);
		$sql = "UPDATE $table_name SET ";
		foreach( $param as $key=>$val ){
			$sql .= $sep.$key.$sep."=".self::GetValueOrNull( $val );
			if( $i < $cnt ){
				$sql .=	 ', ';//echo "[$val]$sql<br>\r\n";
			}
			$i++;
		}
		$sep = is_int($param[$f_id]) ? '' : "'";
		$sql .= " WHERE $f_id=$sep$param[$f_id]$sep";
		return $sql;
	}
	
	public static function GetValueOrNull($val, $sep='\''){
		if( is_int( $val ) ){
			return  $val;
		}elseif( is_bool($val) ) {
			return  $val===true ? 'true' : 'false';
		}
		
		return $val = $val=='' ? 'NULL' : $sep.pg_escape_string( $val ).$sep;
	}
	
//-------------------------------------------------------------------------------	
	
	public function connect($dbname, $login, $password, $host='localhost', $db='mysql'){
		$this->cny_query=0;
 		$this->arr_query = array();
		
		$this->pdo = new PDO("$db:host=$host;dbname=$dbname;", $login, $password);
	    //set the PDO error mode to exception
	    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 	}
 	
 	public function qa($sql){
 		return $this->q_arr($sql);
 	}
 	
	public function q_arr($sql){
		try{
			$this->cny_query++;
	 		$this->arr_query[] = $sql;
			
			$stmt = $this->pdo->query($sql);
			$i=0;
			$res=array();
	      	while($row = $stmt->fetch()){
				$res[$i++]=$row;
			}
			$stmt->closeCursor();
			
			return $res;	
		}catch( PDOException $e ){
		    print "PDO error: " . $e->getMessage() . "<br>\r\n<b>SQL</b>: <pre>$sql</pre>";
		    die();
		}
 	}
 	
 	public function quote($val){
 		return $this->pdo->quote($val);
 	}
 	
	public function qf($sql){
		try{
			$this->cny_query++;
	 		$this->arr_query[] = $sql;
			
			$stmt = $this->pdo->query($sql);
			return $stmt->fetch();	
		}catch( PDOException $e ){
		    print "PDO error: " . $e->getMessage() . "<br>\r\n<b>SQL</b>: <pre>$sql</pre>";
		    die();
		}
 	}
 	
	public function query( $sql ){
		try{
			$this->cny_query++;
	 		$this->arr_query[] = $sql;
	 		
	 		$this->pdo->exec( $sql );	
		}catch( PDOException $e ){
		    print "PDO error: " . $e->getMessage() . "<br>\r\n<b>SQL</b>: <pre>$sql</pre>";
		    die();
		}
 	}
 	
	public function last_insert_id( $name=NULL ){
		try{
			$this->pdo->lastInsertId($name);
		}catch( PDOException $e ){
		    print "PDO error: " . $e->getMessage();
		    die();
		}
 	}
 	
}

?>