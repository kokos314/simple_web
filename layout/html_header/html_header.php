<?php 
	
	HtmlHeader::getInstance()->AddJavaScript('/java/jquery-1.11.3.min.js', true);

	HtmlHeader::getInstance()->AddCss('/all_in_one.css');

	HtmlHeader::getInstance()->SetIfTitle( D_NAME_OF_THE_PORTAL . '' );
	HtmlHeader::getInstance()->SetIfDescription( D_NAME_OF_THE_PORTAL . '' );
	HtmlHeader::getInstance()->SetIfKeywords( 'DIY, Do it yourself, Arduni, ESP8266, DS18b20' );
	
	
	
?><head>
	<base href="./">
	<meta charset="UTF-8">
	
	<?php HtmlHeader::getInstance()->CreateMeta(); ?>
	<link rel="icon" href="/favicon.ico" type="image/ico" sizes="16x16">
	<?php /*<meta name="identifier-url" content="http://www.webestools.com/" />*/?>
	<meta name="revisit-after" content="1" />
	<meta name="robots" content="all,index,follow" />
	<meta name="classification" content="global,all" />
	<meta name="distribution" content="global">
	<meta name="page-topic" content="">
	<meta name="abstract" content="">
	<?php /*
	<link rel="apple-touch-icon" sizes="57x57" href="/img/favicons/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/img/favicons/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/img/favicons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/img/favicons/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/img/favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/img/favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/img/favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/img/favicons/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="/img/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/img/favicons/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="/img/favicons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="/img/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/img/favicons/manifest.json">
	<link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#b91d47">
	<meta name="msapplication-TileImage" content="/img/favicons/mstile-144x144.png">
	<meta name="theme-color" content="#ffffff">*/?>
	
	<?php I18N::HtmlHeaderLang(); ?>

	<!-- Google Authorship and Publisher Markup -->
	<link rel="author" href="https://plus.google.com/114408806417766369147/posts"/>
	<link rel="publisher" href="https://plus.google.com/114408806417766369147"/>
	
<?php /*	
	<!-- BEGIN: Facebook -->
	<meta property="fb:admins" content="100012320767063" />
	<meta property='fb:page_id' content="1517596448274168" />
	<meta property="og:title" content="<?php echo $meta_title; ?>" />
	<meta property="og:description" content="<?php echo $meta_description; ?>" />
	<meta property="og:url" content="http://adachsoft.com/" />
	<meta property='og:site_name' content='ADACHSOFT'/>
	<meta property="og:image" content="<?php echo $meta_img; ?>" /> 
	<!-- END: Facebook -->*/ ?>
	
	<?php /*
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0 feed" href="http://adachsoft.com/rss/feed/" />
	*/?>
	
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@ADACHSOFT" />
	<meta name="twitter:title" content="<?php echo HtmlHeader::getInstance()->GetTitle(); ?>" />
	<meta name="twitter:description" content="<?php echo HtmlHeader::getInstance()->GetDescription(); ?>" />
	<meta name="twitter:creator" content="@ADACHSOFT" />
	<meta name="twitter:url" content="<?php echo D_SERVER_URL; ?>" />
	 
	
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
	<?php Statistics::Javascript(); ?>
	
	<script type="text/javascript">
		$(document).ajaxComplete(function(e, xhr, settings){
	    var loginPageHeader = xhr.getResponseHeader("LoginPage");
			if(loginPageHeader && loginPageHeader !== ""){
				window.location.replace(loginPageHeader);
			}
	    
			//console.log('xhr.status: ' + xhr.status);
		});
		
	
		function Agreement(){
			$.ajax({
			    type     : "GET",
			    url      : "<?php HtmlHelper::Url('home', 'ajax_agreement'); ?>",
			    data     : {
			    },
			    success : function(msg) {
			        $('#agreement_id').css('display', 'none');
			    },
			    complete : function(r) {
			    },
			    error:    function(error) {
			    }
			});
		}

		function ConfirmDel(){
			return confirm('<?php I18N::ET("Czy na pewno chcesz usunąć?"); ?>');
		}
	
	</script>
</head>