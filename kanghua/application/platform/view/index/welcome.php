<!--<div class="bjui-pageHeader" style="background:#FFF;">-->
<!--    <div style="padding: 0 15px;">-->
<!--        <h4 style="margin-bottom:20px;"> 业务数据  </h4>-->
<!---->
<!--        <div class="container-fluid">-->
<!--            <div class="row">-->
<!--                <div class="col-md-2">-->
<!--                    <div class="alert alert-info text-center" role="alert" style="padding:20px 5px;"><h4>当前待处理订单</h4> <h3 class="center-block">{$data.orderCount|default='0'}</h3> </div></div>-->
<!--                <div class="col-md-2">-->
<!--                    <div class="alert alert-info text-center" role="alert" style="padding:20px 5px;"><h4>当前代理商 </h4><h3 class="center-block">{$data.agentCount|default='0'}</h3> </div> </div>-->
<!--                <div class="col-md-2">-->
<!--                    <div class="alert alert-info text-center" role="alert" style="padding:20px 5px;"><h4>当前经销商数</h4> <h3 class="center-block">{$data.shopkeeperCount|default='0'}</h3> </div> </div>-->
<!--                <div class="col-md-2">-->
<!--                    <div class="alert alert-info text-center" role="alert" style="padding:20px 5px;"><h4>当前在售商品数</h4> <h3 class="center-block">{$data.shopItemsCount|default='0'}</h3> </div></div>-->
<!--                <div class="col-md-2">-->
<!--                    <div class="alert alert-info text-center" role="alert" style="padding:20px 5px;"><h4>总订单数</h4> <h3 class="center-block">{$data.ordersAllCount|default='0'}</h3> </div></div>-->
<!--                <div class="col-md-2">-->
<!---->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!---->
<!--    </div>-->
<!--</div>-->
<div class="bjui-pageContent">
    {eq name="$roleId" value="1"}
    <div style="position:absolute;top:15px;right:0;width:300px;">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">本项目分享二维码</h3></div>
                <div class="panel-body bjui-doc" style="padding:0;">
                    <img src="{:url('index/qcode')}" width="100%" alt="">
                </div>
            </div>
        </div>
    </div>
    {/eq}
    <div style="margin-top:5px; margin-right:300px; overflow:hidden;">

        <div class="row" style="padding: 0 8px;">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><h3 class="panel-title">业务数据</h3></div>
                    <div class="panel-body bjui-doc" style="padding:0;">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="alert alert-info text-center" data-toggle="navtab" data-type="POST" data-url="{:url('order/index')}" data-id="platform_Order_index" data-title="订单列表" role="alert" style="padding:20px 5px;"><h4>当前待处理订单</h4> <h3 class="center-block">{$data.orderCount|default='0'}</h3> </div></div>
                            <div class="col-md-2">
                                <div class="alert alert-info text-center" data-toggle="navtab" data-type="POST" data-url="{:url('agent/index')}" data-id="platform_Agent_index" data-title="代理商列表" role="alert" style="padding:20px 5px;">
                                    <h4>当前代理商 </h4><h3 class="center-block">{$data.agentCount|default='0'}</h3>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="alert alert-info text-center" data-toggle="navtab" data-type="POST" data-url="{:url('shopkeeper/index')}" data-id="platform_Shopkeeper_index" data-title="经销商列表" role="alert" style="padding:20px 5px;">
                                    <h4>当前经销商数</h4> <h3 class="center-block">{$data.shopkeeperCount|default='0'}</h3>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="alert alert-info text-center" data-toggle="navtab" data-type="POST" data-url="{:url('sale/goodslist')}" data-id="platform_sale_goodsList" data-title="商品列表" role="alert" style="padding:20px 5px;">
                                    <h4>当前在售商品数</h4> <h3 class="center-block">{$data.shopItemsCount|default='0'}</h3>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="alert alert-info text-center" data-toggle="navtab" data-type="POST" data-url="{:url('order/index')}" data-id="platform_Order_index" data-title="订单列表" role="alert" style="padding:20px 5px;"><h4>
                                        总订单数</h4> <h3 class="center-block">{$data.ordersAllCount|default='0'}</h3>
                                </div>
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row" style="padding: 0 8px;">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><h3 class="panel-title">公告/消息</h3></div>
                    <div class="panel-body bjui-doc" style="padding:0;">
                        <ul>
                            <li>欢迎来到大唐分销系统</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>