<?php
    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$show_form_db = true;
	
	if( $_SERVER['REQUEST_METHOD']=='POST' ){
	    $action = isset( $_POST['action'] ) ? $_POST['action'] : '';
	    if( $action=='config_db' ){
            	        
            $host = $_POST['D_DB_HOST'];
            $port = $_POST['D_DB_PORT'];
            $dbname = $_POST['D_DB_DATABASE'];
            $login = $_POST['D_DB_USER'];
            $password = $_POST['D_DB_PASSWORD'];
            if( pg_connect("host=$host port=$port dbname=$dbname user=$login password=$password") ){
	            $show_form_db = false;
	            log_html( "pg_connect", 's' );
	            
	            install();
	            
	        }else{
	            log_html( "pg_connect", 'e' );
	        }
	    }
	}
?>

<?php if( $show_form_db ): ?>
<form action="install.php" method="post">
    <table>
    	<tr>
    		<td>D_DB_DATABASE</td>
    		<td><input type="text" name="D_DB_DATABASE" value=""></td>
    	</tr>
    	<tr>
    		<td>D_DB_USER</td>
    		<td><input type="text" name="D_DB_USER" value=""></td>
    	</tr>
    	<tr>
    		<td>D_DB_PASSWORD</td>
    		<td><input type="password" name="D_DB_PASSWORD" value=""></td>
    	</tr>
    	<tr>
    		<td>D_DB_HOST</td>
    		<td><input type="text" name="D_DB_HOST" value=""></td>
    	</tr>
    	<tr>
    		<td>D_DB_PORT</td>
    		<td><input type="text" name="D_DB_PORT" value="5432"></td>
    	</tr>
    	<tr>
    		<td colspan="2"><button type="submit" name="action" value="config_db">OK</button></td>
    	</tr>
    </table>
</form>
<?php endif; ?>

