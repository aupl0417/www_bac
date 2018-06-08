<?php
namespace app\common\controller;
use think\Cache;
use think\Config;
use think\Db;
use think\Request;
/**
 *  用于公共异步返回控制器使用
 * User: lirong
 * Date: 2017/6/29
 * Time: 18:36
 */
class ajax extends Platform {



    public function ajax_upload(){
        $rule =[
            'size'=>'4096000',
            'ext'=>'jpg,png,gif'
        ];
        $file =\request()->file('uploads');
        $info =$file->rule($rule)->move(Config::get('upload_folder'));

        if ($info){
            $filename = $info->getSaveName();
            return $this->ajaxReturn(['statusCode'=>200,'message'=>'上传成功','filename'=>'/uploads/' . $filename]);
        }else{
            return $this->ajaxReturn(['statusCode'=>200,'message'=>'上传失败!','filename'=>$file->getError()]);
        }

    }

    /**
     * 编辑器上传图片使用
     */
    public function ajax_editorupload(){
		$dir = input('get.dir', 'image', 'htmlspecialchars,strip_tags,trim');
        $rule =[
            'size'=>'4096000',
            'ext'=>'jpg,png,gif'
        ];
        $file =\request()->file('imgFile');
        $info =$file->rule($rule)->move(Config::get('upload_folder') . DS . $dir);

        if ($info){
            $filename = $info->getSaveName();
            return $this->ajaxReturn(['error'=>0,'url'=> '/uploads' . '/' . $dir . '/' . $filename]);
        }else{
            return $this->ajaxReturn(array('error' => 1, 'message' => $file->getError()));
        }

    }

    /**
     * 用于首页数据展示
     * @return mixed
     */
        public function index()
    {
        // TODO: Implement index() method.
    }/**
     * 增加数据
     * @return mixed
     */public function create()
    {
        // TODO: Implement create() method.
    }/**
     *修改数据
     * @return mixed
     */public function edit()
    {
        // TODO: Implement edit() method.
    }/**
     * 移除数据
     * @return mixed
     */public function remove()
    {
        // TODO: Implement remove() method.
    }

}