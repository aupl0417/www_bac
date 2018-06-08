{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('user/profile')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f">
            <div class="bg_fff solid_last rmb10 solid_b">
                {foreach $bankList as $vo}
                <div class="solid_b">
                    <div class="media" data-url="{:url('Bank/detail', ['id' => $vo.id])}">
                        <div class="media-left">
                            <!--<div style="width: 30px" class="rmt10 rpl15">
                                <img src="__STATIC__/wap/images/zggs-icon.png" alt="" width="30">
                            </div>-->
                        </div>
                        <div class="media-body">
                            <div class="rfs18 c_333 rmb10 rmt10">
                                {$vo.bankName} (储蓄卡)
                            </div>
                            <div class=" rmb10 c_666 rfs16">
                                <!--***  ****  **** 3698-->
                                {$vo.cardNumber}
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}

            </div>
            <div class="bg_fff solid_last solid_b rmt10">

                <div class="solid_b">
                    <a href="{:url('Bank/create')}" id="add_bank" class="c_cc0 external">
                        <div class=" mr0 rpl15 c_cc0 rfs16 rline55 text-center">
                            <i class="fa fa-plus-square-o rfs16"></i> 添加银行卡
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
{/block}

{block name='script'}
<script>

    $$('.media').on('click', function () {
        var url = $$(this).data('url');
        $$.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function(data){
                if(data.statusCode == 300){
                    alertLayout(data.message)
                }else{
                    window.location.href = url;
                }
            }
        });
    });

</script>
{/block}