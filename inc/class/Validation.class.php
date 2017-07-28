<?php

class Validation{
	const P_TRIM = 'trim';
	const P_HTMLSPECOALCHARS = 'htmlspecialchars';
	const P_STRIPSLASHES = 'stripslashes';
	const P_COMMA_TO_DOT = 'comma_to_dot';
	const V_INT = 'int';
	const V_REQUIRED = 'required';
	const V_NUMBER_MIN_MAX = 'number_min_max';
	const V_MIN_MAX = 'min_max';
	const V_WORD_MIN_MAX = 'word_min_max';
	const V_COMPARE = 'compare';
	const V_COUNT_ARRAY = 'count_array';
	const V_EMAIL = 'email';
	const V_NUMERIC = 'numeric';
	const V_PATTERN = 'pattern';
	
	private static $event_message = NULL;
	
	private $array_form = array();
	private $to_parse = array();
	private $to_validate = array();
	private $error_message = array();
	private $errors = array();
	private $is_success = '';
	private $default_success_message = '';
	private $is_validated = false;
	private $validator = array();
	private $validator_error_message = '';
	
	public static function SetEventMessage($fun){
		self::$event_message = $fun;
	}
	
	public function AddValidator($fun){
		$this->validator[] = $fun;
	}
	
	public function SetArrayForm($arr){
		if( is_array($arr) ){
			$this->array_form = array_merge($this->array_form, $arr);
		}
	}
	
	public function SetDefaultSuccessMessage($msg){
		$this->default_success_message = $msg;
	}
	
	public function AddToValidate($name, $param=array(), $own_message=NULL){
		$this->to_validate[$name] = $param;
		$this->error_message[$name] = $own_message;
	}
	
	public function AddToParse($name, $param=array()){
		$this->to_parse[$name] = $param;
	}
	
	public function Parse(){
		$arr = $this->array_form;
		foreach( $this->to_parse as $k=>$v ){
			if( is_array($v) && isset( $arr[$k] ) ){
				foreach( $v as $vali_k => $vali_v ){
					if( is_callable(array($this, "P_".$vali_k)) ){
						$res = call_user_func_array(array($this, "P_".$vali_k),
								array('arr'=>$arr, 'key'=>$k, 'param'=>$vali_v));
						$arr[$k] = $res;
					}
				}//end foreach
			}//end if
		}
		$this->array_form = $arr;
		return $arr;
	}
	
	public function Validate(){
		$arr = $this->array_form;
		$b = true;
		foreach( $this->to_validate as $k=>$v ){
			if( is_array($v) ){
				foreach( $v as $vali_k => $vali_v ){
					if( is_callable(array($this, "V_".$vali_k)) ){
						$res = call_user_func_array(array($this, "V_".$vali_k), 
								array('arr'=>$arr, 'key'=>$k, 'param'=>$vali_v));
						if( $res!==false ){
							if( is_null( self::$event_message ) ){
								$this->errors[$k] = $res;
							}else{
								$this->errors[$k] = call_user_func_array( self::$event_message, array('msg'=>$res) );
							}
							break;
						}
					}else{
						trigger_error("Unknown validator: $vali_k", E_USER_WARNING);
					}
				}//end foreach
			}//end if
			$b = $b & !$this->IsError($k);
		}//end foreach
		
		/*if( $b===true ){
			foreach ( $this->validator as $k=>$v ){
				$res = call_user_func_array( $v, array());
				if( $res!==false ){
					$this->validator_error_message = $res;
					$b = false;
					break;
				}
			}
		}*/
		foreach ( $this->validator as $k=>$v ){
			$res = call_user_func_array( $v, array());
			if( $res!==false ){
				if( is_null( self::$event_message ) ){
					$this->validator_error_message = $res;
				}else{
					$this->validator_error_message = call_user_func_array( self::$event_message, array('msg'=>$res) );
				}
				$b = false;
				break;
			}
		}
		
		$this->is_success = $b;
		$this->is_validated = true;
	}
	
	public function Reset(){
		unset( $this->errors );
		$this->errors = array();
		$this->is_success = false;
		$this->is_validated = false;
	}
	
