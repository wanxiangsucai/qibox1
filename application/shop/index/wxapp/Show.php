<?php
namespace app\shop\index\wxapp;

use app\common\controller\index\wxapp\Show AS _Show; 

//小程序 内容详情页
class Show extends _Show
{
    
    /**
     * 内容详情
     * @param number $id
     * @return \think\response\Json
     */
    public function index($id=0){
        $rs = $this->model->getInfoByid($id , true);
        if(empty($rs['picurls'])){
            $rs['picurls'] = [$rs['picurl']];
        }
        foreach ($rs['picurls'] as $key=>$pic){
            $rs['picurls'][$key] = [
                    'picurl'=>$pic
            ];
        }
        $rs['content'] = $rs['full_content'];
        
        $rs['type1'] = $this->format_type('type1',$rs);
        $rs['type2'] = $this->format_type('type2',$rs);
        $rs['type3'] = $this->format_type('type3',$rs);
        $rs['content'] = str_replace('="/public/uploads', '="'.$this->request->domain().'/public/uploads', $rs['content']);
        unset($rs['full_content'],$rs['sncode']);
        //$rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
//         $data = [
//                 'code'=>0,
//                 'msg'=>'调用成功',
//                 'data'=>$rs,
//         ];
//         return json($data);
        return $this->ok_js($rs);
    }
    
    /**
     * 对商品属性进行转义处理
     * @param unknown $type 只支持type1 type2 type3 三个变量名的处理
     * @param unknown $rs
     * @return NULL|NULL|unknown[][][]|unknown[]
     */
    protected function format_type($type,$rs){
        if($rs[$type]==''){
            return null;
        }
        $value = json_decode($rs[$type],true);
        $array = [];
        foreach ($value AS $vs){
            if($vs==''){
                continue;
            }
            list($name,$price,$num) = explode('|', $vs);
            $array[] = [
                    'name'=>$name,
                    'price'=>$price,
                    'num'=>$num,
            ];
        }
        $model = get_field($rs['mid']);
        $data = [
                'title'=>$model[$type]['title'],
                'items'=>$array,
        ];
        return $array?$data:null;
    }
    

}













