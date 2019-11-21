<?php
namespace app\member\controller;


use app\common\controller\MemberBase;


class Quote extends MemberBase
{
    /**
     * 站内引用的主题列表
     * @param string $type
     * @return mixed|string
     */
    public function index($type='cms'){
        $this->assign('type',$type);
        return $this->fetch();
    }
}
