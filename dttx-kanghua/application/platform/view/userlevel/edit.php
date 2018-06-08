<div class="bjui-pageContent">
    <form action="{:url('platform/userlevel/edit')}" id="j_form_form" class="pageForm" data-toggle="validate">
        <div style="margin:15px auto 0;">

                    <table class="table table-hover" width="100%">
                        <tbody>
                        <tr>
                            <td>
                                <label for="j_custom_level" class="control-label x120">会员等级编号：</label>
                                <input type="hidden" name="ul_id" value="{$data.ul_id}">
                                <input type="text" name="userNo" id="j_custom_level"  value="{$data.ul_user_no}" placeholder="会员等级编号" data-rule="required"  size="30"    class="required">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="j_custom_name" class="control-label x120">会员等级名称：</label>
                                <input type="text" name="name" id="j_custom_name"  value="{$data.ul_name}" placeholder="会员等级名称" data-rule="required" data-title="选择客户名称" size="30"  class="required">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="j_custom_name" class="control-label x120">升级价格：</label>
                                <input type="text" name="price" id="j_custom_price"  value="{$data.ul_money|default='0.00'}" placeholder="升级价格" data-rule="required" data-title="升级价格" size="30"  class="required">
                            </td>
                        </tr>
                        <tr style="visibility: hidden">
                            <td>
                                <label for="j_custom_ratio" class="control-label x120">分润比例：</label>
                                <input type="text" name="ratio" id="j_custom_ratio"  value="{$data.ul_ratio}" placeholder="请填入分润比例" data-rule="required" data-title="请填入数值" size="6"  class="required|number"><i class="text-danger">%</i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="j_custom_upgrade" class="control-label x120">会员升级要求：</label>
                                <textarea type="text" name="upgrade" id="j_custom_upgrade"  placeholder="会员升级要求" data-rule="required"  class="required" cols="30" rows="3" data-toggle="autoheight">{$data.ul_upgrade_require}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="j_custom_mark" class="control-label x120">会员福利：</label>
                                <textarea type="text" name="mark" id="j_custom_mark"  placeholder="备注" data-rule="required" class="required" cols="30" rows="3" data-toggle="autoheight">{$data.ul_level_mark}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="" class="control-label x120">是否启用：</label>
                                <input type="radio" name="status" id="j_custom_sex1" data-toggle="icheck" value="1" checked data-rule="checked" data-label="是&nbsp;&nbsp;">
                                <input type="radio" name="status" id="j_custom_sex2" data-toggle="icheck" {if ($data.ul_status=='0') }checked{/if} value="0" data-label="否">

                            </td>
                        </tr>
                        </tbody>
                    </table>
        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消退出</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存确认</button></li>
    </ul>
</div>