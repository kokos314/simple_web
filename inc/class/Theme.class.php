<?php
	class Theme{
		private static $theme = '';
		private static $theme_path = '';
		private static $path_img = '/img/theme/';
		private static $path_css = '/css/theme/';
		
		public static function SetTheme($theme){
			self::$theme = $theme;
			if( $theme=='' ){
				self::$theme_path = '';
			}else{
				self::$theme_path = $theme . '/';
			}
		}
		
		public static function GetPathImg( $filename ){
			return self::$path_img . self::$theme_path . $filename;
		}
		
		public static function GetPathCss( $filename ){
			return self::$path_css . self::$theme_path . $filename;
		}
	}
?>