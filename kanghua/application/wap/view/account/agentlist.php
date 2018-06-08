{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/index')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">
    <a href="#" data-popover=".popover-links" class="link open-popover rfs14 c_fff">本月<i class="fa fa-angle-down rml5"></i></a>
</div><!--.right-->
{/block}

{block name="content"}
<!--导航弹出框-->
<div class="popover popover-links modal-in mx-pop">
    <div class="popover-angle on-top" style="left: 161px;"></div>
    <div class="popover-inner">
        <div class="list-block bg_fff">
            <div class="solid_b"><a href="{:url('account/agentlist',['m'=>1])}" class="list-button item-link rfs14 external">本月</a></div>
            <div class="solid_b"><a href="{:url('account/agentlist',['m'=>2])}" class="list-button item-link rfs14 external">上月</a></div>
            <div class="solid_b"><a href="{:url('account/agentlist',['m'=>3])}" class="list-button item-link rfs14 external">近三个月</a></div>
        </div>
    </div>
</div>
<!--导航弹出框-->
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content bg_gray">
            {notempty name='list'}
            {foreach name='list' item='vo'}
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">代理收益-订单<span class="c_999">({$vo.ad_order_id})</span></div>
                </div>
                <div class="over rmt10">
                    <div class="pull-left rfs14 c_999">{$vo.ad_create_time|date="Y-m-d H:i:s",###}</div>
                    <div class="pull-right rfs14 c_blue">{$vo.ad_money}</div>
                </div>
            </div><!--.list-->
            {/foreach}
            {else /}
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="rfs16 c_999 text-center">暂无收益记录</div>
                </div>
            </div><!--.list-->
            {/notempty}
        </div><!--.page-content-->
    </div><!--.page-->
</div>


{/block}
{block name="footer"}

{/block}
