<!-- * User: lirong-->
<!-- * Date: 2017/7/1-->
<!-- * Time: 11:17-->
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
        <tr>
            <th align="center">异动编码</th>
            <th align="center">订单编码</th>
            <th align="center">账户ID</th>
            <th align="center">用户ID</th>
            <th align="center">订单交易金额</th>
            <th align="center">分润比例</th>
            <th align="center">分润金额</th>
            <th align="center">异动类型</th>
            <th width="140">计算时间</th>
<!--            <th width="180">备注</th>-->
        </tr>
        </thead>
        <tbody>
        {empty name="data"}
        <tr>
            <td colspan="9" align="center">暂无数据！</td>
        </tr>
        {else /}
        {volist name="data" id='vo'}
        <tr>
            <td align="center">{$vo.ad_id}</td>
            <td align="center">{$vo.ad_order_id}</td>
            <td align="center">{$vo.ad_acid}</td>
            <td align="center">{$vo.ad_nick}</td>

            <td align="center">{$vo.ad_order_money}</td>
            <td align="center">{$vo.ad_ratio+0}%</td>
            <td align="center">{$vo.ad_money}</td>
            <td align="center">{$vo.type_text}</td>
            <td align="center">{$vo.ad_create_time|date='Y-m-d H:i:s',###}</td>
<!--            <td align="center">{$vo.ad_remark}</td>-->
        </tr>
        {/volist}
        {/empty}
        </tbody>
    </table>
</div>
