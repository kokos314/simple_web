<?php
 //TODO - przniesc to gdzies idziej - mało eleancki rozwiązanie
 //set_error_handler( "myErrorHandler" );

//------------------------------------------
/*
 * Auto ładowanie klas
 * 
 */
 function __autoload($class_name)
 {	
 	$class_file = './inc/class/'.$class_name.'.class.php';
 	if( !file_exists($class_file) ){
 		/*$class_file = './inc/class/ORM_class/ORM_out/object/'.$class_name.'.class.php';
 		if( !file_exists($class_file) ){
 			$class_file = './inc/class/ORM_class/ORM_out/base/'.$class_name.'.class.php';
 			if( !file_exists($class_file) ){
 				die( "File not found($class_name): $class_file" );
 				return;
 			}
 		}*/
 		die( "File not found($class_name): $class_file" );
		return;
 	}
 	require_once( $class_file );
 }
//--------------------------------------------------------------------------------

 
 
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	/*
    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
    	require_once( '' );
    	exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Unknown error type: [$errno] $errstr<br />\n";
        break;
    }*/

    require_once( './system/errors/errors.inc.php' );
    /* Don't execute PHP internal error handler */
    return true;
} 

//--------------------------------------------------------------------------------

function get_path($module, $action){
	return './modules/' . $module . '/' . $action.'.php';
}

//--------------------------------------------------------------------------------

function get_html($module, $action, $param=NULL){
	return get_partial(get_path($module, $action), $param);
}

//--------------------------------------------------------------------------------

/*
 * Funkcja wykonuje plik php a nastepnie zwraca kod html
 * 
 */

function get_partial($file, $param=NULL) 
{
	if( is_array( $param ) ){
		foreach ($param as $k => &$v) {
			//echo "$k => $v <br />";
			//if( !is_callable( $v ) )
			 $$k = $v;
	    }
	}
    ob_start();
    require $file;
    return ob_get_clean();
} 

//--------------------------------------------------------------------------------

function get_module($module=NULL, $action=NULL, $param=NULL) 
{
	$module_path = './modules/'.$module.'/';
	if( !file_exists( $module_path.'action/'.$action.'.php' ) ){
		die( 'ERROR: ' . $module_path.'action/'.$action.'.php' );
	}
	
	if( !file_exists( $module_path.'template/'.$action.'.php' ) ){
		die( 'ERROR: ' . $module_path.'template/'.$action.'.php' );
	}
	
	if( is_array( $param ) ){
		foreach ($param as $k => &$v) {
			 $$k = $v;
	    }
	}
    ob_start();
    require $module_path.'action/'.$action.'.php';
    require $module_path.'template/'.$action.'.php';
    return ob_get_clean();
} 

?>