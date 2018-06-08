<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="{$input.orderField|default='up_id'}">
        <input type="hidden" name="orderDirection" value="{$input.orderDirection|default='asc'}">
        <div class="bjui-searchBar">
            <label>用户名：</label><input type="text" id="nick" value="{$input.nick|default=''}" name="nick" class="form-control" size="10">&nbsp;
            <label>真实姓名：</label><input type="text" id="name" value="{$input.name|default=''}" name="name" class="form-control" size="10">&nbsp;
            <label>角色:</label>
            <select name="roleid" data-toggle="selectpicker">
                <option value="">所有角色</option>
                {foreach name='roles' item='vo'}
                <option value="{$vo.ur_roleid}">{$vo.ur_rolename}</option>
                {/foreach}
            </select>&nbsp;
            {if $admin_state}
                {notempty name="platformdata"}
                    <label>平台选择:</label>
                    <select name="platformId" data-toggle="selectpicker" data-rule="required">
                        <option value="">所有角色</option>
                        {foreach name="platformdata" item="vo"}
                        <option {eq name="$input.platformId" value="$vo.pl_id"}selected{/eq} value="{$vo.pl_id}">{$vo.pl_name}</option>
                        {/foreach}
                    </select>&nbsp;
                {/notempty}
            {/if}
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">

            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="50" data-order-field="up_id">ID</th>
                <th align="center">用户名</th>
                <th width="120" align="center">真实姓名</th>
                <th width="120" align="center">角色</th>
                <th width="120" align="center">地区</th>
                <th width="120" align="center" data-order-field="lastlogintime">激活时间</th>
                <th width="50" data-order-field="status">开启状态</th>
                <th align="center" width="250">管理</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="$data.list" item="item" }
            <tr data-id="{$item.u_id}">
                <td align="center">{$item.up_id}</td>
                <td align="center">{$item.u_nick|default=""}</td>
                <td align="center">{$item.u_name|default=""}</td>
                <td align="center">{$item.ur_rolename|default=""}</td>
                <td align="center">{$item.provinceName|default=""}-{$item.cityName|default=""}</td>
                <td align="center">{$item.up_create_time|date='Y-m-d H:i:s',###}</td>
                <td align="center">{eq name="item.up_states" value="1"} <label  class="label label-success">启用</label>        {else /}<label  class="label label-warning">禁用</label>{/eq}</td>
                <td align="center">
                    {gt name="item.u_id" value="1"}
                    <a class="btn btn-green" href="{:url('userlist/edit',['id'=>$item.up_id])}" data-toggle="dialog" mask="true" data-width="550" data-height="200"><span>修改</span></a>  |
                    {eq name="item.up_states" value="0"}
                    <a class="btn btn-green" href="{:url('userlist/change',['state'=>1,'id'=>$item.up_id])}" data-toggle="doajax" data-confirm-msg="确定启用该用户！"><span>启用</span></a>
                    {else /}
                    <a class="btn btn-red" href="{:url('userlist/change',['state'=>0,'id'=>$item.up_id])}" data-toggle="doajax" data-confirm-msg="确定禁用该用户！"><span>禁用</span></a>
                    {/eq}
                    {else /}
                        <button class="btn btn-default" ><span>修改</span></button> | <button class="btn btn-default" ><span>禁用</span></button>
                    {/gt}
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
        <span>&nbsp;条，共 {$data.count|default=""} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$data.count|default=""}" data-page-size="{$input.pageSize|default=""}" data-page-current="{$input.pageCurrent|default=""}"></div>
</div>