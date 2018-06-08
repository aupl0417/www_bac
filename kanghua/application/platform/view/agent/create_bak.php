<script>

    getAgent('provinceId', 'province');
    getAgent('cityId', 'city');

    function getAgent(id, level) {
        $('#' + id).on('change', function () {
            var url = "{:url('Agent/getChannel')}";
            $.get(url, { level : level }, function(data){
            //    console.log(data);
                if(data.state == 'ok'){
                    $("input[name='level']").val(data.message.c_id);
                    $('#levelName').val(data.message.c_name);
                }
            });
        });
    }
    
    $('#nickname').on('blur', function () {
        var url = "{:url('Agent/checkUser')}";
        var nickname = $(this).val();
        $.get(url, { nickname : nickname }, function(data){
            if(data.statusCode == 200){
                var msg = '';//(data.message.name != '' ?: '') + ',' + (data.message.tel !=)
                if(data.message.name != '' && data.message.tel == ''){
                    msg += data.message.name;
                }else if(data.message.name == '' && data.message.tel != ''){
                    msg += data.message.tel;
                }else{
                    msg += data.message.name + ',' + data.message.tel
                }
                $('#checkUser').html(msg);
                $('#userId').val(data.message.id);
            }else if(data.statusCode == 301){
                $('#checkUser').html(data.message.msg);
                $('#userId').val(data.message.id);
                $('.btn-default').attr('disabled', true);
            }else {
                $('#checkUser').html(data.message);
            }
        });
    });
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
                        <input type="text" id="nickname" name="nickname" data-rule="required;length[2~]" size="20" value="">
                        <input type="hidden" id="userId" name="userId" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="control-label x90">信息核实：</label>
                        <label class="control-label x200" id="checkUser"></label>
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