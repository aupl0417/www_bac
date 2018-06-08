
<div class="bjui-pageContent">
    <table class="table table-condensed table-hover">
        <tbody>
            <tr>
                <td>
                    <label for="name" class="control-label x90">仓库名称：</label>
                    <input type="text" name="name" data-rule="required" size="30" value="{$data.name}">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="description" class="control-label x90">仓库说明：</label>
                    <input type="text" name="description" size="30" value="{$data.description}">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="channel" class="control-label x90">售卖渠道：</label>
                    <textarea type="text" name="channel" rows="5" cols="30" data-rule="required" value="{$data.channel}">{$data.channel}</textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="channel" class="control-label x90">创建时间：</label>
                    <input type="text" name="description" size="30" value="{$data.createTime|date='Y-m-d H:i:s', ###}">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="channel" class="control-label x90">创建人：</label>
                    <input type="text" name="description" size="30" value="{$data.nick}">
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