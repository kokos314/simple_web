<?php

class FileUpload{
	const file_idx = 'file_uploaded';
	private static $message = array();
	private static $allowed_types = array();
	private static $size_min = 0;
	private static $size_max = 0;
	private static $res_x_min = 0;
	private static $res_x_max = 0;
	private static $res_y_min = 0;
	private static $res_y_max = 0;
	private static $check_md5 = false;
	private static $file_count_min = 0;
	private static $file_count_max = 0;
	private static $is_error = FALSE;
	private static $event_remove = array();
	private static $prefix_idx = '';
	private static $select_img_radio = FALSE; //Wybór zdjecia pole radio
	
	private static function GetIdX(){
		return self::file_idx . self::$prefix_idx;
	}
	
	private static function SetFile($file, $idx=''){
		if( $idx=='' ){
			$_SESSION[self::GetIdX()][] = $file;
		}else{
			if( !isset( $file[$idx] ) ) $file['id']=$idx;
			$_SESSION[self::GetIdX()][$idx] = $file;
		}
	}
	
	private static function GetFile($idx){
		return isset( $_SESSION[self::GetIdX()][$idx] ) ? $_SESSION[self::GetIdX()][$idx] : false;
	}
	
	private static function DeleteFile($idx){
		unset( $_SESSION[self::GetIdX()][$idx] );
	}

	private static function GetArrayFile(){
		if( !isset($_SESSION[self::GetIdX()]) ) return array();
		return $_SESSION[self::GetIdX()];
	}
	
	private static function CountFile(){
		$cnt = isset($_SESSION[self::GetIdX()]) ? count($_SESSION[self::GetIdX()]) : 0;
		return $cnt;
	}
	
	private static function IsSetFile(){
		return isset($_SESSION[self::GetIdX()]);
	}
	
	public static function ClearFiles(){
		$_SESSION[self::GetIdX()]=array();
	}
	
	public static function ClearFilesIfId(){
		$arr_file = self::GetArrayFile();
		$arr_del = array();
		foreach ( $arr_file as $k=>$v ){
			if( isset($v['id']) ){
				$arr_del[]=$k;
			}
		}
		
		foreach ( $arr_del as $k=>$v ){
			self::DeleteFile($v);
		}
		
		//$_SESSION[self::GetIdX()]=array();
	}
	
	public static function SetPrefixIdx($prefix_idx){
		self::$prefix_idx = $prefix_idx;
	}
	
	public static function SetSelectImgRadio($select_img_radio){
		self::$select_img_radio = $select_img_radio;
	}
	
	public static function AddEventRemove($fun){
		//is_callable($var)
		self::$event_remove[] = $fun;
	}
	
	public static function SetFileCount($file_count_min, $file_count_max){
		self::$file_count_min = $file_count_min;
		self::$file_count_max = $file_count_max;
	}
	
	public static function SetCheckMd5($check_md5){
		self::$check_md5 = $check_md5===true;
	}
	
	public static function SetRes($res_x_min, $res_y_min, $res_x_max, $res_y_max){
		self::$res_x_min = $res_x_min;
		self::$res_x_max = $res_x_max;
		self::$res_y_min = $res_y_min;
		self::$res_y_max = $res_y_max;
	}
	
	public static function SetSize($size_min, $size_max){
		self::$size_min = $size_min;
		self::$size_max = $size_max;
	}
	
	public static function SetAllowedTypes($val, $clear=FALSE){
		if( $clear ) self::$allowed_types = array();
		
		if( is_array($val) ){
			$arr = $val;
		}else{
			$arr[] = $val;
		}
		self::$allowed_types = array_merge( self::$allowed_types, $arr );
	}
	
	private static function ResetError(){
		self::$is_error = false;
	}
	
	private static function SetError(){
		self::$is_error = true;
	}
	
	public static function IsSuccess(){
		return !self::$is_error;
	}
	
