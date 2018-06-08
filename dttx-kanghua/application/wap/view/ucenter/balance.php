{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/index')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">

</div>
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content bg_gray">
            <div class="text-center bg_fff  rpb15 rpt15 gold-box">
                <div class="text-center">
                    <img src="__STATIC__/wap/images/gold-icon.png" alt="">
                </div>
                <p class="rfs18 c_blue text-center rmg0">{$balance}</p>
                <p class="rfs14 c_999 rmt5">余额(元)</p>
                <div class="over rpt5 rpb5 cz-box">
                    <a class="button rfs14 c_999 withdraw" {if condition="$isSatisfy neq 1"}disabled="disabled"{/if} data-url="{:url('ucenter/withdraw')}">提现</a>
                </div>
            </div>
            {if condition='$isSetPayPwd eq 0'}
            <div class="solid_t rpd15 bg_fff">
                <a href="{:url('user/setPayPwd')}" class="external">
                    <div class="over">
                        <div class="pull-left rfs14 c_red">未设置提现密码</div>
                        <div class="pull-right rfs14 c_red">去设置<i class="fa fa-angle-right c_ccc rfs18 rml5"></i></div>
                    </div>
                </a>
            </div>
            {/if}
            {if condition='$isAuth eq 0'}
            <div class="solid_t rpd15 bg_fff">
                <a href="#" class="auth">
                    <div class="over">
                        <div class="pull-left rfs14 c_red">未实名制</div>
                        <div class="pull-right rfs14 c_red">去实名<i class="fa fa-angle-right c_ccc rfs18 rml5"></i></div>
                    </div>
                </a>
            </div>
            {/if}
            {if condition='$bankCount eq 0'}
            <div class="solid_t rpd15 bg_fff rmb15">
                <a href="{:url('Bank/create')}" class="external">
                    <div class="over">
                        <div class="pull-left rfs14 c_red">未绑定银行卡</div>
                        <div class="pull-right rfs14 c_red">去绑定<i class="fa fa-angle-right c_ccc rfs18 rml5"></i></div>
                    </div>
                </a>
            </div>
            {/if}
            <div>
                <div class="toolbar gold-toolbar tabbar bg-white after-bor-none solid_b " style="top: 0;">
                    <div class="toolbar-inner">
                        <a href="#tab-1" class="tab-link rml25 rmr25 active">转入</a>
                        <a href="#tab-2" class="tab-link rml25 rmr25 ">转出</a>
                    </div>
                </div>
                <div class="page-content" style="padding:0;height:auto">
                    <div class="tabs news-cont">
                        <!--第一个tab开始-->
                        <div id="tab-1" class="tab active bg_fff">
                            {foreach $dataIn as $vo}
                            <div class="list-block media-list mg0 rpd15 rmg0 solid_b">
                                <a href="{:url('ucenter/balanceDetail', ['id' => $vo.id])}" class="item-link item-content rpl0 bg_fff external">
                                <div class="over">
                                    <div class="pull-left rfs14 c_333">
                                        {$vo.ca_balance_type}
                                    </div><!--.pull-left-->
                                    <div class="pull-right rfs14 c_blue">
                                        +{$vo.money}
                                    </div><!--.pull-left-->
                                </div><!--.clearfix-->
                                <p class="rfs14 c_999 rmb0">{$vo.ca_create_time}</p>
                                </a>
                            </div>
                            {/foreach}
                        </div>
                        <!--第一个tab结束-->

                        <!--第二个tab开始-->
                        <div id="tab-2" class="tab bg_fff">
                            {foreach $dataOut as $vo}
                            <div class="list-block media-list mg0 rpd15 rmg0 solid_b">
                                <a href="{:url('ucenter/balanceDetail', ['id' => $vo.id])}" class="item-link item-content rpl0 bg_fff external">
                                <div class="over">
                                    <div class="pull-left rfs14 c_333">
                                        {$vo.ca_balance_type}
                                    </div><!--.pull-left-->
                                    <div class="pull-right rfs14 c_blue">
                                        -{$vo.money}
                                    </div><!--.pull-left-->
                                </div><!--.clearfix-->
                                <p class="rfs14 c_999 rmb0">{$vo.ca_create_time}</p>
                                </a>
                            </div>
                            {/foreach}
                        </div>
                        <!--第二个tab开始-->
                    </div>
                </div>
            </div>
        </div><!--.page-content-->
    </div><!--.page-->
</div><!--.pages navbar-fixed-->
{/block}
{block name='script'}
<script>
    $$('.withdraw').on('click', function () {
        var isAuth = "{$isAuth}";
        var url    = $$(this).data('url');
        if(parseInt(isAuth) == 0){
            alertLayout('请在大唐天下app实名认证后，重新登录');return false;
        }else {
            window.location.href = url;
        }
    });

    $$('.auth').on('click', function () {
        alertLayout('请在大唐天下app实名认证后，重新登录');return false;
    });

</script>
{/block}