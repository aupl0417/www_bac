<?php
namespace app\api\controller\front;
use app\api\controller\front\Init;
class Details extends Init
{
    /**
     * 案例详情
     * 2017-06-20
     */
    public function get_details($check=1){
        if($check == 1) {
            $res = $this->check('category_id');
            if($res['code'] != 1) return $this->ret($res);
        }

        $list = db('example')->where(['id' => $this->post['category_id'],'status' => 1])->field('id,atime,name,title,hit,category_id,detail_images,content,wap_detail_images,tag')->find();
        if($list){
			db('example')->where(['id' => $this->post['category_id'],'status' => 1])->setInc('hit');
			return $this->ret(['code' => 1,'data' => $list]);
		}
		return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
    }
    
	public function get_news_details($check=1){
        if($check == 1) {
            $res = $this->check('category_id');
            if($res['code'] != 1) return $this->ret($res);
        }

        $list = db('channel')->where(['id' => $this->post['category_id'],'status' => 1])->field('id,atime,name,hit,category_id,images,content,wap_images,tag')->find();
        if($list){
			db('channel')->where(['id' => $this->post['category_id'],'status' => 1])->setInc('hit');
			return $this->ret(['code' => 1,'data' => $list]);
		}
		return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
    }
}
