<?php
namespace app\payment\model;
use think\Model;
use think\Session;
use think\Db;

/**
 *
 * User: lirong
 * Date: 2017/7/3
 * Time: 14:37
 */
class AccountOrder extends Model{

    protected $name='account_order';
    protected $debug =false;

    public function findAccountOrderList($input){


        $map=[];

        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId'])?$input['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }

        $map['ao_platform_id']=$platformId;

        if (!empty($input['ipt_nick'])){
            $map['ao_buy_nick'] =$input['ipt_nick'];
        }
        if (!empty($input['ipt_aoid'])){
            $map['ao_id']=$input['ipt_aoid'];
        }
        if (!empty($input['ipt_order_id'])){
            $map['ao_order_id']=$input['ipt_order_id'];
        }
        if (!empty($input['ipt_nick'])){
            $map['ao_buy_nick']=$input['ipt_nick'];
        }

        if ($input['ipt_state']!=''){
            $map['ao_state']=$input['ipt_state'];
        }

        $moneyMin =isset($input['ipt_moneyMin'])?$input['ipt_moneyMin']:"";
        $moneyMax =isset($input['ipt_moneyMax'])?$input['ipt_moneyMax']:"";

        if (!empty($moneyMin) && empty($moneyMax)){
            $map['ao_money']=['>=',$moneyMin];
        }elseif (empty($moneyMin) && !empty($moneyMax)){
            $map['ao_money']=['<=',$moneyMax];
        }elseif (!empty($moneyMin) && !empty($moneyMax)){
            $map['ao_money'] =['between',[$moneyMin,$moneyMax]];
        }

        $beginDate =$input['beginDate'];
        $endDate =$input['endDate'];

        if (!empty($beginDate) && empty($endDate)){
            $map['ao_create_time']=['>= time',$beginDate];
        }elseif (empty($beginDate) && !empty($endDate)){
            $map['ao_create_time']=['<= time',$endDate];
        }elseif (!empty($beginDate) && !empty($endDate)){
            $map['ao_create_time'] =['between time',[$beginDate,$endDate]];
        }

        $pagesize =empty($input['pageSize'])?"15":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);
        $order =$input['orderField'] .' '.$input['orderDirection'];
        $data['list'] = Db::name($this->name)
            ->alias('o')
            ->field('o.*,ul.ul_name as onelevelname,ule.ul_name twolevelname,os_status')
            ->join('user_level ul','o.ao_one_userlevel_id=ul.ul_id','left')
            ->join('user_level ule','o.ao_two_userlevel_id=ule.ul_id','left')
            ->join('orders os','o.ao_order_id=os.os_id','left')
            ->where($map)->page($page,$pagesize)->order($order)->fetchSql($this->debug)->select();

        $orderstatus =get_dict(4);
        if (!empty($data['list'])){
            foreach ( $data['list'] as &$item){
                $item['orders_text']=isset($orderstatus[$item['os_status']])?$orderstatus[$item['os_status']]:"--";

            }
        }

        $data['count']=Db::name($this->name)->where($map)->fetchSql($this->debug)->count();

        return $data;

    }


    public function findDetailByAoid($aoid,$field='*'){

        if (empty($aoid)){
            return false;
        }

        $res =Db::name($this->name)->field($field)->where(['ao_id'=>$aoid])->find();
        return $res;

    }


}