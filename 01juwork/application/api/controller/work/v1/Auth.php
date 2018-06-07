<?php
namespace app\api\controller\work\v1;
use app\api\controller\work\v1\Init;
class Auth extends Init
{
    /**
     * 生成Token
     * 2017-06-01
     */
    public function token($check=1){
        if($check == 1) {
            $res = $this->check('appid,access_key,secret_key,sign_code',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $where = [
            'id'            => $this->post['appid'],
            'access_key'    => $this->post['access_key'],
            'secret_key'    => $this->post['secret_key'],
            'sign_code'     => $this->post['sign_code'],
        ];
        $rs = db('app_user')->where($where)->field('id,terminal,status')->find();
        if($rs['status'] != 1) return $this->ret(['code' => 0,'msg' => '您的应用接口已被停用！']);

        db('app_user')->where(['id' => $rs['id']])->setInc('num',1,600);

        $where['terminal']  = $rs['terminal'];
        $where['token']     = md5(implode('-',$where));
        $cache_name = 'api_token_'.$where['token'];
        $where['atime']     = date('Y-m-d H:i:s');

        cache($cache_name,$where,1200);
        return $this->ret(['code' => 1,'data' => $where]);
    }

}
