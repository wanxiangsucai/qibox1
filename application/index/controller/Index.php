<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

class Index extends IndexBase
{
    public function index()
    {print_r(\think\Db::name('wxopen_info')-> column('appid,status,wxapp_subscribe_template_id,uid,qun_id AS aid,gh_id AS wxapp_ghid,payid AS wxapp_payid,paykey AS wxapp_paykey'));exit;
//         if(input('wxapp')==1||get_cookie('wxapp')){
//             set_cookie('wxapp', 1);
//             $this->redirect(url('cms/index/index'),301);
//         }
        if($this->webdb['sys_mode_type']==1 && modules_config('qun') && in_wap()){
            $this->redirect(iurl('qun/api/gethome'),[],301);
        }elseif($this->webdb['qun_index_id'] && modules_config('qun')){
            $this->redirect(iurl('qun/content/show',['id'=>$this->webdb['qun_index_id']]),[],301);
        }elseif( IN_WAP===true && ($sysname = $this->webdb['set_module_wapindex']) ){
            if(modules_config($sysname)){
                $this->redirect(iurl($sysname.'/index/index'),[],301);
            }
        }elseif( ($sysname = $this->webdb['set_module_index'])!='' ){
            if(modules_config($sysname)){
                $this->redirect(iurl($sysname.'/index/index'),[],301);
            }
        }
        define('PAGE_TYPE', 'web_index');
        return $this->fetch('../index');
    }
    
    /**
     * 空方法 ,自适应
     * @param unknown $action
     * @return mixed|string
     */
    public function _empty($action)
    {
        if (!preg_match('/^[\w]+$/i', $action)) {
            $this->error('方法名有误!');
        }
        $id = input('id');
        $id && $this->assign('id',intval($id));
        return $this->fetch();
    }
    
    public function test($page=1){
        /*
        set_time_limit(0);
        $ck = 0;
        $page || $page=1;
        $rows = 200;
        $min = ($page-1)*$rows;
        $array = query("SELECT id,picurl FROM qb_cms_content1 order by id asc limit $min,$rows ");
        foreach($array AS $rs){
            if(preg_match('/^article/', $rs['picurl'])){
                
                $pic = 'uploads/'.$rs['picurl'];
                $s .= $pic.'<br>';
                query("UPDATE qb_cms_content1 SET picurl='$pic' WHERE id='{$rs[id]}' ");
            }
            $ck++;
        }
        if(empty($ck)){
            die('转换结束');
        }
        echo $s;
        $page++;
        echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=/index.php/index/index/test.html?page=$page'>";
        exit;*/
    }
    

    
}

