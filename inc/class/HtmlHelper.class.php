<?php
	class HtmlHelper{
		private static $info_serial = 0;
		
		public static function YoutubleVideo($video_id){
			?>
			<iframe class="youtube" src="https://www.youtube.com/embed/<?php echo $video_id; ?>?ecver=1&autoplay=0" frameborder="0" allowfullscreen></iframe>
			<?php
		}
		
		public static function ImgFigure($src, $alt, $figcaption=NULL){
			if( $figcaption=='' ) $figcaption = $alt;
			?><figure>
			  	<a href="<?php echo $src; ?>" target="_blank"><img alt="<?php echo $alt; ?>" src="<?php echo $src; ?>" ></a>
				<?php if( $figcaption!='' ): ?><figcaption><?php echo $figcaption; ?></figcaption><?php endif; ?>
			</figure><?php
		}
		
		public static function InputFile($name, $multiple=FALSE, $id='file_to_upload'){
			?><input type="file" name="<?php echo $name; ?>" <?php echo $multiple===true ? 'multiple' : ''; ?> id="<?php echo $id; ?>" onchange="FileSelected(this);"><?php
		}
		
		public static function InfoBox($info_text, $info_symbol='?', $box_w='', $box_h=''){
			$id = 'info_box_id_' . self::$info_serial;
			$style = '';
			$style .= $box_w!='' ? "width: $box_w"."px;" : '';
			$style .= $box_h!='' ? "height: $box_h"."px;" : '';
			?><div class="info_box_container" onmouseover="$('#<?php echo $id; ?>').css('display', 'block');" onmouseout="$('#<?php echo $id; ?>').css('display', 'none');"><?php 
				echo $info_symbol;
			?><div id="<?php echo $id; ?>" style="<?php echo $style!='' ? $style : ''; ?>" class="info_box"><?php echo $info_text; ?></div><?php
			self::$info_serial++;
		}
		
		public static function Select($name, $items=array(), $selected='', $tags=''){
			?><select name="<?php echo $name; ?>" <?php echo $tags; ?>>
			<option value=""></option>
			<?php
			foreach( $items as $k=>$v ){
				?><option <?php echo $k==$selected ? ' selected="selected"' : '';?> value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
			}
			?></select><?php
		}
		
		public static function SelectFromDb($sql, $db_key, $db_val, $name, $selected='', $tags=''){
			/*pgsql::getInstance()->query( $sql );
			$res = pgsql::getInstance()->get_array();*/
			$res = pgsql::getInstance()->cq($sql);
			
			$arr = array();
			foreach( $res as $k=>$v ){
				$arr[$v[$db_key]] = $v[$db_val]; 
			}
			
			self::Select($name, $arr, $selected, $tags);
		}
		
		public static function AHrefIfAccess($module, $action, $text, $url_query='', $tags='', $absolute=FALSE){
			if( Access::getInstance()->CheckAccess(User::GetId(), $module, $action) ){
				?><a href="<?php self::Url($module, $action, $url_query, $absolute); ?>" <?php echo " $tags"; ?>><?php echo $text; ?></a><?php
			}
		} 
		
		public static function ButtonIfAccess($module, $action, $text, $url_query='', $is_active=true, $tags='', $absolute=FALSE){
			if( $is_active && Access::getInstance()->CheckAccess(User::GetId(), $module, $action) ){
				?><a href="<?php self::Url($module, $action, $url_query, $absolute); ?>" class="button_standard" <?php echo " $tags"; ?>><?php echo $text; ?></a><?php
			}else{
				?><div class="button_gray" <?php echo " $tags"; ?>><?php echo $text; ?></div><?php
			}
		} 
		
		public static function AHref($module, $action, $text, $url_query='', $tags='', $absolute=FALSE){
			?><a href="<?php echo self::Url($module, $action, $url_query, $absolute); ?>"<?php echo " $tags"; ?>><?php echo $text; ?></a><?php
		}
		
		public static function AHrefPhone($phone, $tags=''){
			?><a href="tel:+48 <?php echo str_replace('-', '', $phone); ?>"<?php echo " $tags"; ?> title="<?php echo str_replace('-', ' ', $phone); ?>"><?php echo $phone; ?></a><?php
		}
		
		public static function Url($module='', $action='', $url_query='', $absolute=FALSE){
			echo self::UrlStr($module, $action, $url_query, $absolute);
		}
		
		public static function FormBegin($module='', $action='', $url_query='', $absolute=FALSE, $method='post', $allow_file=FALSE){
			?><form action="<?php self::Url($module, $action, $url_query, $absolute); ?>" method="<?php echo $method=='post' ? 'post' : 'get'; ?>" <?php echo $allow_file===true ? ' enctype="multipart/form-data"' : ''; ?>><?php
		}
		
		public static function UrlStr($module='', $action='', $url_query='', $absolute=FALSE/*, $hash_param=''*/){
			$module = $module=='' ? Ctrl::M() : $module;
			$action = $action=='' ? Ctrl::A() : $action;
			$arr_ma = Ctrl::GetAliasCtrl($module, $action);
			$module = $arr_ma['module'];
			$action = $arr_ma['action'];
			
			$url='';
			$req = '';
			if(is_array($url_query)){
				if( Ctrl::IsPrettyUrl() ){
					$arr_k = array_keys($url_query);
					$last_key = end($arr_k);
					foreach ( $url_query as $key=>$val ){
						if( $val=='' ) continue;
						$req .= "$key/$val";
						if( $key!=$last_key ){
							$req .= "/";
						}
					}
				}else{
					foreach ( $url_query as $key=>$val ){
						$req .= "&$key=$val";
					}
				}
			}else{
				$req = $url_query;
			}
			
			if( Ctrl::IsPrettyUrl() ){
				if( $module==D_DEFAULT_MODULE && $action==D_DEFAULT_ACTION && $req=='' ){
					$url = '/';
				}else{
					$url = "/$module/$action/".htmlspecialchars($req);
					/*if( $hash_param!='' ){
						$url = $url . $hash_param;
					}*/
				}
			}else{
				if( $module==D_DEFAULT_MODULE && $action==D_DEFAULT_ACTION && $req=='' ){
					$url = 'index.php';
				}else{
					$url = "index.php?m=$module&a=$action".htmlspecialchars($req);
				}
			}
			
			if( $absolute===true ){
				return 'http://'. $_SERVER['HTTP_HOST'] . $url;
				//return 'http://'. D_SERVER . $url;
			}else {
				return $url;
			}
		}
		
		public static function InputCheckbox($name, $checked=FALSE, $label=NULL, $tags='', $val='on'){
			$id = '';
			if( $label!='' ){
				$id = 'InputCheckbox_'.rand(0, 10000).'_'.md5( microtime() . $name );
				?><label for="<?php echo $id; ?>"><?php echo $label; ?></label><?php
			}
			if( !is_bool($checked) ){
				$checked = Misc::GetBoolFromStr($checked);
			}
			?><input <?php echo $id=='' ? '' : " id='$id' "; ?> type="checkbox" name="<?php echo $name; ?>" value="<?php echo $val; ?>" <?php echo ($checked===TRUE || $checked==$val)  ? ' checked="checked"' : ''; ?> <?php echo $tags; ?>/><?php
		}
		
		public static function InputRadio($name, $val='', $checked=FALSE, $label=NULL, $tags=''){
			$id = '';
			/*if( !is_bool($checked) ){
				$checked = Misc::GetBoolFromStr($checked);
			}*/
			if( $label!='' ){
				$id = 'InputCheckbox_'.rand(0, 10000).'_'.md5( microtime() . $name );
			}
			?><input <?php echo $id=='' ? '' : " id='$id' "; ?> type="radio" name="<?php echo $name; ?>" value="<?php echo $val; ?>" <?php echo ($checked===TRUE || $checked==$val)  ? ' checked="checked"' : ''; ?> <?php echo $tags; ?>/><?php
			if( $label!='' ){
				?><label for="<?php echo $id; ?>"><?php echo $label; ?></label><?php
			}
		}
		
		public static function TextArea($name, $val='', $tags=''){
			if( is_array($val) ){
				?><textarea name="<?php echo $name; ?>" <?php echo " $tags"; ?>><?php echo Misc::GetVal( $val, $name ); ?></textarea><?php
			}else{
				?><textarea name="<?php echo $name; ?>" <?php echo " $tags"; ?>><?php echo $val; ?></textarea><?php
			}
			
		}
		
		public static function UlFromDb($sql, $db_key, $db_val, $selected='', $tags='', $tags_li=''){
			pgsql::getInstance()->query( $sql );
			$res = pgsql::getInstance()->get_array();
			
			$arr = array();
			foreach( $res as $k=>$v ){
				$arr[$v[$db_key]] = $v[$db_val]; 
			}
			
			self::Ul($arr, $selected, $tags, $tags_li);
		}
		
		public static function Ul($items=array(), $selected='', $tags='', $tags_li=''){
			?><ul <?php echo $tags; ?>>
			<?php
			foreach( $items as $k=>$v ){
				?><li id="item_li_<?php echo $k;?>"<?php echo " $tags_li"; ?>><?php echo $k==$selected ? "<b>$v</b>" : $v; ?></li><?php
			}
			?></ul><?php
		}
		
		public static function Checkbox($name, $items=array(), $checked=array(), $tags='', $param=array()){
			$tags_span= Misc::GetVal( $param, 'tags_span');
			$tags_button= Misc::GetVal( $param, 'tags_button');
			$tags_button2= Misc::GetVal( $param, 'tags_button2');
			$button_name= Misc::GetVal( $param, 'button_name');
			$button_name2= Misc::GetVal( $param, 'button_name2');
			
			$id1 = time() . "_" .rand(0, 100);
			$id = "checkbox_" . $id1;
			?>
			<?php if( $button_name!='' ): ?>
			<script type="text/javascript">
				function SelectAll<?php echo $id1; ?>(val){
					<?php foreach( $items as $k=>$v ): ?>
						$('#<?php echo "$id"."_$k"; ?>').prop( "checked", val );
					<?php endforeach; ?>
				}
			</script>
			
			<?php if( $button_name!='' ): ?>
			<button type="button" onclick="SelectAll<?php echo $id1; ?>(true);"<?php echo " $tags_button"; ?>><?php echo $button_name; ?></button>
			<?php endif; ?>
			
			<?php if( $button_name2!='' ): ?>
			<button type="button" onclick="SelectAll<?php echo $id1; ?>(false);"<?php echo " $tags_button2"; ?>><?php echo $button_name2; ?></button>
			<?php endif; ?>
			
			<br>
			<?php endif; ?>
			<?php
			foreach( $items as $k=>$v ){
				?>
				<span<?php echo " $tags_span"; ?>>
					<input type="checkbox" name="<?php echo $name."[$k]"; ?>" value="<?php echo $k; ?>" id="<?php echo "$id"."_$k"; ?>" <?php echo isset($checked[$k]) && $checked[$k]==true ? ' checked="checked"' : '';?><?php echo " $tags"; ?> />
					<label for="<?php echo "$id"."_$k"; ?>"><?php echo $v; ?></label>
				</span><?php
			}
			
		}
		
		public static function CheckboxFromDb($sql, $db_key, $db_val, $name, $checked=array(), $tags='', $param=array()){
			pgsql::getInstance()->query( $sql );
			$res = pgsql::getInstance()->get_array();
			
			$arr = array();
			foreach( $res as $k=>$v ){
				$arr[$v[$db_key]] = $v[$db_val]; 
			}
			
			self::Checkbox($name, $arr, $checked, $tags, $param);
		}
	} //END CLASS
?>