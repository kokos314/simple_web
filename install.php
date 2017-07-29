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
            
            
            echo "<pre style='color:white;'>".htmlspecialchars($str)."</pre>";
            file_put_contents('./config/config.php', $str);
            
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
