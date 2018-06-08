{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/index')}" class="back link external""><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">

</div>
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content bg_gray">
            <div class="bg_fff ma-box rpl15 rpr15 rpb40">
                <h6 class="rfs16 text-center c_333 rpt15 rpb15">扫码激活{$platformdata.pl_name}会员</h6>
                <div>
                    <img width="100%" src="{:url('ucenter/qcode', ['code' => $code])}" alt="">
                </div>
                <p class="rfs14 c_666 text-center rmb40">
                    邀请码:
                    <span class="c_blue">{$code}</span>
                </p>
                <span class="y-left bg_gray"></span>
                <span class="y-right bg_gray"></span>
                <span class="sha-bottom"></span>
            </div><!--.bg_fff ma-box rpl15 rpr15 rpb40-->
        </div><!--.page-content-->
    </div><!--.page-->
{/block}
{block name='script'}

{/block}