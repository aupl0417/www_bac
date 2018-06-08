<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <div class="bjui-searchBar">
            <label>仓库编号：</label><input type="text" id="baseId" value="{$baseId|default=''}" name="baseId" class="form-control" size="10">&nbsp;
            <label>仓库名称：</label><input type="text" id="basename" value="{$baseName|default=''}" name="basename" class="form-control" size="10">&nbsp;
            <label>售卖渠道：</label><input type="text" id="channel" value="{$channel|default=''}" name="channel" class="form-control" size="10">&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
                <button type="button" class="btn-green" data-url="{:url('basemanage/create')}" data-toggle="dialog" mask="true" data-width="460" data-height="400" data-icon="plus">新增仓库</button>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="50" align="center">仓库编号</th>
                <th width="100">仓库名称</th>
                <th width="120">仓库说明</th>
                <th width="120" align="center">售卖渠道</th>
                <th width="200" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="baseList" item="item" }
            <tr data-id="{$item.ba_id}">
                <td>{$item.ba_id}</td>
                <td>{$item.ba_name}</td>
                <td>{$item.ba_description}</td>
                <td>{$item.ba_channel}</td>
                <td align="center">
                    <a class="btn btn-green" href="{:url('Basemanage/edit', ['id' => $item['ba_id']])}" data-toggle="dialog" mask="true" data-width="520" data-height="300"><span>编辑</span></a> | <a class="btn btn-green" href="{:url('Basemanage/detail', ['id' => $item['ba_id']])}" data-toggle="dialog" mask="true" data-width="520" data-height="400"><span>查看明细</span></a> | <a class="btn btn-red" href="{:url('Basemanage/remove', ['id' => $item['ba_id']])}" data-toggle="doajax" data-confirm-msg="确定要删除该仓库吗？"><span>删除</span></a>
                </td>
            </tr>
            {/foreach}
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
        <span>&nbsp;条，共 {$page.totalCount} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$page.totalCount}" data-page-size="{$page.pageSize}" data-page-current="{$page.pageCurrent}"></div>
</div>