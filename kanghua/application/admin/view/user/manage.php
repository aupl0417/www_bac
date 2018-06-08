<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="${param.orderField}">
        <input type="hidden" name="orderDirection" value="${param.orderDirection}">
        <div class="bjui-searchBar">
            <label>用户名：</label><input type="text" id="username" value="{$username}" name="username" class="form-control" size="10">&nbsp;
            <label>角色:</label>
            <select name="roleid" data-toggle="selectpicker">
                <option value="">所有角色</option>
                {foreach name="roles" key="key" item="role"}
                <option value="{$key}" {eq name="roleid" value="$key"}selected{/eq} >{$role.rolename}</option> 
                {/foreach}
            </select>&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
                <button type="button" class="btn-green" data-url="{:url('System/adminAdd')}" data-toggle="dialog" mask="true" data-width="520" data-height="188" data-icon="plus">添加管理员</button>&nbsp;
                <button type="button" class="btn-blue" data-url="{:url('System/adminDelete')}?userid={#bjui-selected}" data-toggle="doajax" data-confirm-msg="确定要删除选中项吗？" data-icon="remove" title="可以在控制台(network)查看被删除ID">删除选中行</button>&nbsp;
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table class="table table-bordered table-hover table-striped table-top" data-selected-multi="true">
        <thead>
            <tr>
                <th width="50" data-order-field="userid">ID</th>
                <th>用户名</th>
                <th width="120">角色</th>
                <th width="120">真实姓名</th>
                <th width="120" align="center">最后登录IP</th>
                <th width="120" align="center">最后登录时间</th>
                <th align="center" width="250">管理</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="page_list" item="item" }
            <tr data-id="{$item.userid}">
                <td>{$item.userid}</td>
                <td>{$item.username}</td>
                <td>{$roles[$item['roleid']]['rolename']}</td>
                <td>{$item.nickname}</td>
                <td align="center">{$item.lastloginip}</td>
                <td align="center">{$item.lastlogintime|date="Y-m-d",###}</td>
                <td align="center">
                    
                    {gt name="item.userid" value="1"}
                        <a class="btn btn-green" href="{:url('System/adminEdit?userid='.$item[userid])}" data-toggle="dialog" mask="true" data-width="520" data-height="188"><span>修改</span></a> | <a title="密码将重置为1q2w3e4" class="btn btn-green" href="{:url('System/adminResetPassword?userid='.$item[userid])}" data-toggle="doajax" data-confirm-msg="确定要重置密码吗？"><span>重置密码</span></a> | <a class="btn btn-red" href="{:url('System/adminDelete?userid='.$item[userid])}" data-toggle="doajax" data-confirm-msg="确定要删除该管理员吗？"><span>删除</span></a>
                    {else /}
                        <button class="btn btn-default" ><span>修改</span></button> | <button class="btn btn-default" ><span>重置密码</span></button> | <button class="btn btn-default"><span>删除</span></button>
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
        <span>&nbsp;条，共 {$page.totalCount} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$page.totalCount}" data-page-size="{$page.pageSize}" data-page-current="{$page.pageCurrent}"></div>
</div>