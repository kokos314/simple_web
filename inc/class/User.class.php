<?php


class User{
	private static function GetAllRoles(){ 
		$uid = self::GetId();
		if( $uid==null ){
			return false;
		}
		
		$sql = "SELECT UR.*, R.name FROM users_roles AS UR
				LEFT JOIN roles AS R ON R.roles_id = UR.roles_id
				WHERE UR.users_id = $uid";
		pgsql::getInstance()->query($sql);
		$res = pgsql::getInstance()->get_array();
		$_SESSION['USER']['ROLES'] = $res;
		
		return true;
	}
	
	public static function LogSignIn($method=NULL){
		$u = self::GetUser();
		if( $u==null || ( isset($u['is_log_signin']) && Misc::GetBoolFromStr($u['is_log_signin'])===false ) ) return;
		
		$session_id = session_id();
		$param['users_id'] = self::GetId();
		$param['method'] = $method;
		$param['ip'] = $_SERVER['REMOTE_ADDR'];
		$param['session_id'] = $session_id=='' ? NULL : $session_id;
		$param['user_agent'] = $_SERVER['HTTP_USER_AGENT']=='' ? NULL : $_SERVER['HTTP_USER_AGENT'];
		$sql = Misc::MakeInsertFromRequestParam('users_signin', $param);
		pgsql::getInstance()->query($sql);
	}
	
	public static function HasRole($role_name){
		if( isset($_SESSION['USER']['ROLES']) ){
			foreach( $_SESSION['USER']['ROLES'] as $k=>$v ){
				if( $v['name']==$role_name ){
					return true;
				}
			}
		}
		return false;
	}
	
	
	public static function SetData($data){
		$_SESSION['USER_DATA'] = $data;
	}

	public static function GetData(){
		return isset($_SESSION['USER_DATA']) ? $_SESSION['USER_DATA'] : null;
	}
	
	public static function GetUserData($key) {
		$u = self::GetUser();
		return isset($u[$key]) ? $u[$key] : null;
	}
	
	public static function GetUser() {
		return isset( $_SESSION['USER'] ) ? $_SESSION['USER'] : null;
	}
	
	public static function GetId() {
		$u = User::GetUser();
		if( $u==null ){
			return  null;
		}
		
		return $u['users_id'];
	}

	public static function GetUserFromDb($id) {
		$sql = "SELECT * FROM users WHERE login='$id' OR email='$id'";
		if( is_int($id) ){
			$sql = "SELECT * FROM users WHERE users_id=$id";
		}
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();
	
		return $row;
	}

	public static function GetUserByLoginAndPassword($login, $password) {
		$sql = "
			SELECT * FROM users 
			WHERE is_active AND ((login='$login' OR email='$login' OR normalize_text(phone)=normalize_text('$login')) 
				AND password=MD5('$password'))";
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();
		
		return $row;
	}
	
	public static function SignIn($login, $password) {
		/*$sql = "
			SELECT * FROM users 
			WHERE is_active AND ((login='$login' OR email='$login' OR normalize_text(phone)=normalize_text('$login')) 
				AND password=MD5('$password'))";
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();*/
		
		$row = self::GetUserByLoginAndPassword($login, $password);
		
		if( $row!==false ){
			$_SESSION['USER'] = $row;
			self::GetAllRoles();
			self::LogSignIn(1);
			return true;
		}
		
		return false;
	}
	
	public static function SignInById($id) {
		$sql = "SELECT * FROM users WHERE is_active AND users_id=$id";
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();
	
		if( $row!==false ){
			$_SESSION['USER'] = $row;
			self::GetAllRoles();
			self::LogSignIn(2);
			return true;
		}
	
		return false;
	}
	
	public static function SignOut(){
		$_SESSION['USER'] = NULL;
		unset( $_SESSION['USER'] );
		unset( $_SESSION['USER_DATA'] );
	}
}

?>