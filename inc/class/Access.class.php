<?php

class Access{
//----------------------------------SINGLETON---------------------------------------------
	private static $instance;
	private function __construct() {} 	// Blokujemy domyślny konstruktor publiczny
	private function __clone(){} 		//Uniemozliwia utworzenie kopii obiektu
 
	public static function getInstance()
	{
		return ($i = &self::$instance) ? $i : $i = new self;
	}
	
//----------------------------------------------------------------------------------------
	public function CreateArray($uid){
		$sql = "SELECT 
					P.*, U.users_id, AM.m_name, AM.a_name, R.name AS r_name, U.login
				FROM permissions AS P
				LEFT JOIN access AS ACS ON ACS.access_id = P.access_id
				RIGHT JOIN (
					SELECT M.name AS m_name, M.modules_id,
						A.name AS a_name, A.actions_id, A.access_id FROM modules AS M
					LEFT JOIN actions AS A ON A.modules_id = M.modules_id ) AS AM 
					ON AM.modules_id = P.modules_id AND AM.access_id = ACS.access_id
				LEFT JOIN users_roles UR ON UR.roles_id = P.roles_id
				LEFT JOIN roles AS R ON R.roles_id = UR.roles_id
				LEFT JOIN users AS U ON U.users_id = UR.users_id
				WHERE U.users_id=$uid";
		//$res = pgsql::getInstance()->cq( $sql );
		//echo $sql;
		pgsql::getInstance()->query($sql);
 		$res = pgsql::getInstance()->get_array();
		//var_dump($res);
		$access = array();
		foreach( $res as $k=>$v ){
			$access[$v['m_name']][$v['a_name']]=true;
		}
		$_SESSION['ACCESS'] = $access;
	}
//----------------------------------------------------------------------------------------
	public function CheckAccess($uid, $module, $action){
		if( isset($_SESSION['ACCESS']) ){
			return isset($_SESSION['ACCESS'][$module][$action]) && $_SESSION['ACCESS'][$module][$action];
		}
		
		return false;
	}
//----------------------------------------------------------------------------------------	
	public function Debug(){
		echo "<pre>";
		var_dump($_SESSION['ACCESS']);
		echo "</pre>";
	}
}

?>