<!doctype html>
<html class="login-html" lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="__STATIC__/bootstrap/css/bootstrap.min.css">
    <link href="__STATIC__/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="__STATIC__/bootstrap/css/fonts.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="__STATIC__/css/style.css">
    <style>
        .bor{
            border-color: #0091ff;
        }
    </style>
</head>
<body class="login-body">
<div class="clearfix bg_white pt15 pb15">
    <div class="pull-left fs16 pl30">
        大唐天下分销系统
    </div><!-- .pull-left -->
    <div class="pull-right clearfix pr30">
        <!--   <div class="pull-left plr30">
              <a class="fs16" href="#">登录</a>
          </div>
          <div class="pull-left plr30">
              <a class="fs16" href="#">注册</a>
          </div>
          <div class="pull-left plr30">
              <a class="fs16" href="#">创建项目</a>
          </div> -->
    </div><!-- .pull-right -->
</div><!-- .clearfix bg_white pt15 pb15 -->
<div class="login-box">
    <ul id="myTab" class="nav nav-tabs">
        <li class="active">
            <a href="#login" data-toggle="tab">登录</a>
        </li>
        <li>
            <a href="https://www.dttx.com" target="_blank">大唐C<sup>+</sup>系统</a>
        </li>
    </ul><!-- nav nav-tabs -->
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade in active" id="login">
            <div class="form-box">
                <form action="#">
                    <div class="form-group login-txt">
                        <input type="hidden" name="platformId" id="platformId" value="{$platformId|default='0'}">
                        <input type="text" placeholder="请输入账号" name="ipt_username" id="ipt_username">
                        <em class="user"></em>
                        <em class="ok"></em>
                    </div><!--.form-group -->
                    <div class="form-group login-txt">
                        <input type="password" placeholder="请输入密码" name="ipt_password" id="ipt_password">
                        <em class="psw"></em>
                        <em class="ok"></em>
                    </div><!--.form-group -->
                    <div class="form-group clearfix ma-txt">
                        <div class="pull-left">
                            <input type="text" placeholder="请输入验证码" name="ipt_captcha" id="ipt_captcha">
                        </div>
                        <div class="pull-right">
                                <span>
                                    <img src="{:url('common/publics/captcha')}" id="captcha_img" width="100" height="40" onclick="refreshVerify()" alt="点击刷新验证码">
                                </span>
                        </div>
                    </div><!--.form-group -->
                    <div class="form-group clearfix btn-txt">
                        <button class="btn fs18" id="checkloginBtn">登录</button>
                    </div><!--.form-group -->
                    <div class="form-group clearfix btn-txt">
                        <div class="pull-left check-re fs16">
                            <label class="square mg0">
                                <input type="checkbox" name="ischecked" id="ischecked" checked value="1">
                                <span></span>
                            </label>
                            记住密码
                        </div>
                        <div class="pull-right">
                            <a class="fs16" href="https://u.dttx.com/forget/passwordFind" target="_blank">忘记密码</a>
                        </div>
                    </div><!--.form-group -->
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="enroll">
        </div>
    </div>
</div><!-- .login-box -->
</body>
<script src="__STATIC__/js/jquery.min.js{$version}"></script>
<script src="__STATIC__/bootstrap/js/bootstrap.min.js{$version}"></script>
<script src="__STATIC__/js/plugins/bootbox/bootbox.min.js{$version}"></script>
<script>
    var checklogin ='{:url('platform/login/checklogin')}';
    var platformIndex ='{:url('platform/index/index')}';
</script>
<script src="__STATIC__/js/login/checklogin.js{$version}" type="text/javascript"></script>
</html>