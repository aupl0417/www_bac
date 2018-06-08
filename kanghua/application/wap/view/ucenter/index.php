{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">
    <a href="{:url('store/index')}" class="back link external"><img src="__STATIC__/wap/images/home.png" width="20" alt="返回个人中心"></a>
</div><!--.right-->
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content">
            <div class="bg_fff rpl15 rpr15 rpt20 rpb20">
                <div class="over">
                    <div class="pull-left over">
                        <div class="pull-left">
                            <div class="user-icon-box bg_gray text-center over rpd10">
                                <img width="100%" src="{$member.u_logo}" alt="">
                            </div>
                        </div><!--.pull-left-->
                        <div class="pull-left rml15">
                            <div class="c_333 rfs18 rmt0 rmb10">{$member.u_nick}</div>
                            <div class="user_id">
                                <span class="rfs14 c_666 bg_gray over rmr5 rpr5">
                                    <span class="pull-left rpd5">
                                        <img width="20" src="__STATIC__/wap/images/vip.png" alt="">
                                    </span>
                                    <span class="pull-left c_333 rpt10 rfs14">
                                         {$member.ulname|default='普通会员'}
                                    </span>
                                </span>
                                <span class="rfs14 c_666 bg_gray over rpr5">
                                    <span class="pull-left rpd5">
                                        <img class="" width="20" src="__STATIC__/wap/images/agent.png" alt="">
                                    </span>
                                    <span class="pull-left c_333 rpt10 rfs14">
                                         {$member.cname|default='未代理'}
                                    </span>
                                </span>
                            </div>
                        </div><!--.pull-left-->
                    </div><!--.pull-left clearfix-->
                    <div class="pull-right">
                        <a class="rfs14 c_ccc external" href="{:url('user/profile')}">查看资料 <i class="fa fa-angle-right c_ccc rfs18"></i></a>
                    </div><!--.pull-left-->
                </div><!--.clearfix-->
            </div><!--.bg_fff rpl15 rpr15-->
            <div class="rmt15 rmb15 rpl15 rpr15">
                <div class="row">
                    <div class="col-33">
                        <a href="{:url('ucenter/inviteCode', ['code' => $member['u_code']])}" class="external">
                            <div class="bg_fff text-center rpt30 rpb30 bdr_3">
                                <div>
                                    <img src="__STATIC__/wap/images/ma.png" width="30" alt="">
                                </div>
                                <p class="rfs14 c_666 text-center rmb0 rmt5">邀请码</p>
                            </div>
                        </a>
                    </div><!--.col-33-->
                    <div class="col-33">
                        <a href="{:url('ucenter/inviter', ['code' => $member['u_code']])}" class="external">
                            <div class="bg_fff text-center rpt30 rpb30 bdr_3">
                                <div>
                                    <img src="__STATIC__/wap/images/my-user.png" width="30" alt="">
                                </div>
                                <p class="rfs14 c_666 text-center rmb0 rmt5">我的邀请会员</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-33">
                        <a href="{:url('ucenter/upgrade')}" class="external">
                            <div class="bg_fff text-center rpt30 rpb30 bdr_3">
                                <div>
                                    <img src="__STATIC__/wap/images/upgrade.png" width="30" alt="">
                                </div>
                                <p class="rfs14 c_666 text-center rmb0 rmt5">升级</p>
                            </div>
                        </a>
                    </div>
                </div><!--.row-->
            </div><!--.content-block-->
            {if condition='$accres.code eq 200'}
            <a href="{:url('ucenter/balance')}" class="external">
            {else/}
            <a href="#" class="">
            {/if}
                <div class="over bg_fff rpd15 balance-box">
                    <div class="pull-left over">
                        <span class="pull-left rmr10 s-mt">
                            <img width="16" src="__STATIC__/wap/images/balance.png" alt="">
                        </span>
                        <span class="pull-left rfs16" style="color: #666;">
                            余额
                        </span>
                    </div><!--.pull-left-->
                    <div class="pull-right">
                        {$accres.a_freeMoney|default='0.00'}
                        <i class="fa fa-angle-right c_ccc rfs18"></i>
                    </div><!--.pull-right-->
                </div><!--.over-->
            </a>
            <!--我的收益-->
            <div class="bg_fff rmt15">
                <div class="solid_b rpd15">
                    <span class="rfs16 c_333">我的收益</span>
                </div>
                <div class="row no-gutter rpd15">
                    <div class="col-33 text-center solid_r">
                        <a href="{:url('account/index')}" class="external">
                            <p class="rfs14 c_666">{$accres.a_totalMoney|default='0.00'}</p>
                            <p class="rfs14 c_666">累计收益</p>
                        </a>
                    </div>
                    <div class="col-33 text-center solid_r">
                        <a href="{:url('account/viplist')}" class="external">
                            <p class="rfs14 c_666">{$accres.a_vipMoney|default='0.00'}</p>
                            <p class="rfs14 c_666">累计推广收益</p>
                        </a>
                    </div>
                    <div class="col-33 text-center">
                        <a href="{:url('account/agentlist')}" class="external">
                            <p class="rfs14 c_666">{$accres.a_agentMoney|default='0.00'}</p>
                            <p class="rfs14 c_666">累计代理收益</p>
                        </a>
                    </div>
                </div><!--.row no-gutter-->
            </div><!--.bg_fff rmt15-->
            <!--我的收益-->
            <!--我的订单-->
            <div class="bg_fff rmt15">
                <div class="solid_b rpd15 over">
                    <span class="rfs16 c_333 pull-left">我的订单</span>
                    <span class="pull-right c_999">
                                <a class="c_999 rfs14 external" href="{:url('order/center')}" >
                                全部
                                    <i class="fa fa-angle-right c_ccc rfs18"></i>
                                </a>
                            </span>
                </div>
                <div class="row no-gutter rpd15 order-box">
                    <div class="col-33 text-center solid_r">
                        <a href="{:url('order/center',['type'=>'pay'])}" class="external">
                            <div class="re">
                                        <span>
                                            <img src="__STATIC__/wap/images/order-a.png" width="30" alt="">
                                        </span>
                                <span class="no">{gt name="$count.waitpay" value="99"}99{else /}{$count.waitpay}{/gt}</span>
                            </div>
                            <p class="rfs14 c_666">待付款</p>
                        </a>
                    </div>
                    <div class="col-33 text-center solid_r">
                        <a href="{:url('order/center',['type'=>'send'])}" class="external">
                            <div class="re">
                                        <span>
                                            <img src="__STATIC__/wap/images/order-b.png" width="30" alt="">
                                        </span>
                                <span class="no">{gt name="$count.waitsend" value="99"}99{else /}{$count.waitsend}{/gt}</span>
                            </div>
                            <p class="rfs14 c_666">待发货</p>
                        </a>
                    </div>
                    <div class="col-33 text-center">
                        <a href="{:url('order/center',['type'=>'receive'])}" class="external">
                            <div class="re">
                                        <span>
                                            <img src="__STATIC__/wap/images/order-c.png" width="30" alt="">
                                        </span>
                                <span class="no">{gt name="$count.waitreceive" value="99"}99{else /}{$count.waitreceive}{/gt}</span>
                            </div>
                            <p class="rfs14 c_666">待收货</p>
                        </a>
                    </div>
                </div><!--.row no-gutter-->
            </div><!--.bg_fff rmt15-->
            <!--我的订单-->
            {if $member.up_roleid ==3 }
            <!--我的经销商产品-->
            {notempty name='list'}
            <div class="bg_fff rmt15">
                <div class="solid_b rpd15 over">
                    <span class="rfs16 c_333 pull-left">我的经销商产品</span>
                </div>
                {foreach $list as $vo}
                <a href="{:url('goods/detail', ['id' => $vo.id])}" class="external">
                    <div class="rpd15 row no-gutter">
                        <div class="col-30">
                            <img src="{$vo.image}" width="100%" alt="{$vo.name}">
                        </div>
                        <div class="col-70 rpl15">
                            <div class="rfs16 rmb10 c_333">{$vo.name}</div>
                            <div class="rfs14 rmb10 c_red">&yen;{$vo.price}</div>
                            <div class="rfs14 rmb10 c_999">奖励积分：{$vo.score}</div>
                        </div>
                    </div>
                </a>
                {/foreach}
            </div><!--.bg_fff rmt15-->
            {/notempty}
            <!--我的经销商产品-->
            <!--我的经销商订单-->
            <div class="bg_fff rmt15">
                <div class="solid_b rpd15 over">
                    <span class="rfs16 c_333 pull-left">我的经销商订单</span>
                    <span class="pull-right">
                                <a class="c_999 rfs14 external" href="{:url('order/shopcenter')}">
                                全部
                                <i class="fa fa-angle-right c_ccc rfs18"></i>
                                </a>
                            </span>
                </div>
                <div class="row no-gutter rpd15 order-box">
                    <div class="col-33 text-center solid_r">
                        <a href="{:url('order/shopcenter',['type'=>'pay'])}" class="external">
                            <div class="re">
                                        <span>
                                            <img src="__STATIC__/wap/images/order-a.png" width="30" alt="">
                                        </span>
                                <span class="no">{gt name="$sellercount.waitpay" value="99"}99{else /}{$sellercount.waitpay}{/gt}</span>
                            </div>
                            <p class="rfs14 c_666">待付款</p>
                        </a>
                    </div>
                    <div class="col-33 text-center solid_r">
                        <a href="{:url('order/shopcenter',['type'=>'send'])}" class="external">
                            <div class="re">
                                        <span>
                                            <img src="__STATIC__/wap/images/order-b.png" width="30" alt="">
                                        </span>
                                <span class="no">{gt name="$sellercount.waitsend" value="99"}99{else /}{$sellercount.waitsend}{/gt}</span>
                            </div>
                            <p class="rfs14 c_666">待发货</p>
                        </a>
                    </div>
                    <div class="col-33 text-center">
                        <a href="{:url('order/shopcenter',['type'=>'receive'])}" class="external">
                            <div class="re">
                                        <span>
                                            <img src="__STATIC__/wap/images/order-c.png" width="30" alt="">
                                        </span>
                                <span class="no">{gt name="$sellercount.waitreceive" value="99"}99{else /}{$sellercount.waitreceive}{/gt}</span>
                            </div>
                            <p class="rfs14 c_666">待收货</p>
                        </a>
                    </div>
                </div><!--.row no-gutter-->
            </div><!--.bg_fff rmt15-->
            <!--我的经销商订单-->
            {else/}
            <!--项目商品-->
            {notempty name='goods'}
            <div class="bg_fff rmt15">
                <div class="solid_b rpd15 over">
                    <span class="rfs16 c_333 pull-left">项目商品</span>
                </div>
                <a href="{:url('goods/detail', ['id' => $goods.id])}" class="external">
                    <div class="rpd15 row no-gutter">
                        <div class="col-30">
                            <img src="{$goods.image}" width="100%" alt="{$goods.name}">
                        </div>
                        <div class="col-70 rpl15">
                            <div class="rfs16 rmb10 c_333">{$goods.name}</div>
                            <div class="rfs14 rmb10 c_red">&yen;{$goods.price}</div>
                            <div class="rfs14 rmb10 c_999">奖励积分：{$goods.score}</div>
                        </div>
                    </div>
                </a>
            </div><!--.bg_fff rmt15-->
            {/notempty}
            <!--项目商品-->
            {/if}
        </div><!--.page-content-->
    </div><!--.page-->
</div>
{/block}
{block name='script'}

{/block}