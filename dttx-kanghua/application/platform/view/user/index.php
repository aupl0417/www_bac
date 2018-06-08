<script type="text/javascript">

        $('.tableContent').on('click','.fnick',function () {
            var unick =$(this).data('nick');
            $('#ipt_nick').val(unick);
            $('#pagerForm').submit();
        })
</script>
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:url('')}" method="post">
        <input type="hidden" name="pageSize" value="{$input.pageSize|default='30'}">
        <input type="hidden" name="pageCurrent" value="{$input.pageCurrent|default='1'}">
        <input type="hidden" name="orderField" value="up_create_time">
        <input type="hidden" name="orderDirection" value="desc">
        <div class="bjui-searchBar">
            <label>会员名：</label><input type="text" id="ipt_nick" value="{$input.nick|default=''}" name="ipt_nick" class="form-control" size="10">&nbsp;
            <label>姓名：</label><input type="text" id="ipt_name" value="{$input.name|default=''}" name="ipt_name" class="form-control" size="10">&nbsp;
            <label>手机号：</label><input type="text" id="ipt_tel" value="{$input.tel|default=''}" name="ipt_tel" class="form-control" size="10">&nbsp;

            <label>所在区域:</label>
            <select name="ipt_provinceId" data-toggle="selectpicker" data-nextselect="#j_form_city2" data-refurl="{:url('common/publics/ajax_area')}?pid={value}">
                <option value="all">--省市--</option>
                {foreach name="area" item="vo" }
                    <option value="{$vo.a_id}">{$vo.a_name}</option>
                {/foreach}
            </select>
            <select name="ipt_cityId" id="j_form_city2" data-toggle="selectpicker" data-emptytxt="--城市--">
                <option value="all">--城市--</option>
            </select>
            <label>激活时间</label>
            <input type="text" data-toggle="datepicker" data-pattern="yyyy-MM-dd HH:mm:ss" name="beginDate" value="{$input.beginDate||default=''}" size="20" data-rule="datetime">
            <label>至</label>
            <input type="text" data-toggle="datepicker"  data-pattern="yyyy-MM-dd HH:mm:ss"  name="endDate"  size="20" value="{$input.endDate|default=''}" data-rule="datetime">
            {if $admin_state}
            {notempty name="platformdata"}
            <label>项目选择:</label>
            <select name="platformId" data-toggle="selectpicker" data-rule="required">
                <option value="">请选择项目</option>
                {foreach name="platformdata" item="vo"}
                <option {eq name="$input.platformId" value="$vo.pl_id"}selected{/eq} value="{$vo.pl_id}">{$vo.pl_name}</option>
                {/foreach}
            </select>&nbsp;
            {/notempty}
            {/if}
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
            <div class="pull-right">
