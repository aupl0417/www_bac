<script type="text/javascript">

function returnJSON() {
    return {$json_priv}
}

var navtab =$.CurrentNavtab;
$("#submit_priv").click(function(){

	var treeObj = $.fn.zTree.getZTreeObj("ztree1");
    var nodes = treeObj.getCheckedNodes(true);
    var ids='';
    //拼凑
    $.each(nodes, function(key, item){
        ids += (ids ? ',' + item.id : item.id);
    });

    $.ajax({
        type: "post",
        url: '{:url('')}',
        dataType: "json",
        data : {
            ids : ids,
            roleid: {$roleid}
        },
        success: function(data) {
        	$(this).alertmsg('ok', '保存成功');
            $(this).dialog('closeCurrent');
        }
    });
   // console.log(ids);
});
function closeTable() {
   $('.bjui-dialog').hide();

    // $('#System_adminRoleList').
}
</script>
<div class="bjui-pageContent">
    <div style="padding:20px;">
        <div class="clearfix">
            <div style="float:left; overflow:auto;">
                <ul id="ztree1" class="ztree" data-toggle="ztree" data-check-enable="true"
                    data-options="{
                        expandAll: true,
                        nodes:'returnJSON'
                    }"
                ></ul>
            </div>
        </div>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><button type="button" class="btn-default" id="submit_priv" data-icon="save">保存</button></li>
    </ul>
</div>