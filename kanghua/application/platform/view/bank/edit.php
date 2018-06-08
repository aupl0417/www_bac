
<div class="bjui-pageContent">
    <form action="{:url('')}" class="pageForm" data-toggle="validate">
        <input type="hidden" name="id" value="{$id}">
        <table class="table table-condensed table-hover">
            <tbody>
                <tr>
                    <td>
                        <label for="name" class="control-label x90">银行名称：</label>
                        <input type="text" name="name" data-rule="required;length[4~]" size="20" value="{$bank.bank_name}">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="" class="control-label x85">状态：</label>
                        <input type="radio" name="state" class="shopType" data-toggle="icheck" value="1" data-rule="checked" checked data-label="启用&nbsp;&nbsp;">
                        <input type="radio" name="state" class="shopType" data-toggle="icheck" value="0" {if condition='$bank.bank_enabled eq 0'}data-rule="checked" checked{/if} data-label="禁用">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="" class="control-label x85">类型：</label>
                        <input type="radio" name="type" class="shopType" data-toggle="icheck" value="0" data-rule="checked" checked data-label="银行&nbsp;&nbsp;">
                        <input type="radio" name="type" class="shopType" data-toggle="icheck" value="1"  {if condition='$bank.bank_type eq 1'}data-rule="checked" checked{/if} data-label="第三方支付平台">
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