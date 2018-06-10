<?php
namespace app\common\fun;
use think\Db;
/**
 * Zbuilder表单器
 */
class Zbuilder{
    
    /**
     * 获取附件路径
     * @param int $id 附件id
     * @return string
     */
    public function get_file_path($id=0){
        if(strstr($id,'uploads/')){
            if(!is_numeric($id)){
                return PUBLIC_URL.$id;
            }
            $path=model('admin/attachment')->getFilePath($id);
            if(!$path){
                return '/public/static/admin/img/none.png';
            }
            return $path;
        }else{
            return $id;
        }
    }
    
    /**
     * 获取图片缩略图路径
     * @param int $id 附件id
     * @return string
     */
    public function get_thumb($id=0){
        if(strstr($id,'uploads/')){
            if(!is_numeric($id)){
                return PUBLIC_URL.$id;
            }
            $path=model('admin/attachment')->getThumbPath($id);
            if(!$path){
                return '/public/static/admin/img/none.png';
            }
            return $path;
        }else{
            return $id;
        }
    }
    
    
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string $name 字符串
     * @param integer $type 转换类型
     * @return string
     */
    public function parse_name($name, $type = 0) {
        if ($type) {
            return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }
    
    
    /**
     * 使用bootstrap-datepicker插件的时间格式来格式化时间戳
     * @param null $time 时间戳
     * @param string $format bootstrap-datepicker插件的时间格式 https://bootstrap-datepicker.readthedocs.io/en/stable/options.html#format
     * @return false|string
     */
    public function format_date($time = null, $format='yyyy-mm-dd') {
        $format_map = [
                'yyyy' => 'Y',
                'yy'   => 'y',
                'MM'   => 'F',
                'M'    => 'M',
                'mm'   => 'm',
                'm'    => 'n',
                'DD'   => 'l',
                'D'    => 'D',
                'dd'   => 'd',
                'd'    => 'j',
        ];
        
        // 提取格式
        preg_match_all('/([a-zA-Z]+)/', $format, $matches);
        $replace = [];
        foreach ($matches[1] as $match) {
            $replace[] = isset($format_map[$match]) ? $format_map[$match] : '';
        }
        
        // 替换成date函数支持的格式
        $format = str_replace($matches[1], $replace, $format);
        $time = $time === null ? time() : intval($time);
        return date($format, $time);
    }
    
    /**
     * 加载静态资源
     * @param string $assets 资源名称
     * @param string $type 资源类型
     * @return string
     */
    public function load_assets($assets = '', $type = 'css')
    {
        $assets_list = config('assets.'. $assets);
        
        $result = '';
        foreach ($assets_list as $item) {
            if ($type == 'css') {
                $result .= '<link rel="stylesheet" href="'.$item.'">';
            } else {
                $result .= '<script src="'.$item.'"></script>';
            }
        }
        return $result;
    }
    
    /**
     * 格式化联动数据
     * @param array $data 数据
     * @author 蔡伟明 <314013107@qq.com>
     * @return array
     */
    public function format_linkage($data = [])
    {
        $list = [];
        foreach ($data as $key => $value) {
            $list[] = [
                    'key'   => $key,
                    'value' => $value
            ];
        }
        return $list;
    }
    
    /**
     * 反向获取联动数据
     * @param string $table 表名
     * @param string $id 主键值
     * @param string $id_field 主键名
     * @param string $name_field name字段名
     * @param string $pid_field pid字段名
     * @param int $level 级别
     * @return array
     */
    public function get_level_key_data($table = '', $id = '', $id_field = 'id', $name_field = 'name', $pid_field = 'pid', $level = 1)
    {
        $result = [];
        $level_pid = $this->get_level_pid($table, $id, $id_field, $pid_field);
        $level_key[$level] = $level_pid;
        $level_data[$level] = $this->get_level_data($table, $level_pid, $pid_field);
        
        if ($level_pid != 0) {
            $data = $this->get_level_key_data($table, $level_pid, $id_field, $name_field, $pid_field, $level + 1);
            $level_key = $level_key + $data['key'];
            $level_data = $level_data + $data['data'];
        }
        $result['key'] = $level_key;
        $result['data'] = $level_data;
        
        return $result;
    }
    
    /**
     * 获取联动等级和父级id
     * @param string $table 表名
     * @param int $id 主键值
     * @param string $id_field 主键名
     * @param string $pid_field pid字段名
     * @return mixed
     */
    public function get_level_pid($table = '', $id = 1, $id_field = 'id', $pid_field = 'pid')
    {
        return Db::name($table)->where($id_field, $id)->value($pid_field);
    }
    
    /**
     * 获取联动数据
     * @param string $table 表名
     * @param  integer $pid 父级ID
     * @param  string $pid_field 父级ID的字段名
     * @return false|PDOStatement|string|\think\Collection
     */
    public function get_level_data($table = '', $pid = 0, $pid_field = 'pid')
    {
        if ($table == '') {
            return '';
        }
        
        $data_list = Db::name($table)->where($pid_field, $pid)->select();
        
        if ($data_list) {
            return $data_list;
        } else {
            return '';
        }
    }
    
    /**
     * 使用momentjs的时间格式来格式化时间戳
     * @param null $time 时间戳
     * @param string $format momentjs的时间格式
     * @return false|string
     */
    public function format_moment($time = null, $format='YYYY-MM-DD HH:mm') {
        $format_map = [
                // 年、月、日
                'YYYY' => 'Y',
                'YY'   => 'y',
                //            'Y'    => '',
                'Q'    => 'I',
                'MMMM' => 'F',
                'MMM'  => 'M',
                'MM'   => 'm',
                'M'    => 'n',
                'DDDD' => '',
                'DDD'  => '',
                'DD'   => 'd',
                'D'    => 'j',
                'Do'   => 'jS',
                'X'    => 'U',
                'x'    => 'u',
                
                // 星期
        //            'gggg' => '',
        //            'gg' => '',
        //            'ww' => '',
        //            'w' => '',
                'e'    => 'w',
                'dddd' => 'l',
                'ddd'  => 'D',
                'GGGG' => 'o',
                //            'GG' => '',
                'WW' => 'W',
                'W'  => 'W',
                'E'  => 'N',
                
                // 时、分、秒
                'HH'  => 'H',
                'H'   => 'G',
                'hh'  => 'h',
                'h'   => 'g',
                'A'   => 'A',
                'a'   => 'a',
                'mm'  => 'i',
                'm'   => 'i',
                'ss'  => 's',
                's'   => 's',
                //            'SSS' => '[B]',
        //            'SS'  => '[B]',
        //            'S'   => '[B]',
                'ZZ'  => 'O',
                'Z'   => 'P',
        ];
        
        // 提取格式
        preg_match_all('/([a-zA-Z]+)/', $format, $matches);
        $replace = [];
        foreach ($matches[1] as $match) {
            $replace[] = isset($format_map[$match]) ? $format_map[$match] : '';
        }
        
        // 替换成date函数支持的格式
        $format = str_replace($matches[1], $replace, $format);
        $time = $time === null ? time() : intval($time);
        return date($format, $time);
    }
    
}