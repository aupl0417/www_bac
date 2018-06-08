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
    <title>大唐云商-提交成功</title>
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
                <div class="left"><a href="javascript:window.history.go(-1);" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
                <div class="center sliding small_xs"><?php echo(strip_tags($title));?></div>
            </div>
        </div>
        <div class="pages">
            <div data-page="distribution" class="page">
                <div class="page-content bg-f5f" style="position: static;padding-bottom: 40px;">
                    <div class="text-center" style="padding: 3rem 0 1rem">
                        <img src="__STATIC__/wap/images/succeed-icon-img.png" alt="" width="80">
                    </div>
                    <div class="rfs18 c_008 text-center rmb20">
                        <?php echo(strip_tags($msg));?>
                    </div>
                    <div class="rfs14 c_ccc rmb20 text-center">
                        请耐心等待，大唐工作人员将会尽快联系你
                    </div>
                    <div class="row">
                        <div class="col-33"></div>
                        <div class="col-33"><a href="<?php echo(strip_tags($url));?>" class="my-btn-default bfb100 button rh40 rline40 external">返回</a></div>
                        <div class="col-33"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="__STATIC__/wap/js/framework7.min.js"></script>
<script type="text/javascript" src="__STATIC__/wap/js/yl_rem.js"></script>
<script type="text/javascript" src="__STATIC__/wap/js/js.js"></script>
<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>
</body>

</html>