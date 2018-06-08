<!-- * User: lirong-->
<!-- * Date: 2017/7/1-->
<!-- * Time: 11:17-->
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('finance/accountcashin')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="ci_createTime">
        <input type="hidden" name="orderDirection" value="desc">
        <div class="bjui-searchBar">
            <label>用户ID：</label><input type="text" id="ipt_nick" value="{$input.ipt_nick|default=''}" name="ipt_nick" class="form-control" size="10">&nbsp;
            <label>商户订单号：</label><input type="text" id="ipt_caid" value="{$input.ipt_caid|default=''}" name="ipt_caid" class="form-control" size="22">&nbsp;
            <label>金额：</label>
            <input type="text" id="ipt_moneyMin" data-rule="number" value="{$input.ipt_moneyMin|default=''}" placeholder="从" name="ipt_moneyMin" class="form-control" size="8">&nbsp;
            <input type="text" id="ipt_moneyMax" value="{$input.ipt_moneyMax|default=''}" placeholder="到" name="ipt_moneyMax" class="form-control" data-rule="number" size="8">&nbsp;

            <label>充值方式</label>
            <select name="ipt_payType" id="ipt_payType" data-toggle="selectpicker">
                <option value="">请选择</option>
                {notempty name="paydata"}
                    {foreach name="paydata" item="vo"}
                    <option {eq name="$input.ipt_payType" value="$vo.pb_paytype"}selected{/eq}  value="{$vo.pb_paytype}">{$vo.pb_name}</option>
                    {/foreach}
                {/notempty}
            </select>
            <label>状态</label>
            <select name="ipt_state" id="ipt_state" data-toggle="selectpicker">
                <option value="">全部状态</option>
                <option {eq name="$input.ipt_state" value="0"}selected{/eq} value="0">未结算</option>
                <option {eq name="$input.ipt_state" value="1"}selected{/eq} value="1">已结算</option>
                <option {eq name="$input.ipt_payType" value="-1"}selected{/eq} value="-1">撤销</option>
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
            <label>提交时间：</label>
            <input type="text"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" name="apply_beginDate" value="{$input.apply_beginDate||default=''}" size="18" data-rule="datetime">
            <label>至</label>
            <input type="text"  data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss"  name="apply_endDate"  size="18" value="{$input.apply_endDate|default=''}" data-rule="datetime">

            <label>到账时间：</label>
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
            <th align="center">用户ID</th>
            <th align="center">提交时间</th>
            <th align="center">到账时间</th>
            <th align="center">商户订单号</th>
            <th align="center">第三方流水号</th>
            <th align="center">第三方账号</th>
            <th align="center">金额</th>
            <th align="center">充值方式</th>
            <th align="center" width="140">状态</th>
            <th align="center">备注</th>
<!--            <th align="center">操作</th>-->

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
            <td align="center">{$vo.ci_unick}</td>
            <td align="center">{$vo.ci_createTime}</td>
            <td align="center">{$vo.ci_successTime}</td>
            <td align="center">{$vo.ci_caid}</td>
            <td align="center">{$vo.ci_thirdOrderId}</td>
            <td align="center">{$vo.ci_thirdAccount}</td>
            <td align="center">{$vo.ci_money} </td>
            <td align="center">{$vo.pb_name} </td>
            <td align="center">{$vo.state_text} </td>
            <td align="center">{$vo.ci_memo} </td>
<!--            <td align="center"><a href="" class="btn btn-green" data-toggle="doajax" data-confirm-msg="确认设置为已到账">设置为已到账</a></td>-->

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