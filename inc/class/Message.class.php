<?php

class Message{
	private $title = '';
	private $message = '';
	private $url_redirect = '';
	private $hide = '';
	private $id_msg = '';
	
	//----------------------------------SINGLETON---------------------------------------------
	private static $instance;
	private function __construct() {
		$this->id_msg = 'message_'.time().'_'.rand(100, 1000);
	} 	// Blokujemy domyślny konstruktor publiczny
	private function __clone(){} 		//Uniemozliwia utworzenie kopii obiektu
 
	public static function getInstance(){
		return ($i = &self::$instance) ? $i : $i = new self;
	}	
	//-------------------------------------------------------------------------------
	public function GetIdMsg(){
		return $this->id_msg;
	}
	
	public function SetRedirect($url){
		$this->url_redirect = $url;
	}
	
	public function SetHide($hide){
		$this->hide = $hide;
	}
	
	public function SetTitle($title){
		$this->title = $title;
	}
	
	public function SetMessage($message){
		$this->message = $message;
	}
	
	public function BeginMessage(){
		ob_start();
	}
	
	public function EndMessage(){
		$this->message = ob_get_clean();
	}
	
	public function Create(){
		if( $this->message=='' ) return;
		//$id_msg = 'message_'.time().'_'.rand(100, 1000);
		$id_msg = $this->id_msg;
		$style = '';
		if( $this->hide===TRUE ){
			$style = ' style="display: none;"';
		}
		?>
		<div id="<?php echo $id_msg; ?>" class="message_container"<?php echo $style; ?>>
			<div class="box_standard_style message_body">
				<h3><?php echo $this->title; ?></h3>
				
				<p><?php echo $this->message; ?></p>
				
				<div class="message_button">
					<?php if( $this->url_redirect=='' ): ?>
						<a href="javascript:void(0)" onclick="$('#<?php echo $id_msg; ?>').css('display', 'none');" class="button_standard">OK</a>
					<?php else: ?>
						<a href="<?php echo $this->url_redirect; ?>" class="button_standard">OK</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

}

?>