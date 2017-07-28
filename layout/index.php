<?php 
	$cur_flag = I18N::GetShortCode();
?><!DOCTYPE html>
<html lang="<?php echo $cur_flag; ?>">
	<?php
		HtmlHeader::getInstance()->EGetHtmlHeader();
	?>
<body>
	<?php include_once("analyticstracking.php"); ?>
	
	<script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>
	
	<?php /*
<div style="position: fixed; z-index: -99; width: 100%; height: 100%; left: 0px; top: 0px;">
  <iframe frameborder="0" height="100%" width="100%" 
    src="https://youtube.com/embed/f2yzKIPgIk0?autoplay=1&controls=0&showinfo=0&autohide=1">
  </iframe>
</div>*/?>
	
	<?php Message::getInstance()->Create(); ?>
	
	
	<?php if( (!isset($_COOKIE['AGREEMENT'])) || Ctrl::GV('agreement')=='1' ): ?>
		<div class="agreement" id="agreement_id">
			<div class="agreement_text">
				<div class="agreement_text_content">
					This website uses 'cookies' to give you the best, most relevant experience. Using this website means you're happy with this.
					<a href="http://www.google.com/policies/privacy/partners/">read more</a>
					<a href="javascript:Agreement();" class="agreement_enter"><?php I18N::ET('Accept & Close'); ?></a>
				</div>
				<?php /*
				<div class="agreement_enter">
					<a href="javascript:Agreement();" class="button_s2"><?php I18N::ET('Accept & Close'); ?></a>
				</div>
				
				<div class="agreement_exit">
					<a href="http://google.pl" rel="nofollow"><?php I18N::ET('Exit'); ?></a>
				</div>*/?>
			</div>
		</div>
	<?php endif; ?>
	
	
	
	<header class="header_page">
		<div class="header_page_nav">
			<a href="<?php HtmlHelper::Url('home', 'index')?>"><span>Start</span></a>
			<a href="http://adachsoft.com" target="_blank"><span>Games</span></a>
		
		<?php /*	
			<!-- Umieść ten tag w nagłówku lub tuż przed tagiem zamykającym treść. -->
<script src="https://apis.google.com/js/platform.js" async defer>
  {lang: 'en'}
</script>

<!-- Umieść ten tag w miejscu, w którym ma być widoczny widżet. -->
<div class="g-follow" data-annotation="bubble" data-height="20" data-href="//plus.google.com/u/0/114408806417766369147" data-rel="publisher"></div>
*/?>

<?php /*
			<a href="#"><img alt="" src="/img/icon_fb.png" style="height: 24px;"></a>*/?>

			<a href="https://plus.google.com/u/0/114408806417766369147" target="_blank"><img alt="" src="/img/icon_gp.png" style="height: 24px;"></a>
			<a href="https://twitter.com/ADACHSOFT" target="_blank"><img alt="" src="/img/icon_tw.png" style="height: 24px;"></a>

		</div>
		
		<a href="<?php HtmlHelper::Url('home', 'index'); ?>" class="header_page_title" title="Do It Yourself">
		<?php /*<div class="header_page_title"></div>*/?>
			<div><b>D</b>o <b>I</b>t <b>Y</b>ourself</div>
			<div style="font-size: 16px; border-top: 2px solid #ffffff; padding-top: 2px;">DIY <b>&</b> Home Improvement</div>
		</a>
		
		<div class="banner_header">
			<?php //echo Ctrl::GetHtml('ads', '_adsens_header'); ?>
			<?php echo Ctrl::GetHtml('ads', '_adsens_header_elastic'); ?>
		</div>
		
	</header>

	<nav>
		<a href="<?php HtmlHelper::Url('home', 'index')?>">
			<span style="">start</span>
		</a>
		
		<?php
			$sql = "SELECT * FROM articles WHERE date_pub IS NOT NULL AND languages_id=2 ORDER BY date_pub DESC LIMIT 5";
			$res = pgsql::getInstance()->qa($sql);
		?>
		<?php foreach( $res as $val ): ?>
		<a href="<?php HtmlHelper::Url('home', 'article', array('id'=>$val['articles_id'], 'n'=>Misc::NameForUrl($val['title']))); ?>">
			<img alt="<?php echo $val['title']; ?>" src="<?php echo '/'.$val['img_path']; ?>" />
		</a>
		<?php endforeach; ?>
	</nav>
	
	<section>
		<?php //NavigationBreadcrumb::CreateBreadcrumbHtml(); ?>
		<?php echo $content_data; ?>
	</section>
	
	<footer>
		<div class="banner_footer"><?php echo Ctrl::GetHtml('ads', '_adsens_footer_elastic'); ?></div>
	</footer>
	
	<?php Statistics::DebugInfo(); ?>
	
</body>
</html>