{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="javascript:window.history.go(-1);" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='header'}
{/block}
{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f">
            <div class="amg rpb10">
                <img src="__STATIC__/wap/images/cloud-banner2.png" alt="">
            </div>
            <form action="{:url('')}" method="post">
                <div class="solid_last rmb15 solid_b">
                    <div class="solid_b bg_fff rmb5">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                产品名称<span class="c_red">*</span>
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input type="text" name="product_name" class="rfs14 bor_no rpl0" placeholder="需要进行推广的产品" value="">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b bg_fff rmb5">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                企业名称<span class="c_red">*</span>
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input type="text" name="company_name" class="rfs14 bor_no rpl0" placeholder="产品所属的企业" value="">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b bg_fff rmb5">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                联系人姓名<span class="c_red">*</span>
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input type="text" name="username" class="rfs14 bor_no rpl0" placeholder="产品负责人" value="">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b bg_fff rmb5">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                联系人电话<span class="c_red">*</span>
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input type="text" name="mobile" class="rfs14 bor_no rpl0" placeholder="产品负责人联系电话" value="">
                            </div>
                        </div>
                    </div>
                    <div class="solid_b bg_fff rmb5">
                        <div class="row mr0 phong_form rpl15">
                            <div class="col-33 text_left rfs16 c_333">
                                大唐天下账号
                            </div>
                            <div class="col-66 input_center rpl0">
                                <input type="text" name="dttx_nick" class="rfs14 bor_no rpl0" placeholder="公司或负责人大唐天下账号" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rpl20 rpr20">
                    <div class="row">
                        <div class="col-50">
                            <button type="button" id="cancel" class="button db bfb100 my-btn-default rline40 rh40 rfs16">取消</button>
                        </div>
                        <div class="col-50">
                            <button type="submit" class="button my-btn-blue db bfb bfb100 rline40 rh40 rfs16">确定</button>

                        </div>
                    </div>
                    <div class="rmt10 c_c4c rfs14 text-center rpl25 rpr25">
                        数据提交后，请耐心等待，大唐云商工作 人员将会尽快联系你
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>
{/block}
{block name="script"}
<script>

    $$('#cancel').on('click', function () {
        window.history.back();
    });

</script>
{/block}