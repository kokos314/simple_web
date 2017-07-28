<?php
	$alias_ctrl = array(
		'all_in_one.css' => array('module'=>'home', 'action'=>'css'),
		'sitemap.xml' => array('module'=>'home', 'action'=>'sitemap.xml'),
	);
	
	Ctrl::SetAliasCtrl( $alias_ctrl );
?>