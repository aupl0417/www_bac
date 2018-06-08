<script type="text/javascript">
//单击事件
function ZtreeClick(event, treeId, treeNode) {
    event.preventDefault()
 //   console.log(treeNode);
    var $detail = $('#ztree-detail')
    if ($detail.attr('tid') == treeNode.tId) return
    if (treeNode.name) $('#j_menu_name').val(treeNode.name)
    if (treeNode.module){
        $('#j_menu_module').val(treeNode.module)
    }else {
        $('#j_menu_module').val('');
    }

    if (treeNode.controller) {
        $('#j_menu_class').val(treeNode.controller)
    } else {
        $('#j_menu_class').val('')
    }
    if (treeNode.action) {
        $('#j_menu_action').val(treeNode.action)
    } else {
        $('#j_menu_action').val('')
    }
    if (treeNode.icon) {
        $('#j_menu_icon').val(treeNode.icon)
    } else {
        $('#j_menu_icon').val('')
    }
    if (treeNode.listorder) {
        $('#j_menu_listorder').val(treeNode.listorder)
    } else {
        $('#j_menu_listorder').val('')
    }
    if (treeNode.display == 1) {
        $('#j_menu_display_yes').iCheck('check');
    } else {
        $('#j_menu_display_no').iCheck('check');
    }
    if (treeNode.outside == 1) {
        $('#j_menu_outside_yes').iCheck('check');
    } else {
        $('#j_menu_outside_no').iCheck('check');
    }
    $detail.attr('tid', treeNode.tId)
    $detail.show()
}
//保存属性
function M_Ts_Menu() {
    var zTree  = $.fn.zTree.getZTreeObj("ztree1")
    var name   = $('#j_menu_name').val()
    var module    = $('#j_menu_module').val()
    var controller    = $('#j_menu_class').val()
    var action  = $('#j_menu_action').val()
    var icon  = $('#j_menu_icon').val()
    var listorder  = $('#j_menu_listorder').val()
    var display = $("input[name='display']:checked").val();
    var outside= $("input[name='outside']:checked").val();

    if ($.trim(name).length == 0) {
        $(this).alertmsg('error','菜单名称不能为空！')
        return;
    }
    var upNode = zTree.getSelectedNodes()[0]
    
    if (!upNode) {
        alertMsg.error('未选中任何菜单！')
        return
    }
    upNode.name   = name
    upNode.module    = module
    upNode.controller    = controller
    upNode.action  = action
    upNode.icon  = icon
    upNode.listorder = listorder
    upNode.display = display
    upNode.outside = outside
    /* if(icon != ''){
    	upNode.faicon = icon;
    }else{
    	upNode.faicon = 'cog';
    } */
    upNode.faicon = icon;
    if(display == 1){
    	upNode.font = {'color':''}
    }else{
    	upNode.font = {'color':'#999999'}
    }
 //   console.log(upNode);
    if(upNode.id){
    	$.ajax({
            type     : 'post',
            url      : "{:url('ajax_nodeEdit')}",
            data     : {id:upNode.id, name:name, module:module,controller:controller, action:action, icon:icon, listorder:listorder, display:display,outside:outside},
            cache    : false,
            dataType : 'json',
            success:function(response){
                console.log(response);
                if(response.statusCode==200){
                    zTree.updateNode(upNode)
                    $(this).alertmsg('ok','更新成功！');
                    $(this).navtab('reload');
                }else {
                    $(this).alertmsg('error',response.message);
                }
                
            },
        });
    }else{
    	//新增的直接生效
    	zTree.updateNode(upNode)
    }
    
    
}
//
function M_BeforeNodeDrag(treeId, treeNodes) {
	//不能选顶级节点
	for (var i = 0; i < treeNodes.length; i++) {
        if (treeNodes[i].id == 0) {
            return false;
        }
    }
//    console.log('开始拖拽');
    return true
}
//监听拖拽事件
function M_BeforeNodeDrop(treeId, treeNodes, targetNode, moveType, isCopy) {
//    console.log(treeId);
//    console.log(treeNodes);
//    console.log(targetNode);
//    console.log(moveType);
//    console.log(isCopy);
    //
    /*禁止插入层级为2的节点*/
    /* if (moveType == 'inner' && targetNode.level == 2) {
        return false
    } */
    /*禁止插入剩余层级不足的子节点*/
    /* if (moveType == 'inner' && treeNodes[0].isParent) {
        var molevel = 2 - targetNode.level //剩余层级
        var maxlevel = 1
        var zTree = $.fn.zTree.getZTreeObj("ztree1")
        var nodes = zTree.transformToArray(treeNodes)
        var level = nodes[0].level

  //      console.log(nodes);
        for (var i = 1; i < nodes.length; i++) {
            if (nodes[i].level == (level + 1)) {
                maxlevel++
                level++
            }
        }
        if (maxlevel > molevel) {
            return false
        }
    } */
    var zTree = $.fn.zTree.getZTreeObj("ztree1")
    //var nodes = zTree.transformToArray(treeNodes)
    var ids = '';
    for (var i = 0; i < treeNodes.length; i++) {
        ids += ids ?  ',' + treeNodes[i].id : treeNodes[i].id;
    }
    var jsonstr = $.ajax({
        type     : 'post',
        url      : "{:url('ajax_nodeDrag')}",
        async    : false,
        data     : {
            treeNodes:treeNodes, 
            targetNode:targetNode,
            moveType:moveType
        },
        cache    : false,
        dataType : 'json',
        // success:function(response){
        //     if(response.flag){
        //         console.log('drag');
        //     	return true;
        //     }
            
        // },
    });
    var jsonobj = eval('(' + jsonstr.responseText + ')');
    if(jsonobj.flag == true){
        return true;
    }else{
        return false;
    }
}
//拖拽结束事件
function M_NodeDrop(event, treeId, treeNodes, targetNode, moveType, isCopy) {
    var $log = $('#ztree-log')
    $log.val('拖拽结束！\n'+ $log.val())
}
//删除前事件
function M_BeforeRemove(treeId, treeNode) {
	if(!treeNode.id)
		return true;
	$.ajax({
        type     : 'post',
        url      : "{:url('ajax_nodeDelete')}",
        data     : {id:treeNode.id},
        cache    : false,
        dataType : 'json',
        success:function(response){
            if(response.flag){
                return true;
            }else{
            	return false;
            }
            
        },
    });
}
//删除结束事件
function M_NodeRemove(event, treeId, treeNode) {
    var $log = $('#ztree-log')
    $log.val('删除成功！\n'+ $log.val())
}
//自定义DOM
function M_AddDiyDom(treeId, treeNode) {
    var aObj = $('#' + treeNode.tId + '_a')
    
    if ($('#diyBtn_'+ treeNode.id).length > 0) return
    aObj.append('<button type="button" class="diyBtn1" id="diyBtn_' + treeNode.id +'" title="'+ treeNode.name +'" onfocus="this.blur();"><i class="fa fa-plane"></i></button>')
    $('#diyBtn_'+ treeNode.id).bind('click', function() {$(this).alertmsg('info', (treeNode.name +' 的飞机！'))})
}
function returnJSON() {
    return {$js_node_lists};
}
function getFont(treeId, node) {
    return node.font ? node.font : {};
}
function addHoverDom(treeId, treeNode){
	var IDMark_A = '_a'
	var zTree = $.fn.zTree.getZTreeObj("ztree1")
	var level = treeNode.level
    var $obj  = $('#'+ treeNode.tId + IDMark_A)
    var $add  = $('#diyBtn_add_'+ treeNode.id)
    var $del  = $('#diyBtn_del_'+ treeNode.id)
    
    if (!$add.length) {
    	$add = $('<span class="tree_add" id="diyBtn_add_'+ treeNode.id +'" title="添加"></span>')
        $add.appendTo($obj);
        $add.on('click', function(){
        	$.ajax({
                type     : 'post',
                url      : "{:url('ajax_nodeAdd')}",
                data     : {pid:treeNode.id, c:treeNode.controller,module:treeNode.module},
                cache    : false,
                dataType : 'json',
                success:function(response){
                    console.log(response);
                    if(response.flag){
                    	zTree.addNodes(treeNode, response.data)
                    }
                    
                },
            });
            
        })
    }
    
    if (!$del.length) {
        var $del = $('<span class="tree_del" id="diyBtn_del_'+ treeNode.id +'" title="删除"></span>')
        
        $del.appendTo($obj).on('click', function(event) {
                var delFn = function() {
                    $del.alertmsg('confirm', '确认要删除 '+ treeNode.name +' 吗？', {
                        okCall: function() {
                            zTree.removeNode(treeNode)
                        },
                        cancelCall: function () {
                            return
                        }
                    })
                }
                //删除动作
                if(!treeNode.id)
                    return true;
                $.ajax({
                    type     : 'post',
                    url      : "{:url('ajax_nodeDelete')}",
                    data     : {id:treeNode.id},
                    cache    : false,
                    dataType : 'json',
                    success:function(response){
                        if(response.flag){
                        	delFn()
                        }else{
                            return false;
                        }
                        
                    },
                });
                /* var isdel = M_BeforeRemove(treeId, treeNode)
                if (isdel && isdel == true) delFn() */
            }
        )
    }
}
function expandNode(e) {
    var zTree = $.fn.zTree.getZTreeObj("ztree1"),
    type = e.data.type,
    nodes = zTree.getSelectedNodes();
    if (type.indexOf("All")<0 && nodes.length == 0) {
        alert("请先选择一个父节点");
    }

    if (type == "expandAll") {
        zTree.expandAll(true);
    } else if (type == "collapseAll") {
        zTree.expandAll(false);
    } else {
        //var callbackFlag = $("#callbackTrigger").attr("checked");
        for (var i=0, l=nodes.length; i<l; i++) {
            //zTree.setting.view.fontCss = {};
            if (type == "expand") {
                zTree.expandNode(nodes[i], true, null, null);
            } else if (type == "collapse") {
                zTree.expandNode(nodes[i], false, null, null);
            } else if (type == "toggle") {
                zTree.expandNode(nodes[i], null, null, null);
            } else if (type == "expandSon") {
                zTree.expandNode(nodes[i], true, true, null);
            } else if (type == "collapseSon") {
                zTree.expandNode(nodes[i], false, true, null);
            }
        }
    }
}

