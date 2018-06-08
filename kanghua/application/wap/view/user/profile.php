{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/index')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f" style="padding-bottom: 1.449rem">
            <div class=" solid_last rmb10 solid_b">
                <div class="solid_b bg_fff">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            会员名
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-right">
                            {$user.nickname}
                        </div>
                    </div>
                </div>
                <div class="solid_b bg_fff">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            姓名
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-right">
                            {$user.username}
                        </div>
                    </div>
                </div>
                <!--<div class="solid_b">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            性别
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-right">
                            男
                        </div>
                    </div>
                </div>-->
<!--                <div class="solid_b list-block rmg0 lizx-block bg_fff">-->
<!--                    <div class="item-link smart-select external">-->
<!--                        <div class="item-content">-->
<!--                            <div class="item-inner rpt10 rpb10">-->
<!--                                <div class="item-title c_333 rfs16">手机号</div>-->
<!--                                <div class="item-after rfs14">{$user.tel}</div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
                <div class="solid_b bg_fff">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            手机号
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-right">
                            {$user.tel}
                        </div>
                    </div>
                </div>
                <div class="solid_b bg_fff">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            所属区域
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-right rfs14">
                            {$user.province}-{$user.city}
                        </div>
                    </div>
                </div>
                <div class="solid_b bg_fff">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            推荐人
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-right rfs14">
                            {$user.recommendNick}
                        </div>
                    </div>
                </div>
                <div class="solid_b bg_fff">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            实名制情况
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-right {if condition='$isAuth eq 1'}c_blue{else/}c_red{/if}">
                            {if condition='$isAuth eq 1'}
                                已实名认证
                            {else/}
                                未实名认证
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg_fff solid_last rmb10 solid_b bg_fff">
                <div class="solid_b list-block rmg0 lizx-block">
                    <a href="{:url('User/setPayPwd')}" class="item-link smart-select external">
                        <div class="item-content">
                            <div class="item-inner rpt10 rpb10">
                                <div class="item-title c_333 rfs16">提现密码管理</div>
                                <div class="item-after"></div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="solid_b list-block rmg0 lizx-block bg_fff">
                    <a href="{:url('Bank/index')}" class="item-link smart-select external">
                        <div class="item-content">
                            <div class="item-inner rpt10 rpb10">
                                <div class="item-title c_333 rfs16">银行卡管理</div>
                                <div class="item-after"></div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="solid_b list-block rmg0 lizx-block bg_fff">
                    <a href="{:url('Address/index')}" class="item-link smart-select external">
                        <div class="item-content">
                            <div class="item-inner rpt10 rpb10">
                                <div class="item-title c_333 rfs16">收货地址管理</div>
                                <div class="item-after"></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="rmt10 rpd10">
                <a href="{:url('login/logout')}" class="button my-btn-red button-raised rline50 rfs16 rh50 external">退出登录</a>
            </div>
        </div>

    </div>
</div>
{/block}