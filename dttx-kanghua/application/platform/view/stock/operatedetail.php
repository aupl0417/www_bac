<div class="bjui-pageContent tableContent">
    <form action="{:url('')}" id="j_custom_form" data-toggle="validate" data-alertmsg="false">
        <br/>
        <label class="control-label x120">商品编号：</label>
        <label class="x120">{$stock.number}</label>
        <label class="control-label x90">商品名称：</label>
        <label class="x200">{$stock.name}</label>
        <label class="control-label x90">商品型号：</label>
        <label class="x60">{$stock.model}</label>
        <label class="control-label x90">仓库：</label>
        <label class="x120">{$stock.baseName}</label>
        <label class="control-label x90">库存量：</label>
        <label class="x60">{$stock.stock}</label><br/><br/>
        <table id="tabledit1" class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th width="80" align="center">时间</th>
                <th width="100" align="center">操作</th>
                <th width="50" align="center">数量</th>
                <th width="50" align="center">对方</th>
                <th width="50" align="center">操作人</th>
                <th width="150" align="center">关联单号</th>
            </tr>
            </thead>
            <tbody>
            {foreach name="details" item="item" }
            <tr data-id="{$item.id}">
                <td style="padding-left: 15px;">{$item.createTime|date='Y-m-d H:i:s', ###}</td>
                <td style="padding-left: 15px;" align="center">{if condition="$item.type eq 'storage'"}商品入库（+）{elseif condition="$item.type eq 'allocation'"}商品调拨（-）{elseif condition="$item.type eq 'sale'"}商品销售）（-）{/if}</td>
                <td align="center">{$item.number}</td>
                <td align="center">{$item.baseName|default='-'}</td>
                <td align="center">{$item.name}</td>
                <td align="center">{$item.orderId}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
        <li><button type="submit" class="btn-default">保存</button></li>
    </ul>
</div>