	private function V_required($arr, $key, $param){
		if( isset($arr[$key]) && $arr[$key]!='' ){
			return false;
		}else{
			//return 'Field is required and cannot be empty';
			return 'Pole to jest wymagane i nie może być pusty';
		}
	}
	
	private function V_file_count($arr, $key, $param){
		if( !isset($param['file_key']) ) {
			trigger_error("Validator file: no file_key", E_USER_WARNING);
		}
		
		if( !(isset($param['min']) || isset($param['max'])) ) {
			trigger_error("Validator file: set min or max", E_USER_WARNING);
		}
		
		$file_key = $param['file_key'];
		$arr_files = Ctrl::GetFiles($file_key);
		echo 'CF:'.count($arr_files['name'])."<br>\r\n";
		echo 'CF_min:'.$param['min']."<br>\r\n";
		echo 'CF_max:'.$param['max']."<br>\r\n";
		if( isset($param['min']) && count($arr_files['name']) < $param['min']  ){
			return "Too small number of files, minimum is $param[min]";
		}
		
		if( isset($param['max']) && count($arr_files['name']) > $param['max']  ){
			return "Too many files, maximum is $param[max]";
		}
		
		//Debug::VarDumb( $arr_files );
		return false;
	}
	
	private function V_file($arr, $key, $param){
		if( !isset($param['file_key']) ) {
			trigger_error("Validator file: no file_key", E_USER_WARNING);
		}
		$file_key = $param['file_key'];
		
		if( isset( $param['required'] ) && $param['required']==true ){
			if( $_FILES[$file_key]['name']=='' ){
				return 'The file is required';
			}
		}
		
		if( $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK ){
			$error_msg = array(
					0=>"There is no error, the file uploaded with success",
					1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
					2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
					3=>"The uploaded file was only partially uploaded",
					4=>"No file was uploaded",
					6=>"Missing a temporary folder"
			);
			$key_error = $_FILES[$file_key]['error'];
			return $error_msg[$key_error];
		}
		
		if( isset( $param['type'] ) ){
			$arr_type = is_array($param['type']) ? $param['type'] : array( $param['type'] );
			
			$b = false;
			foreach ( $arr_type as $k=>$v ){
				$b = $b | ($_FILES[$file_key]['type']==$v);
			}
			
			if( !$b ){
				return 'Bad file type';
			}
		}
		
		return false;
	}
	
	private function V_min_max($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		if( !( isset( $param['min'] ) && strlen($str) >= $param['min']) ){
			//return 'The field has enough characters minimum number is ' . $param['min'];
			return 'Pole ma za mało liczbe znków minimalna wartość to ' . $param['min'];
		}
		
		if( isset( $param['max'] ) && !( strlen($str) <= $param['max']) ){
			//return 'The field has too many characters (' . strlen($str) . ') maximum number is ' . $param['max'];
			return 'Pole ma za dużo znaków (' . strlen($str) . ') maksymalna wartość to ' . $param['max'];
		}
		
		return false;
	}
	
	private function V_word_min_max($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		$words = preg_split("/[\s,]+/", $str);
		
		if( !( isset( $param['min'] ) && count($words) >= $param['min']) ){
			//return 'The field has enough words minimum number is ' . $param['min'];
			return 'Pole ma za mało słów minimalna wartość to  ' . $param['min'];
		}
		
		if( isset( $param['max'] ) && !( count($words) <= $param['max']) ){
			//return 'The field has too many words maximum number is ' . $param['max'];
			return 'Pole ma za dużo słów maksymalna warotść to ' . $param['max'];
		}
		
		return false;
	}
	
	private function V_count_array($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
	
		if( ( isset( $param['min'] ) && count($str) < $param['min']) ){
			//return 'Too little option selected. Minumalnie select ' . $param['min'];
			return 'Za mało zaznaczonych opcji. Minimalna liczba to ' . $param['min'];
		}
	
		if( ( isset( $param['max'] ) && count($str) > $param['max']) ){
			//return 'Too many options selected. Maximum select ' . $param['max'];
			return 'Za dużo zaznaczonych opcji. Maksymalnie można ' . $param['max'];
		}
	
		return false;
	}
	
