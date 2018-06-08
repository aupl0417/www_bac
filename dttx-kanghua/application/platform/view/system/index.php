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
<div class="bjui-pageContent tableContent">
    <form action="{:url('')}" id="j_custom_form" data-toggle="validate" data-alertmsg="false">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label for="name" class="control-label x120">项目名称：</label>
                    <label>{$data.pl_name}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="control-label x120">所属公司：</label>
                    <label>{$data.pl_company_name}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="control-label x120">公司联系方式：</label>
                    <label>{$data.pl_contact}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="control-label x120">管理员大唐账号：</label>
                    <label>{$data.pl_dttx_nick}</label>
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
                    <div style="display: inline-block; vertical-align: middle;"><input type="hidden" name="plid" value="{$data.pl_id}">
                        <textarea name="content" id="content" class="j-content" style="width: 670px;" data-toggle="kindeditor" placeholder="自定义编辑框，可以上传图片" data-minheight="200">{$data.pl_content}</textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin-left: 600px;margin-top: 20px;">
                        <button type="submit" class="btn-default btn-nm"> 保存修改 </button>
                        <button type="button" class="btn-close btn-nm"> 关闭 </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>