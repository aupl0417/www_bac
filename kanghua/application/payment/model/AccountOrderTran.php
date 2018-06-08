<?php
namespace app\payment\model;
use think\Db;
use think\Model;

/**
 *
 * User: lirong
 * Date: 2017/7/9
 * Time: 15:14
 */
class AccountOrderTran extends Model{

    protected $name ='account_order_tran';

    /**
     * @param $uid  用户id
     * @param string $type 0会员收益，1，代理收益
     * @param String $date 'all|全部','1|当月','2|上月','3|三个月'
     * @return bool|false|\PDOStatement|string|\think\Collection
     */
    public function findlistByUidAndType($uid,$type='',$date=0){

        if (empty($uid)){
            return false;
        }
        $map['ad_upid']=$uid;
        if ($type!=''){
            $map['ad_type']=$type;
        }
        if ($date>0){
            switch ($date){
                   case 1:
                    $beginThismonth = mktime(0,0,0,date('m'),1,date('y'));
                    $now = time();
                    $map['ad_create_time']=['between',[$beginThismonth,$now]];
                    break;
                case 2:
                    $time = strtotime('last month');
                    $beginpremonth = strtotime(date('Y-m-01', $time)); //上个月的第一天
                    $now = strtotime(date('Y-m-t', $time))+86399; //上个月的最后一天
                    $map['ad_create_time']=['between',[$beginpremonth,$now]];
                    break;
                case 3:
                    //近三个月起止时间
                    $beginLastThreemonth = mktime(0,0,0,date('m')-3,1,date('y'));
                    $now = time();
                    $map['ad_create_time']=['between',[$beginLastThreemonth,$now]];
                    break;
            }
        }

        $res =Db::name($this->name)->where($map)->order('ad_create_time','desc')->select();
        return $res;

    }


    /**
     * 根据订单ID查询分润明细列表
     * @param $orderId
     * @return bool
     */
    public function findTranListByOrderId($orderId){

        if (empty($orderId)){
            return false;
        }

        $res =Db::name($this->name)->where(['ad_order_id'=>$orderId])->select();
        if (!empty($res)){
            foreach ($res as &$item){
                $item['type_text']=$this->getStateText($item['ad_type']);
            }
            return $res;
        }else{
            return false;
        }

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