	public static function ConvertFileArray($img_key){
		$arr_file = array();
		if( isset( $_FILES[$img_key] ) ){
			$keys = array_keys( $_FILES[$img_key]['name'] );
			
			foreach( $keys as $k ){
				$tab['name'] = $_FILES[$img_key]['name'][$k];
				$tab['tmp_name'] = $_FILES[$img_key]['tmp_name'][$k];
				$tab['error'] = $_FILES[$img_key]['error'][$k];
				$tab['size'] = $_FILES[$img_key]['size'][$k];
				$tab['type'] = $_FILES[$img_key]['type'][$k];
				
				$arr_file[] = $tab;
			}
		}
		
		return $arr_file;
	}
	
	public static function AddFile($file_path, $id=NULL){
		if( !file_exists($file_path) ) return false;
		
		$tab['path'] = $file_path;
		$tab['size'] = filesize($file_path);
		$tab['name'] = basename($file_path);
		//$tab['type'] = mime_content_type($file_path);
		
		/*$finfo = finfo_open(FILEINFO_MIME);
		$tab['type'] =  finfo_file($finfo, $file_path);
		finfo_close($finfo);*/
		$tab['type'] = mime_content_type($file_path);
		
		$tab['md5'] = md5_file($file_path);
		if( preg_match('/image/i', $tab['type']) ){
			$tab['res'] = getimagesize($file_path);
		}
		self::SetFile($tab, $id);
		
		return true;
	}
	
	public static function Controller(){
		if( Ctrl::IsPost() ){
			if( Ctrl::PV('file_upload_controller_action')=='upload' ){
				$prefix = time() . '_' . rand(0, 1000) . '_';
				$arr_f = self::ConvertFileArray('img');
				$prefix_idx = Ctrl::PV('file_upload_controller_prefix_idx');
				self::SetPrefixIdx($prefix_idx);
				foreach( $arr_f as $key=>$val ){
					if( $val['error']==UPLOAD_ERR_OK && is_uploaded_file($val['tmp_name']) ){
						$target_path = D_PATH_IMG_TMP . $prefix . $val['name'];
						move_uploaded_file(
		         			$val['tmp_name'],
		         			$target_path
		       			);
						
						$tmp = $val;
						$tmp['path'] = $target_path;
						if( preg_match('/image/i', $tmp['type']) ){
							$tmp['res'] = getimagesize($target_path);
						}
						$tmp['md5'] = md5_file($target_path);
						self::SetFile($tmp);
					}
				}
				
				$GLOBALS['layout']=false;
				FileUpload::ShowFiles();die();
			}elseif( Ctrl::PV('file_upload_controller_action')=='remove' ){
				$file_id = Ctrl::PV('file_upload_controller_file_id');
				$prefix_idx = Ctrl::PV('file_upload_controller_prefix_idx');
				self::SetPrefixIdx($prefix_idx);
				
				if( ($file = self::GetFile($file_id))!==false ){
					foreach( self::$event_remove as $k=>$v ){
						$res = call_user_func_array($v, array($file));
						if( $res===true ){
							if( file_exists($file['path']) ) unlink( $file['path'] );
							self::DeleteFile($file_id);
						}
					}
				}
				
				$GLOBALS['layout']=false;
				FileUpload::ShowFiles();die();
			}
			
		}//END IF POST
	}
	
	public static function GetFileUploadControllerAction(){
		return Ctrl::PV('file_upload_controller_action');
	}
	
	public static function Validation($idx){
		if( ($file = self::GetFile($idx))===false ) return NULL;
		
		
		if( count(self::$allowed_types)>0 && !in_array($file['type'], self::$allowed_types) ){
			return 'Zły typ pliku'; //Bad file type
		}
		
		if( self::$size_min > 0 && $file['size'] < self::$size_min ){
			return 'Plik jest za mały, minimalna to: ' . Misc::human_size( self::$size_min ); //The file is too small
		}
		
		if( self::$size_max > 0 && $file['size'] > self::$size_max ){
			return 'Plik jest za duży, maksymalny to: ' . Misc::human_size( self::$size_max ); //The file is too large
		}
		
		if( self::$res_x_min > 0 && self::$res_y_min > 0 && isset($file['res']) && (self::$res_x_min > $file['res'][0] || self::$res_y_min > $file['res'][1]) ){
			return 'Rozdzielczość pliku jest za mała'; //The resolution of the file is too small
		}
		
		if( self::$res_x_min > 0 && self::$res_y_min > 0 && isset($file['res']) && (self::$res_x_max < $file['res'][0] || self::$res_y_max < $file['res'][1]) ){
			return 'Rozdzielczość pliku jest za duża'; //The resolution of the file is too small
		}
		
		if( self::$check_md5===true ){
			$arr_file = self::GetArrayFile();
			foreach ($arr_file as $val){
				if( $val!=$file && $val['md5']==$file['md5'] ){
					return 'Takie zdjecie już jest wgrane'; //This photo is already loaded
				}
			}
		}
		
		return false;
	}
	
