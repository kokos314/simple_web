<?php
	Ctrl::SetLayout(false);
	header('Content-type: application/xml');
	
	//if( !cache::cache_html_start('sitemap.xml', 'sitemap') ):
	
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'."\r\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

	
    
    <?php /*---------------------------------------------------------------*/ ?>
    <?php /* HOME PAGE */ ?>
    <url>
        <loc><?php HtmlHelper::Url('home', 'index', '', true); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1</priority>
    </url>
    <?php /*---------------------------------------------------------------*/ ?>
    
    
    
</urlset>
<?php 
	/*echo cache::cache_html_end();
	else:
	
	echo cache::cache_read('sitemap.xml', 'sitemap');
	
	endif;*/ 
?>