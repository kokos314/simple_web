<?php
/*
 * Controller
 */
class Ctrl {
	private static $values_from_request=array();
	private static $value_mode = 3;
	private static $alias_ctrl = array();
	private static $ma2alias_ctrl = array();
	private static $module = NULL;
	private static $action = NULL;
	private static $request_if_one = NULL;
	private static $layout = FALSE;
	private static $escape_default_method = NULL;
	
	public static function IsPrettyUrl(){
		return self::$value_mode==2 || self::$value_mode==3;
	}
	
	public static function GetValueMode(){
		return $value_mode;
	}
	
	public static function SetEscapeDefaultMethod($escape_default_method){
		self::$escape_default_method = $escape_default_method; 
	}
	
	public static function AccessUrl(){
		$access_url = $_SERVER['SCRIPT_URI'].( $_SERVER['QUERY_STRING']!='' ?  '?'.$_SERVER['QUERY_STRING'] : '' );
		return $access_url;
	}
	
	public static function SetAliasCtrl($arr=array()){
		self::$alias_ctrl = $arr;
		$idx = self::M().'/'.self::A();
		
		if( isset(self::$alias_ctrl[$idx]) ){
			self::$module = self::$alias_ctrl[$idx]['module'];
			self::$action = self::$alias_ctrl[$idx]['action'];
			if( isset(self::$alias_ctrl[$idx]['redirect']) ){
				$arr_get = self::GVArrayFromGetMethod();
				if( isset($arr_get['module']) ) unset( $arr_get['module'] );
				if( isset($arr_get['action']) ) unset( $arr_get['action'] );
				self::Redirect301(self::$module, self::$action, $arr_get); //TODO - inne przekierowania
				die();
			}
		}
		
		foreach ( self::$alias_ctrl as $key=>$val ){
			$arr_param = explode('/', $key);
			if( count($arr_param)>=2 ){
				self::$ma2alias_ctrl[ $val['module'].'/'.$val['action'] ] = array(
					'module'=>$arr_param[0],
					'action'=>$arr_param[1],
				);	
			}
		}//end foreach
		
		$idx = self::$request_if_one;
		if( self::$request_if_one!='' && isset(self::$alias_ctrl[$idx]) ){
			//echo "Req: ".self::$request_if_one;
			self::$module = self::$alias_ctrl[$idx]['module'];
			self::$action = self::$alias_ctrl[$idx]['action'];
		}
 	}
 	
 	public static function GetAliasCtrl($module, $action){
 		if( isset( self::$ma2alias_ctrl[$module.'/'.$action] ) ){
 			return self::$ma2alias_ctrl[$module.'/'.$action];
 		}
 		return array( 'module'=>$module, 'action'=>$action );
 	}
	
	public static function RedirectToNonWWW(){
		if( substr($_SERVER['SERVER_NAME'], 0, 4) === 'www.' ){
			$pageURL = 'http://'.substr($_SERVER['SERVER_NAME'], 4).$_SERVER["REQUEST_URI"];
			header('HTTP/1.1 301 Moved Permanently', true, 301);
			header('Location: '. $pageURL);
			exit();
		}
	}
	
	public static function Redirect404(){
		header("HTTP/1.0 404 Not Found");
		include_once '404.php';
		exit();	
	}
	
	public static function Redirect301($m, $a, $param=''){
		$url = HtmlHelper::UrlStr($m, $a, $param, true);
		header('HTTP/1.1 301 Moved Permanently', true, 301);
		header("Location: $url");
		exit();	
	}

	public static function Redirect($m, $a, $param=''){
		$url = HtmlHelper::UrlStr($m, $a, $param, true);
		header("location: $url");
		exit();
	}
	
	public static function FileModuleActionExists($module='', $action=''){
		$module = $module=='' ? self::M() : $module;
		$action = $action=='' ? self::A() : $action;
		$path_to_file = './modules/'.$module.'/'.$action.'.php';
		return file_exists($path_to_file);
	}
	
