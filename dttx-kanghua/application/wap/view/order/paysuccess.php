{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/index')}" class="back link"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='header'}
{/block}
{block name='content'}
<div class="pages">
    <div data-page="paysuccess" class="page">
        <div class="page-content bg-white">
            <div class="pay-success-cont">
                <div class="pay-success-icon text-center">
                    <img src="__STATIC__/wap/img/pay-success-icon.png" alt="">
                </div>
                <div class="pay-success-text rpd15 rfs16 rline24">
                    <span class="text-center">订单：{$orderid}</span>
                    <br>
                    恭喜你，<span class="c_cc0">支付成功！</span>可在“个人中心-我的订单”中查看订单<span class="c_blue">3秒</span>后自动跳转“个人主页”...
                </div>
                <div class="rmt20 rpd10">
                    <a href="{:url('center/index')}" class="button my-btn-blue button-raised rh40 rfs16 rline40 external">
                        马上跳转
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
{/block}
{block name='script'}
<script>
    setTimeout(function () {
        window.location.href = "{:url('Ucenter/index')}";
    }, 3000);
</script>
{/block}