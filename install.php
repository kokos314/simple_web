<?php
    if( file_exists( "simple_web" ) ){
        $str = shell_exec('rm -r ./simple_web');
        echo "<pre>".$str."</pre>";
        
        if( !file_exists( "simple_web" ) ){
            ?><span style="color: green;">success</span><?php
        }else{
            ?><span style="color: green;">error: rm -r ./simple_web</span><?php
            die();
        }
    }

    $str = shell_exec('git clone https://github.com/kokos314/simple_web');
    echo "<pre>".$str."</pre>";

    if( file_exists( "simple_web" ) ){
        ?><span style="color: green;">success</span><?php
    }
?>