	public static function Validate(){
		self::ResetError();
		
		$cnt = self::CountFile();
		if( Ctrl::IsPost() && self::$file_count_min > 0 && $cnt < self::$file_count_min ){
			self::SetError();
		}elseif ( Ctrl::IsPost() && self::$file_count_max > 0 && $cnt > self::$file_count_max ){
			self::SetError();
		}
		
		$arr_file = self::GetArrayFile();
		foreach ($arr_file as $key=>$val){
			 if( ($msg = self::Validation($key))!==false ) self::SetError();
		}
	}
	
	public static function GetFiles(){
		return self::GetArrayFile();
	}
	
	public static function ShowFiles(){
		self::ResetError();
		$size_w = 300;
		$size_h = 300;
		?>
			<div class="file_box_show" onclick="$('#file_to_upload').click();" style="width: <?php echo $size_w; ?>px; height: <?php echo $size_h; ?>px;">
				<div class="file_box_input_text">Dodaj zdjęcie</div>
				<div class="file_box_input">+</div>
				<?php
					$cnt = self::CountFile();
					if( Ctrl::IsPost() && self::$file_count_min > 0 && $cnt < self::$file_count_min ): ?>
				<div class="file_box_show_error"><?php self::SetError(); echo 'Za mało zdjeć ('.$cnt.'), minimalna liczba zdjeć to '.self::$file_count_min; ?></div>
				<?php elseif ( Ctrl::IsPost() && self::$file_count_max > 0 && $cnt > self::$file_count_max ): ?>
				<div class="file_box_show_error"><?php self::SetError(); echo 'Za dużo zdjeć ('.$cnt.'), maksymalna liczba zdjeć to '.self::$file_count_max; ?></div>
				<?php endif; ?>
			</div>
		<?php
		if( !self::IsSetFile() ) return;
		
		$arr_del = array();
		$arr_file = self::GetArrayFile();
		foreach ($arr_file as $key=>$val){
			$target_path = $val['path'];
			if( !file_exists($target_path) ) {
				$arr_del[]=$key;
				continue;
			}
			?>
			<div class="file_box_show" style="width: <?php echo $size_w; ?>px; height: <?php echo $size_h; ?>px;">
				<div class="file_box_show_info">
					<?php 
						if( isset($val['res']) ) echo 'Rozdzielczosć: '.$val['res'][0].'x'.$val['res'][1].'&nbsp;<br>';
						echo 'Wielkość: '.Misc::human_size( $val['size'], '' );
					?>
				</div>
				<img onclick="$('#preview_img_<?php echo $key; ?>').css('display', 'block');" alt="" src="<?php echo Misc::PathRemoveDot( $target_path ); ?>">
				<div class="file_box_show_button_close" onclick="FileRemove('<?php echo $key; ?>');">X</div>
				<?php if( ($msg = self::Validation($key))!==false ): self::SetError(); ?>
				<div class="file_box_show_error"><?php echo $msg; ?></div>
				<?php endif; ?>
				<?php if( self::$select_img_radio===true ): ?>
				<div style="position: absolute; bottom: 0px; background-color: black;">
					<?php
						$id_img_radio = isset($val['tmp_name']) ? $val['tmp_name'] : $val['id']; 
					?>
					<input type="radio" name="file_upload_img_radio" value="<?php echo $id_img_radio; ?>"><?php echo $id_img_radio; ?>
				</div>
				<?php endif; ?>
			</div>
			<div class="file_box_show_preview" onclick="$('#preview_img_<?php echo $key; ?>').css('display', 'none');" id="preview_img_<?php echo $key; ?>">
				<div class="box_standard_style file_box_show_preview_box">
					<img alt="" src="<?php echo Misc::PathRemoveDot( $target_path ); ?>">
					<div class="file_box_show_button_close" onclick="">X</div>
				</div>
			</div>
			<?php
		}
		
		foreach($arr_del as $val){
			self::DeleteFile($val);
		}
	}
	
