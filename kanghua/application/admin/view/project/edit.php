<script type="text/javascript">
    function pic_upload_success(file, data) {
        var json = $.parseJSON(data)
        $(this).bjuiajax('ajaxDone', json)
        if (json[BJUI.keys.statusCode] == BJUI.statusCode.ok) {
            $('#image').val(json.filename).trigger('validate')
            $('#j_custom_span_pic').html('<img src="'+ json.filename +'" width="100" />')
        }
    }

    $(function () {
        $('.model').live('blur', function () {
            $(this).attr('data-rule', 'required');
        });
    });

</script>
<div class="bjui-pageContent">
    <form action="{:url('Project/edit')}" id="j_form_form" class="pageForm" data-toggle="validate">
        <div style="margin:15px auto 0;">

                    <table class="table table-hover" width="100%">
                        <tbody>
                        <tr>
                            <td>
                                <label for="j_custom_name" class="control-label x120">项目名称：</label>
                                <input type="hidden" name="plid" value="{$data.pl_id|default=''}">
                                <input type="text" name="name" id="j_custom_name"  value="{$data.pl_name|default=''}" placeholder="项目名称" data-rule="required" data-title="选择客户名称"    class="required">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="j_custom_cname" class="control-label x120">所属公司：</label>

                                <input type="text" name="companyname" id="j_custom_cname"  value="{$data.pl_company_name|default=''}" placeholder="所属公司" data-rule="required"     class="required">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="j_custom_contact" class="control-label x120">联系方式：</label>
                                <input type="text" name="contact" id="j_custom_contact"  value="{$data.pl_contact|default=''}" placeholder="联系方式" data-rule="required"  class="required">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="j_custom_dttxnick" class="control-label x120">大唐账号：</label>
                                <input type="text" name="dttxnick" id="j_custom_dttxnick"  value="{$data.pl_dttx_nick|default=''}" placeholder="大唐账号" data-rule="required;" readonly  class="required"> 此账号填入后不可修改，请确认
                                <input type="hidden" name="servicenick" id="j_custom_servicenick"  value="{$data.pl_tech_nick|default=''}" placeholder="服务费账号"  class="required">
                                <input type="hidden" name="serviceratio" id="j_custom_serviceratio"  value="{$data.pl_tech_ratio|default=''}" placeholder="服务费比例" data-rule="required" class="required">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="" class="control-label x120">是否启用：</label>
                                <input type="radio" name="status" id="j_custom_sex1" data-toggle="icheck" value="1" checked data-rule="checked" data-label="是&nbsp;&nbsp;">
                                <input type="radio" name="status" id="j_custom_sex2" data-toggle="icheck" value="0" data-label="否" {notempty name="$data.pl_states"}{if $data.pl_states ==0 } checked {/if}{/empty}>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label class="control-label x120">项目封面图：</label>
                                <div style="display: inline-block; vertical-align: middle;">
                                    <span id="j_custom_span_pic">{notempty name='data.pl_image'}<img src="{$data.pl_image|default=''}" width="120" />{/notempty}</span>
                                    <div id="j_custom_pic_up" data-toggle="upload" data-uploader="{:url('common/ajax/ajax_upload')}"
                                         data-file-size-limit="1024000000"
                                         data-file-type-exts="*.jpg;*.png;*.gif;*.mpg"
                                         data-file-obj-name="uploads"
                                         data-auto="true"
                                         data-on-upload-success="pic_upload_success"
                                         data-icon="cloud-upload"></div>
                                    <input type="hidden" name="image" value="{$data.pl_image|default=''}" id="image">

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="content" class="control-label x120">项目简介：</label>
                                <div style="display: inline-block; vertical-align: middle;"><input type="hidden" name="plid" value="{$data.pl_id}">
                                    <textarea name="description" id="description" class="j-content" style="width: 670px;">{$data.pl_description}</textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="content" class="control-label x120">项目介绍：</label>
                                <div style="display: inline-block; vertical-align: middle;">
                                    <textarea name="content" id="content" class="j-content" style="width: 670px;" data-toggle="kindeditor" placeholder="自定义编辑框，可以上传图片" data-minheight="200">{$data.pl_content}</textarea>
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