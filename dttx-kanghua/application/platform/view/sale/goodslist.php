<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$page.pageSize}">
        <input type="hidden" name="pageCurrent" value="{$page.pageCurrent}">
        <div class="bjui-searchBar">
            <label>商品编号：</label><input type="text" id="number" value="{$number|default=''}" name="number" class="form-control" size="10">&nbsp;
            <label>商品名：</label><input type="text" id="goodsname" value="{$goodsname|default=''}" name="goodsname" class="form-control" size="10">&nbsp;
            <label>上架状态：</label>
            <select name="isSale" id="isSale" data-width="100" data-toggle="selectpicker">
                <option value="">--请选择--</option>
                <option value="0" {if condition="$isSale === 0"}selected="selected"{/if}>下架</option>
                <option value="1" {if condition="$isSale == 1"}selected="selected"{/if}>上架</option>
            </select>
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <button type="button" class="btn-green" data-url="{:url('Sale/choose')}" data-toggle="dialog" mask="true" data-width="1200" data-height="600" data-icon="plus">选取商品</button>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table class="table table-bordered table-hover table-striped table-top" data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="50" align="center">商品ID</th>
                <th width="50" align="center">商品主图</th>
                <th width="120" align="center">商品编号</th>
                <th align="center">商品名称</th>
                <th width="60" align="center">积分奖励</th>
                <th width="50" align="center">单价</th>
<!--                 <th align="center">规格</th>-->
                <th width="100" align="center">型号</th>
                <th width="50" align="center">数量</th>
                <th width="60" align="center">商品分类</th>
                <th width="200" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="goodsList" item="item" }
            <tr data-id="{$item.id}">
                <td align="center">{$item.id}</td>
                <td align="center"><a href="{$item.image}" title="点击查看大图" target="_blank"><img src="{$item.image}" alt="点击查看大图" width="30px" height="auto"></a></td>
                <td style="padding-left: 15px;">{$item.number}</td>
                <td style="padding-left: 15px;" title="{$item.name}">{$item.name}{if condition="$item.bg_isSale eq 0"}<label class="label label-danger">总部已下架</label>{/if}</td>
                <td align="center">{$item.scoreReward}%</td>
                <td align="center">{$item.price}</td>
<!--                <td align="center">{$item.format}</td>-->
                <td align="center">{$item.model}</td>
                <td align="center">{$item.stock}</td>
                <td align="center">{$item.category}</td>
                <td align="center">
                    {if condition="$item.isSale eq 1"}
                        <a class="btn btn-green" href="{:url('Sale/offShelf', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要该商品下架吗？"><span>下架</span></a>
                        | <a class="btn btn-green" href="{:url('Sale/share', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="250" data-height="305"><span>分享商品</span></a>
                    {else/}
                        <a class="btn btn-green" href="{:url('Sale/onShelf', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要该商品上架吗？"><span>上架</span></a>
                    {/if}
                    | <a class="btn btn-green" href="{:url('Sale/detail', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="1200" data-height="800"><span>查看明细</span></a>
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