$(document).ready(function(){

    $("#expandBtn").bind("click", {type:"expand"}, expandNode);
    $("#collapseBtn").bind("click", {type:"collapse"}, expandNode);
    $("#toggleBtn").bind("click", {type:"toggle"}, expandNode);
    $("#expandSonBtn").bind("click", {type:"expandSon"}, expandNode);
    $("#collapseSonBtn").bind("click", {type:"collapseSon"}, expandNode);
    $("#expandAllBtn").bind("click", {type:"expandAll"}, expandNode);
    $("#collapseAllBtn").bind("click", {type:"collapseAll"}, expandNode);
});

</script>
<div class="bjui-pageContent">
    <div style="padding:20px;">
        <div class="clearfix">
            
                <ul>
                    <li><p>试试看：<br/>
                        &nbsp;单个节点--[ <a id="expandBtn" href="#" title="不想展开我就不展开你..." onclick="return false;">展开</a> ]
                        &nbsp;[ <a id="collapseBtn" href="#" title="不想折叠我就不折叠你..." onclick="return false;">折叠</a> ]
                        &nbsp;[ <a id="toggleBtn" href="#" title="你想怎样？..." onclick="return false;">展开 / 折叠 切换</a> ]<br/>
                        &nbsp;单个节点（包括子节点）--[ <a id="expandSonBtn" href="#" title="不想展开我就不展开你..." onclick="return false;">展开</a> ]
                        &nbsp;[ <a id="collapseSonBtn" href="#" title="不想折叠我就不折叠你..." onclick="return false;">折叠</a> ]<br/>
                        &nbsp;全部节点--[ <a id="expandAllBtn" href="#" title="不管你有多NB，统统都要听我的！！" onclick="return false;">展开</a> ]
                        &nbsp;[ <a id="collapseAllBtn" href="#" title="不管你有多NB，统统都要听我的！！" onclick="return false;">折叠</a> ]</p>
                    <li>
                </ul>
            <div style="float:left; width:400px;overflow:auto;">
                <ul id="ztree1" class="ztree" data-toggle="ztree" 
                    data-options="{
                        maxAddLevel: 5,
                        nodes:returnJSON,
                        onClick: 'ZtreeClick',
                        addHoverDom:'addHoverDom',
                        removeHoverDom:'edit',
                        beforeRemove:'M_BeforeRemove',
                        editEnable:true,
                        beforeDrag:'M_BeforeNodeDrag',
                        beforeDrop:'M_BeforeNodeDrop',
                        setting:{
                            view:{
                                fontCss: getFont,
                                nameIsHTML: true
                            }
                        }
                    }"
                >
                    <!-- <li data-id="1" data-pid="0" data-faicon="rss" data-faicon-close="cab">表单元素</li>
                    <li data-id="10" data-pid="1" data-url="form-button.html" data-tabid="form-button" data-faicon="bell">按钮</li>
                    <li data-id="11" data-pid="1" data-url="form-input.html" data-tabid="form-input" data-faicon="info-circle">文本框</li>
                    <li data-id="12" data-pid="1" data-url="form-select.html" data-tabid="form-select" data-faicon="ellipsis-v">下拉选择框</li>
                    <li data-id="13" data-pid="1" data-url="form-checkbox.html" data-tabid="table" data-faicon="soccer-ball-o">复选、单选框</li>
                    <li data-id="14" data-pid="1" data-url="form.html" data-tabid="form" data-faicon="comments">表单综合演示</li>
                    <li data-id="2" data-pid="0">表格</li>
                    <li data-id="20" data-pid="2" data-url="table.html" data-tabid="table" data-faicon="signal">普通表格</li>
                    <li data-id="21" data-pid="2" data-url="table-fixed.html" data-tabid="table-fixed" data-faicon="rss-square">固定表头表格</li>
                    <li data-id="22" data-pid="2" data-url="table-edit.html" data-tabid="table-edit" data-faicon="bookmark-o">可编辑表格</li> -->
                </ul>
            </div>
            <div id="ztree-detail" style="display:none; margin-left:430px; width:300px; height:240px;">
                <div class="bs-example" data-content="详细信息">
                    <div class="form-group">
                        <label for="j_menu_name" class="control-label x120">菜单名称：</label>
                        <input type="text" class="form-control validate[required] required" name="name" id="j_menu_name" size="15" placeholder="名称" />
                    </div>
                    <div class="form-group">
                        <label for="j_menu_url" class="control-label x120">Module名：</label>
                        <input type="text" class="form-control required" name="module" id="j_menu_module" size="15" placeholder="类名" />
                    </div>
                    <div class="form-group">
                        <label for="j_menu_url" class="control-label x120">Class名：</label>
                        <input type="text" class="form-control required" name="class" id="j_menu_class" size="15" placeholder="类名" />
                    </div>
                    <div class="form-group">
                        <label for="j_menu_tabid" class="control-label x120">Action名：</label>
                        <input type="text" class="form-control" name="action" id="j_menu_action" size="15" placeholder="方法名" />
                    </div>
                    <div class="form-group">
                        <label for="j_menu_icon" class="control-label x120">图标：</label>
                        {notempty name="Detail.icon"}
                        <i id="icon_img" class="fa fa-{$Detail.icon}"></i>
                        {else /}
                        <i id="icon_img"></i>
                        {/notempty}
                        <input type="text" class="" name="icon" id="j_menu_icon" size="15" placeholder="" data-toggle="lookup" data-url="{:url('System/adminNodeIcon')}" data-width="800" data-height="600"/>
                    </div>
                    <div class="form-group">
                        <label for="j_menu_listorder" class="control-label x120">排序：</label>
                        <input type="text" class="form-control" name="listorder" id="j_menu_listorder" size="15"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label x120">是否显示：</label>
                        <input id="j_menu_display_yes" type="radio" name="display" data-toggle="icheck" data-label="显示" value="1" checked>
                        <input id="j_menu_display_no" type="radio" name="display" data-toggle="icheck" data-label="隐藏" value="0">
                        
                    </div>
                    <div class="form-group">
                        <label class="control-label x120">是否前台可选菜单：</label>
                        <input id="j_menu_outside_yes" type="radio" name="outside" data-toggle="icheck" data-label="显示" value="1" >
                        <input id="j_menu_outside_no" type="radio" name="outside" data-toggle="icheck" data-label="隐藏" value="0" checked>

                    </div>
                    <div class="form-group" style="padding-top:8px; border-top:1px #DDD solid;">
                        <label class="control-label x120"></label>
                        <button class="btn btn-green" onclick="M_Ts_Menu();">更新菜单</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
    </ul>
</div>