	public static function FileBox($class='progress_auto'){
		$name = 'img[]';
		$multiple = true;
		$id='file_to_upload';
		$accept = join(', ', self::$allowed_types);
		?>
		<input type="file" name="<?php echo $name; ?>" <?php echo $multiple===true ? 'multiple' : ''; ?> id="<?php echo $id; ?>" <?php echo $accept!='' ? "accept=\"$accept\"" : ''; ?> onchange="FileSelected(this);" style="display: none;">
		<div id="file_box_loading" class="message_container" style="display: none;">
			<div class="file_box_loading_box">
				<div class="file_box_loading_box_text">Ładowanie proszę czekać</div>
				<div class="file_box_loading_box_progress"><?php self::ProgressBar('progress_auto'); ?></div>
			</div>
		</div>
		<div id="file_box_info" class="file_box_info">
		<?php 
			self::ShowFiles();
		?>
		</div><?php
	}
	
	public static function ProgressBarJS(){
		?>
		<script type="text/javascript">
			function progress(e){
			    if(e.lengthComputable){
			        var max = e.total;
			        var current = e.loaded;
		
			        //var Percentage = Math.round( ((current * 100)/max) * 100) / 100;
			        var Percentage = Math.round( ((current * 100)/max) * 1) / 1;
			       
			        $( "#progress" ).text( "" + Percentage + "%" );
			        $( "#progress_bar" ).css( "width", Percentage + "%" );
		
			        if(Percentage >= 100){
			           // process completed  
			        }
			    }  
			 }
			
			function FileSelected(obj){
				var name = $(obj).attr("name");
				var id = $(obj).attr("id");

				$('#file_box_loading').css('display', 'block');
						
				var fd = new FormData();
				fd.append("file_upload_controller_action", "upload");
				fd.append("file_upload_controller_prefix_idx", '<?php echo self::$prefix_idx; ?>');
				var ins = document.getElementById(id).files.length;
				for (var x = 0; x < ins; x++) {
				    fd.append(name, document.getElementById(id).files[x]);
				}
		
				$.ajax({
					url: "<?php HtmlHelper::Url('', ''); ?>",
					type: "POST",
					data: fd,
					success: function( data ) {
						$('#file_box_info').html( data );
						$('#file_box_loading').css('display', 'none');
				  	},
					xhr: function() {
		                var myXhr = $.ajaxSettings.xhr();
		                if(myXhr.upload){
		                    myXhr.upload.addEventListener('progress',progress, false);
		                }
		                return myXhr;
		        	},
					processData: false,  // tell jQuery not to process the data
					contentType: false   // tell jQuery not to set contentType
				});
			}

			function FileRemove(file_id){
				var fd = new FormData();
				fd.append("file_upload_controller_action", "remove");
				fd.append("file_upload_controller_file_id", file_id);
				fd.append("file_upload_controller_prefix_idx", '<?php echo self::$prefix_idx; ?>');
		
				$.ajax({
					url: "<?php HtmlHelper::Url('', ''); ?>",
					type: "POST",
					data: fd,
					success: function( data ) {
						$('#file_box_info').html( data );
				  	},
					processData: false,  // tell jQuery not to process the data
					contentType: false   // tell jQuery not to set contentType
				});
			}
		</script>
		<?php
	}
	
	public static function ProgressBar($class='progress'){
		?><div class="<?php echo $class; ?>">
			<div class="progress_bar" id="progress_bar"></div>
			<div class="progress_info" id="progress">0%</div>
		</div><?php
	}
	
}

?>