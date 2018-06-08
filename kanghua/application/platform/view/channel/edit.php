<div class="bjui-pageContent">
    <form action="{:url('')}" class="pageForm" data-toggle="validate">
        <input type="hidden" name="id" value="{$list.id}">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label for="name" class="control-label x90">渠道等级：</label>
                    <select name="level" id="level"  data-width="200" data-toggle="selectpicker" data-rule="required;number">
                        <option value="all">--等级--</option>
                        {volist name='level' id='vo' key='k'}
                        <option value="{$k}" {if condition="$vo eq $list.level"}selected="selected"{/if}>{$vo}</option>
                        {/volist}
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name" class="control-label x90">渠道名称：</label>
                    <input type="text" id="name"  name="name" data-rule="required;length[4~];" size="20" value="{$list.name}">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="weight" class="control-label x90">职能说明：</label>
                    <textarea type="text" name="description" rows="5" cols="30" placeholder="职能说明，200字内" data-rule="required" value="{$list.description}">{$list.description}</textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name" class="control-label x90">默认角色：</label>
                    <select name="roles" id="roles"  data-width="200" data-toggle="selectpicker" data-rule="required;number">
                        <option value="all">--角色--</option>
                        {volist name='roleList' id='vo'}
                        <option value="{$vo.id}" {if condition="$vo.id eq $list.roleIds"}selected="selected"{/if}>{$vo.name}</option>
                        {/volist}
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