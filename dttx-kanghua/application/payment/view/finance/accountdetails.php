<!-- * User: lirong-->
<!-- * Date: 2017/7/1-->
<!-- * Time: 11:17-->
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('finance/accountdetails')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="ca_create_time">
        <input type="hidden" name="orderDirection" value="desc">
        <div class="bjui-searchBar">
            <label>用户ID：</label><input type="text" id="ipt_nick" value="{$input.ipt_nick|default=''}" name="ipt_nick" class="form-control" size="10">&nbsp;
            <label>异动编号：</label><input type="text" id="ipt_caid" value="{$input.ipt_caid|default=''}" name="ipt_caid" class="form-control" size="10">&nbsp;
            <label>账户编号：</label><input type="text" id="ipt_aid" value="{$input.ipt_aid|default=''}" name="ipt_aid" class="form-control" size="10">&nbsp;
            <label>订单编号：</label><input type="text" id="ipt_order_id" value="{$input.ipt_order_id|default=''}" name="ipt_order_id" class="form-control" size="10">&nbsp;
            <label>异动时间：</label>
            <input type="text"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" name="beginDate" value="{$input.beginDate||default=''}" size="18" data-rule="datetime">
            <label>至</label>
            <input type="text"  data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss"  name="endDate"  size="18" value="{$input.endDate|default=''}" data-rule="datetime">
            <label>状态</label>
            <select name="ipt_type" id="ipt_type" data-toggle="selectpicker">
                <option value="">请选择</option>
                <option {eq name="$input.ipt_type" value="1" }selected{/eq} value="1">转入</option>
                <option {eq name="$input.ipt_type" value="-1" }selected{/eq} value="-1">转出</option>
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
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
            </div>
        </div>


    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
        <tr>
            <th align="center">异动编码</th>
            <th align="center">订单编码</th>
            <th align="center">用户ID</th>
            <th align="center">账户ID</th>
<!--            <th align="center">账号类型</th>-->
            <th align="center" data-order-field="ad_money">交易金额</th>
            <th align="center" data-order-field="ad_type">出入状态</th>
            <th align="center">当前账号余额</th>
            <th align="center" data-order-field="ad_type">异动类型</th>
            <th width="140" data-order-field="ca_create_time">异动时间</th>
            <th width="180">备注</th>
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
            <td align="center">{$vo.ca_id}</td>
            <td align="center">{$vo.ca_order_id|default='--'}</td>
            <td align="center">{$vo.ca_unick}</td>
            <td align="center">{$vo.ca_aid}</td>
<!--            <td align="center">{eq name="$vo.a_payAccountCode" value="ERP_RECHARGE"}余额账户{else /} --- {/eq}</td>-->
            <td align="center">{$vo.ca_money}</td>
            <td align="center">{eq name="$vo.ca_type" value="-1"} <label class="label label-warning">转出</label> {else /} <label class="label label-success">转入</label> {/eq}</td>
            <td align="center">{$vo.ca_balance}</td>
            <td align="center">{$vo.banlance_type_text}</td>
            <td align="center">{$vo.ca_create_time}</td>
            <td title="{$vo.ca_memo}" align="center">{$vo.ca_memo}</td>
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
                <option value="15">15</option>
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