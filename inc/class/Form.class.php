<?php

class Form extends Validation{
	const V_IS_CITY_EXISTS = 'is_city_exists';
	const V_IS_TAGS_EXISTS = 'is_tags_exists';
	const V_CHECK_PHOTOS = 'check_photos';
	const V_CHECK_USER_PASSWORD = 'check_user_password';
	const V_CHECK_PROMOTIONAL_CODE = 'check_promotional_code';
	const V_UNIQUE_SQL = 'unique_sql';
	const P_PHONE = 'phone';
	
	private $symbol_required = ''; 
	private  $version = '1';
	
	
	public function __construct() {
		//$this->SetDefaultSuccessMessage('&#10004;');
		$this->SetDefaultSuccessMessage('');
	}
	
	public function SetVersion($v){
		$this->version = $v;
		
		if( !in_array($v, array('1', '2')) ){
			throw new Exception('There are no defined version');
		}
	}
	
	public function SetSymbolRequired($sr){
		$this->symbol_required = $sr;
	}
	
	public function BeginError($key){
		$numargs = func_num_args();
		if( $numargs > 1 ){
			$key = func_get_arg(1);
		}
		
		$str_class = '';
		if( $this->IsValidated() ){
			$str_class = $this->IsError($key) ? ' class="error"' : ' class="success"';
		}
		?><span<?php echo $str_class; ?>><?php //echo $name;
	}
	
	public function EndError($key){
		/*?><span><?php $this->EGetMessage($key); ?></span><?php
		?></span><?php*/
		?><span data-error-msg="<?php 
			$this->EGetError($key); ?>" onmouseover="ShowToolTipOver(this);" onmouseout="ShowToolTipOut(this);"><?php
				if( $this->IsValidated() && $this->IsError($key) ){
					echo '!';
				}
			?></span><?php
		?></span><?php
	}
	
	public  function IsChecked($key, $check_val, $def=null){
		$v = $this->GetVal($key, $def);
		if( is_array($v) ){
			foreach( $v as $val ){
				if( $val==$check_val ) {
					$v = $val;
					break;
				}
			}
		}
		echo $v==$check_val ? ' checked="checked" ' : '';
	}
	
	public function InputSelectFromDb($key, $sql, $db_val, $tags=''){
		$numargs = func_num_args();
		
		if( $this->version=='1' ){
			$arg0 = func_get_arg(0);
			$arg1 = func_get_arg(1);
			$arg2 = func_get_arg(2);
			$arg3 = func_get_arg(3);
			$arg4 = $numargs >= 5 ? func_get_arg(4) : '';
			
			/*echo "arg0=".$arg0."\r\n<br>";
			echo "arg1=".$arg1."\r\n<br>";
			echo "arg2=".$arg2."\r\n<br>";
			echo "arg3=".$arg3."\r\n<br>";
			echo "arg4=".$arg4."\r\n<br>";*/
			
			$this->InputSelectFromDbV1($arg0, $arg1, $arg2, $arg3, $arg4);
			return;
		}
		
		$this->InputSelectFromDbV1('', $key, $sql, $db_val, $tags);
		
		/*
		$class_css = '';
		if( $this->IsValidated() ){
			$class_css = $this->IsError($key) ? 'class="error"' : 'class="success"';
		}else{
			$class_css = $this->IsError($key) ? 'class="error"' : '';
		}
		
		$tags = $class_css . ' data-error-msg="'. $this->EGetError($key) . '"';
		
		HtmlHelper::SelectFromDb($sql, $key, $db_val, $key, $this->GetVal($key), $tags);*/
	}
	
	public function InputSelectFromDbV1($name, $key, $sql, $db_val, $tags=''){
		$this->BeginError($key);
		HtmlHelper::SelectFromDb($sql, $key, $db_val, $key, $this->GetVal($key), $tags);
		$this->EndError( $key );
	}
	
	public function InputSelect($name, $key, $items, $tags=''){
		$this->BeginError($name, $key);
		HtmlHelper::Select($key, $items, $this->GetVal($key), $tags);
		$this->EndError( $key );
	}
	
	public function InputCheckbox($name, $key, $placeholder='', $tags=''){
		$this->BeginError('', $key);
		?><input type="checkbox" name="<?php echo $key; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" value="1" <?php echo  Misc::GetBoolFromStr( $this->GetVal($key, false) ) ? ' checked="checked" ':''; ?> <?php echo " $tags"; ?>/><?php
		echo $name;
		$this->EndError( $key );
	}
	
