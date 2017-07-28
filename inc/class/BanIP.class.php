<?php
	class BanIP{
		
		public static function CreatebBlacklist(){
			$str = "<?php\r\n";
			$str .='$arr_blacklist = array('."\r\n";
			
			$sql = "SELECT * FROM blacklist WHERE date_delete IS NULL";
			$res = pgsql::getInstance()->qa($sql);
			foreach($res as $val){
				$str .= "'".$val['ip_pattern']."',\r\n"; 
			}
			
			$str .=");\r\n";
			$str .="?>";
			
			file_put_contents('./inc/arr_blacklist.inc.php', $str);
		}
		
		public static function AddIP($ip_pattern, $comment, $insert_users_id){
			$param['ip_pattern'] = $ip_pattern;
			$param['comment'] = $comment;
			$param['insert_users_id'] = $insert_users_id;
		
			$sql = Misc::MakeInsertFromRequestParam('blacklist', $param);
			pgsql::getInstance()->query($sql);
			
			self::CreatebBlacklist();
		}
		
		public static function DeleteIP($blacklist_id){
			$sql = "UPDATE blacklist SET date_delete=now() WHERE date_delete IS NULL AND blacklist_id=$blacklist_id";
			pgsql::getInstance()->query($sql);
			
			self::CreatebBlacklist();
		}
		
		/*Ilość zapytań na minute*/
		public static function GetRPM($ip=NULL){
			if( $ip=='' ) $ip = $_SERVER['REMOTE_ADDR'];
			
			$sql = "
					SELECT 
						count(*)
					FROM statistic AS S
					WHERE date_day=now()::date AND ip='$ip'
					GROUP BY substr(date_insert::text, 1, 16)
					ORDER BY count(*) DESC
					LIMIT 1
			";
			$row = pgsql::getInstance()->qf($sql);
			$cnt = $row['count'];
		}
		
		public static function AutoBan(){
			$request = $_SERVER['REQUEST_URI'];
			$http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$ip = $_SERVER['REMOTE_ADDR'];
			$comment = "SYSTEM: ";
			
			if( $http_referer!='' && !(
					preg_match('/^http/i', $http_referer) || preg_match('/^android\-app/i', $http_referer) || 
					preg_match('/^about\:blank/i', $http_referer) || preg_match('/^sexatlas\.pl/i', $http_referer)
			) ){
				$comment .= "HTTP_REFERER";
				self::AddIP($ip, $comment, NULL);
				return true;
			}
			
			//Jeśli liczba zapytań większa niż 200 na minute to BAN
			if( self::GetRPM() >= 200 ){
				$comment .= "RPM >= 200";
				self::AddIP($ip, $comment, NULL);
				return true;
			}
			
			if( preg_match('/\.sql$/i', $request) ){
				$comment .= "*.sql";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.cgi$/i', $request) ){
				$comment .= "*.cgi";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.aspx$/i', $request) ){
				$comment .= "*.aspx";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.asp$/i', $request) ){
				$comment .= "*.asp";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.dll$/i', $request) ){
				$comment .= "*.dll";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.exe$/i', $request) ){
				$comment .= "*.exe";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.jsp$/i', $request) ){
				$comment .= "*.jsp";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.jspa$/i', $request) ){
				$comment .= "*.jspa";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.sql\.gz$/i', $request) ){
				$comment .= "*.sql.gz";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.sql\.tar$/i', $request) ){
				$comment .= "*.sql.tar";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.asax\.bak$/i', $request) ){
				$comment .= "*.asax.bak";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.pgsql$/i', $request) ){
				$comment .= "*.pgsql";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.sqlite3$/i', $request) ){
				$comment .= "*.sqlite3";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.yml$/i', $request) ){
				$comment .= "*.yml";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/\.ini$/i', $request) ){
				$comment .= "*.ini";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/phpinfo/i', $request) ){
				$comment .= "phpinfo";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/phpMoAdmin/i', $request) ){
				$comment .= "phpMoAdmin";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/phpMyAdmin/i', $request) ){
				$comment .= "phpMoAdmin";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/WEB-INF/i', $request) ){
				$comment .= "WEB-INF";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/<script>/i', $request) ){
				$comment .= "script";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/null\,null\,null/i', $request) ){
				$comment .= "null,null,null";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/select/i', $request) && preg_match('/from/i', $request) ){
				$comment .= "SELECT FROM";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/select/i', $request) && preg_match('/count/i', $request) ){
				$comment .= "SELECT COUNT";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/select/i', $request) && preg_match('/null/i', $request) ){
				$comment .= "SELECT NULL";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/select/i', $request) && preg_match('/char/i', $request) ){
				$comment .= "SELECT CHAR";
				self::AddIP($ip, $comment, NULL);
				return true;
			}elseif( preg_match('/windowswin\.ini/i', $request) ){
				$comment .= "windowswin.ini";
				self::AddIP($ip, $comment, NULL);
				return true;
			}
			
			return FALSE;
		}
	}
?>