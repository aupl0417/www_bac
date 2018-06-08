{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">

</div>
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content bg_gray">
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">异动编号</div>
                    <div class="pull-right rfs16 c_999">{$data.id}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">提现金额</div>
                    <div class="pull-right rfs16 c_blue">{$data.money}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">提现时间</div>
                    <div class="pull-right rfs16 c_999">{$data.co_arriveDateTime}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">提现状态</div>
                    <div class="pull-right rfs16 text-yellow">{$state[$data.co_state]}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">预计到账时间</div>
                    <div class="pull-right rfs16 c_999">{$data.expectTime}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">结算时间</div>
                    <div class="pull-right rfs16 c_999">{$data.co_day_time}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">手续费</div>
                    <div class="pull-right rfs16 c_999">{$data.fee}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">银行卡</div>
                    <div class="pull-right rfs16 c_999">{$data.co_bankName} {$data.co_account}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">备注</div>
                    <div class="pull-right rfs16 c_999">{$data.co_memo|default='暂无'}</div>
                </div>
            </div><!--.list-->
            <div class="list-box rpd15 bg_fff solid_b">
                <div class="over">
                    <div class="pull-left rfs16 c_333">拒绝原因</div>
                    <div class="pull-right rfs16 c_333">{$data.co_reason}</div>
                </div>
            </div><!--.list-->
        </div><!--.page-content-->
    </div><!--.page-->
</div><!--.pages navbar-fixed-->
{/block}
{block name='script'}
<script>

    $$('.left').on('click', function () {
        window.history.back();
    });

</script>
{/block}