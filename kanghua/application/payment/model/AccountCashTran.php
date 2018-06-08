<?php
namespace app\payment\model;
use think\Model;
use think\Session;
use think\Db;

/**
 * 异动明细表查询
 * User: lirong
 * Date: 2017/7/1
 * Time: 20:34
 */
class AccountCashTran extends Model{

    protected $name ='account_cash_tran';
    private $debug =false;

    public function findAccountCashTranList($input){

        $admin_identify =Session::has('admin_identify');
        $map=[];
        if (!$admin_identify){
            $map['ca_platform_id']=Session::get('user.platformId');
        }

        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId'])?$input['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }

        $map['ca_platform_id']=$platformId;

        if (!empty($input['ipt_nick'])){
            $map['ca_unick'] =$input['ipt_nick'];
        }
        if (!empty($input['ipt_aid'])){
            $map['ca_aid']=$input['ipt_aid'];
        }
        if (!empty($input['ipt_caid'])){
            $map['ca_id']=$input['ipt_caid'];
        }

        if (!empty($input['ipt_order_id'])){
            $map['ca_order_id']=$input['ipt_order_id'];
        }

        if (!empty($input['ipt_type'])){
            $map['ca_type']=$input['ipt_type'];
        }

        $beginDate =$input['beginDate'];
        $endDate =$input['endDate'];

        if (!empty($beginDate) && empty($endDate)){
            $map['ca_create_time']=['>= time',$beginDate];
        }elseif (empty($beginDate) && !empty($endDate)){
            $map['ca_create_time']=['<= time',$endDate];
        }elseif (!empty($beginDate) && !empty($endDate)){
            $map['ca_create_time'] =['between time',[$beginDate,$endDate]];
        }

        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);
        $order =$input['orderField'] .' '.$input['orderDirection'];

        $data['list'] = Db::name($this->name)->field('at.*,a.a_payAccountCode')->alias('at')->join('db_account a','at.ca_aid=a.a_id','left')->where($map)->page($page,$pagesize)->order($order)->fetchSql($this->debug)->select();

        if (!empty($data['list'])){
            foreach ($data['list'] as &$item){
                $item['banlance_type_text']=$this->getStateText($item['ca_balance_type']);
            }
        }

        $data['count']=Db::name($this->name)->alias('at')->join('db_account a','at.ca_aid=a.a_id','left')->where($map)->fetchSql($this->debug)->count();

        return $data;
    }


    private function getStateText($num){

        switch ($num){
            case 0:
                $string =  "<label class='label label-warning'>vip收益</label>";
                break;
            case 1:
                $string =  "<label class='label label-primary'>代理收益</label>";
                break;
            case 2:
                $string  ="<label class='label label-info'>推广收益</label>";
                break;
            case 3:
                $string  ="<label class='label label-success'>提现</label>";
                break;
            case 4:
                $string  ="<label class='label label-danger'>提现驳回</label>";
                break;
            case 5:
                $string  ="<label class='label label-default'>技术服务费</label>";
                break;
            case 6:
                $string  ="<label class='label label-default'>大唐服务费</label>";
                break;
            case 7:
                $string  ="<label class='label label-default'>项目方收益</label>";
                break;
            default:
                $string ="<label class='label label-default'>--</label>";
        }
        return $string;
    }




}