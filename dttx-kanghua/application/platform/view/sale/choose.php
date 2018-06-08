<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$page.pageSize}">
        <input type="hidden" name="pageCurrent" value="{$page.pageCurrent}">
        <div class="bjui-searchBar">
            <label>商品编号：</label><input type="text" id="number" value="{$number|default=''}" name="number" class="form-control" size="10">&nbsp;
            <label>商品名：</label><input type="text" id="goodsname" value="{$goodsname|default=''}" name="goodsname" class="form-control" size="15">&nbsp;
            <label for="createTime" class="control-label x85">时间：</label>
            <input type="text" name="beginTime" id="beginTime" value="{$beginTime|default=''}" data-toggle="datepicker" data-rule="date" size="15">&nbsp;
            <input type="text" name="endTime" id="endTime" value="{$endTime|default=''}" data-toggle="datepicker" data-rule="date" size="15">&nbsp;&nbsp;&nbsp;&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
                <button type="button" class="btn-blue" data-url="{:url('Sale/selectItem')}" data-toggle="doajaxchecked" data-confirm-msg="确定要批量选取商品吗？" data-group="ids" data-icon="plus">批量选取</span></button>
<!--                <button type="button" class="btn-blue" data-url="{:url('Sale/select')}?id={#bjui-selected}" data-toggle="doajax" data-group="ids" data-confirm-msg="确定要选取选中项吗？" data-icon="plus" title="可以在控制台(network)查看被删除ID">批量选取</button>&nbsp;-->
            </div>
        </div>

    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
        <tr>
            <th width="20" align="center"><input type="checkbox" class="checkboxCtrl" data-group="ids" data-toggle="icheck"></th>
            <th width="80" align="center">商品预览</th>
            <th width="120" align="center">商品编号</th>
            <th width="100" align="center">商品名称</th>
            <th width="50" align="center">商品分类</th>
            <th width="50" align="center">商品规格</th>
            <th width="50" align="center">商品型号</th>
            <th width="50" align="center">采购价格</th>
            <th width="50" align="center">统一售价</th>
            <th width="50" align="center">利润预估</th>
            <th width="200" align="center">商品简介</th>
            <th width="50" align="center">操作</th>
        </tr>
        </thead>
        <tbody>
        {foreach name="goodsList" item="item" }
        <tr data-id="{$item.id}">
            <td align="center"><input type="checkbox" data-toggle="icheck" class="checkboxCtrl" name="ids" value="{$item.id}"></td>
            <td align="center"><img src="{$item.image}" alt="" width="80px" height="80px"></td>
            <td style="padding-left: 15px;">{$item.number}</td>
            <td style="padding-left: 15px;">{$item.name}</td>
            <td style="padding-left: 15px;">{$item.category}</td>
            <td align="center">{$item.format}</td>
            <td align="center">{$item.model}</td>
            <td align="center">{$item.cost}</td>
            <td align="center">{$item.price}</td>
            <td align="center">{$item.profit}</td>
            <td align="center">{$item.description}</td>
            <td align="center">
                <a class="btn btn-green" href="{:url('Sale/selectItem', ['ids' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要推荐该商品吗？"><span>选取</span></a>
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