	private function V_alphanumeric($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		if( preg_match('/[^a-z_\-0-9\.]/i', $str) ){
			//return "Illegal character.They are permitted only large, lowercase letters, numbers and the '-', '.'";
			return "Niedozwolony znak. Dozwolone są tylko duże, małe litery, cyfry i '-', '.'";
		}
	
		return false;
	}
	
	private function V_pattern($arr, $key, $param){
		if( !isset( $param['error_message'] ) ){
			trigger_error("No parameter: error_message", E_USER_WARNING);
			return false;
		}
		if( !isset( $param['pattern'] ) ){
			trigger_error("No parameter: pattern", E_USER_WARNING);
			return false;
		}
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
	
		if( !preg_match($param['pattern'], $str) ){
			return $param['error_message'];
		}
	
		return false;
	}
	
	private function V_int($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
	
		if( !preg_match('/^\-{0,1}[0-9]+$/i', $str) ){
			return 'Wpisana wartość nie jest liczbą';//Enter the integer number
		}
	
		return false;
	}
	
	private function V_number_min_max($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		if( !( isset( $param['min'] ) && $str >= $param['min']) ){
			return 'Podana wartość jest zbyt mała, minimum to '. $param['min'];//'The given value is too small minimum value is ' . $param['min'];
		}
		
		if( isset( $param['max'] ) && !( $str <= $param['max']) ){
			return 'Podana wartość jest zbyt wysoka, maksymalna to '. $param['max'];//'The given value is too high maximum value is ' . $param['max'];
		}
		
		return false;
	}
	
	private function V_compare($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		if( !isset($param['compare_with']) ) {
			trigger_error("Validator greater: no compare_with", E_USER_WARNING);
			return false;
		}
		
		if( !isset($param['compare_action']) ) {
			trigger_error("Validator greater: no compare_action", E_USER_WARNING);
			return false;
		}
		
		$compare_with = $param['compare_with'];
		$compare_action = $param['compare_action'];
		switch ( $compare_action ){
			/*case '==': if( $str!=$this->GetVal($compare_with) ) return "Given value is different than: $compare_with"; break;
			case '!=': if( $str==$this->GetVal($compare_with) ) return "Given the value is the same as: $compare_with"; break;
			case '>': if( $str<$this->GetVal($compare_with) ) return "Given value is less than: $compare_with"; break;
			case '<': if( $str>$this->GetVal($compare_with) ) return "Given value is greater than: $compare_with"; break;*/
			case '==': if( $str!=$this->GetVal($compare_with) ) return "Podana wartość jest różna od: $compare_with"; break;
			case '!=': if( $str==$this->GetVal($compare_with) ) return "Podana wartość jest taka sama jak: $compare_with"; break;
			case '>': if( $str<$this->GetVal($compare_with) ) return "Podana wartość jest mniejsza od: $compare_with"; break;
			case '<': if( $str>$this->GetVal($compare_with) ) return "Podana wartość jest większa od: $compare_with"; break;
		}
	}
	
	private function V_numeric($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
	
		if( !is_numeric($str) ){
			return "Enter the number";
		}
	
		return false;
	}
	
	private function V_email($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		if( !filter_var($str, FILTER_VALIDATE_EMAIL) ) {
			//return 'This email is incorrect';
			return 'Email jest niepoprawny';
		}
		
		return false;
	}
	
	private function V_login($arr, $key, $param){
		$min = Misc::GetVal($param, 'min', 3);
		$max = Misc::GetVal($param, 'max', 20);
		
		$res = $this->V_required($arr, $key, $param);
		if( $res!==false ){
			return $res;
		}
		
		$res = $this->V_min_max($arr, $key, array('min'=>$min, 'max'=>$max));
		if( $res!==false ){
			return $res;
		}
		
		$res = $this->V_alphanumeric($arr, $key, $param);
		if( $res!==false ){
			return $res;
		}
		
		return false;
	}
	
	private function V_equal($arr, $key, $param){
		if( $param=='' ) return false;
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		if( $str!=$this->GetVal($param) ){
			//return 'The fields must be the same';
			return 'Pola muszą być takie same';
		}
		
		return false;
	}
	
