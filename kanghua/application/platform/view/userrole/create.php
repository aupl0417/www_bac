<div class="bjui-pageContent">
    <form action="{:url('userrole/create')}" class="pageForm" data-toggle="validate">
        <table class="table table-condensed table-hover">
            <tbody>
            {if $admin_state}
                <tr>
                    <td>
                        <label for="j_dialog_operation" class="control-label x90">所属项目：</label>
                        {notempty name="platformdata"}
                        <select name="platformId" data-toggle="selectpicker" data-rule="required">
                            <option value="">请选择项目</option>
                            {foreach name="platformdata" item="vo"}
                            <option value="{$vo.pl_id}">{$vo.pl_name}</option>
                            {/foreach}
                        </select>&nbsp;
                        {/notempty}
                    </td>
                </tr>
            {/if}
                <tr>
                    <td>
                        <label for="j_dialog_operation" class="control-label x90">角色名：</label>
                        <input type="text" name="rolename" data-rule="required" size="20" value=""  class="required">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="j_dialog_code" class="control-label x90">描述：</label>
                        <textarea name="description" cols="20" class="form-control" data-rule="required" style="width: 337px; margin: 0px; height: 50px;"></textarea>
                    </td>
                </tr>
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