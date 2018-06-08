{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="javascript:window.history.go(-1);" class="back link"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='header'}

{/block}
{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f" style="padding-bottom: 1.449rem">
            <div class="rpd10 bg-white rmb10">
                <div class="media">
                    <div class="media-left">
                        <div style="width: 15px;padding-top: 8px;padding-left: 5px;" class="">
                            <i class="fa fa-map-marker c_blue rfs18"></i>
                        </div>
                    </div>
                    {notempty name="address"}
                    <div class="media-body address_manage">
                        <div class="rfs16 rmb10 c_333">{$address.ad_userNick}   {$address.ad_phone}</div>
                        <div class="rfs14 c_666">{$address.province}{$address.city}{$address.region}{$address.ad_address}</div>
                        <input type="hidden" name="addressId" value="{$address.ad_id}">
                    </div>
                    {else/}
                    <div class="media-body">
                        <a href="{:url('Address/create')}" class="external" id="addaddress">
                        <div class="rfs16 rpb10 rpt10 c_333">添加收货地址 ></div></a>
                    </div>
                    {/notempty}
                </div>
            </div>
            <div class="rpd15 solid_b bg-white">
                <div class="media">
                    <div class="media-left">
                        <div style="width: 50px" class="">
                            <img src="{$order.bg_image}" width="50" class="bor_img">
                        </div>
                    </div>
                    <div class="media-body">
                        <div class="rfs14 rmb10 c_333">{$order.bg_name}</div>
                        <div class="rfs16 c_666">￥{$order.bg_price} <span class="rml10 c_999">x{$number}</span> <div class="pull-right button button-raised my-btn-default">{$order.bg_model}</div></div>
                    </div>
                </div>
            </div>
            <div class="rpd15 bg-white">
                <div class="rfs16 c_333 rmb5">奖励：<span class="c_cc0">{$order.bg_price*$order.bg_scoreReward*$number}</span>（积分）</div>
                <div class="rfs14 c_333 rmb5">积分说明：<span class="c_999">大唐天下积分，将在确认收货后10个工作日内到账
</span></div>
            </div>
            <div class="rpd15 c_999 rfs12 rpt10">
                该项目暂不支持退款退货服务,如有疑问请咨询 : <span class="c_blue">{$platformdata.pl_contact}</span>
            </div>
        </div>
    </div>
</div>
{/block}
{block name='footer'}
<form action="{:url('order/generate')}" method="post" id="generateorder">
    <input type="hidden" name="number" value="{$number}">
    <input type="hidden" name="addressid" value="{$address.ad_id}">
    <input type="hidden" name="shopid" value="{$order.si_id}">
<div class="shop-bar">
<div class="over">
    <div class="text-center pull-left c_333 rline60 rpl15 rfs16">
        金额：￥<span class="c_cc0">{$order.bg_price*$number}</span>
    </div>
    <a href="#" id="createorder" data-picker=".picker-2" class="pull-right c_333 shop-bar-buybtn rfs16 open-picker">提交订单</a>
</div>
</div>
</form>
{/block}
{block name='script'}
<script>

    
    $$('.address_manage').on('click', function () {
        window.location.href = "{:url('Address/index',['go'=>'createorder'])}"
    });

    $$('#createorder').once('click',function () {
        var numberval =$$("input[name='number']").val();
        var addressidval =$$("input[name='addressid']").val();
        var shopidval =$$("input[name='shopid']").val();

        $$.ajax({
            type: "POST",
            url: "{:url('order/generate')}",
            data:{number:numberval,addressid:addressidval,shopid:shopidval},
            dataType: "json",
            beforeSend:function () {
                $$('#createorder').addClass('disabled');
            },
            success: function(data){
                if(data.statusCode == 300){
                    myApp.alert(data.message, '提示');
                    return false;
                }else if(data.statusCode == 301){
                    myApp.alert(data.message.msg, '提示', function () {
                        window.location.href = data.message.url;
                    });
                }else{
                    window.location.href = data.closeCurrent;
                }
            },
            complete:function () {
                $$('#createorder').removeClass('disabled');
            }
        });

    })


//    $$('.open-picker').on('click', function () {
//        $$(this).attr('disabled', true);
//        var addressId = $$("input[name='addressId']").val();
//        var url       = $$(this).data('url');
//        url = url + '?aid=' + addressId;
//
//        $$.ajax({
//            type: "get",
//            url: url,
//            dataType: "json",
//            success: function(data){
//                if(data.statusCode == 300){
//                    alertLayout(data.message, '提示');return false;
//                }else{
//                    window.location.href = url + '/orderId/' + data.message;
//                }
//            }
//        });
//    });

</script>
{/block}