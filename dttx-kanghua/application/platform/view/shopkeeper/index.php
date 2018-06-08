<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$page.pageSize}">
        <input type="hidden" name="pageCurrent" value="{$page.pageCurrent}">
        <div class="bjui-searchBar">
            <label>经销商编号：</label><input type="text" id="id" value="{$id|default=''}" name="id" class="form-control" size="10">&nbsp;
            <label>会员名：</label><input type="text" id="name" value="{$name|default=''}" name="name" class="form-control" size="10">
            <label for="createTime" class="control-label x85">加入时间：</label>
            <input type="text" name="beginTime" id="beginTime" value="{$beginTime|default=''}" data-toggle="datepicker" data-rule="date" size="15">&nbsp;
            <input type="text" name="endTime" id="endTime" value="{$endTime|default=''}" data-toggle="datepicker" data-rule="date" size="15">&nbsp;&nbsp;&nbsp;&nbsp;
            <label>所属区域：</label>
            <select name="provinceCode" id="provinceCode" data-width="100" data-toggle="selectpicker" data-nextselect="#cityCode" data-refurl="{:url('common/publics/ajax_area')}?pid={value}">
                <option value="all">&nbsp;&nbsp;--省市--</option>
                {foreach name="area" item="vo" }
                <option value="{$vo.a_id}">{$vo.a_name}</option>
                {/foreach}
            </select>
            <select name="cityCode" id="cityCode"  data-width="100" data-toggle="selectpicker" data-emptytxt="--城市--"">
                <option value="all">&nbsp;&nbsp;--城市--</option>
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
                <th width="80" align="center">经销商id</th>
                <th width="100" align="center">经销商会员名</th>
                <th width="120" align="center">所属区域</th>
                <th width="50" align="center">加入时间</th>
                <th width="50" align="center">店铺地址</th>
                <th width="50" align="center">类型</th>
                <th width="150" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="goodsList" item="item" }
            <tr data-id="{$item.id}">
                <td style="padding-left: 15px;">{$item.id}</td>
                <td style="padding-left: 15px;">{$item.nickName}</td>
                <td align="center">{$item.area}</td>
                <td align="center">{$item.createTime|date='Y-m-d', ###}</td>
                <td align="center">{$item.address}</td>
                <td align="center">{if condition="$item.type eq 0"}实体{else/}电商{/if}</td>
                <td align="center">
                    <a class="btn btn-green" href="{:url('shopkeeper/view', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="900" data-height="600"><span>查看</span></a>
                    {if condition="$item.isBlocked eq 0"}
                    | <a class="btn btn-red" href="{:url('shopkeeper/block', ['id' => $item['id'], 'act' => 'block'])}" data-toggle="doajax" data-confirm-msg="确定要终止该经销商吗？"><span>终止</span></a>
                    {else/}
                    | <a class="btn btn-red" href="{:url('shopkeeper/block', ['id' => $item['id'], 'act' => 'recovery'])}" data-toggle="doajax" data-confirm-msg="确定要恢复该经销商吗？"><span>恢复</span></a>
                    {/if}
                </td>
            </tr>
            {/foreach}
            <tr><td colspan="7"></td></tr>
            <tr>
                <td colspan="7" class="text-left">&nbsp;&nbsp;<i class="fa fa-warning red"></i> 经销商冻结将停止前台页面相关推荐展示,请谨慎操作！</td>
            </tr>
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