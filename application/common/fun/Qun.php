<?php
namespace app\common\fun;

class Qun{
    
    public function topic_num($info=[],$time=3600){        
        return query(config('system_dirname').'_content'.$info['mid'],
                [
                        'where'=>[
                                //'ext_sys'=>$info['ext_sys'],
                                'ext_id'=>$info['ext_id'],                                
                        ],
                        'count'=>'id',
                ],$time);
    }
    
    
}