<div class="bjui-pageContent">
    <table class="table table-condensed table-hover">
        <tbody>
        <tr>
            <td>
                <label for="level" class="control-label x90">渠道等级：</label>
                <label for="name" class="x120">{$list.level}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for="name" class="control-label x90">渠道名称：</label>
                <label for="name" class="x120">{$list.name}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label for="weight" class="control-label x90">职能说明：</label>
                <textarea type="text" name="description" rows="5" disabled cols="30" placeholder="职能说明，200字内" data-rule="required" value="{$list.name}">{$list.name}</textarea>
            </td>
        </tr>
        <tr>
            <td>
                <label for="name" class="control-label x90">默认角色：</label>
                <select name="roles" id="roles" disabled="disabled" data-width="200" data-toggle="selectpicker" data-rule="required;number">
                    <option value="all">--角色--</option>
                    {volist name='roleList' id='vo'}
                    <option value="{$vo.id}" {if condition="$vo.id eq $list.roleIds"}selected="selected"{/if}>{$vo.name}</option>
                    {/volist}
                </select>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
    </ul>
</div>