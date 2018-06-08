<script type="text/javascript">
    $("input[name='type']").on('ifClicked', function(){
        var value = $(this).val();
        if(value == 1){
            $('#webAddress').css('display','block');
            $('#realAddress').css('display', 'none');
            $("input[name='realAddress']").attr('disabled', true);
            $("input[name='webAddress']").attr('disabled', false);
        }else {
            $('#realAddress').css('display','block');
            $('#webAddress').css('display', 'none');
            $("input[name='realAddress']").attr('disabled', false);
            $("input[name='webAddress']").attr('disabled', true);
        }
    });

    function alertErrorMsg(obj, msg) {
        obj.alertmsg('error', msg, {displayMode:'slide', displayPosition:'middlecenter', okName:'Yes', cancelName:'no', title:'错误信息'});
    }

    $('#submit').on('click', function () {
        var type = $("input[name='type']:checked").val();
        var trueName = $("input[name='trueName']").val();
        var dttxnick = $("input[name='dttxnick']").val();
        var sp_provinceId= parseInt($("select[name='sp_provinceId']").val());
        var sp_cityCode  = parseInt($("select[name='sp_cityCode']").val());
        var sp_regionCode= parseInt($("select[name='sp_regionCode']").val());
        var shopName     = $("input[name='shopName']").val();
        var realAddress  = $("input[name='realAddress']").val();
        var webAddress   = $("input[name='webAddress']").val();
        var delivery     = $("input[name='delivery']:checked").val();
        var content      = $("textarea[name='content']").val();

        if(trueName == ''){
            var obj = $("input[name='trueName']");
            alertErrorMsg(obj, '请输入经销商姓名');return false;
        }

        if(dttxnick == ''){
            var obj = $("input[name='dttxnick']");
            alertErrorMsg(obj, '请输入大唐会员名');return false;
        }

        if(isNaN(sp_provinceId)){
            var obj = $("select[name='sp_provinceId']");
            alertErrorMsg(obj, '请选择省份');return false;
        }

        if(isNaN(sp_cityCode)){
            var obj = $("select[name='sp_cityCode']");
            alertErrorMsg(obj, '请选择城市');return false;
        }

        if(isNaN(sp_regionCode)){
            var obj = $("select[name='sp_regionCode']");
            alertErrorMsg(obj, '请选择区县');return false;
        }

//        if(shopName == ''){
//            var obj = $("input[name='shopName']");
//            alertErrorMsg(obj, '请输入店铺名称');return false;
//        }
//
//        if(type == 0 && realAddress == ''){
//            var obj = $("input[name='realAddress']");
//            alertErrorMsg(obj, '请输入店铺地址');return false;
//        }else if(type == 1 && webAddress == ''){
//            var obj = $("input[name='webAddress']");
//            alertErrorMsg(obj, '请输入店铺网址');return false;
//        }
//
//        if(content == ''){
//            var obj = $("textarea[name='content']");
//            alertErrorMsg(obj, '请输入店铺详情');return false;
//        }

        $.ajax({
            type: "POST",
            url : "{:url('shopkeeper/create')}",
            dataType: "json",
            data : { type : type, trueName : trueName, dttxnick : dttxnick, provinceCode : sp_provinceId, cityCode : sp_cityCode, regionCode : sp_regionCode, shopName : shopName, realAddress : realAddress, webAddress : webAddress, delivery : delivery, content : content},
            success: function(data){
                if(data.statusCode == 200){
//                    $('#submit').alertmsg('correct', msg, {displayMode:'slide', displayPosition:'middlecenter', okName:'Yes', cancelName:'no', title:'正确信息'});
                    $('#submit').navtab('closeCurrentTab').navtab({ id:'platform_shopkeeper_index', url: "{:url('shopkeeper/index')}", title:'经销商列表'});
                }else{
                    alertErrorMsg($('#submit'), data.message);
                }
            }
        });
    });
    

    
</script>
<div class="bjui-pageContent tableContent">
    <form id="j_custom_form" data-alertmsg="false">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label for="" class="control-label x85">经销商类型</label>
                    <input type="radio" name="type" class="shopType" data-toggle="icheck" value="0" data-rule="checked" checked data-label="实体经销商&nbsp;&nbsp;">
                    <input type="radio" name="type" class="shopType" data-toggle="icheck" value="1" data-label="网络经销商（电商）">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name" class="control-label x90">经销商姓名<label class="btn-red">*</label></label>
                    <input type="text" name="trueName" data-rule="required;length[2~];" data-toggle="alertmsg" size="20" value="">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name" class="control-label x90">大唐会员名<label class="btn-red">*</label></label>
                    <input type="text" name="dttxnick" data-rule="required;length[6~];remote[{:url('common/publics/checkNickName')}]" size="20" value="">
                </td>
            </tr>

            <tr>
                <td>
                    <label for="name" class="control-label x90">经销商区域<label class="btn-red">*</label></label>

                    <select name="sp_provinceId" id="sp_provinceId" data-width="100" data-rule="required" data-toggle="selectpicker" data-nextselect="#sp_cityId" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                        <option value="">--省市--</option>
                        {foreach name="area" item="vo" }
                        <option value="{$vo.a_id}">{$vo.a_name}</option>
                        {/foreach}
                    </select>
                    <select name="sp_cityCode" id="sp_cityId"  data-width="100" data-rule="required" data-toggle="selectpicker" data-emptytxt="--城市--" data-nextselect="#sp_regionId" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                        <option value="">--城市--</option>
                    </select>
                    <select name="sp_regionCode" id="sp_regionId"  data-width="100" data-rule="required" data-toggle="selectpicker" data-emptytxt="--区县--" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                        <option value="">--区县--</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="shopName" class="control-label x90">店铺名称</label>
                    <input type="text" name="shopName" data-rule="" size="20" value="">
                </td>
            </tr>
            <tr id="realAddress">
                <td>
                    <label for="realAddress" class="control-label x90">店铺地址</label>
                    <input type="text" name="realAddress"  size="20" value="">
                </td>
            </tr>
            <tr id="webAddress" style="display: none;">
                <td>
                    <label for="webAddress" class="control-label x90">店铺网址</label>
                    <input type="text" name="webAddress" disabled="disabled" data-rule="url" size="20" value="">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="control-label x85">发货方式</label>
                    <input type="radio" name="delivery"  data-toggle="icheck" value="0" data-rule="checked" checked data-label="总部发货&nbsp;&nbsp;">
<!--                    <input type="radio" name="delivery"  data-toggle="icheck" value="1" data-label="自行发货">-->
                </td>
            </tr>
            <!--<tr>
                <td>
                    <label for="weight" class="control-label x90">店铺简介</label>
                    <textarea type="text" name="description" rows="5" cols="120" placeholder="一段店铺简介，方便经销商快速了解店铺，200字内" data-rule="required" value=""></textarea>
                </td>
            </tr>-->
            <tr>
                <td>
                    <label for="content" class="control-label x90">店铺详情</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        <textarea name="content" id="content" class="j-content" style="width: 1200px;" data-toggle="kindeditor" placeholder="自定义编辑框，可以上传图片" data-minheight="200"></textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin-left: 600px;margin-top: 20px;">
                        <button type="button" class="btn-close">关闭</button>
                        <button type="button" id="submit" class="btn-default">保存</button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>