	public static function GetValFromRequest(){
		$request = $_SERVER['REQUEST_URI'];
		$params = explode('/', $request);
		$p_arr = array();
		if( count($params)<2 ){
			if( count($params)==1){
				self::$request_if_one = $params[0];
			}
			$p_arr['module']=isset($params[0]) && $params[0]!='' ? $params[0] : D_DEFAULT_MODULE;
			$p_arr['action']=isset($params[1]) && $params[1]!=''  ? $params[1] : D_DEFAULT_ACTION;
		}else{
			if( $params[0]=="" ) {
				if( count($params)==2){
					self::$request_if_one = $params[1];
				}
				
				$p_arr['module']=isset($params[1]) && $params[1]!=''  ? $params[1] : D_DEFAULT_MODULE;
				$p_arr['action']=isset($params[2]) && $params[2]!=''  ? $params[2] : D_DEFAULT_ACTION;
				if( isset($params[1]) ){
					unset($params[1]);
				}
				if( isset($params[2]) ){
					unset($params[2]);
				}
				unset($params[0]);
			}else{
				$p_arr['module']=$params[0];
				$p_arr['action']=$params[1];
				
				unset($params[0]);
				unset($params[1]);
			}
			
			$params = array_merge($params);
			$i=0;
			$pk='';
			foreach( $params as $k=>$v ){
				if( $i%2==0 ){
					$pk=$v;
					if( $pk!='' ) $p_arr[$pk]=null;
				}else{
					if( $pk!='' ) $p_arr[$pk]=urldecode($v);
				}
				$i++;
			}
			
			self::$values_from_request = $p_arr;
			//var_dump(self::$values_from_request);
		}
	}
	
	public static function IfModuleAndAction($tmp_m, $tmp_a, $r_yes, $r_no){
		return (Ctrl::M()==$tmp_m && Ctrl::A()==$tmp_a) ? $r_yes : $r_no;
	}
	
	public static function M(){ //Get module
		if( self::$module==NULL ){
			if( self::$value_mode == 1 ){
				return isset($_GET['m']) ? $_GET['m'] : D_DEFAULT_MODULE;
			}else if( self::$value_mode == 2 ){
				return self::$values_from_request['module'];
			}else if( self::$value_mode == 3 ){
				return isset( $_GET['m'] ) ?
					$_GET['m'] : 
					(isset(self::$values_from_request['module']) ? self::$values_from_request['module'] : D_DEFAULT_MODULE);
			}	
		}
		
		return self::$module;
	}
	
	public static function A(){ //Get action
		if( self::$action==NULL ){
			if( self::$value_mode == 1 ){
				return isset($_GET['a']) ? $_GET['a'] : D_DEFAULT_ACTION;
			}else if( self::$value_mode == 2 ){
				return self::$values_from_request['action'];
			}else if( self::$value_mode == 3 ){
				return isset( $_GET['a'] ) ?
					$_GET['a'] : 
					(isset(self::$values_from_request['action']) ? self::$values_from_request['action'] : D_DEFAULT_ACTION);
			}
		}
		
		return self::$action;
	}
	
	public static function GetMethod(){
		return $_SERVER['REQUEST_METHOD'];
	}
	
	public static function IsPost(){
		return self::GetMethod()=='POST';
	}
	
	public static function IsGet(){
		return self::GetMethod()=='GET';
	}
	
	public static function IsMobile(){
		return preg_match('/mobile/i', $_SERVER['HTTP_USER_AGENT']);
	}
	
	public static function GV($id, $def=NULL, $escape_method=NULL){ //Get value
		if( $escape_method==NULL ) $escape_method = self::$escape_default_method;
		$val = NULL;
		if( self::$value_mode == 1 ){
			//return isset( $_GET[$id] ) ? $_GET[$id] : $def;
			$val = isset( $_GET[$id] ) ? $_GET[$id] : $def;
		}else if( self::$value_mode == 2 ){
			//return isset( self::$values_from_request[$id] ) ? self::$values_from_request[$id] : $def;
			$val = isset( self::$values_from_request[$id] ) ? self::$values_from_request[$id] : $def;
		}else if( self::$value_mode == 3 ){
			/*return isset( $_GET[$id] ) ?
				$_GET[$id] : 
				(isset( self::$values_from_request[$id] ) ? self::$values_from_request[$id] : $def);*/
			$val = isset( $_GET[$id] ) ?
				$_GET[$id] : 
				(isset( self::$values_from_request[$id] ) ? self::$values_from_request[$id] : $def);
		}
		
		if( $escape_method==NULL ){
			return $val;
		}elseif( $escape_method=='pgsql' ){
			return pg_escape_string($val);
		}/*elseif( $escape_method=='mysql' ){
			return mysqli_real_escape_string($val);
		}*/
		
		return $val;
	}
	
