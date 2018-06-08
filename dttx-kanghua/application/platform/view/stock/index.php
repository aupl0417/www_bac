<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$page.pageSize}">
        <input type="hidden" name="pageCurrent" value="{$page.pageCurrent}">
        <div class="bjui-searchBar">
<!--            <label>商品编号：</label><input type="text" id="number" value="{$number|default=''}" name="number" class="form-control" size="10">&nbsp;-->
            <label>商品名称：</label><input type="text" id="goodsname" value="{$goodsname|default=''}" name="goodsname" class="form-control" size="10">&nbsp;
            <label>仓库：</label><input type="text" id="basename" value="{$baseName|default=''}" name="basename" class="form-control" size="10">&nbsp;
            <label>库存量:</label>
            <select name="op" id="op" data-toggle="selectpicker" data-rule="required">
                <option value="lt" {if condition="$op eq 'lt'"}selected="selected"{/if}>小于</option>
                <option value="gt" {if condition="$op eq 'gt'"}selected="selected"{/if}>大于</option>
            </select>
            <input type="text" id="stockcount" value="{$stockcount|default=''}" name="stockcount" class="form-control" size="10">
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
            <button type="button" class="btn-green" data-url="{:url('stock/create')}" data-toggle="dialog" mask="true" data-width="460" data-height="400" data-icon="plus">商品入库</button>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="80" align="center">商品编号</th>
                <th width="100" align="center">商品名称</th>
                <th width="50" align="center">型号</th>
                <th width="50" align="center">仓库</th>
                <th width="50" align="center">库存量</th>
                <th width="150" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="goodsList" item="item" }
            <tr data-id="{$item.id}">
                <td style="padding-left: 15px;">{$item.number}</td>
                <td style="padding-left: 15px;">{$item.name}</td>
                <td align="center">{$item.model}</td>
                <td align="center">{$item.baseName}</td>
                <td align="center">{$item.stock}</td>
                <td align="center">
                    <a class="btn btn-green" href="{:url('Stock/operateDetail', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="1200" data-height="600"><span>查看明细</span></a>
                    | <a class="btn btn-green" href="{:url('Stock/allocation', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="500" data-height="350"><span>商品调拨</span></a>
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