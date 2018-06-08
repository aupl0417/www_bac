<div class="bjui-pageContent">
    <form action="{:url('platform/userlevel/edit')}" id="j_form_form" class="pageForm" data-toggle="validate">
        <div style="margin:15px auto 0;">

                    <table class="table table-hover" width="100%">
                        <tbody>
                        <tr>
                            <td colspan="2">
                                <label for="j_custom_level" class="control-label x120">会员等级编号：</label>
                                <label>{$data.ul_user_no}</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="j_custom_name" class="control-label x120">会员等级名称：</label>
                                <label>{$data.ul_name}</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="j_custom_name" class="control-label x120">升级价格：</label>
                                <label>{$data.ul_money|default='0.00'}</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="j_custom_ratio" class="control-label x120">分润比例：</label>
                                <label>{$data.ul_ratio}%</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="j_custom_upgrade" class="control-label x120">会员升级要求：</label>
                                <label class="">{$data.ul_upgrade_require}</label>
                            </td>
                        </tr>
                        <tr>
                            <td width="120">
                                <label for="j_custom_mark" class="control-label x120">会员福利：</label>
                            </td>
                            <td><b>{$data.ul_level_mark|nl2br} </b></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="" class="control-label x120">是否启用：</label>
                                <label class="label label-default">{eq name="$data.ul_status" value="0"} 否 {else /} 是 {/eq}</label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">退出关闭</button></li>
    </ul>
</div>