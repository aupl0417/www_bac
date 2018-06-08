<?php
namespace app\payment\model;
use think\Model;
use think\Session;
use think\Db;

/**
 *
 * User: lirong
 * Date: 2017/7/2
 * Time: 14:42
 */
class AccountCashIn extends Model{

    protected $name ='account_cash_in';
    protected $debug =false;

    public function findAccountCashInList($input){

        $admin_identify =Session::has('admin_identify');
        $map=[];

        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId'])?$input['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }

        $map['ci_platform_id']=$platformId;

        if (!empty($input['ipt_nick'])){
            $map['ci_unick'] =$input['ipt_nick'];
        }
        if (!empty($input['ipt_caid'])){
            $map['ci_caid']=$input['ipt_caid'];
        }
        if (!empty($input['ipt_name'])){
            $map['ci_platform_id']=$input['ipt_name'];
        }

        if (!empty($input['ipt_state'])){
            $map['ci_state']=$input['ipt_state'];
        }
        if (!empty($input['ipt_payType'])){
            $map['ci_payType']=$input['ipt_payType'];
        }

        $moneyMin =isset($input['ipt_moneyMin'])?$input['ipt_moneyMin']:"";
        $moneyMax =isset($input['ipt_moneyMax'])?$input['ipt_moneyMax']:"";

        if (!empty($moneyMin) && empty($moneyMax)){
            $map['ci_money']=['>=',$moneyMin];
        }elseif (empty($moneyMin) && !empty($moneyMax)){
            $map['ci_money']=['<=',$moneyMax];
        }elseif (!empty($moneyMin) && !empty($moneyMax)){
            $map['ci_money'] =['between',[$moneyMin,$moneyMax]];
        }

        $apply_beginDate =isset($input['apply_beginDate'])?$input['apply_beginDate']:"";
        $apply_endDate =isset($input['apply_endDate'])?$input['apply_endDate']:"";

        $reach_beginDate =isset($input['reach_beginDate'])?$input['reach_beginDate']:"";
        $reach_endDate =isset($input['reach_endDate'])?$input['reach_endDate']:"";

        if (!empty($apply_beginDate) && empty($apply_endDate)){
            $map['ci_createTime']=['>= time',$apply_beginDate];
        }elseif (empty($apply_beginDate) && !empty($apply_endDate)){
            $map['ci_createTime']=['<= time',$apply_endDate];
        }elseif (!empty($apply_beginDate) && !empty($apply_endDate)){
            $map['ci_createTime'] =['between time',[$apply_beginDate,$apply_endDate]];
        }

        if (!empty($reach_beginDate) && empty($reach_endDate)){
            $map['ci_successTime']=['>= time',$reach_beginDate];
        }elseif (empty($reach_beginDate) && !empty($reach_endDate)){
            $map['ci_successTime']=['<= time',$reach_endDate];
        }elseif (!empty($reach_beginDate) && !empty($reach_endDate)){
            $map['ci_successTime'] =['between time',[$reach_beginDate,$reach_endDate]];
        }

        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);
        $order =$input['orderField'] .' '.$input['orderDirection'];
        $data['list'] = Db::name($this->name)->alias('in')->field('in.*,b.pb_name')->join('paytype_business b','in.ci_payType=b.pb_paytype','left')->where($map)->page($page,$pagesize)->order($order)->fetchSql($this->debug)->select();


        if (!empty($data['list']) && !is_string($data['list'])){
            foreach ($data['list'] as &$item){
                $item['state_text']=$this->getStateText($item['ci_state']);
            }
        }

        $data['count']=Db::name($this->name)->where($map)->fetchSql($this->debug)->count();

        return $data;

    }
    private function getStateText($num){
        switch ($num){
            case -1:
                $string = "<label class='label label-danger'>撤销</label>";
                break;
            case 0:
                $string =  "<label class='label label-warning'>未结算</label>";
                break;
            case 1:
                $string =  "<label class='label label-primary'>成功</label>";
                break;
            case 2:
                $string  ="<label class='label label-success'>在途</label>";
                break;
            default:
                $string ="<label class='label label-warning'>未知状态</label>";
        }
        return $string;
    }



}