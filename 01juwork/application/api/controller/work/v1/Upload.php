<?php
namespace app\api\controller\work\v1;
use app\api\controller\work\v1\Init;
class Upload extends Init
{
    public function _initialize()
    {
        parent::_initialize();
        $qiniu_file = array(
            'Config.php',
            'Auth.php',
            'Etag.php',
            'functions.php',
            'Http/Client.php',
            'Http/Error.php',
            'Http/Request.php',
            'Http/Response.php',
            'Processing/Operation.php',
            'Processing/PersistentFop.php',
            'Storage/BucketManager.php',
            'Storage/FormUploader.php',
            'Storage/ResumeUploader.php',
            'Storage/UploadManager.php',
            'Zone.php',
        );
        foreach($qiniu_file as $val){
            $url = 'Qiniu.'.str_replace('/','.',substr($val,0,-4));
            vendor($url);
        }

        config('qiniu',config('cfg.qiniu'));
    }

    /**
     * 上传图片 base64格式
     * 2017-06-07
     */
    public function imagesBase64($check=1){
        if($check == 1) {
            $res = $this->check('filebody,type');
            if($res['code'] != 1) return $this->ret($res);
        }

        if($this->post['type'] != 'image/jpeg') return $this->ret(['code' => 0,'msg' => '文件格式错误！']);


        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $this->post['filebody'], $result)){
            $filebody = base64_decode(str_replace($result[1], '', $this->post['filebody']));
        }else $filebody = base64_decode($this->post['filebody']);

        //充许上传格式
        $ext_arr    = array('gif','jpg','png');

        //充许上传文件大小，限制3M
        $maxsize    = 1024 * 1024 * 3;
        $filesize   = strlen($filebody);
        if($filesize > $maxsize){
            return $this->ret(['code' => 0,'msg' => '图片文件最大不能超过3M']);
        }

        //七牛接口初始化
        $auth   = new \Qiniu\Auth(config('qiniu.ak'), config('qiniu.sk'));
        $token  = $auth->uploadToken(config('qiniu.bucket'));
        $Config = new \Qiniu\Config();
        $qn     = new \Qiniu\Storage\UploadManager();

        list($ret, $err) = $qn->put($token, null, $filebody,$Config);
        //file_put_contents('a.txt',var_export($ret,true));

        if ($err != null) {
            //echo "上传失败。错误消息：".$err->message();
            return $this->ret(['code' => 0,'msg' => '上传失败！'.$err->message()]);
        }else{
            $url = config('qiniu.domain').'/'.$ret['key'];
            return $this->ret(['code' => 1,'data' => ['url' => $url]]);
        }

    }

    /**
     * 文件流上传文件
     * @param int $check
     */
    public function images($check=1){
        if($check == 1) {
            $res = $this->check('filebody');
            if($res['code'] != 1) return $this->ret($res);
        }

        //if($this->post['type'] != 'image/jpeg') return $this->ret(['code' => 0,'msg' => '文件格式错误！']);


        $filebody = $this->post['filebody'];

        //充许上传格式
        $ext_arr    = array('gif','jpg','png');

        //充许上传文件大小，限制3M
        $maxsize    = 1024 * 1024 * 3;
        $filesize   = strlen($filebody);
        if($filesize > $maxsize){
            return $this->ret(['code' => 0,'msg' => '图片文件最大不能超过3M']);
        }

        //七牛接口初始化
        $auth   = new \Qiniu\Auth(config('qiniu.ak'), config('qiniu.sk'));
        $token  = $auth->uploadToken(config('qiniu.bucket'));
        $Config = new \Qiniu\Config();
        $qn     = new \Qiniu\Storage\UploadManager();

        list($ret, $err) = $qn->put($token, null, $filebody,$Config);
        //file_put_contents('a.txt',var_export($ret,true));

        if ($err != null) {
            //echo "上传失败。错误消息：".$err->message();
            return $this->ret(['code' => 0,'msg' => '上传失败！'.$err->message()]);
        }else{
            $url = config('qiniu.domain').'/'.$ret['key'];
            return $this->ret(['code' => 1,'data' => ['url' => $url]]);
        }

    }
}
