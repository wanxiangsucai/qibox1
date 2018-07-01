<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: CaiWeiMing
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\common\controller\IndexBase;
use app\admin\model\Attachment as AttachmentModel;


/**
 * 用于处理ajax请求的控制器
 * @package app\admin\controller
 */
class Ajax extends IndexBase
{
    
    /**
     * 检查附件是否存在
     * @param string $md5 文件md5
     * @return \think\response\Json
     */
    public function check($md5 = '')
    {
        $md5 == '' && $this->error('参数错误');
        
        // 判断附件是否已存在
        if ($file_exists = AttachmentModel::get(['md5' => $md5])) {
            if ($file_exists['driver'] == 'local') {
                $file_path = PUBLIC_URL.$file_exists['path'];
            } else {
                $file_path = $file_exists['path'];
            }
            return json([
                    'code'   => 1,
                    'info'   => '上传成功',
                    'class'  => 'success',
                    'id'     => $file_exists['path'],//$file_exists['id'],
                    'path'   => $file_path
            ]);
        } else {
            $this->error('文件不存在');
        }
    }
    
}