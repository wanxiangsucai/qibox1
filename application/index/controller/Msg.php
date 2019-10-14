<?php
namespace app\index\controller;

use app\common\controller\IndexBase;


class Msg extends IndexBase
{
    public function index()
    {
		return $this->fetch('index');
    }
    
}

