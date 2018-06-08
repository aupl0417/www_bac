{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="javascript:window.history.go(-1);" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
    <div class="right">
        <a href="#"  id="open-share"><img src="__STATIC__/wap/images/fx-icon.png" alt=""></a>
    </div>
{/block}
{block name="content"}
<link rel="stylesheet" href="/static/nativeShare/baidushare.css?v={$version}" />
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-white" style="padding-bottom: 1.449rem">
            <div class="rpd10 solid_b">
                <div class="c_333 rfs16">{$res.bg_name}</div>
                <div class="rmt5 c_cc0 rmb5"> ￥<span class=" rfs16">{$res.bg_price}</span><div class="pull-right"><span class="c_666">奖励积分：</span>{$res.bg_price*$res.bg_scoreReward}</div></div>
            </div>
            <div class="rpd10" id="content">
               {$res.bg_content}
            </div>


            <!-- Picker-buy star-->
            <div class="picker-modal picker-2 bg-white" style="height: 7.5rem;">
                <form action="{:url('order/docreate')}" method="post" id="orderform">
                <div class="picker-modal-inner bg-white">
                    <div class="content-block bg-white over rmg0 rpd0 solid_last">
                        <div class="rpd15 solid_b">
                            <div class="media">
                                <div class="media-left">
                                    <div style="width: 50px" class="">
                                        <img src="{$res.bg_image}" width="50" class="bor_img">
                                    </div>
                                </div>
                                <div class="media-body">
                                    <div class="rfs14 rmb10">{$res.bg_name}</div>
                                    <div class="rfs16" id="goods_price">￥{$res.bg_price}</div>
                                </div>
                            </div>
                        </div>
                        <div class="solid_b">
                            <div class="row mr0 phong_form rpl15">
                                <div class="col-33 text_left rfs16 c_333 rline55">
                                    选择规格
                                </div>
                                <div class="col-66 input_center rpl0 input-radius-active">
                                    {notempty name="models"}
                                        {foreach name="models" item="vo"}
                                        <label class="radius"><input type="radio" name="modeltype" data-price="{$vo.bg_price}" {eq name="$vo.si_id" value="$res.si_id" } checked="checked" {/eq} value="{$vo.si_id}" /><em class="pull-left"></em>
                                            <span class="small_xs ml10">{$vo.bg_model}</span>
                                        </label>
                                        {/foreach}
                                    {/notempty}
                                </div>
                            </div>
                        </div>
                        <div class="solid_b rmt10">
                            <div class="row mr0 phong_form rpl15">
                                <div class="col-33 text_left rfs16 c_333">
                                    购买数量
                                </div>
                                <div class="col-66 input_center rpl0 input-radius-active">
                                    <div class="input-group no-margin pull-left rmt5">
                                                <span class="input-group-btn btn_bon pull-left">
                                                    <button type="button" class="btn btn-default btn-d btn-data"><i class="fa fa-minus c_666"></i></button>
                                                </span>
                                        <input type="text" name="number" class="form-control text-center pull-left nub-input form-data" style="border-left:none" placeholder="数量" value="1">
                                        <span class="input-group-btn btn_top pull-left">
                                                    <button type="button" class="btn btn-default btn-a btn-data"><i class="fa fa-plus c_666"></i></button>
                                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rline40 c_cc0 bg-white text-center row rpl15 rpr15" style="padding-top: 15px;">
                        <div class="col-50">
                            <a href="" class="button button-raised c_333 rfs16 rline40 rh40 solid_all-e6e close-picker">取消</a>
                        </div>
                        <div class="col-50">
                            <a href="#" id="createOrder" class="button my-btn-blue button-raised c_333 rfs16 rline40 rh40 ">确定</a>
                        </div>
                    </div>
                </div>
                    <input type="hidden" name="sid" id="goods_sid" value="{$res.si_id}">
                    <input type="hidden" name="number" id="goods_number" value="1">
                </form>
            </div>
            <!-- Picker-buy end-->
            <!-- Picker star-->
<!--            <div class="picker-modal picker-1 bg-white">-->
<!--                <div class="picker-modal-inner bg-white">-->
<!--                    <div class="content-block bg-white over popup-cont">-->
<!--                        <div class="jiathis_style_32x32 mt-5 row">-->
<!--                            <div class="col-25 text-center"><a class="jiathis_button_tsina dinline"></a><div class="text-center">新浪分享</div></div>-->
<!--                            <div class="col-25 text-center"><a class="jiathis_button_weixin dinline"></a><div class="text-center">微信分享</div></div>-->
<!--                            <div class="col-25 text-center"><a class="jiathis_button_qzone dinline"></a><div class="text-center">QQ空间分享</div></div>-->
<!--                            <div class="col-25 text-center"><a class="jiathis_button_tqq dinline"></a><div class="text-center">腾讯分享</div></div>-->
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
{block name="footer"}
<div class=" shop-bar">
    <div class="over">
        <a href="{:url('store/index')}" class="text-center external pull-left shop-bar-icon c_333">
            <div class="shop-bar-img">
                <img src="__STATIC__/wap/images/dd-icon.png" alt="">
            </div>
            <div class="rfs14 shop-bar-text">公司简介</div>
        </a>
        <a href="{:url('ucenter/index')}" class="text-center external pull-left shop-bar-icon c_333">
            <div class="shop-bar-img">
                <img src="__STATIC__/wap/images/grzx-icon.png" alt="">
            </div>
            <div class="rfs14 shop-bar-text">个人中心</div>
        </a>
        <a href="#"  data-picker=".picker-2" class="pull-right shop-bar-buybtn rfs16 open-picker">立即购买</a>
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

{block name="script"}
<link rel="stylesheet" href="/static/nativeShare/nativeShare.css{$version}" />
<script type="text/javascript" src="__STATIC__/layer_mobile/layer.js{$version}"></script>
<script type="text/javascript" src="__STATIC__/nativeShare/nativeShare.js{$version}"></script>
<script type="text/javascript" src="__STATIC__/js/zepto.min.js{$version}"></script>

<script>
    $$('#content img').css({'width': '100%', 'height': 'auto'});
    $$('.close-picker').on('click', function () {
        var div = document.getElementById('d1');
        div.className="modal-overlay";
    });

    $$(".radius").on('click',function () {
        var price =$$(this).find('input').data('price');
        var sid =$$(this).find('input').val();
        $$('#goods_price').text('￥'+price);
        $$('#goods_sid').val(sid);
    })

    $$('#createOrder').on('click',function () {
        var sid =$$("input[name='modeltype']:checked").val();
        var number =$$("input[name='number']").val()
        $$('#goods_number').val(number);
        $$('#orderform').submit();
    })

    //页面层
    $$('#open-share').on('click', function () {
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
                url:window.location.href,
                title:"{$res.bg_name}",
                desc:"{$res.bg_name}",
                img:'{:url($res.bg_image,'','',true)}',
                img_title:"{$res.bg_name}",
                from:'大唐云商'
            };
            var share_obj = new nativeShare('nativeShare',config);
        }else {
            var goodsDetail = {
                node: {
                    closeBtn: $('.close'),
                    screenW: $('.screenW'),
                    subW: $('.subW'),
                    share: $('#open-share'),
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
                    bdText : "{$res.bg_name}",
                    bdDesc : '{$res.bg_name}',
                    bdUrl : window.location.href,
                    bdPic : '{:url($res.bg_image,'','',true)}',
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
    $('#content img').css({'max-width':'', 'width':'100%'});

    window.onload = function(){
        var oNum = document.getElementsByClassName('btn_top');
        var oBon = document.getElementsByClassName('btn_bon');
        for(i=0;i<oNum.length; i++){
            oNum[i].onclick = function(){
                var abc = Number(this.parentNode.getElementsByTagName('input')[0].value);
                this.parentNode.getElementsByTagName('input')[0].value = abc+1;
            }
        }
        for(i=0;i<oBon.length; i++){
            oBon[i].onclick = function(){
                var abc = Number(this.parentNode.getElementsByTagName('input')[0].value);
                this.parentNode.getElementsByTagName('input')[0].value = abc-1;
                if(Number(this.parentNode.getElementsByTagName('input')[0].value)<1){
                    this.parentNode.getElementsByTagName('input')[0].value=1
                }
            }
        }
    }

</script>
{/block}