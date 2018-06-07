<?php
/**
 * 配置参数设置
 * day:2017-06-17
 */
namespace app\work\controller;
use app\work\controller\Common;
class Upload extends Common
{
    /**
     * Base64 上转图片
     */
    public function images(){
        //config('api_debug',true);
        $res = api('Upload/imagesBase64',$this->post);
        return $res;
    }

    public function images2(){
        //config('api_debug',true);
        $data['filebody'] = file_get_contents($_FILES['file']['tmp_name']);
        $res = api('Upload/images',$data);
        return $res;
    }
}
