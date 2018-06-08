<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="table-edit-lookup.html" method="post">
        <input type="hidden" name="pageCurrent" value="1">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="orderField" value="${param.orderField}">
        <div class="bjui-searchBar">
            <label>名称：</label><input type="text" value="" name="code" size="10">&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a></li>
<!--            <div class="pull-right">-->
<!--                <button type="button" class="btn-blue" data-toggle="lookupback" data-lookupid="ids" data-warn="请至少选择一项职业" data-icon="check-square-o">选择选中</button>-->
<!--            </div>-->
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%">
        <thead>
        <tr>
            <th data-order-field="cm_id">No.</th>
            <th>客户名称</th>
            <th>状态</th>
            <th>是否异常</th>
            <th class="orderby" data-order-direction="asc" data-order-field="cm_create_time">创建日期</th>
<!--            <th width="28"><input type="checkbox" class="checkboxCtrl" data-group="ids" data-toggle="icheck"></th>-->
            <th width="74">操作</th>
        </tr>
        </thead>
        <tbody>
        {empty name="data"}
        <tr>
            <td colspan="6" align="center">暂无客户资料可选！</td>
        </tr>
        {else /}
        {volist name="data" id="vo"}
        <tr>
            <td>{$vo.cm_id}</td>
            <td>{$vo.cm_name}</td>
            <td>{$vo.cm_status?"启用":"未用"}</td>
            <td>{$vo.cm_isAbnormal?"是":"否"}</td>
            <td>{$vo.cm_create_time|date="Y-m-d",###}</td>
<!--            <td><input type="checkbox" name="ids" data-toggle="icheck" value="{inNameID:'{$vo.cm_id}', inName:'{$vo.cm_name}'}"></td>-->
            <td>
                <a href="javascript:;" data-toggle="lookupback" data-args="{inNameID:'{$vo.cm_id}', inName:'{$vo.cm_name}'}" class="btn btn-blue" title="选择本项" data-icon="check">选择</a>
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
        <span>&nbsp;条，共 {$count} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$count}" data-page-size="{$info.pageSize}" data-page-current="{$info.pageCurrent}">
    </div>
</div>