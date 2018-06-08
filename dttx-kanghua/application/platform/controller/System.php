<?php
namespace app\platform\controller;
use app\common\controller\Platform;

use think\Cache;
use think\Db;
use think\Request;
use think\Session;

/**
 *
 * User: lirong
 * Date: 2017/7/4
 * Time: 14:41
 */
class System extends Platform{

    /**
     * 用于首页数据展示
     * @return mixed
     */
    public function index(){

        if (Request::instance()->isPost()){
            $id=input('plid','0','intval');
            $content = $_POST['content'];
            $image   = input('post.image', '', 'htmlspecialchars,strip_tags,trim');
            $description   = input('post.description', '', 'htmlspecialchars,strip_tags,trim');

            if (empty($id)){
                return $this->ajaxReturn(ajaxCallBack(300,'参数错误!'));
            }else{
                $res = Db::name('platform')->where(['pl_id'=>$id])->update(['pl_content'=>$content, 'pl_image' => $image, 'pl_description' => $description]);
                if ($res === false){
                    $this->ajaxReturn(ajaxCallBack('300','修改失败，请重试！'));
                }
//                $platform_content_cacheId =md5('platform_content_'.$id);
//                Cache::rm($platform_content_cacheId);
                $this->ajaxReturn(ajaxCallBack(200,'修改成功!'));
            }
        }else{

            $platfromId =Session::get('user.platformId');
            $platform =new \app\admin\model\Platform();
            $data = $platform->findDetailByid($platfromId);

            $this->assign('data',$data);
            return $this->fetch();
        }
    }






















    /**
     * 增加数据
     * @return mixed
     */
    public function create(){}

    /**
     *修改数据
     * @return mixed
     */
    public function edit(){}

    /**
     * 移除数据
     * @return mixed
     */
    public function remove(){}
}