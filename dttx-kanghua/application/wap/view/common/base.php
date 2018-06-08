<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Access-Control-Allow-Origin" content="*"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    {block name="title"}<title>大唐天下分销平台 - {$title|default=''}</title>{/block}
    <link rel="shortcut icon" href="__STATIC__/wap/images/favicon.ico"></head>
    <link rel="stylesheet" href="__STATIC__/wap/css/framework7.ios.min.css{$version}">
    <link rel="stylesheet" href="__STATIC__/wap/css/framework7.ios.colors.min.css{$version}">
    <link rel="stylesheet" href="__STATIC__/wap/css/framework7-icons.css{$version}">
    <link rel="stylesheet" href="__STATIC__/wap/css/fonts.css{$version}">
    <link rel="stylesheet" href="__STATIC__/wap/css/rem.css{$version}">

</head>
<body class="yl_style ">
<div class="views">
    {block name="top"}{/block}
    <div class="view view-main navbar-through">
        <div class="navbar nav_custom">
            <div class="navbar-inner bg_blue c_fff">
                {block name="leftnav"}{/block}
                <div class="center sliding small_xs">{$title}</div>
                {block name="header"} {/block}
            </div>
        </div>
        {block name="content"} {/block}
        {block name="footer"} {/block}
    </div>
</div>
{block name="othercontent"}{/block}
</body>
<link rel="stylesheet" href="__STATIC__/wap/css/css.css{$version}">
</html>
<script type="text/javascript" src="__STATIC__/wap/js/framework7.min.js{$version}"></script>
<script type="text/javascript" src="__STATIC__/wap/js/yl_rem.js{$version}"></script>
<script type="text/javascript" src="__STATIC__/wap/js/js.js{$version}"></script>

<script>

    // 初始化 app
    var myApp = new Framework7({
        modalTitle: "提示",
        modalButtonOk: "确定",
        modalButtonCancel: "取消",
    });

    function alertLayout(msg, title, callback) {
        if(typeof (title) == 'undefined'){
            title = '提示';
        }

        if(typeof (callback) == 'undefined'){
            myApp.alert(msg, title, callback);
        }else {
            myApp.alert(msg, title);
        }
        return false;
    }
</script>
{block name='script'}{/block}
<div style="display: none"><script src="https://s13.cnzz.com/z_stat.php?id=1263619228&web_id=1263619228" language="JavaScript"></script></div>