<?php
namespace app\payment\controller;
use app\common\controller\Platform;
use app\common\tools\Logs;
use app\payment\model\Account;
use app\payment\model\AccountCashOut;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;

/**
 *  提现管理
 * User: lirong
 * Date: 2017/7/16
 * Time: 14:18
 */
class Cashout extends Platform{

    /**
     * 用于首页数据展示
     * @return mixed
     */

    public function index()
    {
        $input['ipt_nick']=input('post.ipt_nick','','trim');
        $input['ipt_caid']=input('post.ipt_caid','','trim');
        $input['ipt_name']=input('post.ipt_name','','trim');
        $input['ipt_state']=input('post.ipt_state','0','trim');

        $input['ipt_moneyMin']=input('post.ipt_moneyMin','','trim');
        $input['ipt_moneyMax']=input('post.ipt_moneyMax','','trim');

        $input['apply_beginDate']=input('post.apply_beginDate','','trim');
        $input['apply_endDate']=input('post.apply_endDate','','trim');
        $input['reach_beginDate']=input('post.reach_beginDate','','trim');
        $input['reach_endDate']=input('post.reach_endDate','','trim');


        $input['pageSize'] =input('post.pageSize','30','trim');
        $input['pageCurrent'] =input('post.pageCurrent','1','intval');
        $input['orderField'] =input('post.orderField','co_arriveDateTime','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');
        $accountCashOut =new AccountCashOut();
        $res =$accountCashOut->findAccountCashOutList($input);

        $states =get_dict(3);
        $this->assign('states',$states);
        $this->assign('input',$input);
        $this->assign('data',$res);
        return $this->fetch();
    }

