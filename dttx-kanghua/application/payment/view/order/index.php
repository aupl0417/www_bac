<!-- * User: lirong-->
<!-- * Date: 2017/7/1-->
<!-- * Time: 11:17-->
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('order/index')}" method="post">
        <input type="hidden" name="pageSize" value="${model.pageSize}">
        <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
        <input type="hidden" name="orderField" value="ao_create_time">
        <input type="hidden" name="orderDirection" value="desc">
        <div class="bjui-searchBar">
            <label>买家ID：</label><input type="text" id="ipt_nick" value="{$input.ipt_nick|default=''}" name="ipt_nick" class="form-control" size="10">&nbsp;
            <label>订单编号：</label><input type="text" id="ipt_order_id" value="{$input.ipt_order_id|default=''}" name="ipt_order_id" class="form-control" size="10">&nbsp;
            <label>异动编号：</label><input type="text" id="ipt_aoid" value="{$input.ipt_aoid|default=''}" name="ipt_aoid" class="form-control" size="15">&nbsp;
            <label>总金额：</label>
            <input type="text" id="ipt_moneyMin" data-rule="number" value="{$input.ipt_moneyMin|default=''}" placeholder="从" name="ipt_moneyMin" class="form-control" size="10">&nbsp;
            <input type="text" id="ipt_moneyMax" value="{$input.ipt_moneyMax|default=''}" placeholder="到" name="ipt_moneyMax" class="form-control" data-rule="number" size="10">&nbsp;
            <label>异动时间：</label>
            <input type="text"  data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" name="beginDate" value="{$input.beginDate||default=''}" size="18" data-rule="datetime">
            <label>至</label>
            <input type="text"  data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss"  name="endDate"  size="18" value="{$input.endDate|default=''}" data-rule="datetime">
            <label for="ipt_state">状态</label>
            <select name="ipt_state" id="ipt_state"  data-toggle="selectpicker">
                <option value="">全部</option>
                <option {eq name="$input['ipt_state']" value="-1"}selected{/eq} value="-1">驳回</option>
                <option {eq name="$input['ipt_state']" value="0"}selected{/eq} value="0">未处理</option>
                <option {eq name="$input['ipt_state']" value="1"}selected{/eq} value="1">已处理</option>
            </select>
            {if $admin_state}
            {notempty name="platformdata"}
            <label>项目选择:</label>
            <select name="platformId" data-toggle="selectpicker" data-rule="required">
                <option value="">请选择项目</option>
                {foreach name="platformdata" item="vo"}
                <option {eq name="$input.platformId" value="$vo.pl_id"}selected{/eq} value="{$vo.pl_id}">{$vo.pl_name}</option>
                {/foreach}
            </select>&nbsp;
            {/notempty}
            {/if}
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">

            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="120%" data-nowrap="true">
        <thead>
        <tr>
            <th align="center" width="180">异动编号</th>
            <th align="center" width="180">订单编号</th>
            <th align="center">买家</th>
            <th align="center">总金额</th>
            <th align="center" width="220">一级推荐人[用户名/身份/比例/金额]</th>
            <th align="center" width="220">二级推荐人[用户名/身份/比例/金额]</th>
            <th align="center" width="200">省级推荐人[用户名/比例/金额]</th>
            <th align="center" width="200">区级推荐人[用户名/比例/金额]</th>
            <th align="center"data-order-field="ao_create_time">创建时间</th>
            <th align="center">订单状态</th>
            <th align="center">状态</th>
            <th align="center" width="140">操作</th>
        </tr>
        </thead>
        <tbody>
        {empty name="data['list']"}
        <tr>
            <td colspan="12" align="center">暂无数据！</td>
        </tr>
        {else /}
        {volist name="data['list']" id='vo'}
        <tr>
            <td align="center" title="{$vo.ao_id}">{$vo.ao_id}</td>
            <td align="center" title="{$vo.ao_order_id}">{$vo.ao_order_id}</td>
            <td align="center">{$vo.ao_buy_nick}</td>
            <td align="center">{$vo.ao_money}</td>
            <td align="center">{$vo.ao_onelevel_nick|default='--'}/{if $vo.ao_onelevel_nick !=''}{if $vo.onelevelname==''}普通会员{else /}{$vo.onelevelname}{/if}{else /}--{/if}/{$vo.ao_one_ratio+0}%/{$vo.ao_onelevel_money|default='--'+0}</td>
            <td align="center">{$vo.ao_twolevel_nick|default='--'}/{if $vo.ao_twolevel_nick !=''}{if $vo.twolevelname==''}普通会员{else /}{$vo.twolevelname|default='--'}{/if}{else /}--{/if}/{$vo.ao_two_ratio+0}%/{$vo.ao_twolevel_money|default='--'}</td>
            <td align="center">{$vo.ao_province_nick}/{$vo.ao_province_ratio+0}%/{$vo.ao_province_money}</td>
            <td align="center">{$vo.ao_city_nick}/{$vo.ao_city_ratio+0}%/{$vo.ao_city_money}</td>
            <td align="center" title="{$vo.ao_create_time|date='Y-m-d H:i:s',###}">{$vo.ao_create_time|date='Y-m-d H:i:s',###}</td>
            <td align="center">{$vo.orders_text|default='--'}</td>
            <td align="center">{eq name="$vo.ao_state" value="0"}<label class="label label-default">未处理</label>{/eq}{eq name="$vo.ao_state" value="1"}<label class="label label-success">已结算</label>{/eq}{eq name="$vo.ao_state" value="-1"}<label class="label label-danger">驳回</label>{/eq}</td>
            <td align="center">
                {eq name="$vo.ao_state" value="0"}
                {eq name="$vo.os_status" value='3'}<a class="btn btn-green" href="{:url('payment/order/calcorder',['id'=>$vo.ao_order_id])}" data-toggle="doajax"  data-confirm-msg="确定要手动计算当前项吗？"><span>计算分润</span></a>
                <a class="btn btn-red" href="{:url('payment/order/orderstop',['id'=>$vo.ao_id])}" data-toggle="dialog" mask="true" data-width="600" data-height="250" data-confirm-msg="确定要启用选中项吗？"><span>驳回</span></a>
                {else /}
                    <button class="btn btn-default disabled">计算</button>
                    <button class="btn btn-default disabled">驳回</button>
                {/eq}

                {else /}
                <a class="btn btn-green  {eq name="$vo.ao_state" value="-1"}disabled{/eq}" href="{:url('payment/order/calcdetail',['id'=>$vo.ao_order_id])}" data-toggle="dialog" data-width="1200" data-height="400" data-id="dialog-mask" data-mask="true" data-title="订单【{$vo.ao_order_id}】分润明细"><span>查看明细</span></a>
                <button class="btn btn-default disabled">驳回</button>
                {/eq}

            </td>
        </tr>
        {/volist}
        {/empty}
        </tbody>
    </table>
</div>
<div class="bjui-pageFooter">
    <div class="pages">
        <span>每页&nbsp;</span>
        <div class="selectPagesize">
            <select data-toggle="selectpicker" data-toggle-change="changepagesize">
                <option value="30">30</option>
                <option value="60">60</option>
                <option value="120">120</option>
                <option value="150">150</option>
            </select>
        </div>
        <span>&nbsp;条，共 {$data.count} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$data.count}" data-page-size="{$input.pageSize}" data-page-current="{$input.pageCurrent}"></div>
</div>