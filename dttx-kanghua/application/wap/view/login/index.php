{extend name="common:base" /}
{block name="header"}
<div class="right">

</div>
{/block}
{block name="content"}
<div class="pages" >
    <div data-page="distribution" class="page" >
        <div class="page-content bg-white">
            <form method="POST" id="loginform" data-action="{:url('login/checklogin')}" name="forms">
                <div class="login-cont">
                    <div class="rmb40">
                        <div class="login-lizx-input">
                            <input type="hidden" name="platformId" id="platformId" value="{$data.pl_id}">
                            <div class="login-lizx-icon"><img src="__STATIC__/wap/images/login-user-icon.png" alt=""></div>
                            <input type="text" placeholder="大唐天下账户/手机号码" id="wap_username" class="">
                        </div>
                    </div>
                    <div class="over rmb10">
                        <div class="login-lizx-input rmb20">
                            <div class="login-lizx-icon"><img src="__STATIC__/wap/images/login-password-icon.png" alt=""></div>
                            <a href="javascript:;" class="login-lizx-eye" id="aaa"><img id="eye" src="__STATIC__/wap/images/login-eye-hide-icon.png" alt=""></a>
                            <div id="box">
                                <input id="wap_password" type="password" placeholder="请输入用户名密码" >
                            </div>
                        </div>
                        <div class="rmt10 rfs14 c_ccc rmr30 text-right rpb30">
                            若忘记密码,请去往大唐天下app进行找回
                        </div>
                    </div>
                    <div class="rpd10 rpl25 rpr25">
                        <input type="button" value="登录" id="loginBtn" class="button my-btn-blue button-raised rh45 rline45 rfs16">
                        <div class="rmt10 rfs14 c_ccc text-center rpb20">
                            首次登录将自动激活“大唐天下分销系统-{$data.pl_name}”账号
                        </div>
                    </div>
                    <div class="rpd10 rpl25 rpr25">
                        <a href="javascript:;" id="dttxRegister" class="button external my-btn-default-bluefont button-raised rh45 rline45 rfs16 c_blue">去注册大唐天下账号</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
{/block}

{block name='script'}
<script language="JavaScript">
    var aaa=document.getElementById("aaa");
    var wap_password=document.getElementById("wap_password");
    var eye=document.getElementById("eye");
    var eye_open=false;
    aaa.onclick=function(){
        if(eye_open == true){
            wap_password.setAttribute("type", "password");
            eye.setAttribute("src", "__STATIC__/wap/images/login-eye-hide-icon.png");
            eye_open=false;
        }else{
            wap_password.setAttribute("type", "text");
            eye.setAttribute("src", "__STATIC__/wap/images/login-eye-kai-icon.png");
            eye_open=true;
        }
    }
    $$(".login-lizx-input").find("input").focus(function(){
        $$(this).parents(".login-lizx-input").css("border-color","#2e92f4");
    })
    $$(".login-lizx-input").find("input").blur(function(){
        $$(this).parents(".login-lizx-input").css("border-color","#ebedf0");
    })

    $$('#dttxRegister').click(function () {
        myApp.alert('请在大唐天下注册完后返回本项目！','',function () {
            window.location.href='https://wap.dttx.com/register/index';
        })
    })

    $$('#loginBtn').click(function () {
       var wap_username =$$('#wap_username').val();
       var wap_password =$$('#wap_password').val();
       var platformId =$$('#platformId').val();
       var _this =$$(this);
       var loginurl=$$('#loginform').data('action');
  //     console.log(loginurl);
       if (wap_username.length<3){
           myApp.alert('用户名不能为空！','');
           return false;
       }

       if (wap_password.length<3){
           myApp.alert('密码不能为空!','');
           return false;
       }
        $$('#loginBtn').val('登录中...');
       $$.ajax({
            url:loginurl,
            method:'POST',
            data:{username:wap_username,password:wap_password,platformId:platformId},
            dataType:'json',
            beforSend:function () {
                $$('#loginBtn').val('登录中...');
            },
            success:function(data) {
            //    console.log(data);
                if (data.statusCode=='200'){
                    location.href=data.message;
                }else {
                    myApp.alert(data.message,'')
                }
            },
            error:function(){
                myApp.alert('登录超时，请重试!','')
            },
            complete:function(){
                $$('#loginBtn').val('登录')
            }
       })

    });

</script>
{/block}