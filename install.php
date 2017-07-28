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
    
    //-------------------------------------------------------------------------------
    function log_html($message, $log_type='s'){ //s - success, e - error, w - warring
        switch ( $log_type ){
            case 's': ?><span style="color: green;">success: <?php echo $message; ?></span></br><?php ; break;
            case 'e': ?><span style="color: red;">error: <?php echo $message; ?></span></br><?php ; break;
            case 'w': ?><span style="color: yellow;">warring: <?php echo $message; ?></span></br><?php ; break;
        }
    }
?>
</div>