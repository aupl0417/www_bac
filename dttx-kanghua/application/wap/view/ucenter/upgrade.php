{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/index')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">

</div>
{/block}
{block name="content"}
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content rpd15">

            {foreach name='$userlevel' item='level'}
                <div class="bg_fff rmt15 bdr_5">
                    {if condition="$user.up_user_level_id eq $level.ul_id"}
                    <div class="over solid_b rpd15 rpb20">
                        <div class="pull-left c_333 rfs16">
                            <span class="c_red rfs16">{$user.ul_name}</span>
                        </div>
                        <a data-url="{:url('ucenter/upgradeDeal')}" disabled class="button active pull-right my-btn-default external upgrade">已升级</a>
                    </div><!--.over-->
                    {else}
                    <div class="over solid_b rpd15 rpb20">
                        <form action="{:url('ucenter/upgradepay')}" method="post">
                        <div class="pull-left c_333 rfs16">
                            升级为 <span class="c_red">{$level.ul_name}</span>
                            <span class="c_red rfs16">{$level.ul_money}</span>
                        </div>
                            <div class="pull-right">
                                <input type="hidden"  name="uid" value="{$user.up_id}">
                                <input type="hidden"  name="levelid" value="{$level.ul_id}">
                                <input type="submit" {if condition="$level.ul_status eq 0"}disabled="disabled"{/if} value="立即升级" class="button active my-btn-blue">
                            </div>
                        </form>
                    </div><!--.over-->
                    {/if}
                    <div class="rpd15">
                        <p class="rfs12 c_999" style="line-height: 24px;">{$level.ul_level_mark|nl2br}</p>
                    </div>
                </div><!--bg_fff-->
            {/foreach}

        </div><!--.page-content-->
    </div><!--.page-->
</div><!--.pages navbar-fixed-->
{/block}
{block name='script'}
<script>

    $$('.upgrade').on('click', function () {
        var url = $$(this).data('url');
        $$.ajax({
            type: "get",
            url: url,
            dataType: "json",
            success: function(data){

                if(data.statusCode == 200){
                    alertLayout(data.message, '提示');
                    setTimeout(function () {
                        window.location.href = "{:url('ucenter/index')}";
                    }, 2000);
                }else if(data.statusCode == 301){
                    alertLayout(data.message.msg, '提示');
                    setTimeout(function () {
                        window.location.href = data.message.url;
                    }, 2000);
                }else{
                    alertLayout(data.message, '提示');
                }
            }
        });
    });

</script>
{/block}