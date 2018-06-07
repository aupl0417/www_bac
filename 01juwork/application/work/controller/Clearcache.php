<?php
/**
 * 清除缓存
 * day:2017-06-17
 */
namespace app\work\controller;
use app\work\controller\Common;
use think\Cache;

class Clearcache extends Common
{
    public function index(){
        return view();
    }

    public function clear(){
        if($this->post['type'] == 1){
            cache('site_config',null);
        }else{
            Cache::clear();
        }

        return ['code' => 1,'msg' => '清除成功！'];
    }

}
