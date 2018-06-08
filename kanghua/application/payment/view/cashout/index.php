<!-- * User: lirong-->
<!-- * Date: 2017/7/1-->
<!-- * Time: 11:17-->
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('cashout/index')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="co_arriveDateTime">
        <input type="hidden" name="orderDirection" value="desc">
        <div class="bjui-searchBar">
            <label>用户ID：</label><input type="text" id="ipt_nick" value="{$input.ipt_nick|default=''}" name="ipt_nick" class="form-control" size="10">&nbsp;
            <label>真实姓名：</label><input type="text" id="ipt_name" value="{$input.ipt_name|default=''}" name="ipt_name" class="form-control" size="10">&nbsp;
            <label>编号：</label><input type="text" id="ipt_caid" value="{$input.ipt_caid|default=''}" name="ipt_caid" class="form-control" size="20">&nbsp;
            <label>金额：</label>
            <input type="text" id="ipt_moneyMin" data-rule="number" value="{$input.ipt_moneyMin|default=''}" placeholder="从" name="ipt_moneyMin" class="form-control" size="10">&nbsp;
            <input type="text" id="ipt_moneyMax" value="{$input.ipt_moneyMax|default=''}" placeholder="到" name="ipt_moneyMax" class="form-control" data-rule="number" size="10">&nbsp;

            <label>状态</label>
            <select name="ipt_state" id="ipt_state" data-toggle="selectpicker">
                <option selected value="">请选择</option>
                {notempty name="states"}
                    {foreach name="states" item="name" key="k"}
                    <option {if $input['ipt_state']!='' && $input['ipt_state']==$k}selected {/if} value="{$k}">{$name}</option>
                    {/foreach}
                {/notempty}
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
                <button type="submit" class="btn-green" data-toggle="doexport" data-url="{:url('Cashout/exportExcel')}" mask="true">导出</button>
            </div>
        </div>
        <div class="bjui-moreSearch">
            <label>提现时间：</label>
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
            <th align="center">提现编号</th>
            <th align="center">用户名</th>
            <th align="center">开户行</th>
            <th align="center">提现账号</th>
            <th align="center">账号姓名</th>
            <th align="center">提现金额</th>
            <th align="center">手续费</th>
            <th align="center">实际金额</th>
            <th align="center" width="140">状态</th>
            <th align="center" data-order-field="co_arriveDateTime">提现时间</th>
            <th align="center" data-order-field="co_day_time">预计到账时间</th>
            <th align="center">操作</th>
        </tr>
        </thead>
        <tbody>
        {empty name="data['list']"}
        <tr>
            <td colspan="12" align="center">暂无数据！</td>
        </tr>
        {else /}
        {volist name="data['list']" id='vo'}
        <tr>
            <td align="center">{$vo.co_caid}</td>
            <td align="center">{$vo.co_unick}</td>
            <td align="center">{$vo.co_bankName}</td>
            <td align="center">{$vo.co_account}</td>
            <td align="center">{$vo.co_cardmaster}</td>
            <td align="center">{$vo.co_money}</td>
            <td align="center">{$vo.co_tax} </td>
            <td align="center">{$vo.co_money-$vo.co_tax} </td>
            <td align="center">{$vo.state_text|default='未知状态'} </td>
            <td align="center">{$vo.co_arriveDateTime} </td>
            <td align="center">{$vo.co_day_time} </td>

            <td align="center">
                {if !$admin_state && $vo.co_state==0}
                <a class="btn btn-green" href="{:url('payment/cashout/rejected',['id'=>$vo.co_caid])}"  data-toggle="dialog" mask="true" data-width="600" data-height="200"><span>驳回</span></a>
                 |
                <a class="btn btn-red" href="{:url('payment/cashout/settlement',['id'=>$vo.co_caid])}" data-toggle="dialog" mask="true" data-width="600" data-height="200" ><span>结算</span></a>

                {else /}
                <button class="btn btn-default disabled">驳回</button>
                <button class="btn btn-default disabled">结算</button>
                {/if}
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