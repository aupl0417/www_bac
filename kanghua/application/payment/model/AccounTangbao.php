<?php
namespace app\payment\model;
use think\Model;
use think\Session;
use think\Db;

/**
 *
 * User: lirong
 * Date: 2017/7/3
 * Time: 16:18
 */
class AccounTangbao extends Model{

    protected $name ='account_tangbao';
    protected $debug =false;

    public function findAccountTangbao($input){

        $map=[];
        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId'])?$input['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }

        $map['at_platform_id']=$platformId;

        if (!empty($input['ipt_nick'])){
            $map['at_buy_nick'] =$input['ipt_nick'];
        }
        if (!empty($input['ipt_atid'])){
            $map['at_id']=$input['ipt_atid'];
        }
        if (!empty($input['ipt_name'])){
            $map['co_cardmaster']=$input['ipt_name'];
        }

        if (!empty($input['ipt_order_id'])){
            $map['at_order_id']=$input['ipt_order_id'];
        }

        if ($input['ipt_ischecked']!=''){
            $map['at_is_checked']=$input['ipt_ischecked'];
        }

        $moneyMin =isset($input['ipt_moneyMin'])?$input['ipt_moneyMin']:"";
        $moneyMax =isset($input['ipt_moneyMax'])?$input['ipt_moneyMax']:"";

        if (!empty($moneyMin) && empty($moneyMax)){
            $map['at_money']=['>=',$moneyMin];
        }elseif (empty($moneyMin) && !empty($moneyMax)){
            $map['at_money']=['<=',$moneyMax];
        }elseif (!empty($moneyMin) && !empty($moneyMax)){
            $map['at_money'] =['between',[$moneyMin,$moneyMax]];
        }

        $pay_beginDate =isset($input['pay_beginDate'])?$input['pay_beginDate']:"";
        $pay_endDate =isset($input['pay_endDate'])?$input['pay_endDate']:"";

        $reach_beginDate =isset($input['reach_beginDate'])?$input['reach_beginDate']:"";
        $reach_endDate =isset($input['reach_endDate'])?$input['reach_endDate']:"";

        if (!empty($pay_beginDate) && empty($pay_endDate)){
            $map['at_paytime']=['>= time',$pay_beginDate];
        }elseif (empty($pay_beginDate) && !empty($pay_endDate)){
            $map['at_paytime']=['<= time',$pay_endDate];
        }elseif (!empty($pay_beginDate) && !empty($pay_endDate)){
            $map['at_paytime'] =['between time',[$pay_beginDate,$pay_endDate]];
        }

        if (!empty($reach_beginDate) && empty($reach_endDate)){
            $map['at_finshtime']=['>= time',$reach_beginDate];
        }elseif (empty($reach_beginDate) && !empty($reach_endDate)){
            $map['at_finshtime']=['<= time',$reach_endDate];
        }elseif (!empty($reach_beginDate) && !empty($reach_endDate)){
            $map['at_finshtime'] =['between time',[$reach_beginDate,$reach_endDate]];
        }

        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);
        $order =$input['orderField'] .' '.$input['orderDirection'];
        $data['list'] = Db::name($this->name)->alias('tb')->field('tb.*,or.os_status')->join('orders or','tb.at_order_id=or.os_id','left')->where($map)->page($page,$pagesize)->order($order)->fetchSql($this->debug)->select();

        if (!empty($data['list'])){
            $orders =get_dict(4);
            foreach ($data['list'] as &$item){
                $item['ordertext']=isset($orders[$item['os_status']])?$orders[$item['os_status']]:"未知";
            }

        }

        $data['count']=Db::name($this->name)->where($map)->fetchSql($this->debug)->count();

        return $data;



    }





}