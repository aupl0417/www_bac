{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/withdraw')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">

</div>
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content">
            {foreach $data as $vo}
            <div class="list-box rpd15 bg_fff solid_b">
                <a href="{:url('ucenter/withdrawDetail', ['id' => $vo.id])}" class="item-link item-content rpl0 bg_fff external">
                <div class="over">
                    <div class="pull-left rfs16 c_333">余额提现</div>
                    <div class="pull-right rfs16 c_333">{$vo.money}</div>
                </div>
                <div class="over rmt10">
                    <div class="pull-left rfs14 c_999">{$vo.co_arriveDateTime}</div>
                    <div class="pull-right rfs14 text-yellow">{$state[$vo.co_state]}</div>
                </div>
                </a>
            </div><!--.list-->
            {/foreach}
        </div><!--.page-content-->
    </div><!--.page-->
</div><!--.pages navbar-fixed-->
{/block}
{block name='script'}
<script>


</script>
{/block}