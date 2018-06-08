{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="" class="back link"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
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
                                持卡人
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <input type="text" name="username" class=" rfs14 bor_no rpl0" placeholder="请输入持卡人">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                卡号
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <input type="text" name="cardNumber"  class=" rfs14 bor_no rpl0" placeholder="请输入卡号">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg_fff solid_last solid_b">

                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                选择银行
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <select name="bankId" class="form-control bor_no box-shadow rpl0 form-select">
                                    <option value="">请选择银行</option>
                                    {foreach $bankList as $vo}
                                    <option value="{$vo.id}">{$vo.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rmt30 rpd10">
                    <input type="button" id="submit" value="确认添加" class="button my-btn-blue button-raised rh45 rline45 rfs16" />
                </div>
            </form>

        </div>
    </div>
</div>
{/block}

{block name='script'}
<script>
    $$('.left').on('click', function () {
        window.history.back();
    });

    $$('#add_bank').on('click', function () {
        window.location.href = $$(this).data('url');
    });

    $$('#submit').click(function () {
        var username   = $$("input[name='username']").val();
        var cardNumber = $$("input[name='cardNumber']").val();
        var bankId     = $$("select[name='bankId']").val();

        var reg = /^(\d{16}|\d{19})$/;
        if (!reg.test(cardNumber) || !reg.test(cardNumber)) {
            alertLayout('银行卡输入不正确');return false;
        }

//        if(!/^\S{2,}$/.test(username)){
//            alertLayout('请输入长度大于2位的持卡人姓名');return false;
//        }

        $$.ajax({
            type: "POST",
            url: "{:url('Bank/create')}",
            data: { username : username, cardNumber: cardNumber, bankId : bankId },
            dataType: "json",
            success: function(data){
                if(data.statusCode == 200){
                    alertLayout(data.message);
                    setTimeout(function () {
                        window.location.href = "{:url('Bank/index')}";
                    }, 2000);
                }else if(data.statusCode == 301){
                    alertLayout(data.message.msg);
                    setTimeout(function () {
                        window.location.href = data.message.url;
                    }, 2000);
                }else {
                    alertLayout(data.message)
                }
            }
        });
    });



</script>
{/block}