<?php

class Articles{
	private $step;
	private $res;
	private $articles_id;
	
	public function __construct($articles_id){
		$this->articles_id = $articles_id;
		$sql = "SELECT * FROM articles WHERE articles_id=$1 AND languages_id=2";
		$this->res = pgsql::getInstance()->qf($sql, array($articles_id));
		$this->step = 1;
	}
	
	public static function GetUrlStatic($articles_id, $absolute=FALSE){
		$sql = "SELECT * FROM articles WHERE articles_id=$1 AND languages_id=2";
		$res = pgsql::getInstance()->qf($sql, array($articles_id));
		
		$title = $res['title'];
		$tile_url = Misc::NameForUrl($title);
		
		$url = HtmlHelper::UrlStr('home', 'article', array('id'=>$articles_id, 'n'=>$tile_url), $absolute);
		return $url;
	}
	
	public function GetUrl($absolute=FALSE, $refid=NULL){
		$res = $this->res;
		
		$title = $res['title'];
		$tile_url = Misc::NameForUrl($title);
		
		$param = array('id'=>$this->articles_id, 'n'=>$tile_url);
		if( $refid!='' ) $param['refid']=$refid;
		$url = HtmlHelper::UrlStr('home', 'article', $param, $absolute);
		return $url;
	}
	
	public function GetCurrentStep($step_title){
		?><h3 id="step_<?php echo $this->step; ?>">Step <?php echo $this->step.': '.$step_title; ?></h3><?
		$this->step++;
	}
	
	public function SourceCodeFromFile($file, $with_link = FALSE){
		?><div class="source_code"><?php echo htmlspecialchars( file_get_contents($file) ); ?></div><?php
		if( $with_link ){
			?><a href="<?php echo Misc::PathRemoveDot($file); ?>" id="software" target="_blank">Download source code</a><?
		}
	}
	
	public function GetTitle(){
		return $this->res['title'];
	}
	
	public function GetPath(){
		return $this->res['path'];
	}
	
	public function GetDescription(){
		return $this->res['description'];
	}
	
	public function GetHeaderHTML(){
		$res = $this->res;
		
		$path = $res['path'];
		$title = $res['title'];
		$description = $res['description'];
		$description = $this->ParseDescription( $description );
		$tile_url = Misc::NameForUrl($title);
		$img_url = D_SERVER_URL.$res['img_path'];//$path.'MainPicture.jpg';
		$id = $res['articles_id'];
		$url = HtmlHelper::UrlStr('home', 'article', array('id'=>$id, 'n'=>$tile_url), true);
		$url_og = $url;
		$img_url = D_SERVER_URL.$res['img_path'];
		
		if( Ctrl::GV('img_id')!='' ){
			$img_id = Ctrl::GV('img_id');
			$id = $this->articles_id;
			$sql = "SELECT * FROM articles_images WHERE articles_id=$id AND articles_images_id=$img_id";
			$img_row = pgsql::getInstance()->qf($sql);
			if( $img_row!=false ){
				$img_url = D_SERVER_URL.$img_row['img_path'];
				$url_og = HtmlHelper::UrlStr('home', 'article', array('id'=>$id, 'n'=>$tile_url, 'img_id'=>$img_id), true);
			}
		}
		$img_size = getimagesize($img_url);
		
		HtmlHeader::getInstance()->SetTitle( $title );
		HtmlHeader::getInstance()->SetDescription( $description );
		
		HtmlHeader::getInstance()->AddMetaProperty('og:type', 'article');
		HtmlHeader::getInstance()->AddMetaProperty('og:title', $title);
		HtmlHeader::getInstance()->AddMetaProperty('og:description', HtmlHeader::getInstance()->Convert2Description($description, 300));
		HtmlHeader::getInstance()->AddMetaProperty('og:image', $img_url);
		HtmlHeader::getInstance()->AddMetaProperty('og:url', $url_og);
		HtmlHeader::getInstance()->AddMetaProperty('og:image:width', $img_size[0]);
		HtmlHeader::getInstance()->AddMetaProperty('og:image:height', $img_size[1]);
		HtmlHeader::getInstance()->AddMetaProperty('og:site_name', D_NAME_OF_THE_PORTAL_URL);
		HtmlHeader::getInstance()->AddMetaProperty('article:published_time', Misc::GetDate($res['date_pub']));
		HtmlHeader::getInstance()->AddMetaProperty('article:author', 'ADACHSOFT');
		HtmlHeader::getInstance()->AddMeta('twitter:image', $img_url);
		HtmlHeader::getInstance()->AddLink('canonical', $url);
		
		?><a data-pin-do="buttonBookmark" data-pin-save="true" data-pin-media="<?php echo $img_url; ?>" data-pin-description="<?php echo str_replace(array("\n", "\r"), array(' ', ' '), strip_tags($description)); ?>" href="https://www.pinterest.com/pin/create/button/"></a><?php
		?><h1><?php echo $title; ?></h1><?php
		
		?><div class="main_pic"><?php
		HtmlHelper::ImgFigure('/'.$res['img_path'], $title);
		?></div><?php
		?>
			<div class="articles_banner"><?php 
				echo Ctrl::GetHtml('ads', '_adsens_article_elastic');
				//echo Ctrl::GetHtml('ads', '_adsens_content_elastic');
			?></div>
		<div class="articles_desc"><?php
			echo $description;
		?></div><?php
	}
	
	public function ParseDescription($desc){
		$html = $desc;
		$html = str_replace('#time', time(), $html);
		$html = str_replace('#date', date('Y-m-d H:i:s'), $html);
		/*$html = preg_replace_callback(
			'/\#(D_[^\s]+)(?:\s|$)/',
			function( $matches ){
				//var_dump( $matches );
				//$str = constant($matches[0]);
				$str = '';
				if( strlen($matches[0]) > 0  && $matches[0][strlen($matches[0])-1]==' ' ){
					$str = ' ';
				}
				
				return constant($matches[1]).$str;
			},
	        $html
		);*/
	    
		$html = preg_replace_callback(
			'/\#(article_url\(([^\)]*)\))(?:\s|$|\,|\.)/',
			function( $matches ){
				return $this->GetUrl($matches[2], true);
			},
	        $html
		);
		
		$html = preg_replace_callback(
			'/\#(article_link\(([^\)]*)\))(?:\s|$|\,|\.)/',
			function( $matches ){
				$obj = new Articles($matches[2]);
				return '<a href="'.$obj->GetUrl(true).'" target="_blank">'.$obj->GetTitle().'</a>';
			},
	        $html
		);
		
		$html = preg_replace_callback(
			'/\#(goto_url\(([^\)]*)\))/',
			function( $matches ){
				$url = HtmlHelper::UrlStr('home', 'goto', array('id'=>$matches[2]), true);
				return $url;
			},
	        $html
		);
		
		return $html;
	}
	
}


?>