	public function InputText($key){
		$numargs = func_num_args();
	    
		//if( $this->version=='1' || ( $numargs > 1 && $key=='' ) ){
		if( $numargs > 1 && $key=='' ){
			$arg0 = func_get_arg(0);
			$arg1 = func_get_arg(1);
			$arg2 = $numargs >= 3 ? func_get_arg(2) : '';
			$arg3 = $numargs >= 4 ? func_get_arg(3) : 'text';
			$arg4 = $numargs >= 5 ? func_get_arg(4) : '';
			
			$this->InputTextV1($arg0, $arg1, $arg2, $arg3, $arg4);
			return;
		}
		
		$class_css = '';
		if( $this->IsValidated() ){
			$class_css = $this->IsError($key) ? 'class="error"' : 'class="success"';
		}else{
			$class_css = $this->IsError($key) ? 'class="error"' : '';
		}
	    /*
		?><input type="text" name="<?php echo $key; ?>" value="<?php 
	    	$this->EGetVal($key); ?>" data-error-msg="<?php 
	    	$this->EGetError($key); ?>" <?php echo $class_css; ?>><?php*/
		
		$arg0 = '';
		$arg1 = func_get_arg(0);
		$arg2 = $numargs >= 2 ? func_get_arg(1) : '';
		$arg3 = $numargs >= 3 ? func_get_arg(2) : 'text';
		$arg4 = $numargs >= 4 ? func_get_arg(3) : '';
		
		$this->InputTextV1($arg0, $arg1, $arg2, $arg3, $arg4);
	}
	
	public function InputTextV1($name, $key, $placeholder='', $type='text', $tags=''){
		$this->BeginError($name, $key);
		?><input type="<?php echo $type; ?>" name="<?php echo $key; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" value="<?php $this->EGetVal($key); ?>" <?php echo " $tags"; ?>/><?php
		$this->EndError( $key );
	}
	
	public function InputPassword($name, $key, $placeholder='', $tags=''){
		$this->InputText($name, $key, $placeholder, 'password', $tags);
	}
	
	//public function InputTextArea($name, $key, $placeholder='', $tags=''){
	public function InputTextArea($key, $placeholder='', $tags=''){
		if( $key=='' && $placeholder!='' ){
			$numargs = func_num_args();
			$key = $placeholder;
			$placeholder = $tags;
			$tags = $numargs >= 4 ? func_get_arg(3) : '';
		}
		
		/*$str_class = '';
		if( $this->IsValidated() ){
			$str_class = $this->IsError($key) ? ' class="error"' : ' class="success"';
		}
		?><span<?php echo $str_class; ?>><?php
		?><textarea name="<?php echo $key; ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $tags; ?>><?php $this->EGetVal($key); ?></textarea>
		<span><?php $this->EGetMessage($key); ?></span><?php
		?></span><?php*/
		
		$this->BeginError($key);
		?><textarea name="<?php echo $key; ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $tags; ?>><?php $this->EGetVal($key); ?></textarea><?php
		$this->EndError( $key );
	}
	
	//public function Message($name, $key){
	public function Message($key){
		/*$str_class = '';
		if( $this->IsValidated() ){
			$str_class = $this->IsError($key) ? ' class="error"' : ' class="success"';
		}
		?><span<?php echo $str_class; ?>>
		<span><?php $this->EGetMessage($key); ?></span>
		</span><?php*/
		
		$numargs = func_num_args();
		if( $numargs > 1 ){
			$key = func_get_arg(1);
		}
		
		$this->BeginError($key);
		$this->EndError($key);
	}
	
	//public function ValidatorMessage($name){
	public function ValidatorMessage(){
		/*$str_class = '';
		if( $this->IsValidated() ){
			$str_class = $this->IsValidatorError() ? ' class="error"' : ' class="success"';
		}
		?><span<?php echo $str_class; ?>>
		<span><?php echo $this->GetValidatorMessage(); ?></span>
		</span><?php*/
		
		$str_class = '';
		if( $this->IsValidated() ){
			$str_class = $this->IsValidatorError() ? ' class="error"' : ' class="success"';
		}
		?><span<?php echo $str_class; ?>><span data-error-msg="<?php 
			echo $this->GetValidatorMessage(); ?>" onmouseover="ShowToolTipOver(this);" onmouseout="ShowToolTipOut(this);"><?php
				if( $this->IsValidated() && $this->IsValidatorError() ){
					echo '!';
				}
			?></span><?php
		?></span><?php
	}
	
	protected function P_phone($arr, $key, $param){
		$str = trim($arr[$key]);
		$str = str_replace(array('-', ' '), array('', ''), $str);
		$s = '';
		
		for ($i=0;$i<strlen($str);$i++){
			//echo $str . '-' . strlen($str)."<br>\r\n";
			$s .= ( ($i+1) % 3==0 ) && $i < (strlen($str)-1) ? $str[$i].'-' : $str[$i];
		}
		
		return $s;
	}
	