	public function IsSuccess(){
		return $this->IsValidated() && $this->is_success;
	}
	
	public function IsValidated(){
		return $this->is_validated;
	}
	
	public function IsError($key){
		return isset( $this->errors[$key] ) ? true : false;
	}
	
	public function IsValidatorError(){
		//return $this->validator!='' ? true : false;
		return $this->validator_error_message!='' ? true : false;
	}
	
	public function GetAllErrors(){
		return $this->errors;
	}
	
	public function GetError($key){
		return isset( $this->errors[$key] ) ? $this->errors[$key] : null;
	}
	
	public function EGetError($key){
		echo $this->GetError($key);
	}
	
	public function EGetMessage($key){
		if( $this->is_validated && $this->IsError($key) ){
			echo $this->GetError($key);
		}elseif( $this->is_validated ) {
			echo $this->default_success_message;
		}
	}
	
	public function GetMessage($key){
		if( $this->is_validated && $this->IsError($key) ){
			return $this->GetError($key);
		}elseif( $this->is_validated ) {
			return $this->default_success_message;
		}
		
		return null;
	}
	
	public function GetValidatorMessage(){
		//if( $this->is_validated && $this->validator!='' ){
		if( $this->is_validated && $this->validator_error_message!='' ){
			return $this->validator_error_message;
		}elseif( $this->is_validated ) {
			return $this->default_success_message;
		}
		
		return null;
	}
	
	public function GetArrayForm(){
		return $this->array_form;
	}
	
	public function GetSelectedValue($param=array(), $parse_param=array()){
		$arr_out = array();
		foreach( $param as $kk ){
			$val=$this->GetVal($kk);
			$arr_out[$kk]=$val;
			
			if( isset($parse_param[$kk]) ){
				$v = $parse_param[$kk];
				if( is_array($v) ){
					foreach( $v as $vali_k => $vali_v ){
						if( is_callable(array($this, "P_".$vali_k)) ){
							$res = call_user_func_array(array($this, "P_".$vali_k),
									array('arr'=>$arr_out, 'key'=>$kk, 'param'=>$vali_v));
							$arr_out[$kk] = $res;
						}
					}//end foreach
				}//end if
			}else{
				$arr_out[$kk]=$val;
			}
			
		}
		
		return $arr_out;
	}
	
	public function GetVal($key, $def=null){
		return isset( $this->array_form[$key] ) ? $this->array_form[$key] : $def;
	}
	
	public function EGetVal($key){
		echo $this->GetVal($key);
	}
	//------------------------------------------------
	private function P_ucfirst($arr, $key, $param){
		return ucfirst( $arr[$key] );
	}
	
	private function P_trim($arr, $key, $param){
		return trim( $arr[$key] );
	}
	
	private function P_null_to_zero($arr, $key, $param){
		return  $arr[$key]=='' ? '0' : $arr[$key];
	}
	
	private function P_true_or_false($arr, $key, $param){
		return  $arr[$key]=='1' ? 'true' : 'false';
	}
	
	private function P_boolval($arr, $key, $param){
		/*$b = boolval($arr[$key]);
		var_dump($b);
		return  boolval($arr[$key]);*/
		return $this->boolVal($arr[$key]);
	}
	
	private function P_comma_to_dot($arr, $key, $param){
		return str_replace(',', '.', $arr[$key] );
	}
	
	private function P_pg_escape($arr, $key, $param){
		return pg_escape_string( $arr[$key] );
	}
	
	private function P_addslashes($arr, $key, $param){
		return addslashes( $arr[$key] );
	}
	
	private function P_htmlspecialchars($arr, $key, $param){
		return htmlspecialchars( $arr[$key], ENT_QUOTES );
	}
	
	private function P_stripslashes($arr, $key, $param){
		return stripslashes( $arr[$key] );
	}
//------------------------------------------
	private function boolVal($var) {
		$out = false;
		
		if( 
				$var===true ||
				$var==1 ||
				strtolower($var) == 'true' ||
				strtolower($var) == 'on' ||
				strtolower($var) == 'yes' ||
				strtolower($var) == 'y'
		){
			$out = true;
		}
		
		return $out;
	}
}

?>