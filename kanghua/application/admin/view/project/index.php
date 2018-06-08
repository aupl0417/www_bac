<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('admin/project/index')}" method="post">
        <input type="hidden" name="pageSize" value="{$input.pageSize|default="30"}">
        <input type="hidden" name="pageCurrent" value="{$input.pageCurrent|default="1"}">
        <input type="hidden" name="orderField" value="{$input.orderField|default="pl_create_time"}">
        <input type="hidden" name="orderDirection" value="{$input.orderDirection|default="desc"}">
        <div class="bjui-searchBar">
            <label>项目名称：</label><input type="text" id="name" value="{$input.name|default=''}" name="name" class="form-control" size="10">&nbsp;
            <label>公司名称：</label><input type="text" id="companyname" value="{$input.companyname|default=''}" name="companyname" class="form-control" size="10">&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
                <button type="button" class="btn-green" data-url="{:url('Project/create')}" data-toggle="dialog" mask="true" data-width="880" data-height="650" data-icon="plus">添加项目</button>&nbsp;
<!--                <button type="button" class="btn-blue" data-url="{:url('Project/remove')}?userid={#bjui-selected}" data-toggle="doajax" data-confirm-msg="确定要删除选中项吗？" data-icon="remove" title="可以在控制台(network)查看被删除ID">删除选中行</button>&nbsp;-->
                <div class="btn-group">
                    <button type="button" class="btn-default dropdown-toggle" data-toggle="dropdown" data-icon="copy">复选框-批量操作<span class="caret"></span></button>
                    <ul class="dropdown-menu right" role="menu">
                        <li><a href="{:url('admin/project/change',['status'=>1])}" data-toggle="doajaxchecked" data-confirm-msg="确定要启用选中项吗？" data-group="ids" data-idname="ids">批量<span style="color: green;">启用</span></a></li>
                        <li><a href="{:url('admin/project/change','status=0')}" data-toggle="doajaxchecked" data-confirm-msg="确定要禁用选中项吗？" data-idname="ids" data-group="ids">批量<span style="color: red;">禁用</span></a></li>
                        <li class="divider"></li>
                        <li><a href="{:url('admin/project/remove')}" data-toggle="doajaxchecked" data-confirm-msg="确定要删除选中项吗？" data-idname="ids" data-group="ids">删除选中</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
        <tr>
            <th width="50" data-order-field="pl_id">ID</th>
            <th>项目名称</th>
            <th width="120">公司名称</th>
            <th width="120">联系电话</th>
            <th width="120" align="center">大唐账号</th>
            <th align="center" width="50" data-order-field="pl_states">开启状态</th>
            <th width="100" data-order-field="pl_create_time">创建时间</th>
            <th width="26"><input type="checkbox" class="checkboxCtrl" data-group="ids" data-toggle="icheck"></th>
            <th align="center" width="120">管理</th>
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
            <td>{$vo.pl_id}</td>
            <td>{$vo.pl_name}</td>
            <td>{$vo.pl_company_name}</td>
            <td>{$vo.pl_contact}</td>
            <td>{$vo.pl_dttx_nick}</td>
            <td align="center">{eq name="$vo.pl_states" value="0"}<label class="label label-warning">冻结</label>{else /}<label class="label label-success">启用</label> {/eq}</td>
            <td>{$vo.pl_create_time|date="Y-m-d H:i:s",###}</td>
            <td><input type="checkbox" name="ids" data-toggle="icheck" value="{$vo.pl_id}"></td>
            <td>  <a class="btn btn-green" href="{:url('admin/project/edit',['id'=>$vo.pl_id])}" data-toggle="dialog" mask="true" data-width="880" data-height="650"><span>编辑</span></a></td>
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