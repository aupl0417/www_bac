<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$page.pageSize}">
        <input type="hidden" name="pageCurrent" value="{$page.pageCurrent}">
        <div class="bjui-searchBar">
            <label>订单号：</label><input type="text" id="id" value="{$id|default=''}" name="id" class="form-control" size="10">&nbsp;
            <label>会员名：</label><input type="text" id="username" value="{$username|default=''}" name="username" class="form-control" size="10">&nbsp;&nbsp;&nbsp;
            <label>商品名称：</label><input type="text" id="goodsname" value="{$goodsname|default=''}" name="goodsname" class="form-control" size="10">&nbsp;&nbsp;&nbsp;
            <label for="createTime" class="control-label x85">时间：</label>
            <input type="text" name="beginTime" id="beginTime" value="{$beginTime|default=''}" data-toggle="datepicker" data-rule="date" size="15">&nbsp;
            <input type="text" name="endTime" id="endTime" value="{$endTime|default=''}" data-toggle="datepicker" data-rule="date" size="15">&nbsp;&nbsp;&nbsp;&nbsp;
            <label>状态：</label>
            <select name="state" id="state" data-width="100" data-toggle="selectpicker">
                <option value="">--请选择--</option>
                {foreach name='stateList' item='vo'}
                <option value="{$vo.dt_key}" {if condition="$vo.dt_key == $state"}selected="selected"{/if}>{$vo.dt_value}</option>
                {/foreach}
            </select>
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="200" align="center">订单号</th>
                <th  align="center">商品</th>
                <th  align="center">商品名称</th>
                <th  align="center">型号</th>
                <th  align="center">数量</th>
                <th  align="center">单价</th>
                <th  align="center">金额</th>
                <th  align="center">购买人</th>
                <th  align="center">购买时间</th>
                <th  align="center">状态</th>
                <th width="200" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {notempty name="orderList"}
            {foreach name="orderList" item="item" }
            <tr data-id="{$item.id}">
                <td style="padding-left: 15px;">{$item.id}</td>
                <td style="padding-left: 15px;"><a href="{$item.image}" target="_blank"><img src="{$item.image}" alt="" width="60" height="auto"></a></td>
                <td style="padding-left: 15px;">{$item.goodsName}</td>
                <td style="padding-left: 15px;">{$item.model}</td>
                <td align="center">{$item.number}</td>
                <td align="center">{$item.price}</td>
                <td align="center">{$item.pay}</td>
                <td align="center">{$item.nick}</td>
                <td align="center">{$item.createTime|date='Y-m-d H:i:s', ###}</td>
                <td align="center">{$stateList[$item.state + 1]['dt_value']}</td>
                <td align="center">
                    <a class="btn btn-green" href="{:url('Order/detail', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="800" data-height="900"><span>详情</span></a>
                    {if condition="$item.state eq 0"}
<!--                        <a class="btn btn-green" href="{:url('Order/edit', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="500" data-height="450"><span>修改</span></a>-->
                        <a class="btn btn-red" href="{:url('Order/close', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要关闭该订单吗？"><span>关闭</span></a>
                    {eq name=":session('user.roleid')" value="1"}
                    <a class="btn btn-orange" href="{:url('Order/confirmOrderforOrderId', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定该订单已付款吗？"><span>确认已支付</span></a>{/eq}
                    {elseif condition="($item.state eq 1) and ($roleId eq 1)"/}
                        <a class="btn btn-green" href="{:url('Order/delivery', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="500" data-height="200"><span>发货</span></a>
<!--                        <a class="btn btn-green" href="{:url('Order/delivery', ['id' => $item['id']])}" data-toggle="doajax" data-confirm-msg="确定要发货吗？"><span>发货</span></a>-->
                    {/if}

                </td>
            </tr>
            {/foreach}
            {else /}
            <tr>
                <td colspan="11" align="center">暂无订单</td>
            </tr>
        {/notempty}
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