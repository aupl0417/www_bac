<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="ap_create_time">
        <input type="hidden" name="orderDirection" value="desc">
        <div class="bjui-searchBar">
            <label>用户ID：</label><input type="text" id="ipt_nick" value="{$input.ipt_nick|default=''}" name="ipt_nick" class="form-control" size="10">&nbsp;
            <label>订单ID：</label><input type="text" id="ipt_order_id" value="{$input.ipt_order_id|default=''}" name="ipt_order_id" class="form-control" size="10">&nbsp;

            <label>支付金额：</label>
            <input type="text" id="ipt_moneyMin" data-rule="number" value="{$input.ipt_moneyMin|default=''}" placeholder="从" name="ipt_moneyMin" class="form-control" size="8">&nbsp;
            <input type="text" id="ipt_moneyMax" value="{$input.ipt_moneyMax|default=''}" placeholder="到" name="ipt_moneyMax" class="form-control" data-rule="number" size="8">&nbsp;

            <label>支付方式</label>
            <select name="ipt_payType" id="ipt_payType" data-toggle="selectpicker">
                <option value="">请选择</option>
                <option {eq name="$input.ipt_payType" value="1"}selected{/eq}  value="1">余额</option>
                <option {eq name="$input.ipt_payType" value="2"}selected{/eq}  value="2">唐宝</option>
            </select>
            <label>状态</label>
            <select name="ipt_state" id="ipt_state" data-toggle="selectpicker">
                <option value="">全部状态</option>
                {foreach name="input.stateList" item="vo" key="k" }
                <option {if $input.ipt_state==$k}selected{/if} value="{$k}">{$vo}</option>
                {/foreach}
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
            <label>创建时间：</label>
            <input type="text"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" name="apply_beginDate" value="{$input.apply_beginDate||default=''}" size="18" data-rule="datetime">
            <label>至</label>
            <input type="text"  data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss"  name="apply_endDate"  size="18" value="{$input.apply_endDate|default=''}" data-rule="datetime">

            <label>支付时间：</label>
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
<!--            <th align="center">用户ID</th>-->

            <th align="center">支付流水号</th>
            <th align="center">订单流水号</th>
            <th align="center" data-order-field="ap_pay_time">支付时间</th>
            <th align="center">付款人大唐账号</th>
            <th align="center">付款人姓名</th>
            <th align="center" width="120">订单总额</th>
            <th align="center">代购手续费</th>
            <th align="center">订单总金额</th>
            <th align="center">支付方式</th>
            <th align="center" data-order-field="ap_create_time">创建时间</th>
            <th align="center">状态</th>
        </tr>
        </thead>
        <tbody>
        {empty name="data['list']"}
        <tr>
            <td colspan="11" align="center">暂无数据！</td>
        </tr>
        {else /}
        {volist name="data['list']" id='vo'}
        <tr>
<!--            <td align="center">{$vo.ap_pay_unick}</td>-->

            <td align="center">{$vo.ap_pay_order_id}</td>
            <td align="center">{$vo.ap_shop_order_id}</td>
            <td align="center">{$vo.ap_pay_time}</td>
            <td align="center">{$vo.ap_pay_unick|default=''} </td>
            <td align="center">{$vo.u_name|default=''} </td>
            <td align="center">{$vo.ap_order_amount} </td>
            <td align="center">{$vo.ap_agent_amount} </td>
            <td align="center">{$vo.ap_total_money} </td>
            <td align="center">{$vo.ap_only_pay} </td>
            <td align="center">{$vo.ap_create_time|date='Y-m-d H:i:s', ###}</td>
            <td align="center">{$vo.ap_state} </td>
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