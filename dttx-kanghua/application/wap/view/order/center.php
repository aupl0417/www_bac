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
            <div>
                <div class="toolbar gold-toolbar tabbar bg-white after-bor-none solid_b rmb15 order-toolbar" style="top: 0;">
                    <div class="toolbar-inner order-tool">
                        <a href="#tab-1" class="tab-link rml15 rfs16 c_333 rmr25 {eq name="$typeid" value="all"}active{/eq}">
                            全部
                            <span class="no">{gt name="$count.all" value='99'}99{else/ }{$count.all}{/gt}</span>
                        </a>
                        <a href="#tab-2" class="tab-link rml15 rfs16 c_333 rmr25 {eq name="$typeid" value="pay"}active{/eq}">
                            待付款
                            <span class="no">{gt name="$count.waitpay" value='99'}99{else/ }{$count.waitpay}{/gt}</span>
                        </a>
                        <a href="#tab-3" class="tab-link rml15 rfs16 c_333 rmr25 {eq name="$typeid" value="send"}active{/eq}">
                            待发货
                            <span class="no">{gt name="$count.waitsend" value='99'}99{else/ }{$count.waitsend}{/gt}</span>
                        </a>
                        <a href="#tab-4" class="tab-link rml15 rfs16 c_333 rmr25 {eq name="$typeid" value="receive"}active{/eq}">
                            待收货
                            <span class="no">{gt name="$count.waitreceive" value='99'}99{else/ }{$count.waitreceive}{/gt}</span>
                        </a>
                    </div>
                </div>
                <div class="page-content" style="padding:0;height:auto">
                    <div class="tabs news-cont">
                        <!--第一个tab开始-->
                        <div id="tab-1" class="tab {eq name="$typeid" value="all"}active{/eq}">
                            {notempty name="list"}
                            {foreach name='list' item='item'}
                            <div class="my-order bg_fff rmb15">
                                <a href="{:url('order/orderdetail',['id'=>$item.os_id])}" class="external">
                                <div class="over rpd15">
                                    <div class="pull-left rfs14 c_333">
                                        订单号: <span class="c_999">({$item.os_id})</span>
                                    </div>
                                    <div class="pull-right c_red rfs14">{$item.statusText}</div>
                                </div><!--.over rpd15-->
                                </a>
                                    <div class="good-boxin ">
                                        {notempty name="$item.goods"}
                                        {foreach name='$item.goods' item='vo'}
                                        <a href="{$vo.og_goods_url|default=''}" class="row no-gutter rpd15 external">
                                            <div class="col-20">
                                                <div class="solid_all">
                                                    <img src="{$vo.og_goods_img|default=''}" width="100%" alt="">
                                                </div>
                                            </div>
                                            <div class="col-50">
                                                <div class="good-name rml10 c_333 rfs14 rmt5">
                                                    {$vo.og_goods_name|default=''}
                                                </div>
                                                <div class="good-name rfs14 c_999 rml10 rmt5">
                                                  {$vo.og_goods_sku|default=''}
                                                </div>
                                            </div>
                                            <div class="col-30 text-right rmt5">
                                                <div class="rfs14 c_333">&yen;{$vo.og_goods_price|default=''}</div>
                                                <div class="rfs14 c_999">X{$vo.og_goods_num|default=''}</div>
                                            </div>           </a>
                                        {/foreach}
                                        {/notempty}
                                    </div>
                         <!--.over-->
                                <div class="over rpd15 solid_b">
                                    <div class="pull-left rfs14 c_333">
                                        总金额 :
                                        <span class="c_red">&yen;{$item.os_actual_payprice}</span>
                                        （运费¥{$item.os_deliver_price}）
                                    </div>
                                    <div class="pull-right rfs14 c_333">
                                        共{$item.os_goods_num}件
                                    </div>
                                </div><!--.over rpd15 solid_b-->
                                <div class="over text-center tool-box rpd15 row">
                                    <div class="col-25">
                                        <a class="button rpd0 alert-phone" data-phone="{$item.os_seller_phone}" href="#">联系卖家</a>
                                    </div>
