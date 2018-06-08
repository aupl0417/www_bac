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
            {notempty name="$errormessage"}
            <div class="bg_fff solid_last rmb10 solid_b">
                <div class="solid_b">
                    <div class="row mr0 phong_form rpl15">
                      <div class="text-center"><h4>{$errormessage}</h4></div>
                    </div>
                </div>
            </div>
            {else /}
            <form action="{:url('payment/online/viporderpayment')}" method="post">
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
                    </div>
                </div>
                <input type="hidden" name="channelID" id="channelID" value="C000000000000008">
                <input type="hidden" name="settleMode" id="settleMode" value="2"> <!-- 1 积分 2：货款 -->
                <input type="hidden" name="merOrderID" id="out_trade_no" value="{$data.os_id}"/>
                <input type="hidden" name="goodsName" value="{$goodsName}">
                <input type="hidden" name="buyAgentFee" value="0">
                <input type="hidden" name="orderAmount" value="{$data.os_actual_payprice*100}">
                <input type="hidden" name="disabledPay" value=""/>
                <input type="hidden" name="onlyPay" value="1,2"/>
                <input type="hidden" name="giveScore" value="{$data.os_score}"/>
                <input type="hidden" name="autoRecieve" value="1">
                <input type="hidden" name="busID" value="10230,40230">
                <input type="hidden" name="goodsUrl" value="{:url('index/index','','',true)}">
                <input type="hidden" name="recieverID" value="{$data.pl_dttx_uid}">
                <input type="hidden" name="buyerID" value="{$data.os_buyer_dttx_uid}">
                <input type="hidden" name="buyerNick" value="{$data.os_buyer_nick}">
                <input type="hidden" name="payAccountCode" value="FXS_RECHARGE">
                <input type="hidden" name="remark" value="大唐分销系统会员升级订单">
                <div class="rmt30 rpd10">
                    <input type="submit" value="确认支付" class="button my-btn-red button-raised rh40 rfs16 rline40">
                </div>
            </form>
            {/notempty}
        </div>
    </div>
</div>
{/block}
{block name='script'}

{/block}