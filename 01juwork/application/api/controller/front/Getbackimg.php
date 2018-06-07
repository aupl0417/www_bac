<?php
namespace app\api\controller\front;
use app\api\controller\front\Init;
class Getbackimg extends Init
{
    /**
     * 频道内容输出
     * 2017-06-20
     */
    public function get_img($check=1){
        if($check == 1) {
            $res = $this->check('category_id');
            if($res['code'] != 1) return $this->ret($res);
        }

        $list = db('backimg')->where(['category_id' => $this->post['category_id'],'status' => 1])->field('id,atime,name,category_id,images')->order('atime asc,id asc')->select();
        if($list){
			return $this->ret(['code' => 1,'data' => $list]);
		}
		return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
    }
}
