<?php
namespace app\common\upgrade;


class U25{
    public static function up(){
	    
	    $strA = '<?php
namespace app\qun\index;
use app\index\controller\Labelmodels AS _Labelmodels;
class Labelmodels extends _Labelmodels
{  
}';
	    
	    $strB = '<?php
namespace app\qun\index;
use app\common\controller\index\Labelhy AS _Label;
class Labelhy extends _Label
{
}';
	    $strC = '<?php
namespace app\qun\index;
use app\common\controller\index\Label AS _Label;
class Label extends _Label
{
}';
	    
	    $array = modules_config();
	    foreach($array AS $rs){
	        
	        $filename = APP_PATH.$rs['keywords'].'/index/Labelmodels.php';
	        if(!is_file($filename)){
	            $strA = str_replace('qun', $rs['keywords'], $strA);
	            file_put_contents($filename, $strA);
	        }
	        
	        $filename = APP_PATH.$rs['keywords'].'/index/Labelhy.php';
	        if(!is_file($filename)){
	            $strB = str_replace('qun', $rs['keywords'], $strB);
	            file_put_contents($filename, $strB);
	        }
	        
	        $filename = APP_PATH.$rs['keywords'].'/index/Label.php';
	        if(!is_file($filename)){
	            $strC = str_replace('qun', $rs['keywords'], $strC);
	            file_put_contents($filename, $strC);
	        }
	        
	    }
		  
	}
}


