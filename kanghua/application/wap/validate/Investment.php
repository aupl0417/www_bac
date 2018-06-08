<?php
/**
 * Created by PhpStorm.
 * User: aupl
 * Date: 2017/8/9 0009
 * Time: 9:34
 */
namespace app\wap\validate;
use think\Db;
use think\Validate;

class Investment extends Validate {

    protected $rule = [
        'in_product_name' => 'require',
        'in_company_name' => 'require|checkCompanyName',
        'in_username'     => 'require',
        'in_mobile'       => 'require|checkMobile',
//        'in_dttx_nick'    => 'require|checkDttxNick'
        'in_dttx_nick'    => 'checkDttxNick'
    ];

    protected $message = [
        'in_product_name'        => '请输入产品名称',
        'in_company_name.require'=> '请输入产品所属公司',
        'in_username'            => '请输入联系人姓名',
        'in_mobile.require'      => '请输入电话号码',
        'in_dttx_nick.require'   => '公司或负责人大唐天下账号不能为空',
    ];

    /*
     * 验证公司名称
     * */
    public function checkCompanyName($companyName){
        $result = $this->getInvestment(['in_company_name' => $companyName]);
        if(!$result){
            return '公司名称已存在';
        }
        return true;
    }

    /*
     * 验证手机号码
     * */
    protected function checkMobile($mobile){
        if(!preg_match('/^1[34578]\d{9}$/', $mobile)){
            return '联系电话格式不正确';
        }

//        $result = $this->getInvestment(['in_mobile' => $mobile]);
//        if($result !== true){
//            return '电话号码已存在';
//        }

        return true;
    }

    /*
     * 验证大唐账号
     * */
    protected function checkDttxNick($dttxNick){
        if(empty($dttxNick)){
            return true;
        }
        $res = getDttxUserInfo($dttxNick, false);

        if(!$res || $res['status'] != 1001){//检测在大唐系统是否存在该账号
            return '大唐天下账号不存在';
        }

        $result = $this->getInvestment(['in_dttx_nick' => $dttxNick]);//检测是否在表中已存在
        if(!$result){
            return '公司或负责人大唐天下账号已存在';
        }

        return true;
    }

    /*
     * 验证公司名称、电话号码及大唐账号的公共方法
     * */
    protected function getInvestment($condition = array()){
        $where = ['in_status' => array('neq', -1), 'in_isDelete' => 0];
        if(!is_array($condition)|| !$condition){
            return false;
        }
        $where = array_merge($where, $condition);
        $data = Db::name('investment')->where($where)->count();
        if($data){
            return false;
        }

        return true;
    }

}