	public static function GVArrayFromGetMethod($with_module_and_action=true){
		$arr = array();
		if( self::$value_mode == 1 ){
			$arr = $_GET;
			if( !$with_module_and_action ){
				unset(  $arr['m'] );
				unset(  $arr['a'] );
			}
		}else{
			$arr = self::$values_from_request;
			if( !$with_module_and_action ){
				unset(  $arr['module'] );
				unset(  $arr['action'] );
			}
		}
		
		return $arr;
	}
	
	public static function GKeyExists($key){
		if( self::$value_mode==1 ){
			return array_key_exists($key, $_GET );
		}elseif ( self::$value_mode==2 ){
			return array_key_exists($key,  self::$values_from_request );
		}elseif ( self::$value_mode==3 ){
			return array_key_exists($key,  self::$values_from_request ) || array_key_exists($key, $_GET );
		}
	}
	
	public static function GVorPV($id, $def=NULL){
		$v = self::GV($id);
		if( $v!='' ){
			return $v;
		}
		
		return self::PV($id, $def);
	}
	
	public static function PV($id, $def=NULL){ //Post value
		return isset( $_POST[$id] ) ? $_POST[$id] : $def;
	}
	
	public static function RV($id, $def=NULL){ //request value
		return isset( $_REQUEST[$id] ) ? $_REQUEST[$id] : $def;
	}
	
	public static function GetPath($module, $action){
		return './modules/' . $module . '/' . $action.'.php';
	}
	
	public static function GetHtml($module, $action, $param=NULL){
		return get_partial(self::GetPath($module, $action), $param);
	}

	public static function SetLayout($layout){
		self::$layout = $layout;
		$GLOBALS['layout'] = $layout;
	}
	
	public static function GetLayoutPath(){
		$layout = self::$layout;
		if( $layout===false ){
			$layout = isset( $GLOBALS['layout'] ) ? $GLOBALS['layout'] : 'index.php';
		}
		if( $layout===false ) return false;
		
		$fx = '';
		$ext = pathinfo($layout, PATHINFO_EXTENSION);
		if( $ext!='php' ){
			$fx = '.php';
		}
		
		$path_layout = './layout/' . $layout . $fx;
		return $path_layout;
	}
	
	public static function IsLayout(){
		$layout = self::$layout;
		if( $layout===false ){
			$layout = isset( $GLOBALS['layout'] ) ? $GLOBALS['layout'] : 'index.php';
		}
		if( $layout===false ) return false;
		
		return true;
	}
	
	public static function GetLayoutHTML($param=array()){
		$path_layout = self::GetLayoutPath();
		$html = get_partial($path_layout, $param);
		return $html;
	}
	
	public static function GetFiles($file_key){ //return array of files
		$arr_files = array( 'name'=>array(), 'type'=>array(), 'tmp_name'=>array(), 'error'=>array(), 'size'=>array() );
		if( !is_array( $_FILES[$file_key] ) ){
			$arr_files[] = $_FILES[$file_key];
		}else{
			$arr_files = $_FILES[$file_key];
		}
		
		$arr_tmp = array();
		foreach( $arr_files['name'] as $key => $val ){
			if( $val!='' ){
				$arr_tmp[] = $key;
			}
		}
		
		$arr_files_tmp = array();
		foreach($arr_tmp as $idx){
			$arr_files_tmp['name'][] = $arr_files['name'][$idx];
		}
		foreach($arr_tmp as $idx){
			$arr_files_tmp['type'][] = $arr_files['type'][$idx];
		}
		foreach($arr_tmp as $idx){
			$arr_files_tmp['tmp_name'][] = $arr_files['tmp_name'][$idx];
		}
		foreach($arr_tmp as $idx){
			$arr_files_tmp['error'][] = $arr_files['error'][$idx];
		}
		foreach($arr_tmp as $idx){
			$arr_files_tmp['size'][] = $arr_files['size'][$idx];
		}
		
		return $arr_files_tmp;
	}
	
	public static function GetFileName($file_key) {
		return $_FILES[$file_key]['name'];
	}
	
	public static function GetFileType($file_key) {
		return $_FILES[$file_key]['type'];
	}
	
	public static function GetFileTmpName($file_key) {
		return $_FILES[$file_key]['tmp_name'];
	}
	
	public static function GetFileError($file_key) {
		return $_FILES[$file_key]['error'];
	}
	
	public static function GetFileSize($file_key) {
		return $_FILES[$file_key]['size'];
	}
}

?>