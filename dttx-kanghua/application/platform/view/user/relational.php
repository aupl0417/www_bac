<script type="text/javascript">
function ztree_returnjson() {
    return [{$json_priv}]
}

var navtab =$.CurrentNavtab;

function closeTable() {
   $('.bjui-dialog').hide();

    // $('#System_adminRoleList').
}

function search(event, treeId, treeNode, clickFlag) {
    event.preventDefault()
    var num = treeNode.name.indexOf('(');
    var unick =treeNode.name.substring(0,num)
    $('.bjui-dialog').hide();
    $(this).navtab({id:'platform_User_index', url:'/platform/user/index.html?ipt_nick='+unick, title:'会员资料列表',fresh:true,type:'POST'});
}




</script>
<div class="bjui-pageContent">
    <div style="padding:20px;">
        <div class="clearfix">
            <div>
                <fieldset style="width: 100%">
                    <legend>分享关系树</legend>
                        <ul id="ztree1" class="ztree" data-toggle="ztree" data-options="{ expandAll: true,nodes:'ztree_returnjson',onClick:search}"></ul>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>

    </ul>
</div>