{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/balance')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">

</div>
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content bg_gray">
            <div class="over bg_fff rpd15 rpr15 solid_b">
                <div class="pull-left c_333 rfs16">
                    异动编号
                </div>
                <div class="pull-right c_999 rfs16">
                    {$data.id}
                </div>
            </div><!--over bg_fff rpl15 rpr15-->
            <div class="over bg_fff rpd15 rpr15 solid_b">
                <div class="pull-left c_333 rfs16">
                    金额
                </div>
                <div class="pull-right c_blue rfs16">
                    {if condition='$data.ca_type eq -1'}-{else/}+{/if}{$data.money}
                </div>
            </div><!--over bg_fff rpl15 rpr15-->
            <div class="over bg_fff rpd15 rpr15 solid_b">
                <div class="pull-left c_333 rfs16">
                    异动时间
                </div>
                <div class="pull-right c_999 rfs16">
                    {$data.ca_create_time}
                </div>
            </div><!--over bg_fff rpl15 rpr15-->
            <div class="over bg_fff rpd15 rpr15 solid_b">
                <div class="pull-left c_333 rfs16">
                    转向
                </div>
                <div class="pull-right c_666 rfs16">
                    {if condition='$data.ca_type eq -1'}转出{else/}转入{/if}
                </div>
            </div><!--over bg_fff rpl15 rpr15-->
            <div class="over bg_fff rpd15 rpr15 solid_b">
                <div class="pull-left c_333 rfs16">
                    类型
                </div>
                <div class="pull-right c_666 rfs16">
                    {$balanceType[$data.ca_balance_type]}
                </div>
            </div><!--over bg_fff rpl15 rpr15-->
            <div class="over bg_fff rpd15 rpr15 solid_b">
                <div class="pull-left c_333 rfs16">
                    来源订单号
                </div>
                <div class="pull-right c_999 rfs16">
                    {$data.ca_order_id}
                </div>
            </div><!--over bg_fff rpl15 rpr15-->
            <div class="over bg_fff rpd15 rpr15 solid_b rpb40">
                <div class="pull-left c_333 rfs16">
                    备注
                </div>
                <div class="pull-right c_ccc rfs16">
                    {$data.ca_memo}
                </div>
            </div><!--over bg_fff rpl15 rpr15-->
        </div><!--.page-content-->
    </div><!--.page-->
</div><!--.pages navbar-fixed-->
{/block}
{block name='script'}
<script>


</script>
{/block}