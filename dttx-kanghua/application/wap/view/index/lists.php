{extend name="common:base" /}
{block name="leftnav"}
<!--<div class="left"><a href="javascript:window.history.go(-1);" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>-->
{/block}
{block name='header'}
{/block}
{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f">
            <div class="cloud-cont">
                <ul class="cloud-ul">
                    {foreach $list as $vo}
                    <li>
                        <a href="{:url('store/index', ['id' => $vo.pl_id])}" class="external">
                        <div class="cloud-li-img">
                            <img src="{$vo.pl_image}" alt="{$vo.pl_name}" width="100%" height="145px;">
                        </div>
                        <div class="cloud-li-text">
                            <div class="rfs18 c_333">{$vo.pl_name}</div>
                            <div class="cloud-li-text-subtitle fs12" style="margin-top: 5px;">
                                {$vo.pl_description}
                            </div>
                            <i class="fa fa-angle-right"></i>
                        </div>
                        </a>
                    </li>
                    {/foreach}
                </ul>
                <div class="rmt10 rmb10">
                    <a href="{:url('index/investment')}" class="db rfs16 c_333 text-center rpd10 bg-white external">招商加盟</a>
                </div>
            </div>
        </div>

    </div>
</div>
{/block}