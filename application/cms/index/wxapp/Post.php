<?php
namespace app\cms\index\wxapp;

use app\common\controller\index\wxapp\Post AS _Post; 

//小程序 发表内容处理
class Post extends _Post
{
   
    /**
     * 上传文件,图片或视频
     */
    public function postFile(){
        return parent::postFile();
    }
    
    
    
    /**
     * 删除主题
     * @param number $id 主题ID
     * @return \think\response\Json
     */
    public function delete($id=0){
        return parent::delete($id);
    }
    
    /**
     * 修改主题
     * @param number $id
     * @return \think\response\Json
     */
    public function edit($id=0){
        return parent::edit($id);
    }
    
    /**
     * 新发表主题
     * @return \think\response\Json
     */
    public function add($mid=1){
        $data = input();
        if(!$data['fid']){
            return $this->err_js('你没有选择栏目!');
        }elseif(!$data['content']){
            return $this->err_js('内容不能为空!');
        }
        return parent::add($mid);
    }
    
    /**
     * 获取栏目数据
     * @return \think\response\Json
     */
    public function get_sort(){
        return parent::get_sort();
    }
    
    /**
     * 主题点赞
     * @param number $id 主题ID
     * @return \think\response\Json
     */
    public function agree($id=0){
        return parent::agree($id);
    }
    
}













