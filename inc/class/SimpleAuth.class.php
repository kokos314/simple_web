<?php
	class SimpleAuth{
		//--------------------
		private static $users = array(
				'arek' => 'nowydom',
				'kamila' => 'rudykot' 
		);
		//--------------------
	
		
		public static function CheckAuth(){
			
			/*
			var_dump($_SERVER['PHP_AUTH_USER']);
			var_dump($_SERVER['PHP_AUTH_PW']);
			
			if( !isset($_SERVER['PHP_AUTH_USER'] )){
			    self::Auth();
			} else {
				if( in_array($_SERVER['PHP_AUTH_USER'], self::$users)  ){
					$pass = Misc::GetVal(self::$users, $_SERVER['PHP_AUTH_USER']);
					if( $pass!=$_SERVER['PHP_AUTH_PW'] ){
						self::Auth();
					}
				}else{
					self::Auth();
				}
			    //echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
			    //echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
			}*/
			
			//$valid_passwords = array ("kamila" => "rudykot");
			$valid_passwords = self::$users;
			$valid_users = array_keys($valid_passwords);
			
			$user = $_SERVER['PHP_AUTH_USER'];
			$pass = $_SERVER['PHP_AUTH_PW'];
			
			$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);
			
			if (!$validated) {
			  header('WWW-Authenticate: Basic realm="My Realm"');
			  header('HTTP/1.0 401 Unauthorized');
			  die ("Not authorized");
			}
		}
		
		public static function Auth(){
			header('WWW-Authenticate: Basic realm="My Realm"');
		    header('HTTP/1.0 401 Unauthorized');
		    echo 'Text to send if user hits Cancel button';
		    exit;
		}
		
	}
?>