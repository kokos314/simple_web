<?php

class HtmlHeader{
	private $path = './layout/html_header/';
	private $filename = 'html_header';
	private $javascript = array();
	private $css = array();
	private $css_version = '';
	private $meta = array();
	private $meta_property = array();
	private $meta_link = array();
	private $ex_header;
	private $meta_keywords = array();
	//private $theme = '';
	
//----------------------------------SINGLETON---------------------------------------------
	private static $instance;
	private function __construct() {} 	// Blokujemy domyślny konstruktor publiczny
	private function __clone(){} 		//Uniemozliwia utworzenie kopii obiektu
 
	public static function getInstance(){
		return ($i = &self::$instance) ? $i : $i = new self;
	}	
	
//-------------------------------------------------------------------------------
	public function BeginHeader(){
		ob_start();
	}
	
	public function EndHeader(){
		$this->ex_header .= ob_get_clean();
	}
	
	public function SetIfKeywords($keywords){
		//if( !isset($this->meta['keywords']) ) $this->meta['keywords'] = $keywords;
		if( is_array($keywords) ){
			$arr = $keywords;
		}else{
			$arr = explode(',', $keywords);
		}
		$this->meta_keywords = array_merge( $this->meta_keywords, $arr );
	}
	
	public function SetIfDescription($description){
		if( !isset($this->meta['description']) ) $this->meta['description'] = $description;
	}
	
	public function SetIfTitle($title){
		if( !isset($this->meta['title']) ) $this->meta['title'] = $title;
	}
	
	public function SetKeywords($keywords){
		if( is_array($keywords) ){
			$arr = $keywords;
		}else{
			$arr = explode(',', $keywords);
		}
		$this->meta_keywords = array_merge( $this->meta_keywords, $arr );
		//$this->meta['keywords'] = $keywords;
	}
	
	public function SetDescription($description, $convert_text=FALSE){
		$this->meta['description'] = $description;
	}
	
	public function SetTitle($title){
		$this->meta['title'] = $title;
	}
	
	/*public function SetTheme($theme){
		$this->theme = $theme;
	}*/
	
	public function Convert2Description($text, $len=160){
		$meta_desc = $text;
		$meta_desc = str_replace(array("\r", "\n", '"', "'"), array('', '', '', ''), strip_tags($meta_desc));
		$meta_desc = substr(preg_replace('/\s+/', ' ', $meta_desc), 0, $len);
		$meta_desc = strip_tags( $meta_desc );
		
		return $meta_desc;
	}
	
	public function Convert2Keywords($keywords, $max_len=256){
		$arr_key = array();
		if( is_array($keywords) ){
			$arr_key = $keywords;
		}else{
			$arr_key = explode(',', $keywords);
		}
		$arr_key = array_unique( $arr_key );
		
		$str_k0 = '';
		$str_k1 = '';
		$i=0;
		foreach( $arr_key as $key_w){
			$kw = trim($key_w);
			if( $kw=='' ) continue;
			
			$str_k0 .= $kw;
			if( $i < count($arr_key)-1 ){
				$str_k0 .= ',';
			}
			
			if( strlen( $str_k0 ) <= $max_len ){
				$str_k1 = $str_k0;
			}
			$i++;
		}
		
		return $str_k1;
	}
	
	public function SetCssVersion($css_version){
		$this->css_version = $css_version;
	}
	
	public function AddMeta($name, $content){
		$this->meta[$name] = $content;
	}
	
	public function AddMetaProperty($name, $content){
		$this->meta_property[$name] = $content;
	}
	
	public function AddLink($rel, $href){
		$this->meta_link[] = array(
			'rel'=>$rel,
			'href'=>$href,
		);
	}
	
	public function AddJavaScript($file_js, $at_first=FALSE){
		if( $at_first ){
			array_unshift($this->javascript, $file_js);
		}else{
			$this->javascript[] = $file_js;
		}
	}
	
	public function AddCss($file_css){
		$this->css[] = $file_css;
	}
	/*
	public function AddCss($file_css, $theme_on=true){
		if( $theme_on ){
			$this->css[] = $this->theme . $file_css;
		}else{
			$this->css[] = $file_css;
		}
	}*/

	public function GetTitle(){ 
		return isset( $this->meta['title'] ) ? $this->meta['title'] : NULL; 
	}
	
	public function GetDescription(){ 
		return isset( $this->meta['description'] ) ? $this->meta['description'] : NULL; 
	}
	
	public function CreateMeta(){
		echo "\t<!-- BEGIN: Meta -->\r\n";
		
		$this->meta['keywords'] = $this->Convert2Keywords($this->meta_keywords);
		$this->meta['description'] = isset($this->meta['description']) ? $this->Convert2Description($this->meta['description']) : '';
		
		if( isset( $this->meta['title'] ) ){
			echo "\t";
			?><title><?php echo $this->meta['title']; ?></title><?php
			echo "\r\n";
		}
		foreach( $this->meta as $key=>$val ){
			echo "\t";
			?><meta name="<?php echo $key; ?>" content="<?php echo $val; ?>"><?php 
			echo "\r\n";
		}
		foreach( $this->meta_property as $key=>$val ){
			echo "\t";
			?><meta property="<?php echo $key; ?>" content="<?php echo $val; ?>"><?php 
			echo "\r\n";
		}
		foreach( $this->meta_link as $key=>$val ){
			echo "\t";
			?><link <?php echo $this->Attr($val); ?>><?php 
			echo "\r\n";
		}
		$this->CreateJavaScript();
		$this->CreateCss();
		echo $this->ex_header;
		
		echo "\t<!-- END: Meta -->\r\n";
	}
	
	public function CreateJavaScript(){
		echo "\t<!-- BEGIN: JavaScript -->\r\n";
		foreach( $this->javascript as $js ){
			echo "\t";
			?><script type="text/javascript" src="<?php echo $js; ?>"></script><?php 
			echo "\r\n";
		}
		echo "\t<!-- END: JavaScript -->\r\n";
	}
	
	public function CreateCss(){
		echo "\t<!-- BEGIN: CSS -->\r\n";
		foreach( $this->css as $css ){
			echo "\t";
			$css_link = $this->css_version=='' ? $css : $css.'?v='.$this->css_version;
			?><link rel="stylesheet" type="text/css" href="<?php echo $css_link; ?>"><?php 
			echo "\r\n";
		}
		echo "\t<!-- END: CSS -->\r\n";
	}
	
	public function GetHtmlHeader(){
		$param = array(
			//'javascript' =>
		);
		
		$file_header = $this->path.$this->filename.'.php';
		$html = get_partial($file_header, $param);
		return $html;
	}
	
	public function EGetHtmlHeader(){
		echo $this->GetHtmlHeader();
	}
	
	private function Attr($arr){
		$str = " ";
		foreach( $arr as $key=>$val ){
			$str .= "$key='$val' ";
		}
		return $str;
	}
}

?>