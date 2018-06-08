<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$page.pageSize}">
        <input type="hidden" name="pageCurrent" value="{$page.pageCurrent}">
        <div class="bjui-searchBar">
            <label>分类编号：</label><input type="text" id="id" value="{$id|default=''}" name="id" class="form-control" size="10">&nbsp;
            <label>分类名称：</label><input type="text" id="name" value="{$name|default=''}" name="name" class="form-control" size="10">&nbsp;&nbsp;&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
                <button type="button" class="btn-green" data-url="{:url('Category/create')}" data-toggle="dialog" mask="true" data-width="450" data-height="200"" data-icon="plus">添加分类</button>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="30" align="center">分类编号</th>
                <th width="50" align="center">分类名称</th>
                <th width="50" align="center">上级分类</th>
                <th width="50" align="center">添加时间</th>
                <th width="150" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="cateList" item="item" }
            <tr data-id="{$item.id}">
                <td style="padding-left: 15px;">{$item.id}</td>
                <td style="padding-left: 15px;">{$item.name}</td>
                <td style="padding-left: 15px;">{$item.parentName}</td>
                <td align="center">{if condition="$item.createTime neq ''"}{$item.createTime|date='Y-m-d', ###}{/if}</td>
                <td align="center">
                    <a class="btn btn-green" href="{:url('Category/edit', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="450" data-height="250"><span>编辑</span></a>
                    | <a class="btn btn-red" href="{:url('Category/remove', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要删除该分类吗？"><span>删除</span></a>
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