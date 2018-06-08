<script>

    getAgent('provinceId', 'province');
    getAgent('cityId', 'city');

    function getAgent(id, level) {
        $('#' + id).on('change', function () {
            var num =$(this).val();
            if (level=='city' && num!=''){
                $('#levelName').val('区级代理');
            }else {
                $('#levelName').val('省级代理');
            }
        });
    }

</script>
<div class="bjui-pageContent">
    <form action="{:url('')}" class="pageForm" data-toggle="validate">
        <table class="table table-condensed table-hover">
            <tbody>
                <tr>
                    <td>
                        <label for="provinceId" class="control-label x90">代理商区域：</label>
                        <select name="provinceId" id="provinceId" data-width="200" data-toggle="selectpicker" data-rule="required;number" data-nextselect="#cityId" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                            <option value="">--省市--</option>
                            {foreach name="area" item="vo" }
                            <option value="{$vo.a_id}">{$vo.a_name}</option>
                            {/foreach}
                        </select>
                        <select name="cityId" id="cityId"  data-width="200" data-toggle="selectpicker" data-emptytxt="--城市--"">
                            <option value="">--城市--</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="level" class="control-label x90">代理商级别：</label>
                        <input type="text" id="levelName" name="levelName" size="20" readonly value="">
                        <input type="hidden" id="level" name="level" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="nickname" class="control-label x90">代理商会员名：</label>
                        <input type="text" id="dttxnick" name="dttxnick" data-rule="required;length[2~];remote[{:url('/common/publics/checkDttxNick')}]" size="20" value="">
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