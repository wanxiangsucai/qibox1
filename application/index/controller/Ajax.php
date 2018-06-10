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

use app\common\controller\AdminBase; 

use app\admin\model\Attachment as AttachmentModel;
use think\Cache;
use think\Db;

/**
 * 用于处理ajax请求的控制器
 * @package app\admin\controller
 */
class Ajax extends AdminBase
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
    
    /**
     * 获取联动数据
     * @param string $table 表名
     * @param int $pid 父级ID
     * @param string $key 下拉选项的值
     * @param string $option 下拉选项的名称
     * @param string $pidkey 父级id字段名
     * @return \think\response\Json
     */
    public function getLevelData($table = '', $pid = 0, $key = 'id', $option = 'name', $pidkey = 'pid')
    {
        if ($table == '') {
            return json(['code' => 0, 'msg' => '缺少表名']);
        }

        $data_list = Db::name($table)->where($pidkey, $pid)->column($option, $key);

        if ($data_list === false) {
            return json(['code' => 0, 'msg' => '查询失败']);
        }

        if ($data_list) {
            $result = [
                'code' => 1,
                'msg'  => '请求成功',
                'list' => fun('format_linkage',$data_list)
            ];
            return json($result);
        } else {
            return json(['code' => 0, 'msg' => '查询不到数据']);
        }
    }

    /**
     * 获取筛选数据
     * @param string $table 表名
     * @param string $field 字段名
     * @param array $map 查询条件
     * @param string $options 选项，用于显示转换
     * @param string $list 选项缓存列表名称
     * @return \think\response\Json
     */
    public function getFilterList($table = '', $field = '', $map = [], $options = '', $list = '')
    {
        if ($list != '') {
            $result = [
                'code' => 1,
                'msg'  => '请求成功',
                'list' => Cache::get($list)
            ];
            return json($result);
        }
        if ($table == '') {
            return json(['code' => 0, 'msg' => '缺少表名']);
        }
        if ($field == '') {
            return json(['code' => 0, 'msg' => '缺少字段']);
        }
        if (!empty($map) && is_array($map)) {
            foreach ($map as &$item) {
                if (is_array($item)) {
                    foreach ($item as &$value) {
                        $value = trim($value);
                    }
                } else {
                    $item = trim($item);
                }
            }
        }

        $data_list = Db::name($table)->where($map)->group($field)->column($field);
        if ($data_list === false) {
            return json(['code' => 0, 'msg' => '查询失败']);
        }

        if ($data_list) {
            if ($options != '') {
                // 从缓存获取选项数据
                $options = cache($options);
                if ($options) {
                    $temp_data_list = [];
                    foreach ($data_list as $item) {
                        $temp_data_list[$item] = isset($options[$item]) ? $options[$item] : '';
                    }
                    $data_list = $temp_data_list;
                } else {
                    $data_list = parse_array($data_list);
                }
            } else {
                $data_list = parse_array($data_list);
            }

            $result = [
                'code' => 1,
                'msg'  => '请求成功',
                'list' => $data_list
            ];
            return json($result);
        } else {
            return json(['code' => 0, 'msg' => '查询不到数据']);
        }
    }

}