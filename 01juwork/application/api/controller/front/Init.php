<?php
/**
 * ------------------------------
 * 公共文件
 * ------------------------------
 * Create by lazycat
 * 2017-06-01
 * ------------------------------
 */
namespace app\api\controller\front;
use think\Controller;
use think\Db;
use think\Log;
use think\Hook;

class Init extends Controller
{
    //protected $api_cfg;     //接口参数
    protected $token;       //token
    protected $post;        //post数据
    protected $get;         //get数据
    protected $put;         //put数据
    protected $need_field;  //必填字段
    protected $sw;          //事务执行结果
    protected $result;      //返回结果
    protected $dotime;      //执行时间
    protected $terminal;    //终端类型
    protected $admin;       //管理员资料

    public function _initialize(){
        header('Access-Control-Allow-Origin: *');
        debug('begin');
        $this->post = $this->request->isPost() ? input('post.') : $this->request->param();
        $this->terminal = $this->request->isMobile();


        $this->_config();
    }


    /**
     * 请求方法前相关校验
     * @param string $need_field 必填字段
     * @param int $check_require 请求限制
     * @param string $nosign_field 不参与签名字段
     */
    public function check($field='',$check_require=1,$nosign_field=''){
        $need   = [];   //必填字段
        if($field) {
            $need = explode(',', $field);
            foreach ($need as $val) {
                if (!isset($this->post[$val]) || is_null($this->post[$val]) || $this->post[$val] === '') {
                    return ['code' => 0, 'msg' => '参数' . $val . '不能为空！'];
                }
            }
        }

        $this->need_field = $need;
        return ['code' => 1,'msg' => 'ok'];
    }



    /**
     * 接口数据返回
     * @param $data
     */
    public function ret($data){
        $msg = [
            0   => '操作失败！',
            1   => '操作成功！',
            3   => '找不到记录！',
        ];
        if(!isset($data['msg']) && isset($msg[$data['code']]))  $data['msg'] = $msg[$data['code']];
        if(!isset($data['data'])) $data['data'] = '';
        $this->result = jsonp($data);

        return $this->result;
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
