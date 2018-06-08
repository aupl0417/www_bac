<!-- * User: lirong-->
<!-- * Date: 2017/7/1-->
<!-- * Time: 11:17-->
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('finance/accountenquiry')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="a_id">
        <input type="hidden" name="orderDirection" value="desc">
        <div class="bjui-searchBar">
            <label>用户ID：</label><input type="text" id="unick" value="{$input.unick|default=''}" name="unick" class="form-control" size="10">&nbsp;
            <label>姓名：</label><input type="text" id="uname" value="{$input.name|default=''}" name="uname" class="form-control" size="10">&nbsp;
            <label>手机号：</label><input type="text" id="utel" value="{$input.tel|default=''}" name="utel" data-rule="mobile" class="form-control" size="10">&nbsp;
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
            <th align="center" data-order-field="pl_id">ID</th>
            <th align="center">用户ID</th>
            <th align="center">姓名</th>
            <th align="center">手机号</th>
            <th align="center">账户余额</th>
            <th align="center">冻结金额</th>
            <th align="center">状态</th>
            <th align="center" width="140">管理</th>
        </tr>
        </thead>
        <tbody>
        {empty name="data['list']"}
        <tr>
            <td colspan="8" align="center">暂无数据！</td>
        </tr>
        {else /}
        {volist name="data['list']" id='vo'}
        <tr>
            <td align="center">{$vo.a_id}</td>
            <td align="center">{$vo.a_nick}</td>
            <td align="center">{$vo.u_name}</td>
            <td align="center">{$vo.u_tel}</td>
            <td align="center">{$vo.a_freeMoney}</td>
            <td align="center">{$vo.a_frozenMoney}</td>
            <td align="center">
                {switch name="$vo.a_states"}
                    {case value='0'}已冻结{/case}
                    {case value='1'}正常{/case}
                    {case value='-1'}已注销{/case}
                {/switch}
            </td>
            <td align="center">  <a class="btn btn-green" href="{:url('payment/finance/accountdetails',['aid'=>$vo.a_id])}" data-toggle="navtab" data-type="POST"><span>查看明细</span></a> {if !$admin_state} |
                {eq name="$vo.a_states" value="1"}
                <a class="btn btn-red" href="{:url('finance/changestop',['id'=>$vo.a_id])}" data-toggle="dialog" mask="true" data-width="600" data-height="250"><span>冻结</span></a>
                {else /}
                <a class="btn btn-blue" href="{:url('finance/changeopen',['id'=>$vo.a_id,'platformId'=>$vo.a_platform_id])}" data-toggle="doajax" data-confirm-msg="确定要启用选中项吗？" ><span>启用</span></a>
                {/eq}
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