<?php
    //-------------------------------------------------------------------------------
    function install(){
        ?>
        <div style="background-color: black; padding: 10px;">
        <?php
            
            //--------------------------------------------------------
            $tmp_path = sys_get_temp_dir();
            copy("install.php", $tmp_path.'/install.php');
            if( file_exists( $tmp_path.'/install.php' ) ){
                log_html( "copy install.php to tmp", 's' );
            }else{
                log_html( "copy install.php to tmp", 'e' );
                die();
            }
            
            //--------------------------------------------------------
            $str = shell_exec('rm -r ./*');
            if( $str!="" ) echo "<pre>".$str."</pre>";
            
            //--------------------------------------------------------
            $tmp_path = sys_get_temp_dir();
            copy($tmp_path.'/install.php', "install.php");
            if( file_exists( './install.php' ) ){
                log_html( "copy tmp to install.php", 's' );
            }else{
                log_html( "copy tmp to install.php", 'e' );
                die();
            }
            
            //--------------------------------------------------------
            if( file_exists( "simple_web" ) ){
                log_html( "found simple_web", 'w' );
                $str = shell_exec('rm -r ./simple_web');
                if( $str!="" ) echo "<pre>".$str."</pre>";
                
                if( !file_exists( "simple_web" ) ){
                    log_html( "removed simple_web", 's' );
                }else{
                    log_html( "remove simple_web", 'e' );
                    die();
                }
            }
        
            //--------------------------------------------------------
            $str = shell_exec('git clone https://github.com/kokos314/simple_web');
            if( $str!="" ) echo "<pre>".$str."</pre>";
        
            if( file_exists( "simple_web" ) ){
                log_html( "git clone", 's' );
            }else{
                log_html( "git clone", 'e' );
                die();
            }
            
            //--------------------------------------------------------
            $str = shell_exec('mv ./simple_web/* ./');
            if( $str!="" ) echo "<pre>".$str."</pre>";
            
            //--------------------------------------------------------
            $str = shell_exec('rm -r ./simple_web');
            if( $str!="" ) echo "<pre>".$str."</pre>";
            
            //--------------------------------------------------------
            if( !file_exists( "./config" ) ){
                log_html( "mkdir config", 's' );
                mkdir('./config');
            }
            
            //--------------------------------------------------------
            if( !file_exists( "./cache" ) ){
                log_html( "mkdir cache", 's' );
                mkdir('./cache');
            }
            
            //--------------------------------------------------------
            copy( 'config.example.php', './config/config.php' );
            $str = file_get_contents( './config/config.php' );
            
            $str_s = "define('D_DB_DATABASE', '".$_POST['D_DB_DATABASE']."');";
            $str = preg_replace("/define\(\'D_DB_DATABASE\'\,\s+\'([^\']*)\'\)\;/i", $str_s, $str);
            
            $str_s = "define('D_DB_USER', '".$_POST['D_DB_USER']."');";
            $str = preg_replace("/define\(\'D_DB_USER\'\,\s+\'([^\']*)\'\)\;/i", $str_s, $str);
            
            $str_s = "define('D_DB_PORT', '".$_POST['D_DB_PORT']."');";
            $str = preg_replace("/define\(\'D_DB_PORT\'\,\s+\'([^\']*)\'\)\;/i", $str_s, $str);
            
            $str_s = "define('D_DB_PASSWORD', '".$_POST['D_DB_PASSWORD']."');";
            $str = preg_replace("/define\(\'D_DB_PASSWORD\'\,\s+\'([^\']*)\'\)\;/i", $str_s, $str);
            
            $str_s = "define('D_DB_HOST', '".$_POST['D_DB_HOST']."');";
            $str = preg_replace("/define\(\'D_DB_HOST\'\,\s+\'([^\']*)\'\)\;/i", $str_s, $str);
            
            //echo "<pre style='color:white;'>".htmlspecialchars($str)."</pre>";
            file_put_contents('./config/config.php', $str);
            
            
            //--------------------------------------------------------
            if( !file_exists( './inc/function/system.functions.php' ) ){
                log_html( "system.functions.php", 'e' );
                die();
            }
            require_once './inc/function/system.functions.php';
            log_html( "system.functions.php", 's' );
            
            if( !file_exists( './config/config.php' ) ){
                log_html( "config.php", 'e' );
                die();
            }
            require_once './config/config.php';
            log_html( "./config/config.php", 's' );
            
            //--------------------------------------------------------
            $sql = "
                CREATE TABLE languages
                (
                  languages_id serial NOT NULL,
                  name character varying(128) NOT NULL,
                  short_code character varying(4) NOT NULL,
                  active boolean NOT NULL DEFAULT true,
                  CONSTRAINT languages_pkey PRIMARY KEY (languages_id),
                  CONSTRAINT languages_short_code_key UNIQUE (short_code)
                )
                WITH (
                  OIDS=FALSE
                );
            ";
            pgsql::getInstance()->query($sql);
            
            $sql = "INSERT INTO languages VALUES (1, 'Polski', 'PL');";
            pgsql::getInstance()->query($sql);
            
            $sql = "INSERT INTO languages VALUES (2, 'English', 'EN');";
            pgsql::getInstance()->query($sql);
            
            //--------------------------------------------------------
            
            $sql = "
                CREATE TABLE www_bots
                (
                  www_bots_id serial NOT NULL,
                  name character varying,
                  ip character varying(64),
                  comment character varying(128),
                  ip_min character(16),
                  ip_max character varying(16),
                  CONSTRAINT www_bots_pkey PRIMARY KEY (www_bots_id)
                )
                WITH (
                  OIDS=FALSE
                );
            ";
            pgsql::getInstance()->query($sql);
            
            
            $sql = "
                CREATE TABLE statistic
                (
                  statistic_id serial NOT NULL,
                  ip character varying(24) NOT NULL,
                  user_agent text,
                  http_referer text,
                  refid character varying(128),
                  cnt integer,
                  country_code character varying(6),
                  date_insert timestamp without time zone,
                  date_edit timestamp without time zone,
                  http_host character varying(256),
                  module character varying(256),
                  action character varying(256),
                  access_url character varying(2048),
                  www_bots_id integer,
                  users_id integer,
                  screen_w integer,
                  screen_h integer,
                  viewport_w integer,
                  viewport_h integer,
                  session_id character varying(256),
                  http_accept_language character varying(64),
                  parent_id bigint,
                  page_load_time integer, -- Czas ładowania strony w [ms]
                  info character varying(16), -- Pole na dodatkow info
                  date_day date NOT NULL DEFAULT now(), -- Dzien przepisany z date_insert
                  request_method character varying(64),
                  CONSTRAINT statistic_pkey PRIMARY KEY (statistic_id),
                  CONSTRAINT statistic_www_bots_id_fkey FOREIGN KEY (www_bots_id)
                      REFERENCES www_bots (www_bots_id) MATCH SIMPLE
                      ON UPDATE NO ACTION ON DELETE NO ACTION
                )
                WITH (
                  OIDS=FALSE
                );
            ";
            pgsql::getInstance()->query($sql);
            
            
            $sql = "
                CREATE TABLE translations
                (
                  translations_id serial NOT NULL,
                  name_key character varying(1024),
                  name_val character varying(1024),
                  languages_id integer,
                  CONSTRAINT translations_pkey PRIMARY KEY (translations_id),
                  CONSTRAINT translations_languages_id_fkey FOREIGN KEY (languages_id)
                      REFERENCES languages (languages_id) MATCH SIMPLE
                      ON UPDATE NO ACTION ON DELETE NO ACTION,
                  CONSTRAINT translations_name_key_languages_id_key UNIQUE (name_key, languages_id)
                )
                WITH (
                  OIDS=FALSE
                );
            ";
            pgsql::getInstance()->query($sql);
            
        ?>
        </div>
        <?php    

    }
    
    
    //-------------------------------------------------------------------------------
    function log_html($message, $log_type='s'){ //s - success, e - error, w - warring
        switch ( $log_type ){
            case 's': ?><span style="color: green;">success: <?php echo $message; ?></span></br><?php ; break;
            case 'e': ?><span style="color: red;">error: <?php echo $message; ?></span></br><?php ; break;
            case 'w': ?><span style="color: yellow;">warring: <?php echo $message; ?></span></br><?php ; break;
        }
    }
?>
