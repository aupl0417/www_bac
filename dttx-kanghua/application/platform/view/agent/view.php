<div class="bjui-pageContent">
    <form action="{:url('')}" class="pageForm" data-toggle="validate">
        <table class="table table-condensed table-hover">
            <tbody>
                <tr>
                    <td>
                        <label for="provinceId" class="control-label x90">代理商区域：</label>
                        <select name="provinceId" id="provinceId" disabled="disabled" data-width="100" data-toggle="selectpicker" data-rule="required">
                            <option value="">{$agent.province}</option>
                        </select>
                        <select name="cityId" id="cityId" disabled="disabled" data-width="100" data-toggle="selectpicker">
                            <option value="">{$agent.city}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="level" class="control-label x90">代理商级别：</label>
                        <label for="name" class="x120">{$agent.levelName}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="nickname" class="control-label x90">代理商会员名：</label>
                        <label for="name" class="x120">{$agent.nickName}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="state" class="control-label x90">状态：</label>
                        <label for="name" class="x120">{if condition="$agent.state eq 'normal'"}正常{elseif condition="$agent.state eq 'freeze'"/}冻结{else/}终止{/if}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="weight" class="control-label x90">变更理由：</label>
                        <textarea type="text" name="reason" disabled="disabled" rows="5" cols="30" placeholder="请输入变更理由，200字以内" data-rule="required" value="">{$agent.reason}</textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
    </ul>
</div>