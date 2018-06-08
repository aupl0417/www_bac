/**
 * Created by Lenovo on 2017/7/5.
 */
$(function () {

    $('#checkloginBtn').click(function (e) {
        e.preventDefault();
        var _this =$(this);
        var ipt_username =$.trim($('#ipt_username').val());
        var ipt_password =$.trim($('#ipt_password').val());
        var ipt_captcha =$.trim($('#ipt_captcha').val());
        var ipt_platformid =$('#platformId').val();
        var ischecked = $('#ischecked').val();

        if (ipt_username=='' || ipt_username.length<3){
            bootbox.alert({
                title: "提示信息",
                message: "登录账号名不能为空!",
            });
            return false;
        }
        if (ipt_password=='' || ipt_password.length<3){
            bootbox.alert({
                title: "提示信息",
                message: "登录密码不能为空!",
            });
            return false;
        }
        if (ipt_captcha=='' || ipt_captcha.length<3){
            bootbox.alert({
                title: "提示信息",
                message: "验证码不能为空!",
            });
            return false;
        }

        $.ajax({
            type:"POST",
            url:checklogin,
            data:{username:ipt_username,password:ipt_password,captcha:ipt_captcha,platformId:ipt_platformid,isChecked:ischecked},
            dataType:"json",
            beforeSend:function () {
                _this.text('登录中...');
            },
            success:function (data) {
                console.log(data)
                if(data.statusCode=='300'){
                    bootbox.alert({
                        title: "提示信息",
                        message: data.message,
                        callback:refreshVerify()
                    });
                }else {
                    window.location.href=platformIndex
                    // bootbox.alert({
                    //     title: "提示信息",
                    //     message: data.message,
                    //     callback:function () {
                    //
                    //     }
                    // });
                }
            },
            error:function () {
                bootbox.alert({
                    title: "提示信息",
                    message: "登录超时，请重试!"
                });
            },
            complete:function () {
                _this.text('登录');
            }

        })


    });



});

function refreshVerify() {
    var ts =Date.parse(new Date())/1000;
    $('#captcha_img').attr('src','/common/publics/captcha?v='+ts);
}