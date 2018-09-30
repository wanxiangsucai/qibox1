<?php
namespace app\index\controller;

use app\common\controller\IndexBase;


class Index extends IndexBase
{
    public function index()
    {
//         if(input('wxapp')==1||get_cookie('wxapp')){
//             set_cookie('wxapp', 1);
//             $this->redirect(url('cms/index/index'),301);
//         }
        if( IN_WAP===true && ($sysname = $this->webdb['set_module_wapindex']) ){
            //return $this->redirect($sysname.'/index/index');
            if(modules_config($sysname)){
                $this->redirect(url($sysname.'/index/index'),[],301);
            }
        }elseif( ($sysname = $this->webdb['set_module_index'])!='' ){
            //return $this->redirect($sysname.'/index/index');
            if(modules_config($sysname)){
                $this->redirect(url($sysname.'/index/index'),[],301);
            }            
        }
		return $this->fetch('../index');
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

