<div class="bjui-pageContent">
    <form action="{notempty name="Detail"}{:url('System/adminEdit?userid='.$Detail->userid)}{else /}{:url('')}{/notempty}" class="pageForm" data-toggle="validate">
        <table class="table table-condensed table-hover">
            <tbody>
                <!-- <tr>
                    <td colspan="2" align="center"><h3>* 我是一个弹出窗口</h3></td>
                </tr> -->
                <tr>
                    <td>
                        <label for="j_dialog_name" class="control-label x90">用户名：</label>
                        {empty name="Detail"}
                        <input type="text" name="username" data-rule="required;username;remote[get:{:url('System/ajax_checkUsername')}]" size="20" value="" class="required">初始密码为：md1q2w3e4r，请及时修改密码！
                        {else /}
                        <input type="text" disabled size="15" value="{$Detail.username}">
                        {/empty}
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="j_dialog_tel" class="control-label x90">昵称：</label>
                        <input type="text" name="nickname" size="15" value="{$Detail.nickname|default=""}">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="j_dialog_profession" class="control-label x90">角色：</label>
                        <select data-toggle="selectpicker" name="roleid">
                            {foreach name="roles" key="key" item="role"}
                            <option value="{$key}" {notempty name="Detail"}{eq name="Detail.roleid" value="$key"}selected{/eq}{/notempty}>{$role.rolename}</option>
                            {/foreach}
                        </select>
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