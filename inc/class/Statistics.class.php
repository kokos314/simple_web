<?php

class Statistics{
	private static $statistics_id = NULL;
	
	public static function GetId(){
		if( self::$statistics_id==NULL ){
			$id = pgsql::getInstance()->get_seq('statistic_statistic_id_seq');
			self::$statistics_id=$id;
		}
		return self::$statistics_id;
	}
	
	public static function GetLastId(){
		if(  isset( $_SESSION['statistics::last_statistics_id'] ) ){
			return $_SESSION['statistics::last_statistics_id'];
		}
		//return self::$statistics_id;
		return self::GetId();
	}
	
	public static function Update(){
		$b = false;
		/*$statistics_id = Ctrl::GV('statistics_id');
		$vw = Ctrl::GV('viewport_w');
		$vh = Ctrl::GV('viewport_h');
		$sw = Ctrl::GV('screen_w');
		$sh = Ctrl::GV('screen_h');
		$page_load_time = Ctrl::GV('page_load_time');*/
		$statistics_id = Ctrl::PV('statistics_id');
		
		if( $statistics_id!='' ){
			
			$vw = Ctrl::PV('viewport_w');
			$vh = Ctrl::PV('viewport_h');
			$sw = Ctrl::PV('screen_w');
			$sh = Ctrl::PV('screen_h');
			$page_load_time = Ctrl::PV('page_load_time');
			$page_load_time = Misc::GetValueOrNull( $page_load_time );
			
			$sql = "UPDATE statistic SET
					viewport_w=$vw, viewport_h=$vh,
					screen_w=$sw, screen_h=$sh,
					date_edit=now(),
					page_load_time=$page_load_time
					WHERE statistic_id=$statistics_id";
			pgsql::getInstance()->query($sql);
			$b = true;
		}
		return $b;
	}
	
	public static function Save($info=''){
		if( !(isset( $GLOBALS['stat_off'] ) && $GLOBALS['stat_off']===TRUE) ){
			
			$_cnt_v='1';
			$_cnt_v = isset($_COOKIE['cnt_v']) ? $_COOKIE['cnt_v'] : $_cnt_v;
			$parent_id = isset($_COOKIE['last_sid']) ? intval( $_COOKIE['last_sid'] ) : 'NULL';
			
			if( !setcookie("cnt_v", $_cnt_v+1, time()+(3600*24*180), '/', D_SERVER) ){
				$_cnt_v='-1';
			}
			
			$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
			$ip=$_SERVER['REMOTE_ADDR'];
			$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
			$http_accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : ''; 
			$country_code='NULL';
			$access_url=$_SERVER['SCRIPT_URI'].( $_SERVER['QUERY_STRING']!='' ?  '?'.$_SERVER['QUERY_STRING'] : '' );
			$refid = Ctrl::GV('refid');
			$users_id = User::GetId();
			$session_id = session_id();
			$http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$cnt = $_cnt_v;
			
			
			$request_method = substr($request_method, 0, 64);
			$access_url = substr($access_url, 0, 2048);
			$module = substr(Ctrl::M(), 0, 256);
			$action = substr(Ctrl::A(), 0, 256);
			$http_accept_language = substr($http_accept_language, 0, 64);
			$refid = substr($refid, 0, 128);
			

			$user_agent = $user_agent!='' ? "'".pg_escape_string( $user_agent )."'" : 'NULL';
			$http_accept_language = $http_accept_language!='' ? "'".pg_escape_string( $http_accept_language )."'" : 'NULL';
			$http_referer = $http_referer!='' ? "'".pg_escape_string( $http_referer )."'" : 'NULL';
			$refid = $refid!='' ? "'".pg_escape_string( $refid )."'" : 'NULL';
			$http_host = $_SERVER['HTTP_HOST']!='' ? "'".pg_escape_string( $_SERVER['HTTP_HOST'] )."'" : 'NULL';
			$stat_module = $module!='' ? "'".pg_escape_string( $module )."'" : 'NULL';
			$stat_action = $action!='' ? "'".pg_escape_string( $action )."'" : 'NULL';
			$access_url = $access_url!='' ? "'".pg_escape_string( $access_url )."'" : 'NULL';
			$users_id = $users_id!='' ? "'".pg_escape_string( $users_id )."'" : 'NULL';
			$session_id = $session_id!='' ? "'".pg_escape_string( $session_id )."'" : 'NULL';
			$request_method = $request_method!='' ? "'".pg_escape_string( $request_method )."'" : 'NULL';
			$info = $info!='' ? "'".pg_escape_string( $info )."'" : 'NULL';
			
			$id = self::GetId();
			$_SESSION['statistics::last_statistics_id']=$id;
			
			$sql = "
				INSERT INTO statistic(
					statistic_id, date_insert, ip, user_agent, http_referer, 
					refid, cnt, country_code, http_host,
					module, action, access_url, users_id, session_id,
					http_accept_language, parent_id, info, request_method
				)VALUES (
					$id, now(), '$ip', $user_agent, $http_referer, 
					$refid, $cnt, $country_code, $http_host,
					$stat_module, $stat_action, $access_url, $users_id, $session_id,
					$http_accept_language, $parent_id, $info, $request_method
				);
			";
			pgsql::getInstance()->query($sql);
			
			setcookie("last_sid", $id, time()+(3600*24*180), '/', D_SERVER);
			
			//self::$statistics_id = $id;
			return $id;
		}
	}
	
	
	