<!--      {/*          <button type="button" class="btn-green" data-url="{:url('System/adminAdd')}" data-toggle="dialog" mask="true" data-width="600" data-height="400" data-icon="plus">添加用户</button>&nbsp;*/}
                <button type="button" class="btn-blue" data-url="{:url('System/adminDelete')}?userid={#bjui-selected}" data-toggle="doajax" data-confirm-msg="确定要删除选中项吗？" data-icon="remove">删除选中行</button>&nbsp;-->
            </div>
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
        <thead>
            <tr>
                <th width="50" align="center" data-order-field="u_id">ID</th>
                <th align="center">会员名</th>
                <th width="120" align="center">姓名</th>
                <th align="center">手机号码</th>
                <th align="center">所属地区</th>
                <th align="center">推荐人</th>
                <th align="center">会员等级</th>
                <th align="center">代理等级</th>
                <th align="center">身份认证</th>
                <th align="center">角色</th>
                <th align="center" >状态</th>
                <th align="center" >激活</th>
                <th align="center" data-order-field="up_create_time">注册时间</th>
                <th align="center" width="220">操作</th>
            </tr>
        </thead>
        <tbody>
        {empty name="list"}
        <tr>
            <td colspan="14" align="center">暂无数据!</td>
        </tr>
        {else /}
            {foreach name="list" item="item" }
            <tr>
                <td align="center">{$item.up_id}</td>
                <td align="center">{$item.u_nick}</td>
                <td align="center">{$item.u_name}</td>
                <td align="center">{$item.u_tel}</td>
                <td align="center">{$item.provinceName}-{$item.cityName}</td>
                <td align="center">{eq name="item.up_fcode" value="0"}--{else /}<a href="javascript:;" class="fnick" data-nick="{$item.fnick}">{$item.fnick}({$item.fname})</a>{/eq}</td>
                <td align="center">{$item.ulname|default='普通会员'}</td>
                <td align="center">{$item.clname|default='未代理'}</td>
                <td align="center">{$item.u_auth}</td>
                <td align="center">{$item.ur_rolename|default='未分配'}</td>
                <td align="center">{eq name="item.up_states" value="0"} <label class="label-danger label">冻结</label> {else /} <label class="label label-success">正常</label>{/eq}</td>
                <td align="center">{eq name="item.up_isActive" value="0"} <label class="label-danger label">未完成</label> {else /} <label class="label label-success">已完成</label>{/eq}</td>
                <td align="center">{$item.up_create_time|date="Y-m-d H:i:s",###}</td>
                <td align="center" >   <a class="btn btn-green" href="{:url('platform/user/relational',['p'=>$item['up_plateform_id'],'id'=>$item['u_id']])}" data-toggle="dialog" mask="true" data-width="600" data-height="450"><span>关系树</span></a>
                    <a class="btn btn-green" href="{:url('platform/user/edit',['id'=>$item['up_id']])}" data-toggle="dialog"  mask="true" data-width="600" data-height="450"><span>编辑</span></a>&nbsp
                    {if !$admin_state}
                        {eq name="item.up_states" value="0"}
                        <a class="btn btn-green" href="{:url('platform/user/changeopen',['id'=>$item['up_id']])}" data-toggle="doajax" data-confirm-msg="确定要解冻该用户吗？" data-title="解冻用户操作"><span>正常</span></a>
                        {else /}
                    <a class="btn btn-red" href="{:url('platform/user/changestop',['id'=>$item['up_id']])}" data-toggle="dialog" mask="true" data-confirm-msg="确定要冻结该用户吗？" data-width="600" data-title="冻结用户操作"  data-height="220"><span>冻结</span></a>
                        {/eq}
                    {else /}
                        <a class="btn btn-green" href="{:url('platform/user/grantlogin',['id'=>$item['up_id']])}" data-toggle="doajax" data-callback="mycallback" data-loadingmask="true" data-confirm-msg="确定要切换到该用户吗？" data-title="一键登录"><span>一键登录</span></a>
                    {/if}
                </td>
            </tr>
            {/foreach}
        {/empty}
        </tbody>
    </table>
</div>
<div class="bjui-pageFooter">
    <div class="pages">
        <span>每页&nbsp;</span>
        <div class="selectPagesize">
            <select data-toggle="selectpicker" data-toggle-change="changepagesize">
                <option value="30">30</option>
                <option value="60">60</option>
                <option value="120">120</option>
                <option value="150">150</option>
            </select>
        </div>
        <span>&nbsp;条，共 {$count|default=""} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$count|default=''}" data-page-size="{$input.pageSize|default=''}" data-page-current="{$input.pageCurrent|default=''}"></div>
</div>
<script type="text/javascript">
    function mycallback(json) {
    //    $(this).bjuiajax('ajaxDone', json);
    //    $(this).alertmsg('ok',json.message,)
        $(this).alertmsg('ok',json.message,{displayPosition:'middlecenter',displayMode:'slide',mask:true,autoClose:false,title:'授权登录',okCall:function () {
            if (json.statusCode==200){
                window.location.href=json.forward;
            }
        }})
    }
</script>
