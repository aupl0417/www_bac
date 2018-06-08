{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/index')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='header'}
<div class="right">
    <a href="{:url('store/index')}" class="back link external"><img src="__STATIC__/wap/images/home.png" width="20" alt="返回个人中心"></a>
</div><!--.right-->
{/block}
{/block}
{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f">
            <form action="">
                <div class="bg_fff solid_last rmb10 solid_b">
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333 rline55">
                                <span class="c_red">*</span>经销商类型
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <label class="radius"><input type="radio" name="type" checked value="0"><em class="pull-left"></em>
                                    <span class="small_xs ml10">实体经销商</span>
                                </label>
                                <label class="radius"><input type="radio" name="type" value="1"><em class="pull-left"></em>
                                    <span class="small_xs ml10">网络经销商</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                <span class="c_red">*</span>经销商姓名
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input type="text" name="trueName" class=" rfs14 bor_no rpl0" placeholder="请填写真实姓名" value="">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                <span class="c_red">*</span>大唐账号
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input type="text" name="dttxnick" readonly class=" rfs14 bor_no rpl0" placeholder="将作为登录及结算凭证" value="{$dttxNick}">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                <span class="c_red">*</span>经销商区域
                            </div>
                            <div class="col-66 input_center rpl0">
                                <select name='provinceId' id="provinceId" class="form-control bor_no box-shadow rpl0 form-select rmb5" data-nextselect="#cityId" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                                    <option value="">请选择</option>
                                    {foreach $area as $vo}
                                    <option value="{$vo.a_id}">{$vo.a_name}</option>
                                    {/foreach}
                                </select>
                                <select name='cityId' id="cityId" class="form-control bor_no box-shadow rpl0 form-select rmb5"  data-nextselect="#regionId" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                                    <option value="">请选择</option>
                                </select>
                                <select name='regionId' id='regionId' class="form-control bor_no box-shadow rpl0 form-select rmb5">
                                    <option value="">请选择</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                店铺名称
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input name="shopName" type="text" class=" rfs14 bor_no rpl0" placeholder="请输入店铺名称" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg_fff rmb20">
                    <div class="">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                店铺地址/网址
                            </div>
                        </div>
                    </div>
                    <div class="rpd15 rpt0">
                        <div class="rpb20 row">
                            <textarea class="bor_no bg-f9f rpd10 col-100" name="address" rows="6" placeholder="例 : http://huaban.com/boards/17335972/"></textarea>
                        </div>
                    </div>
                </div>
                <div class="rmt20 rpd10">
                    <input type="button" id="submit" value="提交申请" class="button my-btn-blue button-raised rh40 rfs16" />
                </div>
            </form>

        </div>
    </div>
</div>
{/block}

{block name='script'}
<script>
    $$('select').on('change', function () {
        var value = $$(this).val();
        var id    = $$(this).data('nextselect');
        var url   = $$(this).data('refurl');
        if(url){
            url = url.replace('{value}', value);
            $$.ajax({
                type: "GET",
                url : url,
                dataType: "json",
                success: function(data){
                    var len = data.length;
                    var html = '';
                    for(var i=0; i < len; i++){
                        html += '<option value="' + data[i].value + '">'  + data[i].label + '</option>';
                    }
                    $$(id).html(html);
                }
            });
        }
    });
    
    $$('#submit').on('click', function () {
        var type     = $$("input[name='type']:checked").val();
        var trueName = $$("input[name='trueName']").val();
        var dttxnick = $$("input[name='dttxnick']").val();
        var provinceId = $$("select[name='provinceId']").val();
        var cityId   = $$("select[name='cityId']").val();
        var regionId = $$("select[name='regionId']").val();
        var shopName = $$("input[name='shopName']").val();
        var address  = $$("textarea[name='address']").val();

        if(trueName == ''){
            alertLayout('请输入真实姓名');return false;
        }

        if(dttxnick == ''){
            alertLayout('请输入大唐账号');return false;
        }

        if(provinceId == ''){
            alertLayout('请选择省份');return false;
        }

        if(cityId == ''){
            alertLayout('请选择城市');return false;
        }

        if(regionId == ''){
            alertLayout('请选择区县');return false;
        }

        /*if(shopName == ''){
            alertLayout('请输入店铺名称');return false;
        }

        if(address == ''){
            alertLayout('请输入店铺地址/网址');return false;
        }*/

        if(type == 1){
            var Expression=/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
            var objExp=new RegExp(Expression);
            if(objExp.test(address) != true){
                alertLayout('请输入正确的网址');return false;
            }
        }

        $$.ajax({
            type: "POST",
            url: "{:url('Shopkeeper/create')}",
            data: { type : type, trueName: trueName, dttxnick:dttxnick, provinceCode : provinceId, cityCode : cityId, regionCode : regionId, shopName : shopName, realAddress : address,webAddress : address },
            dataType: "json",
            success: function(data){
                if(data.statusCode == 200){
                    myApp.alert(data.message, '提示', function () {
                        window.location.href = "{:url('ucenter/index')}";
                    });
                }else if(data.statusCode == 301){
                    myApp.alert(data.message.msg, '提示', function () {
                        window.location.href = data.message.url;
                    });
                }else {
                    myApp.alert(data.message, '提示');
                }
            }
        });
    });

</script>
{/block}