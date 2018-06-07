<?php
namespace app\api\controller\front;
use app\api\controller\front\Init;
class Bottommenu extends Init
{
    /**
     * 底部菜单获取
     * 2017-06-20
     */
    public function get_menu(){
		$list = db('bottom_menu')->where(['upid' => 0,'status' => 1])->order('atime asc,id asc')->field('id,atime,name,images,url')->select();
		if(count($list)>0){
			foreach($list as $key => $val){
				$list[$key]['child'] = db('bottom_menu')->where(['status' => 1,'upid' => $list[$key]['id']])->field('id,atime,name,images,url')->order('atime asc,id asc')->select();
			} 
			return $this->ret(['code' => 1,'data' => $list]);
		}
		return $this->ret(['code' => 0,'msg' => "数据不存在！"]);
    }
}
