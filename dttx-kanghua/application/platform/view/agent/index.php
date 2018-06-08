<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$page.pageSize}">
        <input type="hidden" name="pageCurrent" value="{$page.pageCurrent}">
        <div class="bjui-searchBar">
            <label>代理商编号：</label><input type="text" id="id" value="{$id|default=''}" name="id" class="form-control" size="10">&nbsp;
            <label>代理商会员名：</label><input type="text" id="name" value="{$name|default=''}" name="name" class="form-control" size="10">&nbsp;&nbsp;&nbsp;
            <label>所属区域：</label>
            <select name="provinceCode" id="provinceCode" data-width="100" data-toggle="selectpicker" data-nextselect="#cityCode" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                <option value="all">&nbsp;&nbsp;--省市--</option>
                {foreach name="area" item="vo" }
                <option value="{$vo.a_id}" {if condition="$provinceId eq $vo.a_id"}selected="selected"{/if}>{$vo.a_name}</option>
                {/foreach}
            </select>
            <select name="cityCode" id="cityCode"  data-width="100" data-toggle="selectpicker" data-emptytxt="--城市--"">
                <option value="all">&nbsp;&nbsp;--城市--</option>
            </select>
            <label>代理等级：</label>
            <select name="level" id="level" data-width="100" data-toggle="selectpicker">
                <option value="">--请选择--</option>
                {foreach name="levelList" item="vo" }
                <option value="{$vo.id}" {if condition="$vo.id == $level"}selected="selected"{/if}>{$vo.name}</option>
                {/foreach}
            </select>
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <button type="button" class="btn-green" data-url="{:url('Agent/create')}" data-toggle="dialog" mask="true" data-width="600" data-height="300" data-icon="plus">添加代理商</button>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="30" align="center">代理编号</th>
                <th width="50" align="center">代理商等级</th>
                <th width="50" align="center">代理商会员名</th>
                <th width="50" align="center">姓名</th>
                <th width="120" align="center">所属区域</th>
                <th width="50" align="center">成为代理时间</th>
                <th width="50" align="center">状态</th>
                <th width="150" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
        {notempty name='agentList'}
            {foreach name="agentList" item="item" }
            <tr data-id="{$item.id}">
                <td style="padding-left: 15px;">{$item.id}</td>
                <td style="padding-left: 15px;">{$item.levelName}</td>
                <td style="padding-left: 15px;">{$item.nickName}</td>
                <td align="center">{$item.trueName}</td>
                <td align="center">{$item.area}</td>
                <td align="center">{$item.createTime|date='Y-m-d', ###}</td>
                <td align="center">{if condition="$item.state eq 'normal'"}<label for="" class="label label-success">正常</label>{elseif condition="$item.state eq 'freeze'"/}<label for="" class="label label-danger">冻结</label>{else/}终止{/if}</td>
                <td align="center">
                    {if condition="$item.state neq 'blocked'"}
                    <a class="btn btn-green" href="{:url('Agent/changeState', ['id' => $item['id'], 'state' => $item['state']])}" data-toggle="dialog" mask="true" data-width="550" data-height="250"><span>变更状态</span></a>
                    {else/}
                    <a class="btn btn-green" href="{:url('Agent/view', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="550" data-height="400"><span>查看</span></a>
                    {/if}
                </td>
            </tr>
            {/foreach}
        <tr><td colspan="8"></td></tr>
        <tr>
            <td colspan="8" class="text-left">&nbsp;&nbsp;<i class="fa fa-warning red"></i> 代理冻结将停止计算后续订单分润收益,请谨慎操作！</td>
        </tr>
        {else /}
        <tr>
            <td colspan="8">暂无记录！</td>
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