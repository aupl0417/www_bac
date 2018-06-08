{extend name="common:base" /}
{block name="header"}
<div class="right">

</div>
{/block}
{block name="content"}
<div class="pages">
    <div data-page="distribution" class="page">
        <div class="page-content bg-f5f">
            <div class="rpd15 bg-white rfs14 rline28 rpt10 rpb10">
                你将激活“大唐天下分销系统-<span class="c_blue">{$platformdata.pl_name|default=''}</span>”账号<br/>
            </div>
            <div class="bg_fff solid_last rmb5 solid_b rmt10">
                <div class="solid_b">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                           项目推荐人
                        </div>
                        <div class="col-66 input_center rpl0 input-radius-active">
                            <input type="text" class=" rfs14 bor_no rpl0" placeholder="推荐人账号或手机号" value="{$url_nick|default=''}" name="fcode" id="fcode">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row no-gutter">
                <div class="col-33"></div>
                <div class="col-66">
                    <div class="rml15 c_cc0 rfs14 rmb5" id="fcodeinfo">{$url_info|default=''}
                    </div>
                    <div class="rml15 c_999 rfs14">
                        不影响原有大唐天下分享推荐关系；
                    </div>
                </div>
            </div>
            <div class="bg_fff solid_last rmt10 solid_b">
                <div class="solid_b">
                    <div class="row mr0 phong_form rpl15">
                        <div class="col-33 text_left rfs16 c_333">
                            所在区域
                        </div>
                        <div class="col-66 input_center rpl0">
                            <select name='provinceId' id="provinceId" class="form-control bor_no box-shadow rpl0 form-select rmb5" data-nextselect="#cityId" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                                <option value="">请选择</option>
                                {foreach $area as $vo}
                                <option value="{$vo.a_id}">{$vo.a_name}</option>
                                {/foreach}
                            </select>
                            <select name='cityId' id="cityId" class="form-control bor_no box-shadow rpl0 form-select rmb5"  data-nextselect="#regionId" data-refurl="{:url('common/publics/ajax_area')}?pid={value}&show=1">
                                <option value="">请选择</option>
                            </select>
                            <select name='regionId' id='regionId' class="form-control bor_no box-shadow rpl0 form-select rmb5">
                                <option value="">请选择</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <div class="rmt30 rpd10">
                <input type="button" value="确认" id="activeBtn" data-url="{:url('login/active')}" class="button my-btn-blue button-raised rh45 rline45 rfs16">
            </div>
        </div>
    </div>
</div>
{/block}

{block name='script'}
<script language="JavaScript">
    var flag =false;
    $$('select').on('change', function () {
        var value = $$(this).val();
        var id    = $$(this).data('nextselect');
        var url   = $$(this).data('refurl');
        if(url){
            url = url.replace('{value}', value);
            $$.ajax({
                type: "GET",
                url : url,
                dataType: "json",
                success: function(data){
                    var len = data.length;
                    var html = '';
                    for(var i=0; i < len; i++){
                        html += '<option value="' + data[i].value + '">'  + data[i].label + '</option>';
                    }
                    $$(id).html(html);
                }
            });
        }
    });

    $$('#fcode').blur(function () {
         var nick =$$(this).val();
         if(nick.length>2){
             $$.ajax({
                 url:"{:url('common/publics/checkdttxactive')}",
                 method:'POST',
                 data:{dttxnick:nick,uid:{$userid}},
                 dataType:'json',
                 beforSend:function () {
                     $$('#fcodeinfo').text('查询中...');
                 },
                 success:function(data) {
                     if (data.statusCode=='200'){
                         flag =true;
                     }
                     $$('#fcodeinfo').text(data.message);
                 },
                 error:function(){
                     myApp.alert('网络延时，请重试!','')
                 }
             });
         }
    });


    $$('#activeBtn').click(function () {

       var provinceId =$$('#provinceId').val();
       var cityId =$$('#cityId').val();
       var regionId =$$('#regionId').val();
       var fcode =$$('#fcode').val();
       var _this =$$(this);
       var activeurl=_this.data('url');

//       if (fcode.length<3){
//           myApp.alert('推荐人不能为空！','');
//           return false;
//       }

       if (provinceId==''){
           myApp.alert('所在省不能为空！','');
           return false;
       }

       if (cityId==''){
           myApp.alert('所在市不能为空！','');
           return false;
       }

       if (regionId==''){
           myApp.alert('所在区县不能为空！','');
           return false;
       }

       $$.ajax({
            url:activeurl,
            method:'POST',
            data:{provinceId:provinceId,cityId:cityId,regionId:regionId,fcode:fcode},
            dataType:'json',
            beforSend:function () {
                _this.text('激活中...');
            },
            success:function(data) {
                if (data.statusCode=='200'){
                         location.href=data.closeCurrent;
                }else {
                    myApp.alert(data.message,'')
                }
            },
            error:function(){
                myApp.alert('网络错误，请重试!','')
            },
            complete:function(){
                _this.text('确认')
            }
       })

    });








</script>
{/block}