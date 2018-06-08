{extend name="common:base" /}
{block name='top'}
<!--底部弹出层-->
<div id="d1" class="modal-overlay"></div>
<div class="picker-modal picker-1 modal-in yue-modal bg_fff">
    <div class="toolbar solid_b">
        <div class="toolbar-inner bg_gray">
            <div class="left"><a href="#" class="close-picker rfs14 c_red">取消</a></div>
            <div class="center sliding rfs14 c_333">选择提现方式</div>
            <div class="right"><a href="#" class="close-picker rfs14 c_blue">完成</a></div>
        </div><!--.toolbar-inner bg_gray-->
    </div><!--.toolbar solid_b-->
    <div class="picker-modal-inner">
        <div class="content-block bg_fff rpd0 rmg0 re">
            <form action="#">
                {foreach $bankList as $vo}
                <div class="solid_b rpd15 over bankList">
                    <label class="square rml10 over">
                        <div class="pull-left c_333 over">
                            <span class="pull-left bankName">
                                {$vo.bankName}
                            </span>
                        </div><!--.pull-left c_333 over-->
                        <div class="c_999 pull-right over">
                            <span class="pull-left cardNumber">{$vo.cardNumber}</span>
                            <span class="pull-left rml10 radius">
                                <input type="radio" name="bankId" value="{$vo.id}" {if condition="$vo.ab_is_default_card eq 1"}checked{/if}>
                                <em></em>
                            </span>
                        </div><!--.c_999 pull-right over-->
                    </label>
                </div><!--.over solid_b rpd15-->
                {/foreach}
                <div class="ab rmt30">
                    <a class="yue-btn button button-fill color-red external" href="{:url('Bank/create')}">+ &nbsp; 添加银行卡</a>
                </div><!--.over solid_b rpd15-->
            </form>
        </div><!--.content-block bg_fff rpd0 rmg0 rpb30-->
    </div><!--.picker-modal-inner-->
</div><!--.picker-modal picker-1 modal-in yue-modal bg_fff-->
<!--底部弹出层-->
<!--弹框-->
<div id="d2" class="modal-overlay"></div>
<div class="successmsg modal modal-in yue-modal-in">
    <div class="modal-inner">
        <div>
            <img src="__STATIC__/wap/images/tx-s.png" width="50" alt="">
        </div>
        <p class="rfs18 text-center c_blue">提现成功</p>
        <p class="rfs14 text-left rline30">处理时间为<span class="c_blue">3</span>个工作日,请留意您的银行卡信息。单次余额提现最小值<span class="c_blue">1</span>元,最大值<span class="c_blue">5</span>万每天只能提现<span class="c_red">1</span>次</p>
    </div>
</div>
<div class="modal modal-in yue-modal-in errormsg">
    <div class="modal-inner">
        <p class="rfs18 text-center c_blue" id="errormsg"></p>
    </div>
</div>
<div class="modal modal-in confirm-layer" style="display: none; margin-top: -119px;">
    <div class="modal-inner">
        <div class="modal-title"></div>
        <div class="modal-text"></div>
    </div>
    <div class="modal-buttons modal-buttons-3 ">
        <span class="modal-button" style="font-size: 14px;"><a href="{:url('')}" class="external">重新输入</a></span>
        <span class="modal-button modal-button-bold" style="font-size: 14px;"><a href="{:url('user/setPayPwd')}" class="external">修改提现密码</a></span>
    </div>
</div>
<!--弹框-->
{/block}
{block name="leftnav"}
<div class="left"><a href="{:url('ucenter/balance')}" class="back link external"><i class="fa fa-angle-left fs30 c_fff"></i></a></div>
{/block}
{block name="header"}
<div class="right">
    <a href="#" class="link open-popover rfs14 c_fff">提现记录</a>
</div>
{/block}
{block name="content"}
<div class="popover popover-links modal-in mx-pop">
    <div class="popover-angle on-top" style="left: 161px;"></div>
    <div class="popover-inner">
        <div class="list-block bg_fff">
            <div class="solid_b"><a href="{:url('ucenter/withdrawList', ['month' => $currentMonth])}" class="list-button item-link rfs14 external">本月</a></div>
            <div class="solid_b"><a href="{:url('ucenter/withdrawList', ['month' => $lastMonth])}"    class="list-button item-link rfs14 external">上月</a></div>
            <div class="solid_b"><a href="{:url('ucenter/withdrawList', ['month' => $lastThreeMonth])}" class="list-button item-link rfs14 external">近三个月</a></div>
        </div>
    </div>
