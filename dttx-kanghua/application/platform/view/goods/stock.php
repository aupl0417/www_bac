<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <input type="hidden" name="id" value="{$goods.id}">
            <tr>
                <th width="80" align="center">规格</th>
                <th width="100" align="center">型号</th>
                <th width="120" align="center">数量</th>
            </tr>
        </thead>
        <tbody>
            <tr data-id="{$goods.id}">
                <td style="padding-left: 15px;">{$goods.format}</td>
                <td style="padding-left: 15px;">{$goods.model}</td>
                <td align="center">{$goods.stock}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>
    </ul>
</div>