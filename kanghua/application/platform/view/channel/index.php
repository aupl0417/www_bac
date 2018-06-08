<div class="bjui-pageHeader">
    <button type="button" class="btn-green" data-url="{:url('Channel/create')}" data-toggle="dialog" mask="true" data-width="460" data-height="400" data-icon="plus">新增渠道</button>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="30" align="center">序号</th>
                <th width="30" align="center">等级</th>
                <th width="120" align="center">称号</th>
                <th width="200" align="center">职能说明</th>
                <th width="200" align="center">默认后台角色</th>
                <th width="150" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach name="channelList" item="item" }
            <tr data-id="{$item.id}">
                <td style="padding-left: 15px;">{$item.id}</td>
                <td style="padding-left: 15px;">{$item.level}</td>
                <td align="center">{$item.name}</td>
                <td align="center">{$item.description}</td>
                <td align="center">{$item.rolename}</td>
                <td align="center">
                    <a class="btn btn-green" href="{:url('Channel/edit', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="460" data-height="400"><span>编辑</span></a>
                    <a class="btn btn-green" href="{:url('Channel/view', ['id' => $item['id']])}" data-toggle="dialog" mask="true" data-width="460" data-height="400"><span>查看</span></a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>