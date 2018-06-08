<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('admin/project/reviewlist')}" method="post">
        <input type="hidden" name="pageSize" value="{$input.pageSize|default="30"}">
        <input type="hidden" name="pageCurrent" value="{$input.pageCurrent|default="1"}">
        <input type="hidden" name="orderField" value="{$input.orderField|default="in_createTime"}">
        <input type="hidden" name="orderDirection" value="{$input.orderDirection|default="desc"}">
        <div class="bjui-searchBar">
            <label>产品名称：</label><input type="text" id="productname" value="{$input.productname|default=''}" name="productname" class="form-control" size="10">&nbsp;
            <label>公司名称：</label><input type="text" id="companyname" value="{$input.companyname|default=''}" name="companyname" class="form-control" size="10">&nbsp;
            <label>大唐账号：</label><input type="text" id="dttxnick" value="{$input.dttxnick|default=''}" name="dttxnick" class="form-control" size="10">&nbsp;
            <label>联系电话：</label><input type="text" id="mobile" value="{$input.mobile|default=''}" name="mobile" class="form-control" size="10">&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
        <tr>
            <th width="50" data-order-field="in_id">ID</th>
            <th>产品名称</th>
            <th width="120">公司名称</th>
            <th width="120">联系电话</th>
            <th width="120" align="center">大唐账号</th>
            <th width="120" align="center">联系人姓名</th>
            <th width="100" data-order-field="in_createTime">创建时间</th>
            <th align="center" width="50">审核状态</th>
            <th align="center" width="120">操作</th>
        </tr>
        </thead>
        <tbody>
        {empty name="data['list']"}
        <tr>
            <td colspan="9" align="center">暂无数据！</td>
        </tr>
        {else /}
        {volist name="data['list']" id='vo'}
        <tr>
            <td>{$vo.in_id}</td>
            <td>{$vo.in_product_name}</td>
            <td>{$vo.in_company_name}</td>
            <td>{$vo.in_mobile}</td>
            <td>{$vo.in_dttx_nick}</td>
            <td>{$vo.in_username}</td>
            <td>{$vo.in_createTime|date="Y-m-d H:i:s",###}</td>
            <td align="center">{if condition='$vo.in_status eq -1'}<label for="" class="label label-danger">未通过</label>{elseif condition='$vo.in_status eq 0'/}<label for="" class="label label-default">待审核</label>{elseif condition='$vo.in_status eq 1'/}<label for="" class="label label-success">已通过</label>{/if}</td>
            <td align="center">
                {if condition='$vo.in_status eq 0'}
                    <a class="btn btn-green" href="{:url('admin/project/review',['id'=>$vo.in_id])}" data-toggle="dialog" mask="true" data-width="880" data-height="650"><span>审核</span></a>
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