<!--
 * 订单详情
 * User: lirong
 * Date: 2017/7/18
 * Time: 10:59
-->
{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="javascript:window.history.go(-1);" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">
    <a href="{:url('ucenter/index')}" class="back link external"><img src="__STATIC__/wap/images/home.png" width="20" alt="返回个人中心"></a>
</div><!--.right-->
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content bg_fff">
            <div class="bg_fff rpd15 over">
                <div class="pull-left">
                    <div class="wl-box rmr20">
                        <img src="{$data['goods'][0]['og_goods_img']}" width="100" alt="">
                        <span class="c_fff">{$data.os_goods_num}件商品</span>
                    </div>
                </div><!--.pull-left-->
                <div class="pull-left">
                    <p class="rfs14 c_333 rmt0">
                        物流状态：
                        <span class="c_blue">{if $data.os_status >1}已发货{else/}未发货{/if}</span>
                    </p>
                    <p class="rfs14 c_333 rmt0">
                        承运公司：
                        <span class="c_999">{$data.os_deliver_name|default=""}</span>
                    </p>
                    <p class="rfs14 c_333 rmt0">
                        运单编号：
                        <span class="c_999">{$data.os_deliver_num|default=""}</span>
                    </p>
<!--                    <p class="rfs14 c_333 rmt0">-->
<!--                        官方电话：-->
<!--                        <span class="c_red">95554</span>-->
<!--                    </p>-->
                </div>
            </div><!--.over-->
            <div class="rpt5 rpb10 bg_gray"></div>
<!--            <div class="rfs16 c_333 solid_b rpd15 bg_fff">物流跟踪</div>-->
<!--            <div class="company-introduce bg_fff rpd15">-->
<!---->
               <!--物流时间轴开始-->
<!--                <div class="re time-course rpl15 rpr15 bg_fff ">-->
<!--                    <div class="time-node1 re rpl15">-->
<!--                        <div class="rfs14 rmg0 rmb30 over">-->
<!--                            <div class="pull-left">-->
<!--                                <div>-->
<!--                                    <span class="c_blue">快递员正在派件</span>-->
<!--                                </div>-->
<!--                                <span class="c_333">派件员:李志强 : </span>-->
<!--                                <span class="c_red">1357765123</span>-->
<!--                            </div>-->
<!--                            <div class="pull-right rfs12 c_blue  text-right">-->
<!--                                2017-02-22<br>-->
<!--                                15:20:43-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <span class="time-node active"></span>-->
<!--                    </div>-->
<!--                    <div class="time-node1 re rpl15">-->
<!--                        <div class="rfs14 rmg0 rmb30 over">-->
<!--                            <div class="pull-left">-->
<!--                                <div>-->
<!--                                    <span class="c_666">快件已到达浙江杭州紫金港</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="pull-right rfs12 c_666 text-right">-->
<!--                                2017-02-22<br>-->
<!--                                15:20:43-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <span class="time-node"></span>-->
<!--                    </div>-->
<!--                    <div class="time-node1 re rpl15">-->
<!--                        <div class="rfs14 rmg0 rmb30 over">-->
<!--                            <div class="pull-left">-->
<!--                                <div>-->
<!--                                    <span class="c_666">快件已到达浙江杭州</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="pull-right rfs12 c_666 text-right">-->
<!--                                2017-02-22<br>-->
<!--                                15:20:43-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <span class="time-node"></span>-->
<!--                    </div>-->
<!--                    <div class="time-node1 re rpl15">-->
<!--                        <div class="rfs14 rmg0 rmb30 over">-->
<!--                            <div class="pull-left">-->
<!--                                <div>-->
<!--                                    <span class="c_666">安徽合肥已发出</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="pull-right rfs12 c_666 text-right">-->
<!--                                2017-02-22<br>-->
<!--                                15:20:43-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <span class="time-node"></span>-->
<!--                    </div>-->
<!--                    <div class="time-node1 re rpl15">-->
<!--                        <div class="rfs14 rmg0 rmb30 over">-->
<!--                            <div class="pull-left">-->
<!--                                <div>-->
<!--                                    <span class="c_666">收件员已揽件</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="pull-right rfs12 c_666 text-right">-->
<!--                                2017-02-22<br>-->
<!--                                15:20:43-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <span class="time-node"></span>-->
<!--                    </div>-->
<!--                    <div class="time-node1 re rpl15">-->
<!--                        <div class="rfs14 rmg0 rmb30 over">-->
<!--                            <div class="pull-left">-->
<!--                                <div>-->
<!--                                    <span class="c_666">包裹正在等待揽件</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="pull-right rfs12 c_666 text-right">-->
<!--                                2017-02-22<br>-->
<!--                                15:20:43-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <span class="time-node"></span>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div><!--.page-content-->
    </div><!--.page-->
</div>
{/block}

