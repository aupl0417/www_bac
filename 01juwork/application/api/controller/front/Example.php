<?php
namespace app\api\controller\front;
use app\api\controller\front\Init;
class Example extends Init
{
    /**
     * 频道内容输出
     * 2017-06-20
     */
/*     public function example(){
 		$list = db('example_category')->where(['upid' => 0,'status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
		foreach($list){
			$list['child'] = db('example_category')->where(['upid' => $this->post['category_id'],'status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
		}
        foreach($list['child'] as $key => $val){
            $list['child'][$key]['child'] = db('example')->where(['status' => 1,'category_id' => $val['id']])->field('id,atime,name,images,detail_images,content,tag,wap_images,category_id,platform_id,application_id')->order('atime asc,id asc,sort asc')->select();
        } 
        if($list){
			return $this->ret(['code' => 1,'data' => $list]);
		}
		return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
    } */
	
    public function example(){
		if(isset($this->post['category_id']) && $this->post['category_id']!=""){
			$map['id'] = $this->post['category_id'];
		}else{
			$map['upid'] = 0;
		}
		$map['status'] = 1;
		
 		$list = db('example_category')->where($map)->order('atime asc,id asc,sort asc')->field('id,atime,category_name,url')->select();
		foreach($list as $key=>$val){
			$list[$key]['child'] = db('example_category')->where(['upid' => $val['id'],'status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,url')->select();
			foreach($list[$key]['child'] as $k=>$v){
				$list[$key]['child'][$k]['child'] = db('example')->where(['status' => 1,'category_id' => $v['id']])->field('id,name,title,atime,images,detail_images,wap_images,wap_detail_images,tag,content,category_id,platform_id,application_id')->order('atime asc,id asc,sort asc')->select();
			}
		}

        if($list){
			return $this->ret(['code' => 1,'data' => $list]);
		}
		return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
    }
	
    /**
     * 案例查询
     * 2017-08-15
     */
    public function get_example(){
		if(isset($this->post['category_id']) && $this->post['category_id'] !=""){
			$map['category_id'] = $this->post['category_id'];
		}
		if(isset($this->post['platform_id']) && $this->post['platform_id'] !=""){
			$map['platform_id'] = $this->post['platform_id'];
		}
		if(isset($this->post['application_id']) && $this->post['application_id'] !=""){
			$map['application_id'] = $this->post['application_id'];
		}
		$map['status'] = 1;
        $list = db('example')->where($map)->field('id,atime,name,images,detail_images,content,tag,wap_images')->order('atime asc,id asc,sort asc')->select();

        if($list){
			return $this->ret(['code' => 1,'data' => $list]);
		}
		return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
    }
}
