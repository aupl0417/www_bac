{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="javascript:window.history.go(-1);" class="back link"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='header'}
<div class="right">
    <a href="{:url('ucenter/index')}" class="back link external"><img src="__STATIC__/wap/images/home.png" width="20" alt="返回个人中心"></a>
</div>
{/block}

{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f">
            <form action="{:url('payment/online/orderpayment')}" method="post">
                <div class="bg_fff solid_last rmb10 solid_b">
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                订单号
                            </div>
                            <div class="col-66 rpl0 input-radius-active rpt15 rfs14">
                                {$data.os_id}
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                订单信息
                            </div>
                            <div class="col-66 rpl0 input-radius-active rpt15 rfs14">
                                {$goodsName}
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                支付方式
                            </div>
                            <div class="col-66 rpl0 input-radius-active rfs14 rpt15">
                                余额/唐宝支付
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                需付款
                            </div>
                            <div class="col-66 rpl0 input-radius-active rfs14 c_blue rpt15">
                                {$data.os_actual_payprice}
                            </div>
                        </div>
                    </div>                    </div>
<!--                    <div class="bg_fff solid_last rmb10 solid_b">-->
<!--                    <div class="solid_b">-->
<!--                        <div class="row mr0 phong_form rpl15">-->
<!--                            <div class="text_left rfs16 c_333">-->
<!--                                <span id="timer" class="text-center">剩余时间：00：00：00</span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
                <input type="hidden" name="channelID" id="channelID" value="C000000000000008">
                <input type="hidden" name="settleMode" id="settleMode" value="2"> <!-- 1 积分 2：货款 -->
                <input type="hidden" name="merOrderID" id="out_trade_no" value="{$data.os_id}"/>
                <input type="hidden" name="goodsName" value="{$goodsName}">
                <input type="hidden" name="buyAgentFee" value="0">
                <input type="hidden" name="orderAmount" value="{$data.os_actual_payprice*100}">
                <input type="hidden" name="disabledPay" value=""/>
                <input type="hidden" name="onlyPay" value="1,2"/>
                <input type="hidden" name="giveScore" value="{$data.os_score}"/>
                <input type="hidden" name="autoRecieve" value="0">
                <input type="hidden" name="busID" value="10230,40230">
                <input type="hidden" name="goodsUrl" value="{:url('index/index','','',true)}">
                <input type="hidden" name="recieverID" value="{$data.pl_dttx_uid}">
                <input type="hidden" name="buyerID" value="{$data.os_buyer_dttx_uid}">
                <input type="hidden" name="buyerNick" value="{$data.os_buyer_nick}">
                <input type="hidden" name="payAccountCode" value="FXS_RECHARGE">
                <input type="hidden" name="remark" value="大唐分销系统订单,{$data.os_id}">

                <div class="rmt30 rpd10">
                    <input type="submit" value="确认支付" class="button my-btn-red button-raised rh40 rfs16 rline40">
                </div>
            </form>

        </div>
    </div>
</div>
{/block}
{block name='script'}
<script>
//    function leftTimer(year,month,day,hour,minute,second){
//        var leftTime = (new Date(year,month-1,day,hour,minute,second)) - (new Date()); //计算剩余的毫秒数
//        if (leftTime<0){
//            document.getElementById("timer").innerHTML = "订单已取消!";
//            return false;
//        }
//        var days = parseInt(leftTime / 1000 / 60 / 60 / 24 , 10); //计算剩余的天数
//        var hours = parseInt(leftTime / 1000 / 60 / 60 % 24 , 10); //计算剩余的小时
//        var minutes = parseInt(leftTime / 1000 / 60 % 60, 10);//计算剩余的分钟
//        var seconds = parseInt(leftTime / 1000 % 60, 10);//计算剩余的秒数
//        days = checkTime(days);
//        hours = checkTime(hours);
//        minutes = checkTime(minutes);
//        seconds = checkTime(seconds);
//        setInterval("leftTimer(2017,8,7,15,50,00)",1000);
//        document.getElementById("timer").innerHTML = "剩余时间："+hours+"：" +minutes+"："+seconds;
//    }
//    function checkTime(i){ //将0-9的数字前面加上0，例1变为01
//        if(i<10)
//        {
//            i = "0" + i;
//        }
//        return i;
//    }
//    window.onload=leftTimer();
</script>
{/block}