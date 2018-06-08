<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$page.pageSize}">
        <input type="hidden" name="pageCurrent" value="{$page.pageCurrent}">
        <div class="bjui-searchBar">
            <label>编号：</label><input type="text" id="id" value="{$id|default=''}" name="id" class="form-control" size="10">&nbsp;
            <label>银行名称：</label><input type="text" id="bankname" value="{$bankname|default=''}" name="bankname" class="form-control" size="10">&nbsp;&nbsp;&nbsp;
            <label>状态：</label>
            <select name="state" id="state" data-width="100" data-toggle="selectpicker">
                <option {eq name="$state" value="all"} selected {/eq} value="all">&nbsp;&nbsp;--请选择--</option>
                <option {eq name="$state" value="1"} selected {/eq} value="1">启用</option>
                <option {eq name="$state" value="0"} selected {/eq} value="0">禁用</option>
            </select>
            <!--<label>平台：</label>
            <select name="platformId" id="platformId" data-width="100" data-toggle="selectpicker">
                <option value="all">&nbsp;&nbsp;--平台--</option>
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select>-->
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
                <button type="button" class="btn-green" data-url="{:url('Bank/create')}" data-toggle="dialog" mask="true" data-width="500" data-height="300" data-icon="plus">添加银行</button>
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="30" align="center" data-order-field="bank_id">编号</th>
                <th width="50" align="center">银行名称</th>
                <th width="120" align="center">类型</th>
                <th width="120" align="center" data-order-field="bank_enabled">状态</th>
                <th width="150" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="bankList" item="item" }
            <tr data-id="{$item.bank_id}">
                <td style="padding-left: 15px;">{$item.bank_id}</td>
                <td style="padding-left: 15px;">{$item.bank_name}</td>
                <td align="center">{if condition='$item.bank_type eq 0'}银行{else/}第三方支付平台{/if}</td>
                <td align="center">{if condition='$item.bank_enabled eq 1'} <label class="label label-success">启用</label> {else/}<label class="label label-danger">禁用</label> {/if}</td>
                <td align="center">
                    <a class="btn btn-green" href="{:url('Bank/edit', ['id' => $item['bank_id']])}" data-toggle="dialog" mask="true" data-width="500" data-height="300"><span>编辑</span></a>
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