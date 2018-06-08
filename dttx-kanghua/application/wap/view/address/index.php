{extend name="common:base" /}
{block name="leftnav"}
<div class="left"><a href="javascript:window.history.go(-1);" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name='header'}
<div class="right">
    <a href="{:url('ucenter/index')}" class="back link external"><img src="__STATIC__/wap/images/home.png" width="20" alt="返回个人中心"></a>
</div>
{/block}
{block name='content'}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f">

            <form action="">
                <!-- On both sides -->
                <div class="list-block media-list rmg0 ad-list-block rpb40 rmb15">
                    <ul>
                        {foreach $address as $vo}
                        <li class="swipeout">
                            <div class="swipeout-content">
                                <div class="item-link item-content">
                                    <div class="item-inner">
                                        <div class="over rmb15 rpl15 rpr15 get_delivery_address" data-id="{$vo.id}">
                                            <div class="pull-left small_xs rfs16 c_333">{$vo.nick}</div>
                                            <div class="pull-right c_333 rfs14">{$vo.phone}</div>
                                        </div>
                                        <div class="item-subtitle white-normal solid_b rpb10 rpl15 rpr15 get_delivery_address" data-id="{$vo.id}">{$vo.area} {$vo.address}</div>
                                        <div class="rpt10 rpb10 rpl15 rpr15">
                                            <label class="square radius" data-id="{$vo.id}" data-url="{:url('Address/setAddress', ['id' => $vo.id])}">
                                                <input type="radio" name="isDefault" class="isDefault" data-id="{$vo.id}" {if condition="$vo.isDefault eq 1"}checked{/if} value='{$vo.isDefault}'>
                                                <em class="pull-left" style="margin-top: 2px"></em>
                                                <span class="small_xs rml10 rfs16">默认地址</span>
                                            </label>
                                            <div class="pull-right rfs16 rline26">
                                                <a href="{:url('Address/edit', ['id' => $vo.id])}" class="edit_address c_666 external">
                                                    <i class="gi gi-edit rmr5 rmb5"></i>编辑
                                                </a>
                                                <a data-url="{:url('Address/remove', ['id' => $vo.id])}" class="c_666 swipeout-delete">
                                                    <i class="fa fa-trash-o rml5 rmr5 "></i>删除
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
<!--                                <div class="swipeout-actions-right"><a data-url="{:url('Address/edit', ['id' => $vo.id])}" class="edit_address demo-mark bg-orange external">编辑</a><a data-url="{:url('Address/edit', ['id' => $vo.id])}" class="swipeout-delete">删除</a></div>-->
                            </div>
                            <div style="height: 10px;" class="bg-f5f"></div>

                        </li>
                        {/foreach}
                    </ul>
                </div>

            </form>

        </div>
        <a class="addaddress-box text-center c_fff rfs16 external" style="z-index: 9999999;" href="{:url('Address/create')}">
            <i class="fa fa-plus-square rfs20 c_fff"></i>
            添加新地址
        </a>
    </div>
</div>
{/block}
{block name='script'}
<script>

    $$('.get_delivery_address').on('click', function () {
        var go = "{$go}";
        var id = $$(this).data('id');
//        console.log(go + '__');return false;
        var url = "{:url('order/createorder')}?asid=" + id;
        if(go == 'createorder'){
            window.location.href = url;
        }
    });

    $$('.square').on('click', function () {
        var url   = $$(this).data('url');
        var obj   = $$(this).find('input');
//        if(obj.val() == 0){
            $$.ajax({
                type: "GET",
                url : url,
                dataType: "json",
                success: function(data){
//                    console.log(data);return false;
                    if(data.statusCode == 200){
                        {eq name='go' value='createorder'}
                            window.location.href="{:url('order/createorder')}";
                        {else /}
                            window.location.reload();
                        {/eq}
                    }else if(data.statusCode == 301){
                        myApp.alert(data.message.msg, '提示', function () {
                            window.location.href = data.message.url;
                        });
                    }
                }
            });
//        }
    });

    $$('.swipeout-delete').on('click', function () {
        var url = $$(this).data('url');
        myApp.confirm('确定要删除该地址？', '提示', function () {
            $$.ajax({
                type: "GET",
                url : url,
                dataType: "json",
                success: function(data){
                    if(data.statusCode == 200){
                        myApp.alert(data.message, '提示', function () {
                            window.location.reload();
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
        }, function () {
            window.location.reload();
        });
    });

</script>
{/block}