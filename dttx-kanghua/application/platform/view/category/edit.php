
<div class="bjui-pageContent">
    <form action="{:url('')}" class="pageForm" data-toggle="validate">
        <input type="hidden" name="id" value="{$cate.id}">
        <table class="table table-condensed table-hover">
            <tbody>
                <tr>
                    <td>
                        <label for="name" class="control-label x90">分类名称：</label>
                        <input type="text" name="name" data-rule="required" size="20" value="{$cate.name}">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="parentId" class="control-label x90">上级分类：</label>
                        <select name="parentId" id="parentId" data-toggle="selectpicker" data-width="200">
                            <option value=""></option>
                            {foreach $cateList as $vo}
                            <option value="{$vo.id}"{if condition="$vo.id eq $cate.pid"}selected="selected"{/if}>{$vo.name}</option>
                            {/foreach}
                        </select>
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