	//---------------------------------------------------------------------------
	
	protected function V_is_city_exists($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		$sql = "SELECT * FROM cities WHERE name='$str'";
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();
		if( $row===false ){
			return 'The city with that name does not exist in our database';
		}
		
		return false;
	}
	
	protected function V_is_tags_exists($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' || !is_array($str) ) return false;
		
		foreach( $str as $key => $val ){
			$sql = "SELECT * FROM tags WHERE tags_id=$key";
			pgsql::getInstance()->query($sql);
			$row = pgsql::getInstance()->fetch_a();
			if( $row===false ){
				return "The tag id:$key does not exist";
			}
		}
		
		return false;
	}
	
	protected function V_check_user_password($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		if( !isset($param['users_id']) ) {
			trigger_error("Validator check_user_password: no users_id", E_USER_WARNING);
			return false;
		}
		
		$sql = "SELECT * FROM users WHERE users_id=$param[users_id] AND password=md5('$str')";
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();
		if( $row===false ){
			return 'Bad password';
		}
		
		return false;
	}
	
	protected function V_unique_login($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
		
		$sql = "SELECT * FROM users WHERE login='$str'";
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();
		if( $row!==false ){
			return 'This login is already taken';
		}
		
		return false;
	}
	
	protected function V_unique_email($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
	
		$sql = "SELECT * FROM users WHERE email='$str'";
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();
		if( $row!==false ){
			return 'This email is already taken';
		}
	
		return false;
	}
	
	protected function V_unique_sql($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
	
		if( !( isset($param['sql']) ) ){
			return 'No parameter sql';
		}
		
		if( !( isset($param['param']) ) ){
			return 'No parameter param';
		}
		
		$sql = $param['sql'];
		foreach( $param['param'] as $val ){
			$p = pg_escape_string( $this->GetVal($val) );
			$sql = str_replace("#$val#", $p, $sql);
		}
		
		pgsql::getInstance()->query($sql);
		$row = pgsql::getInstance()->fetch_a();
		if( $row!==false ){
			return 'This value must be unique';
		}
	
		return false;
	}
	
	protected function V_check_promotional_code($arr, $key, $param){
		$str = Misc::GetVal($arr, $key);
		if( $str=='' ) return false;
	
		$uid = User::GetId();
		$sql = "SELECT * FROM promotional_code WHERE users_id=$uid AND code='$str' AND data_use IS NULL";
		pgsql::getInstance()->query($sql);echo $sql;
		$row = pgsql::getInstance()->fetch_a();
		if( $row===false ){
			return 'Bad promo code';
		}
	
		return false;
	}
	
	protected function V_check_photos($arr, $key, $param){
		if( !isset($param['hookers_id']) ) {
			trigger_error("Validator check_photos: no hookers_id", E_USER_WARNING);
		}
	
		$sql = "
			SELECT I.* FROM hookers_images AS HI
			LEFT JOIN images AS I ON I.images_id=HI.images_id
			WHERE date_delete IS NULL AND hookers_id=$param[hookers_id]";
		pgsql::getInstance()->query($sql);
		$row_img = pgsql::getInstance()->get_array();
		//Debug::VarDumb($row_img);
		if( isset($param['min']) && count($row_img) < $param['min']  ){
			return "Too small number of files, minimum is $param[min]";
		}
		
		if( isset($param['max']) && count($row_img) > $param['max']  ){
			return "Too many files, maximum is $param[max]";
		}
		
		foreach ($row_img as $val0){
			if( isset($param['min_x']) && $val0['width'] < $param['min_x']  ){
				return "Too small photo, minimum is $param[min_x]";
			}
			
			if( isset($param['max_x']) && $val0['width'] > $param['max_x']  ){
				return "Too large photo, maximum is $param[max_x]";
			}
			
			if( isset($param['min_y']) && $val0['height'] < $param['min_y']  ){
				return "Too small photo, minimum is $param[min_y]";
			}
			
			if( isset($param['max_y']) && $val0['height'] > $param['max_y']  ){
				return "Too large photo, maximum is $param[max_y]";
			}
			
			if( isset($param['min_size']) && $val0['filesize'] < $param['min_size']  ){
				return "Too small size, minimum is $param[min_size]";
			}
			
			if( isset($param['max_size']) && $val0['filesize'] > $param['max_size']  ){
				return "Too large size, maximum is $param[max_size]";
			}
			
			foreach ($row_img as $val1){
				if( $val0['images_id']!=$val1['images_id'] && $val0['md5_file']==$val1['md5_file'] ){
					return  "Photos can not be repeated";//"Zdjęcia nie mogą się powtarzać";
				}
			}
		}
		
		return false;
	}
}

?>