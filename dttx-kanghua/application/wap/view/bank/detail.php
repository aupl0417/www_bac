{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="{:url('bank/index')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f" style="padding-bottom: 1.449rem">
            <div class="bg_fff solid_last rmb10 solid_b">
                <div class="solid_b">
                    <div class=" mr0 phong_form rpl15 rline55 c_333 rfs16">
                        {$cardInfo.bankName}<span class="c_666">(储蓄卡)</span>
                    </div>
                </div>
                <div class="solid_b">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            持卡人
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-left">
                            {$cardInfo.accountName}
                        </div>
                    </div>
                </div>
                <div class="solid_b">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            卡号
                        </div>
                        <div class="col-66 text_right rpl0 input-radius-active text-left">
                            {$cardInfo.cardNumber}
                        </div>
                    </div>
                </div>
            </div>

            <div class="rmt10 rpd10">
                <a data-url="{:url('Bank/remove', ['id' => $cardInfo.id])}" id="unbind" class="button my-btn-red button-raised rline50 rfs16 rh50">解除绑定</a>
            </div>
        </div>

    </div>
</div>
{/block}

{block name='script'}
<script>
    $$('#unbind').on('click', function () {
        $$.ajax({
            type: "GET",
            url: $$(this).data('url'),
            dataType: "json",
            success: function(data){
                if(data.statusCode == 200){
                    myApp.alert(data.message, '提示', function () {
                        window.location.href = "{:url('Bank/index')}";
                    });
                }else if(data.statusCode == 301){
                    myApp.alert(data.message.msg, '提示', function () {
                        window.location.href = data.message.url;
                    });
                }else {
                    myApp.alert(data.message, '提示');
                }
            }
        });
    });
</script>
{/block}