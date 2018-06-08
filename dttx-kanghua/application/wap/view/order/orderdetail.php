<!--
 * 订单详情
 * User: lirong
 * Date: 2017/7/18
 * Time: 10:59
-->
{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/index')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">
    <a href="{:url('ucenter/index')}" class="back link external"><img src="__STATIC__/wap/images/home.png" width="20" alt="返回个人中心"></a>
</div><!--.right-->
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content bg_gray">
            <div class="rpd15 bg_fff over rmb15">
                {if $data.os_status==2}
                <div class="pull-left rpt25" style="width:28px;">
                    <img src="__STATIC__/wap/images/car.png" width="26" alt="">
                </div>
                {else /}
                <div class="pull-left" style="width:28px;">
                    <img src="__STATIC__/wap/images/car.png" width="26" alt="">
                </div>
                {/if}
                <div class="pull-left rpl15">
                    <div class="rfs16 c_red rmb10">
                        {switch name="$data.os_status"}
                            {case value="0"}等待支付{/case}
                            {case value="1"}等待卖家发货{/case}
                            {case value="2"}卖家已发货{/case}
                            {case value="3"}交易完成{/case}
                            {case value="4"}退款中{/case}
                            {case value="5"}已退款{/case}
                        {/switch}
                    </div>
                    <div class="rfs14 c_999">{if $data.os_status==2}还剩{$outtime}自动收货{/if}</div>
                </div>
            </div>
            <div class="rpd15 bg_fff over row no-gutter rmb15">
                <div class="pull-left rpt30 col-10" style="width:28px;">
                    <img src="__STATIC__/wap/images/ade.png" width="14" alt="">
                </div>
                <div class="pull-left  col-65 rpl15">
                    <div class="rfs16 c_333 rmb10">收货人:{$data.os_receiver_name}</div>
                    <div class="rfs14 c_999">{$data.os_address}</div>
                </div>
                <div class="pull-right col-25">
                    <div class="rfs14 c_blue">{$data.os_receiver_phone}</div>
                </div>
            </div>

            <div class="my-order bg_fff rmb15">
                <div class="over rpd15">
                    <div class="pull-left rfs14 c_333">
                        订单号: <span class="c_999">({$data.os_id})</span>
                    </div>
                    <div class="pull-right c_red rfs14">{$data.statusText}</div>
                </div><!--.over rpd15-->
                <div class="good-boxin rpd15">
                    {notempty name="$data.goods"}
                    {foreach name="$data.goods" item="goods"}
                        <a href="{:url('goods/detail',['id'=>$goods.og_shopid])}" class="external row no-gutter ">
                            <div class="col-20">
                                <div class="solid_all">
                                    <img src="{$goods.og_goods_img}" width="100%" alt="">
                                </div>
                            </div>
                            <div class="col-50">
                                <div class="good-name rml10 c_333 rfs14 rmt5">
                                   {$goods.og_goods_name}
                                </div>
                                <div class="good-name rfs14 c_999 rml10 rmt5">
                                    {$goods.og_goods_sku}
                                </div>
                            </div>
                            <div class="col-30 text-right">
                                <div class="rfs14 c_333">&yen;{$goods.og_goods_price}</div>
                                <div class="rfs14 c_999">X{$goods.og_goods_num}</div>
                            </div>
                        </a>
                    {/foreach}
                    {/notempty}
                </div><!--.over-->
                <div class="over rpd15 solid_b">
                    <div class="pull-left rfs14 c_333">
                        总金额 :
                        <span class="c_red">&yen;{$data.os_actual_payprice}</span>
                        （运费¥{$data.os_deliver_price}）
                    </div>
                    <div class="pull-right rfs14 c_333">
                        共{$data.os_goods_num}件
                    </div>
                </div><!--.over rpd15 solid_b-->
                <div class="over text-center tool-box rpd15 row">
                    <div class="col-25">
                        <a class="button rpd0 alert-phone" data-phone="{$data.os_seller_phone}" href="#">联系卖家</a>
                    </div>
                    {switch name="$data.os_status"}
                    {case value="-1"}    <div class="col-25">  <a class="button cur rpd0 external" href="{:url('order/payorder',['id'=>$data.os_id])}">订单已关闭</a>                       </div>{/case}
                    {case value="0"}    <div class="col-25">   <a class="button cur rpd0 external" href="{:url('order/payorder',['id'=>$data.os_id])}">支付订单</a>    </div>{/case}
                    {case value="1"}<div class="col-25"></div>
                    <div class="col-25">   <a class="button rpd0 " href="javascrpt:;">待发货</a></div>
                    <div class="col-25">    <a class="button cur rpd0 ok-btn" data-orderid="{$data.os_id}" href="javascript:;">确认收货</a>    </div>{/case}
                    {case value="2"}
                    <div class="col-25"></div>
                    <div class="col-25">  <a class="button rpd0 external" href="{:url('order/lookexpress',['id'=>$data.os_id])}">查看物流</a></div>
                    <div class="col-25">  <a class="button cur rpd0 ok-btn" data-orderid="{$data.os_id}" href="javascript:;">确认收货</a>    </div>
                    {/case}
                    {case value="3"}    <div class="col-25">   <a class="button rpd0" href="javascrpt:;">交易成功</a>    </div>{/case}
                    {case value="4"}    <div class="col-25">   <a class="button rpd0" href="javascrpt:;">退款中</a>    </div>{/case}
                    {case value="5"}    <div class="col-25">   <a class="button rpd0" href="javascrpt:;">退款成功</a>    </div>{/case}
                    {/switch}
                </div>
            </div><!--.my-order-->

            <div class="rpd15 bg_fff over rmb15">

                <div class="rfs14 c_333 rmb10">创建时间:  <span class="c_999">{notempty name="$data.os_create_time"}{$data.os_create_time|date='Y-m-d H:i:s',###}{else /}--{/notempty}</span></div>
                {notempty name="$data.os_pay_time"}  <div class="rfs14 c_333 rmb10">付款时间:  <span class="c_999">{$data.os_pay_time|date='Y-m-d H:i:s',###}</span></div>{/notempty}
                {notempty name="$data.os_deliver_time"}    <div class="rfs14 c_333 rmb10">发货时间:  <span class="c_999">{$data.os_deliver_time|date='Y-m-d H:i:s',###}</span></div>{/notempty}
                {if $data.os_status>2}    <div class="rfs14 c_333 rmb10">收货时间:  <span class="c_999">{$data.os_auto_receiver_time|date='Y-m-d H:i:s',###}</span></div>{/if}
            </div>

        </div><!--.page-content-->
    </div><!--.page-->
</div>
{/block}

{block name="othercontent"}
<div id="d1" class="modal-overlay"></div>
<div class="modal modal-in order-modal phone-modal"><div class="modal-inner">
        <div class="modal-text"><a data-url="" class="external dial-phone"></a></div>
    </div>
</div>
<!--电话号码弹框-->

<!--确定收到商品弹框-->
<div id="d2" class="modal-overlay"></div>
<div class="modal modal-in yue-modal-in order-modal-in order-modal sentbtn">
    <div class="modal-inner rpt30 rpb30 rpl20 rpr20">
        <div>
            <img src="__STATIC__/wap/images/order-icon.png" width="50" alt="">
        </div>
        <p class="rfs16 text-center c_333">请确认您已收到该货品!</p>
        <div class="row rpt30">

            <div class="col-50">
                <a class="button solid_all no-ok">取消</a>
            </div>
            <div class="col-50">
                <input type="hidden" id="goodsorderid" value="">
                <a class="button button-fill order-ok">确定</a>
            </div>
        </div>
    </div>
</div>
<!--确定收到商品弹框-->
<!--延迟收货弹框-->
<div id="d3" class="modal-overlay"></div>
<div class="modal modal-in yue-modal-in order-modal sentbtn yan-modal">
    <div class="modal-inner rpt30 rpb30 rpl20 rpr20">
        <div>
            <form action="">
                <div class="row phong_form rmb15 no-gutter">
                    <div class="col-25 text-left rfs14 c_333 rpt10">
                        延时收货
                    </div>
                    <div class="col-75 input_center input-radius-active" >
                        <select class="sel" name="" id="">
                            <option value="">一天</option>
                            <option value="">两天</option>
                            <option value="">三天</option>
                            <option value="">四天</option>
                        </select>
                    </div>
                </div>
                <div class="row phong_form rmb15 no-gutter">
                    <div class="col-25 text-left rfs14 c_333 rpt10">
                        延时原因
                    </div>
                    <div class="col-75 input_center input-radius-active">
                        <textarea class="txt rpd10" placeholder="请简单说明延时原因"></textarea>
                    </div>
                </div>
                <div class="row rpt30">

                    <div class="col-50">
                        <a class="button solid_all yan-no-ok">取消</a>
                    </div>
                    <div class="col-50">
                        <a class="button button-fill order-ok yan-ok">确定</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--延迟收货弹框-->
{/block}
{block name='script'}
<script>var myApp = new Framework7();</script>
<script>
    var div = document.getElementById('d1');
    var div2 = document.getElementById('d2');
    $$(".alert-phone").click(function(){
        var phone =$$(this).data('phone');

        if(typeof (phone)!= 'undefined'){
            $$('.dial-phone').data('url', 'tel://' + phone);
            $$('.dial-phone').text(phone);
            $$(".phone-modal").show();
            div.className="modal-overlay-visible modal-overlay";
        }
    })
    div.onclick=function(){
        $$(".phone-modal").hide();
        div.className="modal-overlay";
    }
    $$(".ok-btn").click(function(){
        var orderid =$$(this).data('orderid');
        $$('#goodsorderid').val(orderid);
        $$(".order-modal-in").show();
        div2.className="modal-overlay-visible modal-overlay";
    })
    $$(".order-ok").click(function(){
        var orderid = $$('#goodsorderid').val();
        if (orderid!='' && orderid.length==25){
            $$.ajax({
                type:'POST',
                url:"{:url('order/confirmOrder')}",
                data:{id:orderid},
                dataType:"json",
                beforeSend:function () {
                    console.log('beforeSend');
                },
                success:function (data) {
                    alert(data.message);
                    if (data.statusCode==200)
                        window.setTimeout(function () {
                            window.location.reload();
                        },3)
                },
                error:function () {
                    console.log('error');
                }

            })

            $$(".order-modal-in").hide();
            div2.className="modal-overlay";
        }else {
            return false;
        }
    })
    $$(".no-ok").click(function(){
        $$(".order-modal-in").hide();
        div2.className="modal-overlay";
    })
    $$(".yan-btn").click(function(){
        $$(".yan-modal").show();
        div2.className="modal-overlay-visible modal-overlay";
    })
    $$(".yan-ok").click(function(){
        $$(".yan-modal").hide();
        div2.className="modal-overlay";
    })
    $$(".yan-no-ok").click(function(){
        $$(".yan-modal").hide();
        div2.className="modal-overlay";
    })
</script>
{/block}