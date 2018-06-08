<div class="bjui-pageContent">
    <form action="{:url('cashout/settlement')}" class="pageForm" data-toggle="validate">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label for="j_dialog_tel" class="control-label x90">结算备注：</label>
                    <input type="hidden" name="id" value="{$co_caid}">
                    <textarea name="message" id="" cols="40"  rows="5" data-rule=""  data-toggle="autoheight"></textarea>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
        <li><button type="submit" class="btn-default">确认</button></li>
    </ul>
</div>