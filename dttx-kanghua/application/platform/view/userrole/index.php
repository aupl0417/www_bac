<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="{$input.orderField|default='ur_roleid'}">
        <input type="hidden" name="orderDirection" value="{$input.orderDirection|default='asc'}">
        <div class="bjui-searchBar">
            <label>角色名称：</label><input type="text" id="nick" value="{$input.rolename|default=''}" name="rolename" class="form-control" size="10">&nbsp
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
                <a href="{:url('userrole/create')}" class="btn btn-green" data-toggle="dialog" data-width="520" data-height="240" data-mask="true">添加角色</a>
            </div>
        </div>
    </form>

</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="50" align="center">ID</th>
                <th width="150">角色名</th>
                <th>描述</th>
                <th width="120" align="center">启用</th>
                <th width="200" align="center">管理</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="$data.list" item="item" }
            <tr data-id="{$item.ur_roleid}">
                <td>{$item.ur_roleid}</td>
                <td>{$item.ur_rolename}</td>
                <td>{$item.ur_description}</td>
                <td align="center">
                    {eq name="item.ur_allowedit" value="1"}
                    {eq name="item.ur_status" value="1"}
                    <a class="btn btn-green" data-confirm-msg="确认禁用该角色吗？" data-toggle="doajax" href="{:url('userrole/change',['id'=>$item['ur_roleid'],'state'=>0])}">禁用</a>{else /}
                    <a class="btn btn-red" data-confirm-msg="确认启用该角色吗？" data-toggle="doajax" href="{:url('userrole/change',['id'=>$item['ur_roleid'],'state'=>1])}">启用</a>{/eq}
                    {else /}
                    {eq name="item.ur_status" value="1"}
                    <a class="btn disabled">禁用</a>
                    {else /}
                    <a class="btn disabled">启用</a>{/eq}
                    {/eq}
                </td>
                <td align="center">
                    {eq name="item.ur_allowedit" value="1"}
                        <a class="btn btn-green" href="{:url('userrole/rolesetting?roleid='.$item['ur_roleid'])}" data-toggle="dialog" mask="true" data-width="500" data-height="650"><span>权限设置</span></a> | <a class="btn btn-green" href="{:url('userrole/edit?id='.$item['ur_roleid'])}" data-toggle="dialog" mask="true" data-width="520" data-height="188"><span>修改</span></a> | <a class="btn btn-red" href="{:url('userrole/remove?id='.$item['ur_roleid'])}" data-toggle="doajax" data-confirm-msg="确定要删除该角色吗？"><span>删除</span></a>
                    {else /}
                        <button class="btn btn-default disabled"><span>权限设置</span></button> | <button  class="btn btn-default disabled"><span>修改</span></button> | <button class="btn btn-default disabled"><span>删除</span></button>
                    {/eq}
                </td>
            </tr>
            {/foreach}
            <tr><td colspan="5"></td></tr>
            <tr>
                <td colspan="5" class="text-left">     &nbsp;&nbsp;<i class="fa fa-warning red"></i> 系统默认角色不允许修改，如需调整请自行添加角色，分配权限。</td>
            </tr>
        </tbody>



    </table>

</div>
<div class="bjui-pageFooter">

<!--    <div class="pages">-->
<!--        <span>每页&nbsp;</span>-->
<!--        <div class="selectPagesize">-->
<!--            <select data-toggle="selectpicker" data-toggle-change="changepagesize">-->
<!--                <option value="30">30</option>-->
<!--                <option value="60">60</option>-->
<!--                <option value="120">120</option>-->
<!--                <option value="150">150</option>-->
<!--            </select>-->
<!--        </div>-->
<!--        <span>&nbsp;条，共 {$data.count} 条</span>-->
<!--    </div>-->
<!--    <div class="pagination-box" data-toggle="pagination" data-total="{$data.count}" data-page-size="{$input.pageSize}" data-page-current="{$input.pageCurrent}"></div>-->
</div>