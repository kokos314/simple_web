<?php
	$request = $_SERVER['REQUEST_URI'];
	BanIP::AutoBan();
?><!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>404 Not Found</h1>
<p>
	The requested URL <b><?php echo $request; ?></b> was not found on this server.<br>
	<?php /*echo "M: <b>$module</b>"; ?><br>
	<?php echo "A: <b>$action</b>"; */?>
</p>
<a href='<?php HtmlHelper::Url('home', 'index', '', true); ?>'><?php echo D_NAME_OF_THE_PORTAL_URL; ?></a>
</body></html>