    /**
     * 驳回
     * @return mixed
     */
    public function rejected(){

        $accountCashOut =new AccountCashOut();
        if (Request::instance()->isPost()){
            $caid=input('post.id','');
            $content =input('post.message','','trim');
            if (empty($caid) || !is_numeric($caid)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误，请重试!'));
            }
            if (empty($content)){
                $this->ajaxReturn(ajaxCallBack(300,'驳回原因不能为空!'));
            }
            $res =Db::name('account_cash_out')->where(['co_caid'=>$caid])->lock(true)->find();
            if (empty($res)){
                $this->ajaxReturn(ajaxCallBack(300,'您处理的订单不存在或已被删除，请确认后再试!',true,'payment_Cashout_index'));
            }

            if ($res['co_state']!==0){
                $this->ajaxReturn(ajaxCallBack(300,'该订单已处理，请勿重复操作!',true,'payment_Cashout_index'));
            }

            $account =new Account();
            $accountdata =$account->findAccountByUid($res['co_uid']);
            if ($accountdata['code']==300){
                $this->ajaxReturn(ajaxCallBack(300,$accountdata['data']));
            }
            $accountdata =$accountdata['data'];
            if ($accountdata['a_states']==0){
                $this->ajaxReturn(ajaxCallBack(300,'提现账户异常，请确认该账户状态正常'));
            }

            Db::startTrans();
            try{
                $outdata =[
                    'co_reason'=>$content,
                    'co_state'=>-1,
                    'co_operId'=>Session::get('user.userId'),
                    'co_operTime'=>mytime()
                ];
                $cashoutdata =Db::name('account_cash_out')->where(['co_caid'=>$caid])->update($outdata);
                if (!$cashoutdata){
                    Logs::writeMongodb(400020,'account_cash_out',$caid,'提现驳回记录更新失败',$outdata,'Ym');
                    throw new Exception('提现记录更新失败，请重试!');
                }
                $info = array(
                    'ca_id'          => getTimeMarkID(),
                    'ca_uid'         => $res['co_uid'],
                    'ca_unick'       => $res['co_unick'],
                    'ca_platform_id' => $res['co_platform_id'],
                    'ca_aid'         => $accountdata['a_id'],
                    'ca_money'       => $res['co_money'],
                    'ca_business_id' =>1,
                    'ca_operate_id'=>Session::get('user.userId'),
                    'ca_balance'     => $accountdata['a_freeMoney'],
                    'ca_balance_type'=> 4,//驳回
                    'ca_memo'=>'账号余额提现驳回',
                    'ca_type'        => 1,
                    'ca_create_time' => mytime(),
                );

                $cash_tran = Db::name('account_cash_tran')->insert($info);
                if(!$cash_tran){
                    Logs::writeMongodb(400020,'account_cash_tran',$caid,'创建资金进出账异动表失败',$info,'Ym');
                    throw new Exception('创建资金进出账异动表失败');
                }
                $accountdata['a_freeMoney'] += $res['co_money'];
                $accountdata['a_frozenMoney']-=$res['co_money'];

                $accountdata['a_crc']=Account::getVerifyAndOutCrcCode($accountdata,true);

                $accountstate = Db::name('account')->update($accountdata);
                if(!$accountstate){
                    throw new Exception('更新账户失败');
                }
                Db::commit();
                Logs::writeMongodb(400021,'account_cash_out',$caid,'提现驳回成功',$accountdata,'Ym');
                $this->ajaxReturn(ajaxCallBack(200,'提现驳回成功!',true,'payment_Cashout_index'));
            }catch (\Exception $e){
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300,$e->getMessage()));
            }

        }else{
            $id =Request::instance()->param('id','');
            if (empty($id) || !is_numeric($id)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误!'));
            }

            $res =$accountCashOut->findDetailByCaid($id);
            if (empty($res)){
                $this->ajaxReturn(ajaxCallBack(300,'您处理的订单不存在或已被删除，请确认后再试!'));
            }

            $this->assign('co_caid',$id);
            return $this->fetch();
        }
    }

    /**
     * 提现结算
     * @return mixed
     */
    public function settlement(){
        $accountCashOut =new AccountCashOut();
        if (Request::instance()->isPost()){
            $caid=input('post.id','');
            $content =input('post.message','','trim');
            if (empty($caid) || !is_numeric($caid)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误，请重试!'));
            }
//            if (empty($content)){
//                $this->ajaxReturn(ajaxCallBack(300,'结算备注不能为空!'));
//            }

            $res =Db::name('account_cash_out')->where(['co_caid'=>$caid])->lock(true)->find();
            if (empty($res)){
                $this->ajaxReturn(ajaxCallBack(300,'您处理的订单不存在或已被删除，请确认后再试!',true,'payment_Cashout_index'));
            }

            if ($res['co_state']!==0){
                $this->ajaxReturn(ajaxCallBack(300,'该订单已处理，请勿重复操作!',true,'payment_Cashout_index'));
            }

            $account =new Account();
            $accountdata =$account->findAccountByUid($res['co_uid']);
            if ($accountdata['code']==300){
                $this->ajaxReturn(ajaxCallBack(300,$accountdata['data']));
            }
            $accountdata =$accountdata['data'];
            if ($accountdata['a_states']==0){
                $this->ajaxReturn(ajaxCallBack(300,'提现账户异常，请确认该账户状态正常'));
            }

            $res =Db::name('account_cash_out')->where(['co_caid'=>$caid])->update(['co_state'=>1,'co_reason'=>$content]);
            if ($res){
                Logs::writeMongodb(400031,'account_cash_out',$caid,'结算成功',['co_caid'=>$caid,'co_state'=>1,'co_reason'=>$content],'Ym');
                $this->ajaxReturn(ajaxCallBack(200,'结算成功！',true,'payment_Cashout_index'));
            }else{
                Logs::writeMongodb(400030,'account_cash_out',$caid,'结算失败',['co_caid'=>$caid,'co_state'=>1,'co_reason'=>$content],'Ym');
                $this->ajaxReturn(ajaxCallBack(300,'结算失败，请重试!'));
            }
        }else{
            $id =Request::instance()->param('id','');
            if (empty($id) || !is_numeric($id)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误!'));
            }

            $res =$accountCashOut->findDetailByCaid($id);
            if (empty($res)){
                $this->ajaxReturn(ajaxCallBack(300,'您处理的订单不存在或已被删除，请确认后再试!'));
            }

            $this->assign('co_caid',$id);
            return $this->fetch();
        }


    }

    /**
     * 导出到Excel
     * */
    public function exportExcel(){
        $input['ipt_nick']  = input('ipt_nick','','trim');
        $input['ipt_caid']  = input('ipt_caid','','trim');
        $input['ipt_name']  = input('ipt_name','','trim');
        $input['ipt_state'] = input('ipt_state','','trim');

        $input['ipt_moneyMin'] = input('ipt_moneyMin','','trim');
        $input['ipt_moneyMax'] = input('ipt_moneyMax','','trim');

        $input['apply_beginDate'] = input('apply_beginDate','','trim');
        $input['apply_endDate']   = input('apply_endDate','','trim');
        $input['reach_beginDate'] = input('reach_beginDate','','trim');
        $input['reach_endDate']   = input('reach_endDate','','trim');


        $input['pageSize']        = input('pageSize','30','trim');
        $input['pageCurrent']     = input('pageCurrent','1','intval');
        $input['orderField']      = input('orderField','co_arriveDateTime','trim');
        $input['orderDirection']  = input('orderDirection','desc','trim');
        $input['platformId']      = input('platformId',$this->platformId,'intval');

        //提现列表数据
        $accountCashOut = new AccountCashOut();
        $result = $accountCashOut->findAccountCashOutList($input);

        if(!$result['list']){
            $this->ajaxReturn(ajaxCallBack(300, '数据不存在'));
        }

        //数据存在则执行导出
        $name = date("Y-m") . '月提现报表';
        $objPHPExcel = new \PHPExcel();
        $allLetter = range('A', 'Z');

        $objPHPExcel->getProperties()->setCreator('生机密码');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:K1');

        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(45);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(24);

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', '提现编号')
                    ->setCellValue('B2', '用户名')
                    ->setCellValue('C2', '开户行')
                    ->setCellValue('D2', '提现账号')
                    ->setCellValue('E2', '账号姓名')
                    ->setCellValue('F2', '提现金额')
                    ->setCellValue('G2', '手续费')
                    ->setCellValue('H2', '实际金额')
                    ->setCellValue('I2', '状态')
                    ->setCellValue('J2', '提现时间')
                    ->setCellValue('K2', '预计到账时间');

        $status = array(-1 => '驳回', '未处理', '结算', '在途');
        foreach ($result['list'] as $k => $item) {
            $num = $k + 3;
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $num, $item['co_caid'])
                        ->setCellValue('B' . $num, $item['co_unick'])
                        ->setCellValue('C' . $num, $item['co_bankName'])
                        ->setCellValue('D' . $num, $item['co_account'])
                        ->setCellValue('E' . $num, $item['co_cardmaster'])
                        ->setCellValue('F' . $num, $item['co_money'])
                        ->setCellValue('G' . $num, $item['co_tax'])
                        ->setCellValue('H' . $num, $item['co_money'] - $item['co_tax'])
                        ->setCellValue('I' . $num, $status[$item['co_state']])
                        ->setCellValue('J' . $num, $item['co_arriveDateTime'])
                        ->setCellValue('K' . $num, $item['co_day_time']);
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        for ($i = 1; $i <= 10; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($allLetter[$i])->setWidth(20);
        }
//        $objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(30);
        $endCell = 'K' . (count($result['list']) + 2);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style'  => \PHPExcel_Style_Border::BORDER_THIN,
                    'color'  => array('argb' => 'FF000000'),
                ),
            ),  'alignment'  => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        );

        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A2:' . $endCell)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A'.(count($result['list']) + 4))
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->getStyle('A'.(count($result['list']) + 7))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->setTitle($name);
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function create(){}
    public function edit(){}
    public function remove(){}
}