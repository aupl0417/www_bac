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
        <div class="page-content">
            <div class="row rpt15 rpb15 no-gutter my-user-tit">
                <div class="col-33 text-center rfs16 c_333">会员名</div>
                <div class="col-33 text-center rfs16 c_333">姓名</div>
                <div class="col-33 text-center rfs16 c_333">激活时间</div>
            </div>
            <div class="list-block accordion-list rmg0 my-user-block">
                <ul>
                    {foreach $list as $vo}
                    <li class="accordion-item">
                        <a href="#" class="item-link item-content rpl0 bg_fff">
                            <div class="item-inner solid_b">
                                <div class="item-title row no-gutter">
                                    <div class="col-33 text-center rfs14 c_333 rpl15 text-left">{$vo.nick}</div>
                                    <div class="col-33 text-center rfs14 c_333">{$vo.name}</div>
                                    <div class="col-33 text-center rfs14">{$vo.createTime|date='Y-m-d H:i:s', ###}</div>
                                </div>
                            </div>
                        </a>
                        <div class="accordion-item-content">
                            <div class="content-block row no-gutter rpt15 rpb15 bg_fff">
                                <div class="col-50 c_999 rfs14">电话:{$vo.tel}</div>
                                <div class="col-50 c_999 rfs14">所属区域:{$vo.province}{$vo.city}</div>
                            </div>
                        </div>
                    </li>
                    {/foreach}
                </ul>
            </div>
        </div><!--.page-content-->
    </div><!--.page-->
</div><!--.pages navbar-fixed-->
{/block}
{block name='script'}

{/block}