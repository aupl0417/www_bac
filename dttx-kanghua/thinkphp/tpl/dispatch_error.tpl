{__NOLAYOUT__}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Access-Control-Allow-Origin" content="*"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>跳转提示</title>
    <link rel="stylesheet" href="__STATIC__/wap/css/framework7.ios.min.css">
    <link rel="stylesheet" href="__STATIC__/wap/css/framework7.ios.colors.min.css">
    <link rel="stylesheet" href="__STATIC__/wap/css/framework7-icons.css">
    <link rel="stylesheet" href="__STATIC__/wap/css/fonts.css">
    <link rel="stylesheet" href="__STATIC__/wap/css/rem.css">
    <link rel="stylesheet" href="__STATIC__/wap/css/css.css">
</head>
<body class="yl_style ">

<div class="views">
    <div class="view view-main navbar-through">
        <div class="navbar nav_custom">
            <div class="navbar-inner bg_blue c_fff">
          <!--      <div class="left"><a href="javascript:window.history.go(-1);" class="back link"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>-->
                <div class="center sliding small_xs">操作失败</div>
            </div>
        </div>
        <div class="pages">
            <div data-page="paysuccess" class="page">
                <div class="page-content bg-white">
                    <div class="pay-success-cont">
                        <div class="pay-success-icon text-center">
                            <img src="__STATIC__/wap/images/pay-faild-icon.png" alt="">
                        </div>
                        <div class="pay-success-text rpd15 rfs16 rline24 text-center">
                            <span class="c_red"><?php echo(strip_tags($msg));?></span>
                            <br>
                            <br>
                            <span class="c_blue"> 页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b></span>
                        </div>

                        <div class="rmt20 rpd10">
                            <a id="href" href="<?php echo($url);?>" class="button my-btn-blue button-raised external rh40 rfs16 rline40">马上跳转</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="__STATIC__/wap/js/framework7.min.js"></script>
<script type="text/javascript" src="__STATIC__/wap/js/yl_rem.js"></script>
<script type="text/javascript" src="__STATIC__/wap/js/js.js"></script>

<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>

</body>

</html>