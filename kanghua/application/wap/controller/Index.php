<?php
namespace app\wap\controller;
use app\common\controller\Common;
use think\Db;
use think\Request;

/**
 *
 * User: lirong
 * Date: 2017/7/9
 * Time: 11:15
 */
class Index extends Common{


    public function index(){
        $this->redirect('store/index');
    }

    /*
     * 聚合页
     * */
    public function lists(){
        $list = Db::name('platform')->where(['pl_isDelete' => 0, 'pl_states' => 1])->field('pl_id,pl_name,pl_image,pl_content,pl_description')->select();
        $this->assign('list', $list);
        $this->assign('title', '大唐云商');
        return $this->fetch();
    }


    /*
     * 招商加盟
     * */
    public function investment(){
        if(Request::instance()->isPost()){
            $data['in_product_name'] = input('post.product_name', '', 'htmlspecialchars,strip_tags,trim');
            $data['in_company_name'] = input('post.company_name', '', 'htmlspecialchars,strip_tags,trim');
            $data['in_username']     = input('post.username', '', 'htmlspecialchars,strip_tags,trim');
            $data['in_mobile']       = input('post.mobile', '', 'htmlspecialchars,strip_tags,trim');
            $data['in_dttx_nick']    = input('post.dttx_nick', '', 'htmlspecialchars,strip_tags,trim');

            $res = $this->validate($data, 'Investment');
            if($res !== true){
                dispatchJump('error', $res, url('wap/index/investment'));
            }

            $data['in_createTime'] = time();
            $res = Db::name('investment')->insert($data);
            !$res && dispatchJump('error', '提交失败', url('wap/index/investment'));
            sendMail('lirong@01ju.com', $this->setEmailBody($data));
            dispatchJump('success', '提交成功', url('wap/index/lists'));
        }else{
            $this->assign('title', '招商加盟');
            return $this->fetch();
        }
    }

    private function setEmailBody($data){
        $emailContent=<<<EOF
<div style="background-color:#ECECEC;">
<table cellpadding="5" align="left" style="width: 600px; margin: 0px auto; text-align: left; position: relative; font-size: 14px; font-family:微软雅黑, 黑体; line-height: 1.5; border-collapse: collapse; background-position: initial initial; background-repeat: initial initial;background:#fff;">
<tbody>
<tr>
<td>产品名称：</td>
<td>%s</td>
</tr>
<tr>
<td>企业名称：</td>
<td>%s</td>
</tr>
<tr>
<td>联系人姓名：</td>
<td>%s</td>
</tr>
<tr>
<td>联系人电话：</td>
<td>%s</td>
</tr>
<tr>
<td>大唐天下账号：</td>
<td>%s</td>
</tr>
<tr>
<td>生成时间：</td>
<td>%s</td>
</tr>
</tbody>
</table>
</div>
EOF;
        return sprintf($emailContent, $data['in_product_name'], $data['in_company_name'], $data['in_username'], $data['in_mobile'], $data['in_dttx_nick'], date('Y-m-d H:i:s', $data['in_createTime']));;
    }







}