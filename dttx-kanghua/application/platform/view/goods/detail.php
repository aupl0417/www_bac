<div class="bjui-pageContent tableContent">
    <form action="{:url('')}" id="j_custom_form" data-toggle="validate" data-alertmsg="false">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label for="name" class="control-label x90">商品名称：</label>
                    <label>{$goods.bg_name}</label>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <label class="control-label x90">商品主图：</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        <span id="j_custom_span_pic"><img src="{$goods.bg_image}" width="100" /></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="scoreReward" class="control-label x90">积分奖励：</label>
                    <label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.bg_scoreReward}%</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="cateId" class="control-label x90">商品分类：</label>
                    <label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.ca_name}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="control-label x90">是否上架：</label>
                    <label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{if condition="$goods.bg_isSale eq 0"}未上架{elseif condition="$goods.bg_isSale eq 1"}已上架{elseif condition="$goods.bg_isSale eq 2"}已下架{/if}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="channel" class="control-label x90">商品属性：</label>
                    <div style="display: inline-block; vertical-align: middle;width: 900px;">
                    <table id="tabledit1" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th title="型号" align="center" width="100px">型号</th>
                            <th title="规格" align="center" width="100px">规格</th>
                            <th title="单位" align="center" width="100px">单位</th>
                            <th title="成本价格" align="center" width="100px">成本价格（￥）</th>
                            <th title="建议零售价" align="center" width="100px">建议零售价（￥）</th>
                            <th title="库存量" align="center" width="100px">库存量</th>
                            <th title="装箱规格" align="center" width="100px">装箱规格</th>
                            <th title="说明" align="center" width="380px">说明</th>
                        </tr>
                        </thead>
                            <tr>
                                <td align="center"><label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.bg_model}</label></td>
                                <td align="center"><label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.bg_format}</label></td>
                                <td align="center"><label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.bg_unit}</label></td>
                                <td align="center"><label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.bg_cost}</label></td>
                                <td align="center"><label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.bg_price}</label></td>
                                <td align="center"><label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.gs_goodsStock}</label></td>
                                <td align="center"><label for="name" class="x90" style="margin-left: 0px;padding-left: 0px;">{$goods.bg_packFormat}</label></td>
                                <td align="center"><label for="name" style="margin-left: 0px;padding-left: 0px;">{$goods.bg_instruction}</label></td>
                            </tr>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="content" class="control-label x90">商品详情：</label>
                    <div style="display: inline-block; vertical-align: middle;width: 900px;">
                        {$goods.bg_content}
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
    </ul>
</div>