</div>
<!--导航弹出框-->
<div class="pages navbar-fixed">
    <div data-page="case" class="page">
        <div class="page-content bg_gray">
            <div class="list-block yue-block">
                <form action="#">
                    <ul>
                        <li>
                            <div class="solid_b">
                                <div class="row mr0 phong_form rpl15">
                                    <div class="col-33 text_left rfs16 c_333">
                                        提现金额
                                    </div>
                                    <div class="col-66 input_center rpl0 input-radius-active">
                                        <input type="text" name="balance" class=" rfs14 bor_no rpl0" placeholder="请输入提现金额">
                                    </div>
                                </div>
                            </div>
                            <p class="rfs14 c_999 rpt10 rpl15 rpb10 bg_gray rmg0">您当前金额为：{$balance}&nbsp;&nbsp;&nbsp;&nbsp;最多可提现：{if condition='$balance <= 50000'}{$balance}{else/}50000{/if}</p>
                        </li>
                        <li>
                            <div class="rpd15 solid_b">
                                <div class="over">
                                    <div class="rfs14 pull-left">手续费</div>
                                    <div class="rfs14 pull-right c_blue">免费</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="rpd15 solid_b">
                                <a href="#" class="rfs14 open-picker"  data-picker=".picker-1">
                                <div class="over rfs14">
                                    {empty name='defaultBank'}
                                    <div class="rfs14 pull-left c_333 ">请选择银行卡</div>
                                    {else/}
                                    <div class="rfs14 pull-left c_333" id="bankName">{$defaultBank}</div>
                                    {/empty}
                                    <div class="pull-right">
<!--                                        <a href="#" class="rfs14 open-picker"  data-picker=".picker-1">-->
                                            切换
<!--                                        </a>-->
                                    </div>
                                </div>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="solid_b">
                                <div class="row mr0 phong_form rpl15">
                                    <div class="col-33 text_left rfs16 c_333">
                                        提现密码
                                    </div>
                                    <div class="col-66 input_center rpl0 input-radius-active">
                                        <input type="text" class=" rfs14 bor_no rpl0" style="display: none;">
                                        <input name="password" autocomplete="off" onfocus="this.type='password'" type="text" class=" rfs14 bor_no rpl0" placeholder="请输入提现密码">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="rpd15">
                        <a id="cash" data-url="{:url('')}" class="button my-btn-blue rh40 rline40">提现</a>
                        <p class="rfs14 rmt30 c_999 rmb15">试运行期间，每天限提现1次，金额不能小于1元，不超过50000元。
                            提现申请后，处理时间为3个工作日，请耐心等待。</p>
                    </div>
                </form>
            </div><!--.list-block-->
        </div><!--.page-content-->
    </div><!--.page-->
</div><!--.pages navbar-fixed-->
{/block}

{block name='script'}
<script>
    var div = document.getElementById('d1');
    var div2 = document.getElementById('d2');
    div.onclick=function (){
        div.className="modal-overlay";
        $$(".picker-modal").hide();
    }
    $$('.open-picker').on('click', function (){
        div.className="modal-overlay-visible modal-overlay";
    });
    $$('.close-picker').on('click', function (){
        div.className="modal-overlay";
    });
    var cash=document.getElementById("cash");

    $$("input[name='bankId']").on('change', function () {
       var bankId = $$(this).val();
       var cardNumber = $$(this).parents('.over').find('.cardNumber').text()
       var bankName   = $$(this).parents('.over').find('.bankName').text()
        bankName = cardNumber.replace('**** **** **** **** ', bankName);
       $$('#bankName').html(bankName);
    });

    div2.click(function(){
        return;
    })
    cash.onclick=function(){

        var url = $$(this).data('url');
        var balance = $$("input[name='balance']").val();
        var bankId  = $$("input[name=bankId]:checked").val();
        var password= $$("input[name=password]").val();

        $$.ajax({
            type: "POST",
            url : url,
            dataType: "json",
            data : { balance : balance, bankId : bankId, password : password},
            success: function(data){
                if(data.statusCode == 200){
                    $$(".successmsg").show();
                    div2.className="modal-overlay-visible modal-overlay";
                    setTimeout(function(){
                        $$(".successmsg").hide();
                        div2.className="modal-overlay";
                        window.location.href = "{:url('ucenter/withdrawList')}";
                    }, 2000);
                }else if(data.statusCode == 301){
                    $$(".errormsg").find('p').html(data.message.msg);
                    $$(".errormsg").show();
                    div2.className="modal-overlay-visible modal-overlay";
                    setTimeout(function () {
                        $$(".errormsg").hide();
                        div2.className="modal-overlay";
                        window.location.href = data.message.url;
                    }, 2000);
                }else if(data.statusCode == 302){
                    $$(".confirm-layer").find('.modal-text').html(data.message);
                    $$(".confirm-layer").show();
                    div2.className="modal-overlay-visible modal-overlay";
                }else{
                    $$(".errormsg").find('p').html(data.message);
                    $$(".errormsg").show();
                    div2.className="modal-overlay-visible modal-overlay";
                    setTimeout(function () {
                        $$("input[name='balance']").val('');
                        $$(".errormsg").hide();
                        div2.className="modal-overlay";
                    }, 2000);
                }
            }
        });
    }

</script>
{/block}