<!--                                    <div class="col-25">-->
<!--                                        <a class="button rpd0 yan-btn" href="#">延时</a>-->
<!--                                    </div>-->
<!--                                    <div class="col-25">-->
<!--                                        <a class="button rpd0" href="#">查看物流</a>-->
<!--                                    </div>-->

                                        {switch name="$item.os_status"}
                                        {case value="-1"}    <div class="col-25">  <a class="button cur rpd0 external" href="{:url('order/payorder',['id'=>$item.os_id])}">订单已关闭</a>                       </div>{/case}
                                        {case value="0"}    <div class="col-25">   <a class="button cur rpd0 external" href="{eq name='$item.os_bus_id' value='0'}{:url('order/payorder',['id'=>$item.os_id])}{else /}{:url('ucenter/payorder',['id'=>$item.os_id])}{/eq}">支付订单</a>    </div>{/case}
                                    {case value="1"}<div class="col-25"></div>
                                            <div class="col-25">   <a class="button rpd0 " href="javascrpt:;">待发货</a></div>
                                            <div class="col-25">    <a class="button cur rpd0 ok-btn" data-orderid="{$item.os_id}" href="javascript:;">确认收货</a>    </div>{/case}
                                    {case value="2"}
                                    <div class="col-25"></div>
                                    <div class="col-25">  <a class="button rpd0 external" href="{:url('order/lookexpress',['id'=>$item.os_id])}">查看物流</a></div>
                                    <div class="col-25">  <a class="button cur rpd0 ok-btn" data-orderid="{$item.os_id}" href="javascript:;">确认收货</a>    </div>
                                    {/case}
                                        {case value="3"}    <div class="col-25">   <a class="button rpd0" href="javascrpt:;">交易成功</a>    </div>{/case}
                                        {case value="4"}    <div class="col-25">   <a class="button rpd0" href="javascrpt:;">退款中</a>    </div>{/case}
                                        {case value="5"}    <div class="col-25">   <a class="button rpd0" href="javascrpt:;">退款成功</a>    </div>{/case}
                                        {/switch}


                                </div>
                            </div><!--.my-order-->
                            {/foreach}
                            {else /}
                                <div class="my-order rmb15 bg_fff">
                                    <div class="over rpd15 c_999 text-center">
                                            暂无订单!
                                    </div><!--.over rpd15-->
                                </div>
                            {/notempty}

                        </div><!--#tab-1-->
                        <!--第一个tab结束-->

                        <!--第二个tab开始 待支付  -->
                        <div id="tab-2" class="tab {eq name="$typeid" value="pay"}active{/eq}">
                            {notempty name="waitpaylist"}
                            {foreach name='waitpaylist' item='item'}
                            <div class="my-order bg_fff rmb15">
                                <div class="over rpd15">
                                    <a href="{:url('order/orderdetail',['id'=>$item.os_id])}" class="external">
                                    <div class="pull-left rfs14 c_333">
                                        订单号: <span class="c_999">({$item.os_id})</span>
                                    </div>
                                    <div class="pull-right c_red rfs14">{$item.statusText}</div>
                                    </a>
                                </div><!--.over rpd15-->

                                    <div class="good-boxin ">
                                        {notempty name="$item.goods"}
                                        {foreach name='$item.goods' item='vo'}
                                        <a href="{$vo.og_goods_url|default=''}" class="row no-gutter rpd15 external">
                                        <div class="col-20">
                                            <div class="solid_all">
                                                <img src="{$vo.og_goods_img}" width="100%" alt="">
                                            </div>
                                        </div>
                                        <div class="col-50">
                                            <div class="good-name rml10 c_333 rfs14 rmt5">
                                                {$vo.og_goods_name}
                                            </div>
                                            <div class="good-name rfs14 c_999 rml10 rmt5">
                                                {$vo.og_goods_sku}
                                            </div>
                                        </div>
                                        <div class="col-30 text-right">
                                            <div class="rfs14 c_333">&yen;{$vo.og_goods_price}</div>
                                            <div class="rfs14 c_999">X{$vo.og_goods_num}</div>
                                        </div>
                                        </a>
                                        {/foreach}
                                        {/notempty}
                                    </div>
                                <!--.over-->
                                <div class="over rpd15 solid_b">
                                    <div class="pull-left rfs14 c_333">
                                        总金额 :
                                        <span class="c_red">&yen;{$item.os_actual_payprice}</span>
                                        （运费¥{$item.os_deliver_price}）
                                    </div>
                                    <div class="pull-right rfs14 c_333">
                                        共{$item.os_goods_num}件
                                    </div>
                                </div><!--.over rpd15 solid_b-->
                                <div class="over text-center tool-box rpd15 row">
                                    <div class="col-25">
                                        <a class="button rpd0 alert-phone" data-phone="{$item.os_seller_phone}" href="#">联系卖家</a>
                                    </div>
                                    <!--                                    <div class="col-25">-->
                                    <!--                                        <a class="button rpd0 yan-btn" href="#">延时</a>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="col-25">-->
                                    <!--                                        <a class="button rpd0" href="#">查看物流</a>-->
                                    <!--                                    </div>-->
                                    <div class="col-25">
                                        {if $item.os_status ==0 }
                                        <a class="button cur rpd0 external" href="{eq name='$item.os_bus_id' value='0'}{:url('order/payorder',['id'=>$item.os_id])}{else /}{:url('ucenter/payorder',['id'=>$item.os_id])}{/eq}">支付订单</a>
                                        {/if}
                                    </div>
                                </div>
                            </div><!--.my-order-->
                            {/foreach}
                            {else /}
                            <div class="my-order rmb15 bg_fff">
                                <div class="over rpd15 c_999 text-center">
                                    暂无订单!
                                </div><!--.over rpd15-->
                            </div>
                            {/notempty}
                        </div>
                        <!--第二个tab开始-->

                        <!--第三个tab开始 代发货-->
                        <div id="tab-3" class="tab {eq name="$typeid" value="send"}active{/eq}">

                            {notempty name="waitsend"}
                            {foreach name='waitsend' item='item'}
                            <div class="my-order bg_fff rmb15">
                                <div class="over rpd15">
                                    <a href="{:url('order/orderdetail',['id'=>$item.os_id])}" class="external">
                                        <div class="pull-left rfs14 c_333">
                                            订单号: <span class="c_999">({$item.os_id})</span>
                                        </div>
                                        <div class="pull-right c_red rfs14">{$item.statusText}</div>
                                    </a>
                                </div><!--.over rpd15-->

                                <div class="good-boxin ">
                                    {notempty name="$item.goods"}
                                    {foreach name='$item.goods' item='vo'}
                                    <a href="{$vo.og_goods_url|default=''}" class="row no-gutter rpd15 external">
                                    <div class="col-20">
                                        <div class="solid_all">
                                            <img src="{$vo.og_goods_img}" width="100%" alt="">
                                        </div>
                                    </div>
                                    <div class="col-50">
                                        <div class="good-name rml10 c_333 rfs14 rmt5">
                                            {$vo.og_goods_name}
                                        </div>
                                        <div class="good-name rfs14 c_999 rml10 rmt5">
                                            {$vo.og_goods_sku}
                                        </div>
                                    </div>
                                    <div class="col-30 text-right">
                                        <div class="rfs14 c_333">&yen;{$vo.og_goods_price}</div>
                                        <div class="rfs14 c_999">X{$vo.og_goods_num}</div>
                                    </div></a>
                                    {/foreach}{/notempty}
                                </div><!--.over-->
                                <div class="over rpd15 solid_b">
                                    <div class="pull-left rfs14 c_333">
                                        总金额 :
                                        <span class="c_red">&yen;{$item.os_actual_payprice}</span>
                                        （运费¥{$item.os_deliver_price}）
                                    </div>
                                    <div class="pull-right rfs14 c_333">
                                        共{$item.os_goods_num}件
                                    </div>
                                </div><!--.over rpd15 solid_b-->
                                <div class="over text-center tool-box rpd15 row">
                                    <div class="col-25">
                                        <a class="button rpd0 alert-phone" data-phone="{$item.os_seller_phone}" href="#">联系卖家</a>
                                    </div>
                                    <!--                                    <div class="col-25">-->
                                    <!--                                        <a class="button rpd0 yan-btn" href="#">延时</a>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="col-25">-->
                                    <!--                                        <a class="button rpd0" href="#">查看物流</a>-->
                                    <!--                                    </div>-->
                                    <div class="col-25"></div>
                                    <div class="col-25">   <a class="button rpd0 " href="javascrpt:;">待发货</a></div>
                                    <!--<div class="col-25">
                                        {if $item.os_status >=1 }
                                        <a class="button cur rpd0 ok-btn" data-orderid="{$item.os_id}" href="javascript:;">确认收货</a>
                                        {/if}
                                    </div>-->
                                </div>
                            </div><!--.my-order-->
                            {/foreach}
                            {else /}
                            <div class="my-order rmb15 bg_fff">
                                <div class="over rpd15 c_999 text-center">
                                    暂无订单!
                                </div><!--.over rpd15-->
                            </div>
                            {/notempty}

                        </div>
                        <!--第三个tab开始-->

                        <!--第三个tab开始 待收货-->
                        <div id="tab-4" class="tab {eq name="$typeid" value="receive"}active{/eq}">


                            {notempty name="waitrecvied"}
                            {foreach name='waitrecvied' item='item'}
                            <div class="my-order bg_fff rmb15">
                                <div class="over rpd15">
                                    <a href="{:url('order/orderdetail',['id'=>$item.os_id])}" class="external">
                                    <div class="pull-left rfs14 c_333">
                                        订单号: <span class="c_999">({$item.os_id})</span>
                                    </div>
                                    <div class="pull-right c_red rfs14">{$item.statusText}</div>
                                    </a>
                                </div><!--.over rpd15-->

                                    <div class="good-boxin">
                                        {notempty name="$item.goods"}
                                        {foreach name='$item.goods' item='vo'}
                                        <a href="{$vo.og_goods_url|default=''}" class="row no-gutter rpd15 external">
                                        <div class="col-20">
                                            <div class="solid_all">
                                                <img src="{$vo.og_goods_img}" width="100%" alt="">
                                            </div>
                                        </div>
                                        <div class="col-50">
                                            <div class="good-name rml10 c_333 rfs14 rmt5">
                                                {$vo.og_goods_name}
                                            </div>
                                            <div class="good-name rfs14 c_999 rml10 rmt5">
                                                {$vo.og_goods_sku}
                                            </div>
                                        </div>
                                        <div class="col-30 text-right">
                                            <div class="rfs14 c_333">&yen;{$vo.og_goods_price}</div>
                                            <div class="rfs14 c_999">X{$vo.og_goods_num}</div>
                                        </div> </a>
                                        {/foreach}
                                        {/notempty}
                                    </div>
                                   <!--.over-->
                                <div class="over rpd15 solid_b">
                                    <div class="pull-left rfs14 c_333">
                                        总金额 :
                                        <span class="c_red">&yen;{$item.os_actual_payprice}</span>
                                        （运费¥{$item.os_deliver_price}）
                                    </div>
                                    <div class="pull-right rfs14 c_333">
                                        共{$item.os_goods_num}件
                                    </div>
                                </div><!--.over rpd15 solid_b-->
                                <div class="over text-center tool-box rpd15 row">
                                    <div class="col-25">
                                        <a class="button rpd0 alert-phone" data-phone="{$item.os_seller_phone}" href="#">联系卖家</a>
                                    </div>
                                    <!--                                    <div class="col-25">-->
                                    <!--                                        <a class="button rpd0 yan-btn" href="#">延时</a>-->
                                    <!--                                    </div>-->
                                    <div class="col-25"></div>
                                    <div class="col-25">
                                        <a class="button rpd0 external" href="{:url('order/lookexpress',['id'=>$item.os_id])}">查看物流</a>
                                    </div>
                                    <div class="col-25">
                                        {if $item.os_status >=1 }
                                        <a class="button cur rpd0 ok-btn" data-orderid="{$item.os_id}" href="javascript:;">确认收货</a>
                                        {/if}
                                    </div>
                                </div>
                            </div><!--.my-order-->
                            {/foreach}
                            {else /}
                            <div class="my-order rmb15 bg_fff">
                                <div class="over rpd15 c_999 text-center">
                                    暂无订单!
                                </div><!--.over rpd15-->
                            </div>
                            {/notempty}


                        </div>
                        <!--第三个tab开始-->

                    </div>
                </div>
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
                        <a class="button solid_all order-ok yan-ok">确定</a>
                    </div>
                    <div class="col-50">
                        <a class="button button-fill yan-no-ok">取消</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--延迟收货弹框-->
{/block}
{block name='script'}
<script>
    var div = document.getElementById('d1');
    var div2 = document.getElementById('d2');
    $$(".alert-phone").click(function(){
        var phone = $$(this).data('phone');
        if(typeof (phone)!= 'undefined'){
            $$('.dial-phone').data('url', 'tel://' + phone);
            $$('.dial-phone').text(phone);
            $$(".phone-modal").show();
            div.className="modal-overlay-visible modal-overlay";
        }
    });
    $$('.dial-phone').click(function () {
        var url = $$(this).data('url');
        window.location.href = url;
    });
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