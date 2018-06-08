<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('platform/userlevel/index')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="{$inut.orderField|default='ul_id'}">
        <input type="hidden" name="orderDirection" value="{$inut.orderDirection|default='asc'}">
        <div class="bjui-searchBar">
            <label>等级名称：</label><input type="text" id="name" value="{$input.name|default=''}" name="name" class="form-control" size="10">&nbsp;
            {if $admin_state}
            {notempty name="platformdata"}
            <label>项目选择:</label>
            <select name="platformId" data-toggle="selectpicker" data-rule="required">
                <option value="">请选择项目</option>
                {foreach name="platformdata" item="vo"}
                <option {eq name="$input.platformId" value="$vo.pl_id"}selected{/eq} value="{$vo.pl_id}">{$vo.pl_name}</option>
                {/foreach}
            </select>&nbsp;
            {/notempty}
            {/if}
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
                {if !$admin_state}
                <button type="button" class="btn-green" data-url="{:url('platform/userlevel/create')}" data-toggle="dialog" mask="true" data-width="800" data-height="400" data-icon="plus">添加等级</button>&nbsp;
<!--                <button type="button" class="btn-blue" data-url="{:url('Project/remove')}?userid={#bjui-selected}" data-toggle="doajax" data-confirm-msg="确定要删除选中项吗？" data-icon="remove" title="可以在控制台(network)查看被删除ID">删除选中行</button>&nbsp;-->
            {/if}
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
        <tr>
            <th width="50" data-order-field="pl_id">ID</th>
            <th width="120">等级编号</th>
            <th width="140">等级名称</th>
            <th width="140">升级价格</th>
<!--            <th width="120">分润比例</th>-->
            <th>升级要求</th>
            <th>会员福利描述</th>
            <th width="80" align="center">状态</th>
            <th align="center" width="140">管理</th>
        </tr>
        </thead>
        <tbody>
        {empty name="data['list']"}
        <tr>
            <td colspan="8" align="center">暂无数据！</td>
        </tr>
        {else /}
        {volist name="data['list']" id='vo'}
        <tr>
            <td>{$vo.ul_id}</td>
            <td>{$vo.ul_user_no}</td>
            <td>{$vo.ul_name}</td>
            <td>{$vo.ul_money}</td>
<!--            <td>{$vo.ul_ratio}%</td>-->
            <td>{$vo.ul_upgrade_require}</td>
            <td>{$vo.ul_level_mark}</td>
            <td align="center" width="80">{eq name="$vo.ul_status" value='1'} <label class="label label-default">启用</label> {else /} <label class="label label-danger">禁用</label> {/eq}</td>
            <td align="center">  <a class="btn btn-green" href="{:url('platform/userlevel/edit',['id'=>$vo.ul_id])}" data-toggle="dialog" mask="true" data-width="800" data-height="450"><span>编辑</span></a>
                <a class="btn btn-red" href="{:url('platform/userlevel/read',['id'=>$vo.ul_id])}" data-toggle="dialog" mask="true" data-width="800" data-height="450" data-title="查看详情"><span>查看</span></a>
            </td>
        </tr>
        {/volist}
        {/empty}
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
        <span>&nbsp;条，共 {$data.count} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$data.count}" data-page-size="{$input.pageSize}" data-page-current="{$input.pageCurrent}"></div>
</div>