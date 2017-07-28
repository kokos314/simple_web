<?php

class CreateSettings{
	const FILE_NAME = "./inc/class/Settings.class.php";
	
	public static function CreateIf(){
		if( !file_exists(self::FILE_NAME) ){
			self::Create();
		}
	}
	
	public static function Create(){
		$str = "<?php\r\n";
		$str .= "/* CreateSettings: ".date('Y-m-d H:i:s')." */\r\n";
		$str .= "class Settings{\r\n";
		
		$sql = "
			SELECT S.*, ST.name AS t_name FROM settings AS S
			LEFT JOIN setting_types AS ST ON ST.setting_types_id=S.setting_types_id";
		pgsql::getInstance()->query($sql);
		$res = pgsql::getInstance()->get_array();
		foreach( $res as $k=>$v ){
			$str .= "\tconst $v[name] = " . self::GetVal($v) . ";\r\n";
		}
		
		$str .= "}\r\n";
		$str .= "?>";
		
		file_put_contents(self::FILE_NAME, $str);
	}
	
	public static function GetVal($row){
		/*if( $row['value']=='' ){
			return null;
		}*/
		
		switch( $row['t_name'] ){
			case 'INT':
				if( $row['value']=='' ){
					return '0';
				}
				return unserialize( $row['value'] );
				break;
			case 'STRING':
				if( $row['value']=='' ){
					return "''";
				}
				return "'" . addslashes( unserialize( $row['value'] ) ) . "'";
				break;
			case 'BOOLEAN':
				if( $row['value']=='' ){
					return 'false';
				}
				return ((bool)unserialize( $row['value'] ))===true ? 'true' : 'false';
				break;
		}
		
	}
}

?>