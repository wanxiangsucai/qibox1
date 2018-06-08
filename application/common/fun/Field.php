<?php
namespace app\common\fun;

class Field{
    
    public function load_js($type=''){
		static $ifload = [];
		if($ifload[$type]!==true){
			$ifload[$type] = true;
			return true;
		}
		
	}
}