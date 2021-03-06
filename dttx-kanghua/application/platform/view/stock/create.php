<script>
    $('#goodsNumber').blur(function(){
        var goodsNumber = $(this).val();
        var url = "{:url('Common/Publics/getgoodsmodel')}";
        $.get(url, { number : goodsNumber }, function(data){
            if(data.state == 'ok'){
                $('#modelId').html(data.message);
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
                        <label for="name" class="control-label x90">商品编号：</label>
                        <input type="text" id="goodsNumber" name="goodsNumber" data-rule="required;number;length[6~];remote[{:url('platform/stock/checkgoodsnumber')}]" size="20" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="name" class="control-label x90">商品名称：</label>
                        <input type="text" name="goodsName" data-rule="required;length[6~]" size="20" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="parentId" class="control-label x90">商品型号：</label>
                        <select name="modelId" id="modelId" data-rule="required" style="width: 200px;height: 25px;border-color: rgb(169, 169, 169);border-radius: 0px;" width="200">
                            <option value="请选择商品型号"></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="parentId" class="control-label x90">入库仓库：</label>
                        <select name="baseId" id="baseId" data-toggle="selectpicker" data-rule="required" data-width="200">
                            <option value="请选择入库仓库"></option>
                            {foreach $baseList as $vo}
                            <option value="{$vo.id}">{$vo.name}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="name" class="control-label x90">数量：</label>
                        <input type="text" name="goodsCount" data-rule="required;number" size="20" value="">
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