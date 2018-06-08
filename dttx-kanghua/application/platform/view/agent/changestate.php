<div class="bjui-pageContent">
    <form action="{:url('')}" class="pageForm" data-toggle="validate">
        <table class="table table-condensed table-hover">
            <input type="hidden" name="id" value="{$id}">
            <tbody>
                <tr>
                    <td>
                        <label for="state" class="control-label x90">变更状态：</label>
                        <select name="state" id="state" data-width="100" data-toggle="selectpicker" data-rule="required">
                            <option value="">--状态--</option>
                            {if condition="$state eq 'normal'"}
                                <option value="freeze" {if condition="$state eq 'freeze'"}selected="selected"{/if}>冻结</option>
                            {elseif condition="$state eq 'freeze'"/}
                                <option value="normal" {if condition="$state eq 'normal'"}selected="selected"{/if}>解冻</option>
                            {/if}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="weight" class="control-label x90">变更理由：</label>
                        <textarea type="text" name="reason" rows="5" cols="30" placeholder="请输入变更理由，200字以内" data-rule="required" value=""></textarea>
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