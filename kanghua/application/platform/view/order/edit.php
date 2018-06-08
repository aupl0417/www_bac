<div class="bjui-pageContent tableContent">
    <form action="{:url('')}" id="j_custom_form" data-toggle="validate" data-alertmsg="false">
        <input type="hidden" name="id" value="{$id}">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label class="control-label x90">订单号：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_id}</label>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <label class="control-label x90">商品主图：</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        <span id="j_custom_span_pic"><a href="{$order.og_goods_img}" target="_blank"><img src="{$order.og_goods_img}" width="100" /></a></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">型号：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.og_goods_sku}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">数量：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.og_goods_num}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">单价（￥）：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.og_goods_price}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">总金额（￥）：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_actual_payprice}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">购买人：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_buyer_nick}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">购买人电话：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_seller_phone}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">收货人姓名：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_receiver_name}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">收货人电话：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_receiver_phone}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">收货人地址：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.province}{$order.city}{$order.region}{$order.os_address}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">购买时间：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_create_time|date='Y-m-d H:i:s', ###}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">支付时间：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_pay_time|date='Y-m-d H:i:s', ###}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">支付方式：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_pay_type}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">买家留言：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_buyer_note}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">奖励积分：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_score}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">配送方式：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_deliver_name}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">物流号码：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_deliver_num}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">发货时间：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_deliver_time}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label x90">运费：</label>
                    <label class="" style="margin-left: 0px;padding-left: 0px;">{$order.os_deliver_price}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="status" class="control-label x90">订单状态<label class="btn-red">*</label></label>
                    <select id="status" name="status" data-rule="required" data-width="100" data-toggle="selectpicker">
                        <option value="">--请选择--</option>
                        <option value="0">--未付款--</option>
                        <option value="1">--已付款--</option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
        <li><button type="submit" class="btn-default">保存</button></li>
    </ul>
</div>