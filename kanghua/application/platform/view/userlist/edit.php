<div class="bjui-pageContent">
    <form action="{:url('userlist/edit')}" class="pageForm" data-toggle="validate">
        <table class="table table-condensed table-hover">
            <tbody>
                <tr>
                    <td>
                        <label for="j_dialog_name" class="control-label x90">用户名：</label>
                        <label class="">{$data.u_nick}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="j_dialog_profession" class="control-label x90">角色：</label>
                        <input type="hidden" name="rid" value="{$data.up_id}">
                        <select name="roleid" data-toggle="selectpicker">
                            {foreach name='roles' item='vo'}
                                <option {eq name="$data.up_roleid" value="$vo.ur_roleid"}selected{/eq} value="{$vo.ur_roleid}">{$vo.ur_rolename}</option>
                            {/foreach}
                        </select>&nbsp;
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