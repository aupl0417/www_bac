<?php
namespace app\work\controller;
use think\Config;
use think\Controller;
use think\Session;
class Init extends Controller
{
    protected $api_cfg;     //接口参数
    protected $token;       //授权token
    protected $apiurl;      //接口请求地址
    protected $post;
    protected $get;
    protected $param;
    public function _initialize()
    {
        $this->post     = input('post.');
        $this->get      = input('get.');
        $this->param    = $this->request->param();
        Session::init();
        $this->apiurl   = 'http://api.'.config('url_domain_root').'/work.v1.';
        $this->api_cfg  = $this->api_cfg();
        config('apiurl',$this->apiurl);
        config('api_cfg',$this->api_cfg);

        $cache_name     = 'apitoken_'.session_id();
        $this->token    = cache($cache_name);

        //每隔10分钟生成一次token
        if(empty($this->token)) {
            //config('api_debug',true);
            $res = api('auth/token', $this->api_cfg);
            if ($res['code'] != 1) {
                return $res;
            }
            $this->token = $res['data'];
            cache($cache_name,$this->token,600);
        }
        config('token',$this->token);

        $this->_config();
    }


    /**
     * 接口参数
     * @return array
     */
    private function api_cfg()
    {
        $data = [
            'appid'         => 1,
            'access_key'    => '1f982aa4178c278c95529e28b0f1b20f',
            'secret_key'    => 'cfdb08305ddf31113eed7d6bd7c6ce94',
            'sign_code'     => 'a35bbf3cf3cb98f91bc748f4660127ee',
        ];
        return $data;
    }

    /**
     * 获取配置
     */
    public function _config(){
        $list = db('config_category')->cache('site_config')->where(['status' => 1,'upid' => ['gt',0]])->field('group_name,config')->select();
        $cfg = [];
        foreach($list as $key => $val){
            if($val['config']){
                $val['config'] = unserialize(html_entity_decode($val['config']));
            }
            $cfg[$val['group_name']] = $val['config'];
        }
        config('cfg',$cfg);
        return $cfg;
    }
}
