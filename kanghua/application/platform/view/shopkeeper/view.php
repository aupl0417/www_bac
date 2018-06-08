<div class="bjui-pageContent tableContent">
        <table class="table table-condensed table-hover">
            <tbody>
            <tr>
                <td>
                    <label for="" class="control-label x85">经销商类型</label>
                    <input type="radio" disabled="disabled" class="shopType" data-toggle="icheck" value="0" data-rule="checked" checked data-label="实体经销商&nbsp;&nbsp;">
                    <input type="radio" disabled="disabled" class="shopType" data-toggle="icheck" value="1" {if condition="$shop.s_type eq 1"}data-rule="checked" checked{/if} data-label="网络经销商（电商）">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name" class="control-label x90">经销商姓名</label>
                    <label for="name" class="" style="margin-left: 0px;padding-left: 0px;">{$shop.s_userTrueName}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name" class="control-label x90">大唐会员名</label>
                    <label for="name" class="">{$shop.s_userDttxNick}</label>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="name" class="control-label x90">经销商区域</label>
                    <select id="provinceCode" disabled="disabled" data-width="100" data-toggle="selectpicker">
                        <option value="all">{$province}</option>
                    </select>
                    <select id="cityCode" disabled="disabled"  data-width="100" data-toggle="selectpicker" data-emptytxt="{$city}" >
                        <option value="">{$city}</option>
                    </select>
                    <select id="regionCode" disabled="disabled" data-width="100" data-toggle="selectpicker" data-emptytxt="{$region}" >
                        <option value="">{$region}</option>
                    </select>

                </td>
            </tr>
            <tr>
                <td>
                    <label for="shopName" class="control-label x90">店铺名称</label>
                    <label for="name" class="">{$shop.s_name}</label>
                </td>
            </tr>
            <tr id="realAddress">
                <td>
                    <label for="realAddress" class="control-label x90">店铺地址</label>
                    <label for="name" class="">{$shop.s_address}</label>
                </td>
            </tr>
            <tr id="webAddress" style="display: none;">
                <td>
                    <label for="webAddress" class="control-label x90">店铺网址</label>
                    <label for="name" class="">{$shop.s_address}</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="" class="control-label x85">发货方式</label>
                    <input type="radio"  data-toggle="icheck" disabled value="0" data-rule="checked" checked data-label="总部发货&nbsp;&nbsp;">
                    <input type="radio"  data-toggle="icheck" disabled value="1" {if condition="$shop.s_delivery eq 1"}data-rule="checked" checked{/if}data-label="自行发货">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="weight" class="control-label x90">店铺简介</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        {$shop.s_description}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="content" class="control-label x90">店铺详情</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        {$shop.s_content}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name" class="control-label x90">审核状态<label class="btn-red">*</label></label>
                    <select id="provinceCode" name="state" disabled data-width="100" data-toggle="selectpicker">
                        <option value="">--请选择--</option>
                        <option value="pass" {if condition="$shop.s_state eq 'pass'"}selected="selected"{/if}>--审核通过--</option>
                        <option value="deny" {if condition="$shop.s_state eq 'deny'"}selected="selected"{/if}>--审核拒绝--</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="reason" class="control-label x90">审核原因</label>
                    <div style="display: inline-block; vertical-align: middle;">
                        {$shop.s_reason}
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
    </ul>
</div>