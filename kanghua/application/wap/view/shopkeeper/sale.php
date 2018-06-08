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
                            <div class="col-33 text_left rfs16 c_333">
                                销售商品
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <select name="goodsNum" id="goodsNum" class="form-control bor_no box-shadow rpl0 form-select" data-nextselect="#model" data-refurl="{:url('common/Publics/getgoodssku')}?number={value}">
                                    <option value="">请选择商品</option>
                                    {notempty name='$goodsList'}
                                    {foreach $goodsList as $vo}
                                    <option value="{$vo.si_id|default='0'}"> {$vo.bg_name|default='没有商品'}-[{$vo.bg_model
                                        }]</option>
                                    {/foreach}
                                    {/notempty}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                商品型号
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
<!--                                <select name="model" id="model" class="form-control bor_no box-shadow rpl0 form-select" data-nextselect="#number" data-refurl="{:url('common/Publics/getgoodsskuprice')}?number={number}&model={value}">-->
<!--                                    <option>请选择商品型号</option>-->
<!--                                </select>-->
                                <div class="rfs14 rline40 rmt5" id="model"></div>
                            </div>
                        </div>
                    </div>

                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                商品金额
                            </div>
                            <div class="col-66 input_center rpl0 ">
                                <div class="rfs14 rline40 rmt5" id="total"></div>
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                商品数量
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <input name="number" type="text" id="number" class=" rfs14 bor_no rpl0" data-price="" placeholder="请输入商品数量">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                商品总金额
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <div class="rfs14 rline40 rmt5" id="totalmoney"></div>
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                消费者账号
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input type="text" name="dttxnick" class=" rfs14 bor_no rpl0" placeholder="请输入大唐账号或手机号">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="rline40 rfs14 c_red" id="checkUser">

                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333 rline55">
                                发货方式
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <label class="radius"><input type="radio" name="delivery" checked value="0"><em class="pull-left"></em>
                                    <span class="small_xs ml10">总部发货</span>
                                </label>
                                <!--<label class="radius"><input type="radio" name="delivery" value="1"><em class="pull-left"></em>
                                    <span class="small_xs ml10">总部发货</span>
                                </label>-->
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                收货人
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input name="receiver" type="text" class=" rfs14 bor_no rpl0" placeholder="请输入收货人姓名">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                联系电话
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input name="phone" type="text" class=" rfs14 bor_no rpl0" placeholder="请输入收货人手机号码">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                所在区域
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
                                邮政编码
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input name="postage" type="text" class=" rfs14 bor_no rpl0" placeholder="请输入邮政编码">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg_fff rmb20">
                    <div class="">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                收货地址
                            </div>
                        </div>
                    </div>
                    <div class="rpd15 rpt0">
                        <div class="rpb20 row">
                            <textarea name="address" class="bor_no bg-f9f rpd10 col-100" rows="6" placeholder="请在此输入详细收货地址....."></textarea>
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

    $$('#goodsNum').on('change', function () {
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
                    if(data.state == 'ok'){
                    //    $$(id).html(data.message);
                        $$('#model').text(data.model);
                        $$('#total').text(data.price);
                    }else {
                        $$('#model').text(data.model);
                        $$('#total').text(data.price);
                    }
                }
            });
        }
    });

//    $$('#model').on('change', function () {
//        var value = $$(this).val();
//        var number = $$('#goodsNum').val();
//        var id    = $$(this).data('nextselect');
//        var url   = $$(this).data('refurl');
//        if(url){
//            url = url.replace('{value}', value);
//            url = url.replace('{number}', number);
//            $$.ajax({
//                type: "GET",
//                url : url,
//                dataType: "json",
//                success: function(data){
//                    if(data.state == 'ok'){
//                        $$(id).attr('data-price', data.message);
//                    }
//                }
//            });
//        }
//    });

    $$("input[name='number']").blur(function () {
        var number = $$(this).val();
        if(isNaN(number) || number <= 0){
            alertLayout('请输入大于0的数字');return false;
        }
        $$('#totalmoney').html(parseFloat($$('#total').text()) * number);
    });

    $$("input[name='dttxnick']").blur(function () {
        var value = $$(this).val();
        var url   = "{:url('common/Publics/checkuser')}"
        $$.ajax({
            type: "post",
            url : url,
            dataType: "json",
            data: { dttxnick : value },
            success: function(data){
                $$('#checkUser').html(data.message);
                if(data.statusCode == 300){
                   $$('#submit').attr('disabled', true);
                }else{
                    $$('#submit').removeAttr('disabled');
                }
            }
        });
    });
    
    $$('#submit').on('click', function () {

        var goodsNum = $$("select[name='goodsNum']").val();
        var model    = $$("select[name='model']").val();
        var number   = $$("input[name='number']").val();
        var price    = $$("input[name='number']").data('price');
        var dttxnick = $$("input[name='dttxnick']").val();
        var delivery = $$("input[name='delivery']:checked").val();
        var address  = $$("textarea[name='address']").val();
        var receiver  = $$("input[name='receiver']").val();
        var phone     = $$("input[name='phone']").val();
        var provinceId= $$('#provinceId').val();
        var cityId    = $$('#cityId').val();
        var regionId  = $$('#regionId').val();
        var postage   = $$("input[name='postage']").val();

        if(goodsNum == ''){
            alertLayout('请选择商品');return false;
        }

        if(model == ''){
            alertLayout('请选择商品型号');return false;
        }

        if(number == ''){
            alertLayout('请输入商品量');return false;
        }

        if(dttxnick == ''){
            alertLayout('请输入大唐账号或手机号');return false;
        }

        if(receiver == ''){
            alertLayout('请输入收货人姓名');return false;
        }

        if(phone == ''){
            alertLayout('请输入收货人手机号码');return false;
        }

        var pattern = /^1[34578]\d{9}$/;
        if(!pattern.test(phone)){
            alertLayout('请输入正确的手机号码');return false;
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

//        if(postage == ''){
//            alertLayout('请输入邮政编码');return false;
//        }

        if(postage != '' && !is_postcode(postage)){
            alertLayout('邮政编码格式不正确');return false;
        }

        if(address == ''){
            alertLayout('请输入收货地址');return false;
        }

        $$.ajax({
            type: "POST",
            url: "{:url('Shopkeeper/sale')}",
            data: { goodsNum : goodsNum, model: model, dttxnick:dttxnick, number : number, price : price, delivery : delivery, address : address,receiver : receiver, phone : phone, provinceId : provinceId, cityId : cityId, regionId : regionId, postage : postage},
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

    function is_postcode(postcode) {
        if ( postcode == "") {
            return false;
        } else {
            if (! /^[0-9][0-9]{5}$/.test(postcode)) {
                return false;
            }
        }
        return true;
    }

</script>
{/block}