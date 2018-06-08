{extend name="common:base" /}
{block name='header'}
<div class="right">
    {if condition="$isLogin eq 1"}
<!--    <a href="{:url('ucenter/index')}" class="external"><img src="__STATIC__/wap/images/grzx-white-icon.png" alt="" width="20"></a>-->
        <a href="#" class="open-share"><img src="__STATIC__/wap/images/fx-icon.png" alt=""></a>
    {/if}
</div>
{/block}
{block name='content'}
<link rel="stylesheet" href="/static/nativeShare/baidushare.css?v={$version}" />
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg_gray" style="padding-bottom: 1.449rem">
            <div style="line-height: 24px;" id="content">{$content}</div>

            {foreach $goodsList as $vo}
            {if condition="$code neq ''"}
                <a href="{:url('Goods/detail', ['id' => $vo.si_id, 'code' => $code])}" class="external">
            {else/}
                <a href="{:url('Goods/detail', ['id' => $vo.si_id])}" class="external">
            {/if}
                <div class="good-boxin row no-gutter rpd15 bg_fff solid_t">
                    <div class="col-20">
                        <div class="solid_all">
                            <img src="{$vo.bg_image}" width="100%" alt="{$vo.bg_name}">
                        </div>
                    </div>
                    <div class="col-50">
                        <div class="good-name rml10 c_333 rfs14 rmt5">
                            {$vo.bg_name}
                        </div>
                        <div class="good-name rfs14 c_999 rml10 rmt5">
                            奖励积分：{$vo.bg_scoreReward*$vo.bg_price}
                        </div>
                    </div>
                    <div class="col-30 text-right">
                        <div class="rfs14 c_333">&yen;{$vo.bg_price}</div>
                        <div class="rfs14 c_999"></div>
                    </div>
                </div><!--.over-->
            </a>
            {/foreach}
            <!-- Picker star-->
<!--            <div class="picker-modal picker-1 bg-white" style="height: 6.521rem">-->
<!--                <div class="picker-modal-inner bg-white">-->
<!--                    <div class="content-block bg-white over popup-cont">-->
<!--                        <div class="jiathis_style_32x32 mt-5 row">-->
<!--                            <div class="col-25 text-center"><a class="jiathis_button_tsina dinline"></a><div class="text-center rmt5">新浪分享</div></div>-->
<!--                            <div class="col-25 text-center"><a class="jiathis_button_weixin dinline"></a><div class="text-center rmt5">微信分享</div></div>-->
<!--                            <div class="col-25 text-center"><a class="jiathis_button_qzone dinline"></a><div class="text-center rmt5">空间分享</div></div>-->
<!--                            <div class="col-25 text-center"><a class="jiathis_button_tqq dinline"></a><div class="text-center rmt5">腾讯分享</div></div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="solid_t rline40 c_cc0 bg-white close-picker text-center">-->
<!--                        取消-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            <!-- Picker end-->


            <!--遮罩层开始-->
            <div class="modal-overlay" id="d1"></div>
            <!--遮罩层结束-->

        </div>
    </div>
</div>
{/block}
{block name='footer'}
<div class=" shop-bar rmb-5">
    <div class="over row">
        {if $isLogin==0}
        {if condition="$code neq ''"}
        <a href="{:url('login/index', ['code' => $code])}" class="external text-center c_fff col-lz-5 rline55 rfs18 bg_blue rmg0">
        {else/}
        <a href="{:url('login/index')}" class="external text-center c_fff col-lz-5 rline55 rfs18 bg_blue rmg0">
        {/if}
             登录/注册
        </a>
        <a  class="open-share text-center c_fff col-lz-5 rline55 rfs18 bg-4ac rmg0">分享</a>
<!--        <a href="{:url('shopkeeper/create')}"  data-picker=".picker-1" class=" c_fff rfs16 open-picker col-lz-5 bg-4ac text-center rline55 rmg0 rpd0 external">经销商申请</a>-->
        {else /}
        <a href="{:url('ucenter/index')}" class="external text-center c_fff col-lz-5 rline55 rfs18 bg_blue rmg0">
            我的主页
        </a>
            {if $isShopkeep==1}
            <a href="{:url('shopkeeper/sale')}"  data-picker=".picker-1" class=" c_fff rfs16 open-picker col-lz-5 bg-4ac text-center rline55 rmg0 rpd0 external">销售录入</a>
            {else /}
            <a  href="#"  class=" c_fff rfs16 open-share col-lz-5 bg-4ac text-center rline55 rmg0 rpd0 external">分享</a>
            {/if}
        {/if}
    </div>
