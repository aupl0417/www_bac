<?php
namespace app\api\controller\front;
use app\api\controller\front\Init;
class Channel extends Init
{
    /**
     * 频道内容输出
     * 2017-06-20
     */
    public function channel($check=1){
        if($check == 1) {
            $res = $this->check('category_id');
            if($res['code'] != 1) return $this->ret($res);
        }

/*         $list = db('channel_category')->where(['upid' => $this->post['category_id'],'status' => 1])->order('sort asc,id asc')->field('id,category_name,images,url,sub_title,content')->select();
        foreach($list as $key => $val){
            $list[$key]['child'] = db('channel')->where(['status' => 1,'category_id' => $val['id']])->field('name,images,content')->select();
        } */
 		$list = db('channel_category')->where(['id' => $this->post['category_id'],'status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        $list['child'] = db('channel_category')->where(['upid' => $this->post['category_id'],'status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        foreach($list['child'] as $key => $val){
            $list['child'][$key]['child'] = db('channel')->where(['status' => 1,'category_id' => $val['id']])->field('id,name,atime,images,wap_images,content,tag,hit')->order('atime asc,id asc,sort asc')->select();
        } 
        if($list){
			return $this->ret(['code' => 1,'data' => $list]);
		}
		return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
    }
	
	//获取后台配置
    public function get_config(){
		return $this->ret(['code' => 1,'data' => $this->_config()]);
    }
	
	//联系我们
    public function contact_our($check=1){
        if($check == 1) {
            $res = $this->check('name,mobile,company,need_content');
            if($res['code'] != 1) return $this->ret($res);
        } 
		if(strlen($this->post['name'])> 15 || strlen($this->post['name'])< 3){
			return $this->ret(['code' => 0,'msg' => "请输入正确的姓名！"]);
		}
		if(!is_numeric($this->post['mobile']) || strlen($this->post['mobile'])!= 11){
			return $this->ret(['code' => 0,'msg' => "请输入正确的手机号码"]);
		}
		if(strlen($this->post['company'])> 60 ||strlen($this->post['company'])< 6){
			return $this->ret(['code' => 0,'msg' => "请输入正确的公司名称"]);
		}
		//return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
		$data['name']			 = $this->post['name'];
		$data['mobile']			 = $this->post['mobile'];
		if(isset($this->post['qq']) && $this->post['qq']){		
			$data['qq']			 = $this->post['qq'];
		}
		$data['company']		 = $this->post['company'];
		$data['need_content']	 = $this->post['need_content'];
		
 		$result = db('contact')->insert($data);
        if($result){
			return $this->ret(['code' => 1,'data' => '提交成功']);
		}
		return $this->ret(['code' => 0,'msg' => "提交失败！"]);
    }
}