	public static function Javascript(){
		?>
		<script type="text/javascript">
			var statistics_id = <?php echo Statistics::GetId(); ?>;
			var startTime = (new Date()).getTime();
			var page_load_time;
			
			$(window).on("load",function(){
				var endTime = (new Date()).getTime();
				var millisecondsLoading = endTime - startTime;
				page_load_time = millisecondsLoading;

				<?php if( Ctrl::GV('debug')=='1' ): ?>
				$('#debug_time_load').text( page_load_time + 'ms' );
				<?php endif; ?>
				//console.log( 'statistics load: ' + statistics_id + " t:" + millisecondsLoading );
			});
			
			//$(window).on('beforeunload', function(){
			$(window).on('unload', function(){
				//console.log( 'statistics unload: ' + statistics_id );
				/*var str_url = 
	  	  	  		"/index.php?statistics_id=" + statistics_id + 
	  	  	  		"&viewport_w=" + $(window).width() + 
		  	  	  	"&viewport_h=" + $(window).height() + 
		  	  		"&screen_w=" + screen.width + 
		  	  		"&screen_h=" + screen.height + 
		  	  		"&page_load_time=" + page_load_time;*/

				var formData = "statistics_id=" + statistics_id + 
	  	  	  		"&viewport_w=" + $(window).width() + 
		  	  	  	"&viewport_h=" + $(window).height() + 
		  	  		"&screen_w=" + screen.width + 
		  	  		"&screen_h=" + screen.height + 
		  	  		"&page_load_time=" + page_load_time;
	  	  		
	  			$.ajax({
	  				type: "POST",
					data : formData,
	  				//url: str_url,
	  				url: '<?php HtmlHelper::Url('home', 'enter', array(), true); ?>',
	  				async : false,
	  				context: document.body,
	  				success: function(data) {
	  					console.log( 'statistics success: ' + statistics_id );
	  				}
	  			}).done(function(data) {
	  				//console.log( 'statistics done: ' + data );
	  			});
			});

			<?php if( Ctrl::GV('debug')=='1' ): ?>
			$( document ).ready(function() {
				$( "#screen_info" ).text( $(window).width() + "x" + $(window).height() );
			});
			
			$( window ).resize(function() {
				$( "#screen_info" ).text( $(window).width() + "x" + $(window).height() );
			});
			<?php endif; ?>
		
			
		</script>
		<?php
	}
	
	public static function DebugInfo(){
		if( Ctrl::GV('debug')=='1' ):
		?>
		<div class="debug">
			<table>
				<tr>
					<th>SCREEN INFO:</th>
					<td><div id="screen_info">screen_info</div></td>
				</tr>
				<tr>
					<th>TIME LOAD:</th>
					<td><div id="debug_time_load">loading...</div></td>
				</tr>
				<tr>
					<th>COUNT OF SQL QUERY:</th>
					<td><?php echo pgsql::getInstance()->get_cnt_query(); ?></td>
				</tr>
				<tr>
					<th>TIME EXEC:</th>
					<td>#time_load#</td>
				</tr>
				<tr>
					<th>MEMORY USAGE:</th>
					<td>#mem_usage#</td>
				</tr>
				<tr>
					<th>MEMORY PEAK USAGE:</th>
					<td>#mem_peak_usage#</td>
				</tr>
				<tr>
					<th>PAGE SIZE:</th>
					<td>#page_size#</td>
				</tr>
				<tr>
					<th>COUNT OF INCLUDED FILES:</th>
					<td>#cnt_included_files#</td>
				</tr>
			</table>
			<button type="button" onclick="if( $('#debug_info_sql').css('display')=='none') $('#debug_info_sql').css('display', 'block'); else $('#debug_info_sql').css('display', 'none');">SQL</button>
			<div id="debug_info_sql">
				<?php
					$arr_sql = pgsql::getInstance()->get_array_query();
					foreach ( $arr_sql as $key=>$val ):
					$class_name = $key % 2 == 0 ? 'debug_info_sql_r0' : 'debug_info_sql_r1';
				?>
				<div class="<?php echo $class_name; ?>"><pre><?php echo $val; ?></pre></div>
				<?php endforeach;?>
			</div>
		</div>
		<?php
		endif;
	}
}

?>