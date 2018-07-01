<?php
namespace app\common\controller;



//定义是会员中心
define('IN_MEMBER', true);

/**
 * 后台总控制器
 */
class MemberBase extends Base
{
    /**
     * 会员中心初始化
     */
    protected function _initialize()
    {
        parent::_initialize();
        
        hook_listen('member_begin',$array=['user'=>$this->user]);     //钩子扩展
        
        if(!defined('LOGIN_PAGE')&&empty($this->user)){
            $this->success('请先登录',iurl('index/login/index'));
        }
        
        // 自动表单公共模板
        $this->assign('auto_tpl_base_layout',APP_PATH.'member/view/default/layout.htm');
    }
}
