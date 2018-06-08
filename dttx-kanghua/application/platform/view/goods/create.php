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
                    <label for="name" class="control-label x90">商品名称：</label>
                    <input type="text" name="name" data-rule="required" size="30" value="">
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <label class="control-label x90">商品主图：</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        <span id="j_custom_span_pic"></span>
                        <div id="j_custom_pic_up" data-toggle="upload" data-uploader="{:url('common/ajax/ajax_upload')}"
                             data-file-size-limit="1024000000"
                             data-file-type-exts="*.jpg;*.png;*.gif;*.mpg"
                             data-file-obj-name="uploads"
                             data-auto="true"
                             data-on-upload-success="pic_upload_success"
                             data-icon="cloud-upload"></div>
                        <input type="hidden" name="image" value="" id="image">

                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="scoreReward" class="control-label x90">积分奖励：</label>
                    <select name="scoreReward" id="scoreReward" data-toggle="selectpicker" data-width="100" data-rule="required">
<!--                        <option value="200">200%</option>-->
                        <option value="100" selected="selected">100%</option>
<!--                        <option value="50">50%</option>-->
<!--                        <option value="25">25%</option>-->
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="cateId" class="control-label x90">商品分类：</label>
                    <select name="cateId" id="cateId" data-toggle="selectpicker" data-rule="required" data-width="200">
                        <option value="">请选择分类</option>
                        {foreach $cateList as $vo}
                        <option value="{$vo.id}">{$vo.name}</option>
                        {/foreach}
                    </select>
                    <button type="button" class="btn-green btn-sm" data-url="{:url('Category/create', ['tab' => 'goods'])}" data-toggle="dialog" mask="true" data-width="450" data-height="200" data-icon="plus">添加分类</button>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="control-label x85">是否上架：</label>
                    <input type="radio" name="isSale"  data-toggle="icheck" value="1" data-rule="checked" checked data-label="是&nbsp;&nbsp;">
                    <input type="radio" name="isSale"  data-toggle="icheck" value="0" data-label="否">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="channel" class="control-label x90">商品属性：</label>
                    <div style="display: inline-block; vertical-align: middle;">
                    <table id="tabledit1" class="table table-bordered table-hover table-striped" data-toggle="tabledit" data-initnum="1" data-action="{:url('Goods/addAttribute')}" data-single-noindex="true">
                        <thead>
                        <tr data-idname="customList[#index#].id">
                            <th title="型号" width="100px"><input type="text" class="model" name="customList[#index#][model]" placeholder="型号" value="" size="10"></th>
                            <!--<th title="规格" width="100px"><input type="text" name="customList[#index#][format]" data-rule="" placeholder="规格" value="" size="3"></th>
                            <th title="单位" width="100px"><input type="text" name="customList[#index#][unit]" data-rule="" placeholder="单位" value="" size="1"></th>-->
                            <th title="成本价格" width="100px"><input type="text" name="customList[#index#][cost]" data-rule="required;number" placeholder="0.00" value="" size="1"></th>
                            <th title="建议零售价" width="100px"><input type="text" name="customList[#index#][price]" data-rule="required;number" placeholder="0.00" value="" size="1"></th>
                            <th title="库存量" width="100px"><input type="text" name="customList[#index#][stock]" data-rule="required;number" placeholder="库存量" value="" size="1"></th>
                            <!--<th title="装箱规格" width="100px"><input type="text" name="customList[#index#][packFormat]" data-rule="" placeholder="装箱规格" value="" size="1"></th>-->
                            <th title="说明" width="380px"><textarea name="customList[#index#][instruction]" data-toggle="autoheight" placeholder="说明" cols="10" rows="1"></textarea></th>
                            <th title="" data-addtool="true" width="120">
<!--                                <a href="javascript:;" class="btn btn-green" data-toggle="dosave">保存</a>-->
                                <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                </td>
            </tr>
            <!--<tr>
                <td>
                    <label for="weight" class="control-label x90">商品简介：</label>
                    <textarea type="text" name="description" rows="5" cols="120" placeholder="一段商品简介，方便经销商快速了解商品，200字内" data-rule="required" value=""></textarea>
                </td>
            </tr>-->
            <tr>
                <td>
                    <label for="content" class="control-label x90">商品详情：</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        <textarea name="content" id="content" class="j-content" style="width: 1200px;" data-toggle="kindeditor" placeholder="自定义编辑框，可以上传图片" data-minheight="200"></textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin-left: 600px;margin-top: 20px;">
                        <button type="button" class="btn-close btn-nm">关闭</button>
                        <button type="submit" class="btn-default btn-nm">保存</button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>