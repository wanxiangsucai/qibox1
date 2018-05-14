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
use think\Image;
use think\File;

/**
 * 附件控制器
 * @package app\admin\controller
 */
class Attachment extends IndexBase
{    
    /**
     * H5上传图片进行压缩处理
     * @param string $dir
     * @param string $from
     * @param string $module
     * @return unknown|\think\response\Json|string|string|\think\response\Json
     */
    protected function upBase64Pic($dir='',$from='',$module=''){
        
        $base64_image_content = $this->request->post('imgBase64');
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $new_file = config('upload_path') . DS . $dir . DS . date('Ymd') . DS ;
            
            if(!file_exists(dirname($new_file))) {
                mkdir(dirname($new_file), 0777, true);
            }
            if(!file_exists($new_file)) {
                mkdir($new_file, 0777, true);
            }
            
            $name = time().rand(1,10000). '.' .$type;
            $new_file = config('upload_path') . '/' . $dir . '/' . date('Ymd') . '/' ;
            $path = str_replace(PUBLIC_PATH,'',$new_file);
            
            $new_file = $new_file.$name;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1],'', $base64_image_content)))){
                $file_info = [
                        'path'=>$path . $name,
                        'url'=>PUBLIC_URL . $path . $name,
                ];
                return $this->succeFile($from , $path.$name , $file_info);
            }else{
                return $this->errFile($from,'文件写入失败！');
            }
        }else{
            return $this->errFile($from,'文件获取失败！');
        }
    }

    /**
     * 上传附件
     * @param string $dir 保存的目录:images,files,videos,voices
     * @param string $from 来源，wangeditor：wangEditor编辑器, ueditor:ueditor编辑器, editormd:editormd编辑器等
     * @param string $module 来自哪个模块
     * @return mixed
     */
    public function upload($dir = '', $from = '', $module = '')
    {
        if (!$this->user) {
            return $this->errFile($from,'请先登录!!');
        }
        //应付大文件的上传
        set_time_limit(0);
        // 临时取消执行时间限制
        set_time_limit(0);
        if ($dir == '') return $this->errFile($from,'没有指定上传目录');
        if ($from == 'ueditor') return $this->ueditor();
        if ($from == 'jcrop') return $this->jcrop();
        if ($from == 'base64') return $this->upBase64Pic($dir, $from, $module);
        return $this->saveFile($dir, $from, $module);
    }
    
    /**
    * 返回ckeditor编辑器上传文件时需要返回的js代码
    * @param string $callback 回调
    * @param string $file_path 文件路径
    * @param string $error_msg 错误信息
    * @return string
    */
    protected function ck_js($callback = '', $file_path = '', $error_msg = '')
    {
        return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback, '$file_path' , '$error_msg');</script>";
    }
    
    protected function errFile($from='',$error_msg=''){        
        switch ($from) {
            case 'wangeditor':
                return "error|{$error_msg}";
                break;
            case 'ueditor':
                return json(['state' => $error_msg]);
                break;
            case 'editormd':
                return json(["success" => 0, "message" => $error_msg]);
                break;
            case 'ckeditor':
                $callback = $this->request->get('CKEditorFuncNum');
                return $this->ck_js($callback, '', $error_msg);
                break;
            default:
                return json([
                'code'   => 0,
                'class'  => 'danger',
                'info'   => $error_msg
                ]);
        }
    }
    
    protected function succeFile($from,$file_path,$file_info){
        switch ($from) {
            case 'wangeditor':
                return $file_path;
                break;
            case 'ueditor':
                return json([
                "state" => "SUCCESS",          // 上传状态，上传成功时必须返回"SUCCESS"
                "url"   => $file_path, // 返回的地址
                "title" => $file_info['name'], // 附件名
                ]);
                break;
            case 'editormd':
                return json([
                "success" => 1,
                "message" => '上传成功',
                "url"     => $file_path,
                ]);
                break;
            case 'ckeditor':
                $callback = $this->request->get('CKEditorFuncNum');
                return $this->ck_js($callback, $file_path);
                break;
            default:
                return json([
                'code'   => 1,
                'info'   => '上传成功',
                'class'  => 'success',
                'id'     => $file_info['path'],
                'url'     => $file_info['url'],
                'path'   => $file_path
                ]);
        }
    }

    /**
     * 保存附件
     * @param string $dir 附件存放的目录
     * @param string $from 来源
     * @param string $module 来自哪个模块
     * @return string|\think\response\Json
     */
    protected function saveFile($dir = '', $from = '', $module = '')
    {
        //获取附件表单名
        switch ($from) {
            case 'editormd':
                $file_input_name = 'editormd-image-file';
                break;
            case 'ckeditor':
                $file_input_name = 'upload';
                break;
            default:
                $file_input_name = 'file';
        }
        //上传的临时文件
        $file = $this->request->file($file_input_name);
        if (empty($file)) {
            return $this->errFile($from,'上传失败,文件太大超出服务器php.ini的限制'.ini_get('upload_max_filesize'));
        }

        // 判断附件存在的情况
        if ( ($file_exists = AttachmentModel::get(['md5' => $file->hash('md5')]))!=false ) {
            if ($file_exists['driver'] == 'local') {
                $file_path = PUBLIC_URL. $file_exists['path'];
            } else {
                $file_path = $file_exists['path'];
            }
            return $this->succeFile($from,$file_path,$file_exists);
        }
        
        // 附件大小限制
        $size_limit = config('webdb.upfileMaxSize') ? config('webdb.upfileMaxSize') : 1024000;
        $size_limit = $size_limit * 1024;        
        
        // 附件类型限制
        $ext_limit = $dir == 'images' ? 'gif,jpg,jpeg,png' : str_replace('.', '', config('webdb.upfileType'));
        $ext_limit = $ext_limit != '' ? parse_attr($ext_limit) : '';        

        // 判断附件格式是否符合
        $file_name = $file->getInfo('name');
        $file_ext  = strtolower(substr($file_name, strrpos($file_name, '.')+1));
        
        if ($ext_limit == '') {
            $error_msg = '系统没设置允许上传附件的类型！';
        }elseif ($file->getMime() == 'text/x-php' || $file->getMime() == 'text/html') {
            $error_msg = '禁止上传非法文件！';
        }elseif($file_ext==''){
            $error_msg = '无法获取上传文件的后缀！';
        }elseif(!in_array($file_ext, $ext_limit)) {
            $error_msg = '系统未允许上传此类型的文件！';
        }elseif ($file->getInfo('size') > $size_limit) {
            $error_msg = '附件过大';
        }else{
            $error_msg = false;
        }
        
        $upfile_num = intval( get_cookie('upfile_num') );
        if(!$this->admin && $upfile_num>50){
            $error_msg = '本次上传超50个了！';
        }else{
            $upfile_num++;
            set_cookie('upfile_num', $upfile_num);
        }
        
        
        if ($error_msg !==false ) {
            return $this->errFile($from,$error_msg);
        }
        
        Hook_listen('upload_attachment_begin', $file, ['from' => $from, 'module' => $module]);  //上传前处理
                
       //用于第三方文件上传扩展
//         $hook_result = Hook_listen('upload_driver', $file, ['from' => $from, 'module' => $module], true);
//         if ($hook_result['code']==1) {
//             return $hook_result['data'];
//         }
        if (config('webdb.upload_driver')  != 'local') {
            $hook_result = \think\Hook::listen('upload_driver', $file, ['from' => $from, 'module' => $module], true);
            if (false !== $hook_result) {
                return $hook_result;
            }
        }


        // 移动到根目录/uploads/ 目录下
        $info = $file->move(config('upload_path') . DS . $dir);

        if($info){
            $path = 'uploads/' . $dir . '/' . str_replace('\\', '/', $info->getSaveName());
            
            //对于一些相机拍摄的原始图大于1M的进行压缩
            if(in_array($file_ext,['jpeg','jpg','png','bmp']) && $file->getInfo('size') >1000000){
                $this->compress_image(PUBLIC_PATH.$path);
            }
            
            Hook_listen('upload_attachment_end', $info, ['from' => $from, 'module' => $module]);  //上传后处理
            
            // 图片加水印
            if ( $dir == 'images' && config('if_gdimg') == 1 && config('waterimg') ) {
                $this->create_water($info->getRealPath());
            }

            // 缩略图路径
            $thumb_path_name = '';
            // 生成缩略图
            if ($dir == 'images' && config('upload_image_thumb') != '') {
                $thumb_path_name = $this->create_thumb($info, $info->getPathInfo()->getfileName(), $info->getFilename());
            }

            // 获取附件信息
            $file_info = [
                 'uid'    => intval($this->user['uid']),
                'name'   => $file->getInfo('name'),
                'mime'   => $file->getInfo('type'),
                'path'   => $path,
                'ext'    => $info->getExtension(),
                'size'   => $info->getSize(),
                'md5'    => $info->hash('md5'),
                'sha1'   => $info->hash('sha1'),
                'thumb'  => $thumb_path_name,
                'module' => $module
            ];

            // 写入数据库
            if (($file_add = AttachmentModel::create($file_info))!=false) {
                $file_path = PUBLIC_URL. $file_info['path'];
                return $this->succeFile($from,$file_path,$file_info);
            } else {
                return $this->errFile($from,'文件入库失败');
            }
        }else{
            return $this->errFile($from,'上传失败');           
        }
    }
    
    /**
     * 对大图进行压缩
     * @param string $file 文件路径
     */
    protected function compress_image($file = '',$thumb_max_width=1920,$thumb_max_height=1920)
    {
        // 读取图片
        $image = Image::open($file);
        // 生成压缩图
        $image->thumb($thumb_max_width, $thumb_max_height, 1);

        $thumb_path_name = $file;
        //$thumb_path_name = dirname($file).'/_'. basename($file);        
        $image->save($thumb_path_name);
        if(is_file($thumb_path_name)){
            //copy($thumb_path_name,$file);
            //unlink($file);
            return true;
        }        
    }


    /**
     * 处理Jcrop图片裁剪
     */
    protected function jcrop()
    {
        $file_path = $this->request->post('path', '');
        $cut_info  = $this->request->post('cut', '');
        $module    = $this->request->param('module', '');

        // 上传图片
        if ($file_path == '') {
            $file = $this->request->file('file');
            if (!is_dir(config('upload_temp_path'))) {
                mkdir(config('upload_temp_path'), 0766, true);
            }
            $info = $file->move(config('upload_temp_path'), $file->hash('md5'));
            if ($info) {
                return json(['code' => 1, 'src' => PUBLIC_URL. 'uploads/temp/'. $info->getFilename()]);
            } else {
                $this->error('上传失败');
            }
        }

        //$file_path = config('upload_temp_path') . str_replace(PUBLIC_URL. 'uploads/temp/', '', $file_path);
        $file_path = config('upload_path') . str_replace(PUBLIC_URL. 'uploads', '', $file_path);

        if (is_file($file_path)) {
            // 获取裁剪信息
            $cut_info  = explode(',', $cut_info);

            // 读取图片
            $image = Image::open($file_path);

            $dir_name = date('Ymd');
            $file_dir = config('upload_path') . DS . 'images/' . $dir_name . '/';
            if (!is_dir($file_dir)) {
                mkdir($file_dir, 0766, true);
            }
            $file_name     = md5(microtime(true)) . '.' . $image->type();
            $new_file_path = $file_dir . $file_name;

            // 裁剪图片
            $image->crop($cut_info[0], $cut_info[1], $cut_info[2], $cut_info[3], $cut_info[4], $cut_info[5])->save($new_file_path);

            // 水印功能
            if (config('upload_thumb_water') == 1 && config('upload_thumb_water_pic') > 0) {
                $this->create_water($new_file_path);
            }

            // 是否创建缩略图
            $thumb_path_name = '';
            if (config('upload_image_thumb') != '') {
                $thumb_path_name = $this->create_thumb($new_file_path, $dir_name, $file_name);
            }

            // 保存图片
            $file = new File($new_file_path);
            $file_info = [
                    'uid'    => intval($this->user['uid']),
                'name'   => $file_name,
                'mime'   => $image->mime(),
                'path'   => 'uploads/images/' . $dir_name . '/' . $file_name,
                'ext'    => $image->type(),
                'size'   => $file->getSize(),
                'md5'    => $file->hash('md5'),
                'sha1'   => $file->hash('sha1'),
                'thumb'  => $thumb_path_name,
                'module' => $module
            ];

            if (($file_add = AttachmentModel::create($file_info))!=false) {
                // 删除临时图片
                //unlink($file_path);
                // 返回成功信息
                return json([
                    'code'  => 1,
                    'id'    => $file_info['path'],//$file_add['id'],
                    'src'   => PUBLIC_URL . $file_info['path'],
                    'thumb' => $thumb_path_name == '' ? '' : PUBLIC_URL . $thumb_path_name,
                ]);
            } else {
                $this->error('上传失败');
            }
        }
        $this->error('文件不存在-'.$file_path);
    }

    /**
     * 创建缩略图
     * @param string $file 目标文件，可以是文件对象或文件路径
     * @param string $dir 保存目录，即目标文件所在的目录名
     * @param string $save_name 缩略图名
     * @return string 缩略图路径
     */
    protected function create_thumb($file = '', $dir = '', $save_name = '')
    {
        // 获取要生成的缩略图最大宽度和高度
        list($thumb_max_width, $thumb_max_height) = explode(',', config('upload_image_thumb'));
        // 读取图片
        $image = Image::open($file);
        // 生成缩略图
        $image->thumb($thumb_max_width, $thumb_max_height, config('upload_image_thumb_type'));
        // 保存缩略图
        $thumb_path = config('upload_path') . DS . 'images/' . $dir . '/thumb/';
        if (!is_dir($thumb_path)) {
            mkdir($thumb_path, 0766, true);
        }
        $thumb_path_name = $thumb_path. $save_name;
        $image->save($thumb_path_name);
        $thumb_path_name = 'uploads/images/' . $dir . '/thumb/' . $save_name;
        return $thumb_path_name;
    }

    /**
     * 添加水印
     * @param string $file 要添加水印的文件路径
     */
    protected function create_water($file = '')
    {
        $path = model('admin/attachment')->getFilePath(config('upload_thumb_water_pic'), 1);
        $thumb_water_pic = realpath(ROOT_PATH . 'public/' . $path);

        // 读取图片
        $image = Image::open($file);
        // 添加水印
        $image->water($thumb_water_pic, config('upload_thumb_water_position'), config('upload_thumb_water_alpha'));
        // 保存水印图片，覆盖原图
        $image->save($file);
    }
    
    
    /**
     * 处理百度编辑器ueditor上传
     * @return string|\think\response\Json
     */
    private function ueditor(){
        $action      = $this->request->get('action');
        $config_file = PUBLIC_PATH.'static/libs/ueditor/php/config.json';  //配置文件
        $config      = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($config_file)), true);        
        switch ($action) {
            /* 获取配置信息 */
            case 'config':
                $result = $config;
                break;
                
                /* 上传图片 */
            case 'uploadimage':
                return $this->saveFile('images', 'ueditor');
                break;
                /* 上传涂鸦 */
            case 'uploadscrawl':
                return $this->saveFile('images', 'ueditor_scrawl');
                break;
                
                /* 上传视频 */
            case 'uploadvideo':
                return $this->saveFile('videos', 'ueditor');
                break;
                
                /* 上传附件 */
            case 'uploadfile':
                return $this->saveFile('files', 'ueditor');
                break;
                
                /* 列出图片 */
            case 'listimage':
                return $this->showFile('listimage', $config);
                break;
                
                /* 列出附件 */
            case 'listfile':
                return $this->showFile('listfile', $config);
                break;
                
                /* 抓取远程附件 */
                //            case 'catchimage':
                //                $result = include("action_crawler.php");
                //                break;
                
            default:
                $result = ['state' => '请求地址出错'];
                break;
        }
        
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                return htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                return json(['state' => 'callback参数不合法']);
            }
        } else {
            return json($result);
        }
    }

    /**
     * 显示附件列表（ueditor）
     * @param string $type 类型
     * @param $config
     * @return \think\response\Json
     */
    private function showFile($type = '', $config){
        /* 判断类型 */
        switch ($type) {
            /* 列出附件 */
            case 'listfile':
                $allowFiles = $config['fileManagerAllowFiles'];
                $listSize = $config['fileManagerListSize'];
                $path = realpath(config('upload_path') .'/files/');
                break;
                /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $config['imageManagerAllowFiles'];
                $listSize = $config['imageManagerListSize'];
                $path = realpath(config('upload_path') .'/images/');
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        
        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;
        
        /* 获取附件列表 */
        $files = $this->getfiles($path, $allowFiles);
        if (!count($files)) {
            return json(array(
                    "state" => "no match file",
                    "list" => array(),
                    "start" => $start,
                    "total" => count($files)
            ));
        }
        
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}
        
        /* 返回数据 */
        $result = array(
                "state" => "SUCCESS",
                "list"  => $list,
                "start" => $start,
                "total" => count($files)
        );
        
        return json($result);
    }
    
    /**
     * 遍历获取目录下的指定类型的附件
     * @param string $path 路径
     * @param string $allowFiles 允许查看的类型
     * @param array $files 文件列表
     * @return array|null
     */
    public function getfiles($path = '', $allowFiles = '', &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                                'url'=> str_replace("\\", "/", substr($path2, strlen($_SERVER['DOCUMENT_ROOT']))),
                                'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }
    
    /**
     * 删除附件
     * @param string $ids 附件id
     * @return mixed
     */
    protected function deleteFile($ids = '')
    {
        $ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
        if (empty($ids)) $this->error('缺少主键');

        $files_path = AttachmentModel::where('id', 'in', $ids)->column('path,thumb', 'id');

        foreach ($files_path as $value) {
            $real_path       = realpath(config('upload_path').'/../'.$value['path']);
            $real_path_thumb = realpath(config('upload_path').'/../'.$value['thumb']);

            if (is_file($real_path) && !unlink($real_path)) {
                $this->error('删除失败');
            }
            if (is_file($real_path_thumb) && !unlink($real_path_thumb)) {
                $this->error('删除缩略图失败');
            }
        }
        if (AttachmentModel::where('id', 'in', $ids)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}