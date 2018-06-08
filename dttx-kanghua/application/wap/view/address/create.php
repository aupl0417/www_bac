{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='header'}
<div class="right">
    <a id="submit" class="rfs16 c_fff">保存</a>
</div>
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

<!--                    <div class="solid_b">-->
<!--                        <div class="row mr0 phong_form rpl15">-->
<!--                            <div class="col-33 text_left rfs16 c_333">-->
<!--                                邮政编码-->
<!--                            </div>-->
<!--                            <div class="col-66 input_center rpl0">-->
<!--                                <input name="postage" type="text" class=" rfs14 bor_no rpl0" placeholder="请输入邮政编码">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpd15">
                            <textarea name="address" class="bor_no bg-f9f rpd10 col-100 rfs14" rows="6" placeholder="请在此输入详细收货地址....."></textarea>
                        </div>
                    </div>
                </div>
                <div class="rpd10 bg-white">
                    <div class="row mr0 phong_form">
                        <div class="col-33 text_left rfs16 c_333 rline45">
                            设为默认地址
                        </div>
                        <div class="col-66 input_center rpl0">
                            <div class="item-input pull-right rmt10">
                                <label class="label-switch">
                                    <input type="checkbox" name="isDefault" value="0">
                                    <div class="checkbox"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
{/block}
{block name='footer'}

{/block}
{block name='script'}
<script>
    $$('.left').on('click', function () {
        window.history.back();
    });

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

    $$('.label-switch').on('click', function () {
        var obj = $$("input[name='isDefault']");
        if(obj.val() == 0){
            obj.prop('checked', false).val('1');
        }else{
            obj.prop('checked', true).val('0');
        }
    });
    
    $$('#submit').on('click', function () {
        var receiver  = $$("input[name='receiver']").val();
        var phone     = $$("input[name='phone']").val();
        var provinceId= $$('#provinceId').val();
        var cityId    = $$('#cityId').val();
        var regionId  = $$('#regionId').val();
        var postage   = $$("input[name='postage']").val();
        var address   = $$("textarea[name='address']").val();
        var isDefault = $$("input[name='isDefault']").val();
        var url       = "{:url('Address/create')}";

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

        if(postage == ''){
            alertLayout('请输入邮政编码');return false;
        }

//        if(!is_postcode(postage)){
//            alertLayout('邮政编码格式不正确');return false;
//        }

        if(address == ''){
            alertLayout('请输入详细地址');return false;
        }
        $$(this).attr('disabled', true);
        $$.ajax({
            type: "POST",
            url: url,
            data: { receiver: receiver, phone:phone, provinceId : provinceId, cityId : cityId, regionId : regionId, postage : postage, address : address, isDefault : isDefault },
            dataType: "json",
            success: function(data){
                if(data.statusCode == 200){
                    myApp.alert(data.message, '提示', function () {
                        window.location.href = "{:url('Address/index')}";
                    });
                }else if(data.statusCode == 301){
                    myApp.alert(data.message.msg, '提示', function () {
                        window.location.href = data.message.url;
                    });
                }else {
//                    $$('#submit').attr('disabled', false);
                    $$('#submit').removeAttr('disabled');
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