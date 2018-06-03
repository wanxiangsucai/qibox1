<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

class Map extends IndexBase
{
    public function index($xy='',$title=''){       
        if(empty($xy)){
            $xy = '113.220243,28.181133';
        }
        list($position_x,$position_y) = explode(',',$xy);
        $this->assign('position_x',$position_x);
        $this->assign('position_y',$position_y);
        $this->assign('title',$title);
        return $this->fetch('bdmap');
    }
}

?>