<script type="text/javascript">
    function pic_upload_success(file, data) {
        var json = $.parseJSON(data)
        $(this).bjuiajax('ajaxDone', json)
        if (json[BJUI.keys.statusCode] == BJUI.statusCode.ok) {
            console.log(json);
            $('#image').val(json.filename).trigger('validate')
            $('#j_custom_span_pic').html('<img src="'+ json.filename +'" width="100" />')
        }
    }
</script>
<div class="bjui-pageContent tableContent">
    <form action="{:url('')}" id="j_custom_form" data-toggle="validate" data-alertmsg="false">
        <input type="hidden" name="id" value="{$goods.bg_id}">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label for="name" class="control-label x120">商品名称：</label>
                    <input type="text" name="name" data-rule="required" size="30" value="{$goods.bg_name}">
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <label class="control-label x120">商品主图：</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        <span id="j_custom_span_pic"><img src="{$goods.bg_image|default=''}" width="120" /></span>
                        <div id="j_custom_pic_up" data-toggle="upload" data-uploader="{:url('common/ajax/ajax_upload')}"
                             data-file-size-limit="1024000000"
                             data-file-type-exts="*.jpg;*.png;*.gif;*.mpg"
                             data-file-obj-name="uploads"
                             data-auto="true"
                             data-on-upload-success="pic_upload_success"
                             data-icon="cloud-upload"></div>
                        <input type="hidden" name="image" value="{$goods.bg_image}" id="image">

                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="scoreReward" class="control-label x120">积分奖励：</label>
                    <select name="scoreReward" id="scoreReward" data-toggle="selectpicker" data-width="100" data-rule="required">
<!--                        <option value="200" {if condition="$goods.bg_scoreReward eq 200"}selected="selected"{/if}>200%</option>-->
                        <option value="100" {if condition="$goods.bg_scoreReward eq 100"}selected="selected"{else/}selected="selected{/if}">100%</option>
<!--                        <option value="50" {if condition="$goods.bg_scoreReward eq 50"}selected="selected"{/if}>50%</option>-->
<!--                        <option value="25" {if condition="$goods.bg_scoreReward eq 25"}selected="selected"{/if}>25%</option>-->
                    </select>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="cateId" class="control-label x120">商品分类：</label>
                    <select name="cateId" id="cateId" data-toggle="selectpicker" data-rule="required" data-width="200">
                        <option value="">请选择分类</option>
                        {foreach $cateList as $vo}
                        <option value="{$vo.id}" {if condition="$vo.id eq $goods.ca_id"}selected="selected"{/if}>{$vo.name}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="control-label x120">是否上架：</label>
                    <input type="radio" name="isSale"  data-toggle="icheck" value="1" data-rule="checked" checked data-label="是&nbsp;&nbsp;">
                    <input type="radio" name="isSale"  data-toggle="icheck" value="0"  {if condition="$goods.bg_isSale eq 0"} data-rule="checked"  checked{/if} data-label="否">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="channel" class="control-label x120">商品属性：</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        <table id="tabledit1" class="table table-bordered table-hover table-striped" data-toggle="tabledit" data-initnum="0" data-action="{:url('Goods/addAttribute')}" data-single-noindex="true">
                            <thead>
                            <tr data-idname="id">
                                <th title="型号" width="100px"><input type="text" name="model" data-rule="required" placeholder="型号" value="" size="10"></th>
                                <!--<th title="规格" width="100px"><input type="text" name="customList[#index#][format]" data-rule="" placeholder="规格" value="" size="3"></th>
                                <th title="单位" width="100px"><input type="text" name="customList[#index#][unit]" data-rule="" placeholder="单位" value="" size="1"></th>-->
                                <th title="成本价格" width="100px"><input type="text" name="cost" data-rule="required;number" placeholder="0.00" value="" size="1"></th>
                                <th title="建议零售价" width="100px"><input type="text" name="price" data-rule="required;number" placeholder="0.00" value="" size="1"></th>
                                <th title="库存量" width="100px"><input type="text" name="goodsStock" data-rule="required;number" placeholder="库存量" value="" size="1"></th>
                                <!--<th title="装箱规格" width="100px"><input type="text" name="customList[#index#][packFormat]" data-rule="" placeholder="装箱规格" value="" size="1"></th>-->
                                <th title="说明" width="380px"><textarea name="instruction" data-toggle="autoheight" placeholder="说明" cols="10" rows="1"></textarea></th>
                                <th title="操作" data-addtool="false" width="120">
                                    <!--                                <a href="javascript:;" class="btn btn-green" data-toggle="dosave">保存</a>-->
                                    <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr data-id="{$goods.gs_id}">
                                <td>{$goods.bg_model}</td>
                                <td>{$goods.bg_cost}</td>
                                <td>{$goods.bg_price}</td>
                                <td>{$goods.gs_goodsStock}</td>
                                <td>{$goods.bg_instruction}</td>
                                <td data-noedit="true">
                                    <button type="button" class="btn-green" data-toggle="doedit">编辑</button>
<!--                                    <a href="ajaxDone2.html" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>-->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="content" class="control-label x120">商品详情：</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        <textarea name="content" id="content" class="j-content" style="width: 1000px;" data-toggle="kindeditor" placeholder="自定义编辑框，可以上传图片" data-minheight="200">{$goods.bg_content}</textarea>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
        <li><button type="submit" class="btn-default btn-nm">保存</button></li>
    </ul>
</div>