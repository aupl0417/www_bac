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

            <button type="button" class="btn-green" data-toggle="navtab" data-id="platform_Goods_create" data-fresh="true" data-url="{:url('Goods/create')}" data-title="新增商品">新增商品</button>
            <div class="pull-right">
<!--                <button type="button" class="btn-blue" data-url="ajaxDone2.html?id={#bjui-selected}" data-toggle="doajax" data-confirm-msg="确定要删除选中项吗？" data-icon="remove" title="可以在控制台(network)查看被删除ID">删除选中行</button>&nbsp;-->
                <div class="btn-group">
                    <button type="button" class="btn-default dropdown-toggle" data-toggle="dropdown" data-icon="copy">复选框-批量操作<span class="caret"></span></button>
                    <ul class="dropdown-menu right" role="menu">
                        <li><a href="{:url('Goods/batchOffShelf')}" data-toggle="doajaxchecked" data-confirm-msg="确定要下架商品吗？" data-group="ids">批量<span style="color: red;">下架</span></a></li>
                        <li class="divider"></li>
                        <li><a href="{:url('Goods/batchOnShelf')}" data-toggle="doajaxchecked" data-confirm-msg="确定要上架商品吗？"  data-group="ids">批量<span style="color: green;">上架</span></a></li>

<!--                        <li><a href="ajaxDone2.html" data-toggle="doajaxchecked" data-confirm-msg="确定要删除选中项吗？" data-idname="delids" data-group="ids">删除选中</a></li>-->
                    </ul>
                </div>
            </div>
<!--            <a href="{:url('Goods/')}" data-toggle="doajaxchecked" data-confirm-msg="确定要删除选中项吗？" data-idname="delids" data-group="ids">删除选中</a>-->
        </div>

    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table class="table table-bordered table-hover table-striped table-top" data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="30" align="center"><input type="checkbox" class="checkboxCtrl" data-group="ids" data-toggle="icheck"></th>
                <th width="50" align="center">商品主图</th>
                <th width="120" align="center">商品编号</th>
                <th align="center">商品名称</th>
                <th width="60" align="center">积分奖励</th>
                <th width="50" align="center">单价</th>
<!--                <th width="80" align="center">规格</th>-->
                <th width="100" align="center">型号</th>
                <th width="50" align="center">数量</th>
                <th width="80" align="center">商品分类</th>
                <th width="60" align="center">是否推荐</th>
                <th width="210" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="goodsList" item="item" }
            <tr data-id="{$item.id}">
                <td align="center"><input type="checkbox" data-toggle="icheck" class="checkboxCtrl" name="ids" value="{$item.id}"></td>
                <td align="center"><a href="{$item.image}" title="点击查看大图"  target="_blank"><img src="{$item.image}" alt="点击查看大图" width="30px" height="auto"></a></td>
                <td style="padding-left: 15px;">{$item.number}</td>
                <td style="padding-left: 15px;">{$item.name}</td>
                <td align="center">{$item.scoreReward}%</td>
                <td align="center">{$item.price|default='0.00'}</td>
<!--                <td align="center">{$item.format}</td>-->
                <td align="center">{$item.model}</td>
                <td align="center">{$item.stock|default='0'}</td>
                <td align="center">{$item.category}</td>
                <td align="center">{if condition="$item.isRecommend eq 0"}否{else/}是{/if}</td>
                <td style="padding-left: 15px;">
                    {if condition="$item.isSale eq 1"}
                        <a class="btn btn-red" href="{:url('Goods/offShelf', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要该商品下架吗？"><span>下架</span></a>
                    {else/}
                        <a class="btn btn-green" href="{:url('Goods/onShelf', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要该商品上架吗？"><span>上架</span></a>
                    {/if}
                    |
<!--                    <a class="btn btn-green" href="{:url('Goods/edit', ['id' => $item['id']])}"  data-toggle="dialog" mask="true" data-width="1200" data-height="800"><span>编辑</span></a>-->

                    <button type="button" class="btn-green" data-toggle="navtab" data-id="platform_Goods_edit" data-fresh="true" data-url="{:url('Goods/edit', ['id' => $item['id']])}" data-title="编辑商品">编辑</button>


                    | <a class="btn btn-green" href="{:url('Goods/detail', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="1200" data-height="800"><span>查看明细</span></a>
                    {if condition="$item.isRecommend eq 0"}
                        | <a class="btn btn-green" href="{:url('Goods/recommend', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要推荐该商品吗？"><span>推荐</span></a>
                    {/if}
<!--                    | <a class="btn btn-red" href="{:url('Goods/delete', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要删除该商品吗？"><span>删除</span></a>-->
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
<script type='text/javascript'>
    $('body').delegate('.goods_url >img', 'mouseover', function(event) {
        $(this).parent('td').append('<div><img src="'+$(this).attr('src')+'"/></div>');
    }).delegate('.goods_url >img', 'mouseout', function(event) {
            $(this).parent('td').find('div').remove();
        });
</script>