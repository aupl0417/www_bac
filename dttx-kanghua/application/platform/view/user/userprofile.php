<div class="bjui-pageContent">
    <table class="table table-condensed table-hover">
        <tr>
            <td>
                <label class="control-label x120">账号：</label>
                <label for="">{$data.u_nick}</label>
            </td>
        </tr>  <tr>
            <td>
                <label class="control-label x120">姓名：</label>
                <label for="">{$data.u_name}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label class="control-label x120">联系方式：</label>
                <label for="">{$data.u_tel}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label class="control-label x120">分享ID：</label>
                <label for="">{$data.u_code}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label class="control-label x120">角色名称：</label>
                <label for="">{$data.ur_rolename|default='普通会员'}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label class="control-label x120">代理名称：</label>
                <label for="">{$data.c_name|default='无代理'}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label class="control-label x120">地区：</label>
                <label for="">{$data.provinceName}-{$data.cityName}</label>
            </td>
        </tr>
        <tr>
            <td>
                <label class="control-label x120">激活时间：</label>
                <label for="">{$data.up_create_time|date='Y-m-d H:i:s',###}</label>
            </td>
        </tr>
    </table>

</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">关闭</button></li>

    </ul>
</div>