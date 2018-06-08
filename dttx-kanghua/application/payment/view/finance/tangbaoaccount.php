<!-- * User: lirong-->
<!-- * Date: 2017/7/1-->
<!-- * Time: 11:17-->
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('finance/tangbaoaccount')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="at_create_time">
        <input type="hidden" name="orderDirection" value="desc">
        <div class="bjui-searchBar">
            <label>用户ID：</label><input type="text" id="ipt_nick" value="{$input.ipt_nick|default=''}" name="ipt_nick" class="form-control" size="10">&nbsp;
            <label>异动编号：</label><input type="text" id="ipt_atid" value="{$input.ipt_atid|default=''}" name="ipt_atid" class="form-control" size="20">&nbsp;
            <label>订单编号：</label><input type="text" id="ipt_order_id" value="{$input.ipt_order_id|default=''}" name="ipt_order_id" class="form-control" size="20">&nbsp;
            <label>金额：</label>
            <input type="text" id="ipt_moneyMin" data-rule="number" value="{$input.ipt_moneyMin|default=''}" placeholder="从" name="ipt_moneyMin" class="form-control" size="10">&nbsp;
            <input type="text" id="ipt_moneyMax" value="{$input.ipt_moneyMax|default=''}" placeholder="到" name="ipt_moneyMax" class="form-control" data-rule="number" size="10">&nbsp;
            <label>结算状态</label>
            <select name="ipt_ischecked" id="ipt_ischecked" data-toggle="selectpicker">
                <option {eq name="$input.ipt_ischecked" value=""}selected{/eq} value="">请选择</option>
                <option {eq name="$input.ipt_ischecked" value="0"}selected{/eq} value="0">未对账</option>
                <option {eq name="$input.ipt_ischecked" value="1"}selected{/eq} value="1">已对账</option>
            </select>
            {if $admin_state}
            {notempty name="platformdata"}
            <label>项目选择:</label>
            <select name="platformId" data-toggle="selectpicker" data-rule="required">
                <option value="">请选择项目</option>
                {foreach name="platformdata" item="vo"}
                <option {eq name="$input.platformId" value="$vo.pl_id"}selected{/eq} value="{$vo.pl_id}">{$vo.pl_name}</option>
                {/foreach}
            </select>&nbsp;
            {/notempty}
            {/if}
            <label>更多</label>
            <button type="button" class="showMoreSearch" data-toggle="moresearch" data-name="custom2"><i class="fa fa-angle-double-down"></i></button>
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
            </div>
        </div>
        <div class="bjui-moreSearch">
            <label>付款时间：</label>
            <input type="text"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" name="pay_beginDate" value="{$input.pay_beginDate||default=''}" size="18" data-rule="datetime">
            <label>至</label>
            <input type="text"  data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss"  name="pay_endDate"  size="18" value="{$input.pay_endDate|default=''}" data-rule="datetime">
            <label>结算时间：</label>
            <input type="text"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" name="reach_beginDate" value="{$input.reach_beginDate||default=''}" size="18" data-rule="datetime">
            <label>至</label>
            <input type="text"  data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss"  name="reach_endDate"  size="18" value="{$input.reach_endDate|default=''}" data-rule="datetime">
        </div>

    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
        <tr>
            <th align="center">异动编号</th>
            <th align="center">订单编号</th>
            <th align="center">买家</th>
            <th align="center">金额</th>
            <th align="center">支付唐宝数</th>
            <th align="center">订单状态</th>
            <th align="center">付款时间</th>
            <th align="center">结算时间</th>
            <th align="center" width="140">结算状态</th>
            <th align="center" width="140">操作</th>
        </tr>
        </thead>
        <tbody>
        {empty name="data['list']"}
        <tr>
            <td colspan="10" align="center">暂无数据！</td>
        </tr>
        {else /}
        {volist name="data['list']" id='vo'}
        <tr>
            <td align="center">{$vo.at_id}</td>
            <td align="center">{$vo.at_order_id}</td>
            <td align="center">{$vo.at_buy_nick}</td>
            <td align="center">{$vo.at_money}</td>
            <td align="center">{$vo.at_paytangbao}</td>
            <td align="center">{$vo.ordertext}</td>
            <td align="center">{$vo.at_paytime} </td>
            <td align="center">{$vo.at_finshtime} </td>
            <td align="center">{eq name="$vo.at_is_checked" value="0"} <label class="label label-default">未对账</label>  {else /}
                <label class="label label-success">已对账</label>  {/eq} </td>
            <td align="center">
                {eq name="$vo.os_status" value="3"}
                    {eq name="$vo.at_is_checked" value="0"}
                    <a href="{:url('finance/taobaoconfirm')}" data-toggle="doajax"  data-confirm-msg="确定修改本条记录为已对账状态吗？" class="btn btn-red" data-type="post" data-data="id={$vo.at_id}">确认对账</a>
                    {else /}
                    <button class="btn btn-default" disabled>订单已对账</button>
                    {/eq}
                {else /}
                <button class="btn btn-default" disabled>订单未完成</button>
                {/eq}
            </td>
        </tr>
        {/volist}
        {/empty}
        </tbody>
    </table>
</div>
<div class="bjui-pageFooter">
    <div class="pages">
        <span>每页&nbsp;</span>
        <div class="selectPagesize">
            <select data-toggle="selectpicker" data-toggle-change="changepagesize">
                <option value="30">30</option>
                <option value="60">60</option>
                <option value="120">120</option>
                <option value="150">150</option>
            </select>
        </div>
        <span>&nbsp;条，共 {$data.count} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$data.count}" data-page-size="{$input.pageSize}" data-page-current="{$input.pageCurrent}"></div>
</div>