<div class="bjui-pageContent">
    <form action="{:url('user/edit')}" class="pageForm" data-toggle="validate">
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
                            <option value="0">未指定</option>
                            {foreach name='roles' item='vo'}
                                <option {eq name="$data.up_roleid" value="$vo.ur_roleid"}selected{/eq} value="{$vo.ur_roleid}">{$vo.ur_rolename}</option>
                            {/foreach}
                        </select>&nbsp;
                    </td>
                </tr>
<!--                <tr>-->
<!--                    <td>-->
<!--                        <label for="j_dialog_profession" class="control-label x90">代理级别：</label>-->
<!---->
<!--                        <select name="agent_level" data-toggle="selectpicker">-->
<!--                            <option value="0">未代理</option>-->
<!--                            {foreach name='chanels' item='vo'}-->
<!--                                <option {eq name="$data.up_user_agent_level" value="$vo.c_id"}selected{/eq} value="{$vo.c_id}">{$vo.c_name}</option>-->
<!--                            {/foreach}-->
<!--                        </select>&nbsp;-->
<!--                    </td>-->
<!--                </tr>-->
    <tr>
                    <td>
                        <label for="j_dialog_profession" class="control-label x90">用户级别：</label>

                        <select name="userlevel" data-toggle="selectpicker">
                            <option value="0">普通会员</option>
                            {foreach name='userlevels' item='vo'}
                                <option {eq name="$data.up_user_level_id" value="$vo.ul_id"}selected{/eq} value="{$vo.ul_id}">{$vo.ul_name}</option>
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