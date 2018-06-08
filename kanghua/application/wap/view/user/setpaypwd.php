{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('user/profile')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
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
                                当前提现密码
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <input name="currentPayPwd" type="password" class=" rfs14 bor_no rpl0" placeholder="未设置则无需输入">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="bg_fff solid_last solid_b">
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                新的提现密码
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <input type="password" name="password" class=" rfs14 bor_no rpl0" placeholder="请设置新的提现密码">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                确认提现密码
                            </div>
                            <div class="col-66 input_center rpl0 input-radius-active">
                                <input name="repassword" type="password" class=" rfs14 bor_no rpl0" placeholder="请确认提现密码">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rpd10 rfs12 c_cc0">
                    提现密码需要设置6位数字
                </div>
                <div class="rmt10 rpd10">
                    <input type="button" id="submit" value="确认" class="button my-btn-blue button-raised rh45 rline45 rfs16" />
                </div>
            </form>

        </div>
    </div>
</div>
{/block}

{block name='script'}
<script>

    var myApp = new Framework7();

    $$('#submit').click(function () {
        var currentPayPwd = $$("input[name='currentPayPwd']").val();
        var password      = $$("input[name='password']").val();
        var repassword    = $$("input[name='repassword']").val();
        var isSetPayPwd   = "{$isSetPayPwd}";

        if(isSetPayPwd == 1){
            if(currentPayPwd == ''){
                alertLayout('提现密码不能空');return false;
            }
            if (! /^\d{6}$/.test(currentPayPwd)) {
                alertLayout('密码为6位数字');return false;
            }
        }

        if(password == ''){
            alertLayout('新的提现密码不能为空');return false;
        }

        if(repassword == ''){
            alertLayout('确认提现密码不能为空');return false;
        }

        if (! /^\d{6}$/.test(password)) {
            alertLayout('新的提现密码为6位数字');return false;
        }

        if (! /^\d{6}$/.test(repassword)) {
            alertLayout('确认提现密码为6位数字');return false;
        }

        if(password != repassword){
            alertLayout('两密码不一致');return false;
        }

        $$.ajax({
            type: "POST",
            url: "{:url('User/setPayPwd')}",
            data: { currentPayPwd : currentPayPwd, password: password, repassword:repassword, isSetPayPwd : isSetPayPwd },
            dataType: "json",
            success: function(data){
                if(data.statusCode == 200){
                    myApp.alert(data.message, '提示', function () {
                        window.location.href = "{:url('User/profile')}";
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