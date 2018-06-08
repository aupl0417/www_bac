<?php
namespace app\wap\controller;
use app\common\controller\Wap;
use app\payment\model\AccountOrderTran;
use think\Request;
use think\Session;

/**
 * 账户收益类
 * User: lirong
 * Date: 2017/7/9
 * Time: 15:25
 */
class Account extends Wap{

    public function index(){
        $day =Request::instance()->param('m','0','intval');
        $userid =Session::get('user.userId');
        $accountOrderTran =new AccountOrderTran();
        $res = $accountOrderTran->findlistByUidAndType($userid,'',$day);

        switch ($day){
            case 0:
                $title='累计收益';
                break;
            case 1:
                $title="本月累计收益";
                break;
            case 2:
                $title="上月累计收益";
                break;
            case 3:
                $title="近三月累计收益";
                break;
        }
        $this->assign('list',$res);
        $this->assign('title',$title);
        return $this->fetch();
    }

    public function viplist(){
        $userid =Session::get('user.userId');
        $day =Request::instance()->param('m','0','intval');
        switch ($day){
            case 0:
                $title='推广收益明细';
                break;
            case 1:
                $title="本月推广收益明细";
                break;
            case 2:
                $title="上月推广收益明细";
                break;
            case 3:
                $title="近三月推广收益明细";
                break;
        }
        $accountOrderTran =new AccountOrderTran();
        $res = $accountOrderTran->findlistByUidAndType($userid,2,$day);

        $this->assign('list',$res);
        $this->assign('title',$title);
        return $this->fetch();
    }

    public function agentlist(){
        $day =Request::instance()->param('m','0','intval');
        switch ($day){
            case 0:
                $title='代理收益明细';
                break;
            case 1:
                $title="本月代理收益明细";
                break;
            case 2:
                $title="上月月代理收益明细";
                break;
            case 3:
                $title="近三月代理收益明细";
                break;
        }
        $userid =Session::get('user.userId');
        $accountOrderTran =new AccountOrderTran();
        $res = $accountOrderTran->findlistByUidAndType($userid,1,$day);

        $this->assign('list',$res);
        $this->assign('title',$title);
        return $this->fetch();
    }

    public function cashout(){


        $this->assign('title','余额提现');
        return $this->fetch();
    }


}