<div class="bjui-pageContent tableContent">
    <form action="{:url('')}" id="j_custom_form" data-toggle="validate" data-alertmsg="false">
        <input type="hidden" name="id" value="{$id}">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label for="os_deliver_name" class="control-label x90">配送方式：</label>
                    <input type="text" name="os_deliver_name" data-rule="required;" size="30" value="">
                </td>
            </tr>

            <tr>
                <td>
                    <label for="os_deliver_num" class="control-label x90">物流号码：</label>
                    <input type="text" name="os_deliver_num" data-rule="required;" size="30" value="">
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