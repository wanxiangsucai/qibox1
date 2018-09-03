<?php
namespace app\common\traits;
//use app\common\builder\Form;
use app\index\model\Labelhy AS LabelModel;
//use app\common\traits\AddEditList;

trait LabelhyEdit {
    use LabelEdit;
    
    /**
     * 自动生成表格
     * @param unknown $info
     * @param unknown $tab_items
     * @return mixed|string
     */
    protected function get_form_table($info,$tab_items) {
        $this->form_items = $tab_items;
        //$this->form_items[] = ['number','cache_time','标签缓存时间','单位是秒'];
        return $this->editContent($info);
    }

    public function delete($name='',$hy_id=0){
        if (LabelModel::destroy(['name'=>$name,'ext_id'=>$hy_id])) {
            $this -> success('删除成功');
        } else {
            $this -> error('删除失败');
        } 
    }
    
    /**
     * 取得某条标签数据 
     * @return array|NULL[]|unknown
     */
    protected function getTagInfo(){
        return getArray( LabelModel::get([
                'name'=>input('name'),
                'ext_id'=>input('hy_id'),                
        ]) );
    }
    
    //保存标签数据
    protected function save($array){
        $result = LabelModel::save_data($array);
        if($result===true){
            if($this->request->isAjax()){
                $this->success('设置成功');
            }else{
                echo '<script type="text/javascript">
                    parent.layer.msg("设置成功!");
                    parent.layer.close(parent.layer.getFrameIndex(window.name));parent.location.reload();
                    </script>';
                exit;
            }
        }else{
            $this->error('设置失败'.$result);
        }
    }

} 