</div>
<section class="screenW">
    <div class="subW">
        <div class="info">
            <div class="shareBox">
                <div class="bdsharebuttonbox"  style="margin-top: 10px;">
                    <a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间">QQ空间</a>
                    <a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博">新浪微博</a>
                    <a href="#" class="bds_sqq" data-cmd="sqq" title="分享到QQ好友">QQ</a>
                    <a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博">腾讯微博</a>
                    <a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信">微信</a>
                </div>
                <div class="bdsharebuttonbox">
                    <a href="#" onclick="return false;" class="popup_more" data-cmd="more"></a>
                </div>
            </div>
        </div>
        <div class="close">关闭</div>
    </div>
</section>
{/block}
{block name='script'}
<link rel="stylesheet" href="/static/nativeShare/nativeShare.css{$version}" />
<script type="text/javascript" src="__STATIC__/layer_mobile/layer.js{$version}"></script>
<script type="text/javascript" src="__STATIC__/nativeShare/nativeShare.js{$version}"></script>
<script type="text/javascript" src="__STATIC__/js/zepto.min.js{$version}"></script>

<script>

    window.onload = function() {
        $$('#content img').css({'width': '100%', 'height': 'auto'});
	}
    $$('.open-picker').on('click', function () {
        var div = document.getElementById('d1');
        div.className="modal-overlay-visible modal-overlay";
    });
    $$('.close-picker').on('click', function () {
        var div = document.getElementById('d1');
        div.className="modal-overlay";
    });
    //页面层
    $$('.open-share').on('click', function () {
        var UA = navigator.appVersion.toLowerCase();
        var isqqBrowser = (UA.split("mqqbrowser/").length > 1) ? true : false;
        var isucBrowser = (UA.split("ucbrowser/").length > 1) ? true : false;
        var isWeiXinBrowser = (UA.split("micromessenger/").length > 1) ? true : false;
        var content ="";

        if ((isqqBrowser && !isWeiXinBrowser) || isucBrowser) {
            content='<div id="nativeShare"></div>';
            layer.open({
                type: 1
                ,content: content
                ,anim: 'up'
                ,style: 'position:fixed; bottom:0; left:0; width: 100%; height: 200px; padding:10px 0; border:none;'
            });
            var config = {
                url:window.location.href+'?id='+{$platform.pl_id},
                title:"大唐云商-{$title}",
                desc:"{$platform.pl_description}",
                img:'{:url($platform.pl_image,'','',true)}',
                img_title:"{$title}",
                from:'大唐云商'
            };
            var share_obj = new nativeShare('nativeShare',config);
        }else {
            var goodsDetail = {
                node: {
                    closeBtn: $('.close'),
                    screenW: $('.screenW'),
                    subW: $('.subW'),
                    share: $('.open-share'),
                    shareBox: $('.shareBox')
                },
                /*入口*/
                init: function() {
                    var self = this;
                    self.closeTap();
                    self.shareTap();
                },
                /*分享点击弹窗*/
                shareTap: function() {
                    var self = this;
                    self.node.share.on('tap', function() {
                        self.wShow();
                        self.node.shareBox.show().siblings().hide();
                    });
                },
                /*点击关闭弹窗*/
                closeTap: function() {
                    var self = this;
                    self.node.closeBtn.on('tap', function() {
                        self.wHide();
                    });
                },
                /*窗口显示*/
                wShow: function() {
                    var self = this;
                    self.node.screenW.show();
                    self.node.subW.addClass('move').removeClass('back');
                },
                /*窗口隐藏*/
                wHide: function() {
                    var self = this;
                    self.node.subW.addClass('back').removeClass('move');
                    setTimeout(function() {
                        self.node.screenW.hide();
                    }, 500);
                }
            };
            /*商品js入口*/
            goodsDetail.init();

            /*百度分享js*/
            window._bd_share_config = {
                common: {
                    bdText : "大唐云商-{$title}",
                    bdDesc : '{$platform.pl_description}',
                    bdUrl : window.location.href+'?id='+{$platform.pl_id},
                    bdPic : '{:url($platform.pl_image,'','',true)}',
                },
                share: {},
                image: {
                    "viewList": ["qzone", "tsina", "sqq", "tqq", "weixin"],
                    "viewText": "分享到：",
                    "viewSize": "16"
                },
                selectShare: {
                    "bdContainerClass": null,
                    "bdSelectMiniList": ["qzone", "tsina", "sqq", "tqq", "weixin"]
                }
            };
            with(document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];
        }
    })
</script>
{/block}