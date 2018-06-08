<?php
namespace app\payment\model;
use think\Model;
use think\Session;
use think\Db;

/**
 *
 * User: aupl
 * Date: 2017/7/133
 * Time: 18:46
 */
class AccountPayIn extends Model{

    protected $name  = 'account_pay_in';
    protected $debug = false;

    public function findAccountPayInList($input){

        $map=[];
        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId']) ? $input['platformId'] : "0";
        }else{
            $platformId = Session::get('user.platformId');
        }

        $map['ap_platform_id'] = $platformId;

        if (!empty($input['ipt_nick'])){
            $map['ap_pay_unick'] = $input['ipt_nick'];
        }

        if (!empty($input['ipt_channel_id'])){
            $map['ap_channel_id'] = $input['ipt_channel_id'];
        }
        if (!empty($input['ipt_order_id'])){
            $map['ap_shop_order_id']=$input['ipt_order_id'];
        }

        if (!empty($input['ipt_name'])){
            $map['ap_platform_id'] = $input['ipt_name'];
        }

        if ($input['ipt_state']!==''){
            $map['os_status'] = $input['ipt_state'];
        }
        if (!empty($input['ipt_payType'])){
            $map['ap_only_pay'] = $input['ipt_payType'];
        }

        $moneyMin = isset($input['ipt_moneyMin']) ? $input['ipt_moneyMin'] : '';
        $moneyMax = isset($input['ipt_moneyMax']) ? $input['ipt_moneyMax'] : '';

        if (!empty($moneyMin) && empty($moneyMax)){
            $map['ap_pay_money'] = ['>=', $moneyMin];
        }elseif (empty($moneyMin) && !empty($moneyMax)){
            $map['ap_pay_money'] = ['<=', $moneyMax];
        }elseif (!empty($moneyMin) && !empty($moneyMax)){
            $map['ap_pay_money'] = ['between', [$moneyMin,$moneyMax]];
        }

        $apply_beginDate = isset($input['apply_beginDate'])? $input['apply_beginDate'] : '';
        $apply_endDate   = isset($input['apply_endDate'])  ? $input['apply_endDate']   : '';
        $reach_beginDate = isset($input['reach_beginDate'])? $input['reach_beginDate'] : '';
        $reach_endDate   = isset($input['reach_endDate'])  ? $input['reach_endDate']   : '';

        if (!empty($apply_beginDate) && empty($apply_endDate)){
            $map['ap_create_time'] = ['>= time', strtotime($apply_beginDate)];
        }elseif (empty($apply_beginDate) && !empty($apply_endDate)){
            $map['ap_create_time'] = ['<= time', strtotime($apply_endDate)];
        }elseif (!empty($apply_beginDate) && !empty($apply_endDate)){
            $map['ap_create_time'] = ['between time', [strtotime($apply_beginDate), strtotime($apply_endDate)]];
        }

        if (!empty($reach_beginDate) && empty($reach_endDate)){
            $map['ap_pay_time']=['>= time',$reach_beginDate];
        }elseif (empty($reach_beginDate) && !empty($reach_endDate)){
            $map['ap_pay_time']=['<= time',$reach_endDate];
        }elseif (!empty($reach_beginDate) && !empty($reach_endDate)){
            $map['ap_pay_time'] =['between time',[$reach_beginDate,$reach_endDate]];
        }

        $pagesize = empty($input['pageSize']) ? "30" : intval($input['pageSize']);
        $page     = empty($input['pageCurrent']) ? 1 : intval($input['pageCurrent']);
        $order    = $input['orderField'] .' '.$input['orderDirection'];
        $data['list'] = Db::name($this->name)->alias('in')->field('in.*,or.os_status,u.u_name')
                      ->join('user_platform up','in.ap_pay_id=up.up_id','LEFT')
                      ->join('user u','up.up_uid=u.u_id','LEFT')
                      ->join('orders or','in.ap_shop_order_id=or.os_id','left')
                      ->where($map)->page($page,$pagesize)
                      ->order($order)->fetchSql($this->debug)->select();

        if (!empty($data['list']) && !is_string($data['list'])){
            foreach ($data['list'] as &$item){
                $item['ap_only_pay'] = $item['ap_only_pay'] == 1 ? '余额' : ($item['ap_only_pay'] == 2 ? '唐宝' : '');
                $item['ap_state'] = $this->getStateText($item['os_status']);
            }
        }

        $data['count'] = Db::name($this->name)->alias('in')->join('orders or','in.ap_shop_order_id=or.os_id','left')->where($map)->fetchSql($this->debug)->count();

        return $data;
    }


    private function getStateText($num){
        switch ($num){
            case -1:
                $string = "<label class='label label-danger'>关闭</label>";
                break;
            case 0:
                $string =  "<label class='label label-warning'>待付款</label>";
                break;
            case 1:
                $string =  "<label class='label label-primary'>待发货</label>";
                break;
            case 2:
                $string  ="<label class='label label-success'>待收货</label>";
                break;
            case 3:
                $string  ="<label class='label label-success'>交易完成</label>";
                break;
            case 4:
                $string  ="<label class='label label-warning'>退款中</label>";
                break;
            case 5:
                $string  ="<label class='label label-success'>已退款</label>";
                break;
            default:
                $string ="<label class='label label-warning'>未知状态</label>";
        }

        return $string;
    }



}