<?php 
	$cur_flag = I18N::GetShortCode();
?><!DOCTYPE html>
<html lang="<?php echo $cur_flag; ?>">
	<?php
		HtmlHeader::getInstance()->EGetHtmlHeader();
	?>
<body>
	<?php include_once("analyticstracking.php"); ?>
	
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
	
	
	<?php echo $content_data; ?>
	
	
	<?php Statistics::DebugInfo(); ?>
	
</body>
</html>