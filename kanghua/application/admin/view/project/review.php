<div class="bjui-pageContent">
    <form action="{:url('Project/review')}" id="j_form_form" class="pageForm" data-toggle="validate">
        <div style="margin:15px auto 0;">
            <table class="table table-hover" width="100%">
                <tbody>
                <tr>
                    <td>
                        <label for="j_product_name" class="control-label x120">产品名称：</label>
                        <input type="hidden" name="id" value="{$data.in_id|default=''}">
                        <label>{$data.in_product_name|default=''}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="j_custom_cname" class="control-label x120">所属公司：</label>
                        <label for="">{$data.in_company_name|default=''}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="j_custom_contact" class="control-label x120">联系方式：</label>
                        <label for="">{$data.in_mobile|default=''}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="j_custom_contact" class="control-label x120">大唐账号：</label>
                        <label for="">{$data.in_dttx_nick|default=''}</label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="status" class="control-label x120">审核状态<label class="btn-red">*</label></label>
                        <select name="status" data-rule="required" data-width="100" data-toggle="selectpicker">
                            <option value="">--请选择--</option>
                            <option value="1">--审核通过--</option>
                            <option value="-1">--审核拒绝--</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="reason" class="control-label x120">审核原因</label>
                        <div style="display: inline-block; vertical-align: middle;">
                            <textarea type="text" rows="2" cols="71" name="reason" placeholder="请输入审核原因，200字内" value=""></textarea>
                        </div>
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