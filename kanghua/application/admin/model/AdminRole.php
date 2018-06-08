<?php 
/*
 * 后台管理员模型
 */
namespace app\admin\model;

use think\Model;
class AdminRole extends Model {
	
	public function get_role_list(){
		$result = $this->select();
		if(!$result)
			return false;
		//按角色ID来排列数组
		foreach ($result as $v){
			$data[$v->roleid] = $v->toArray();
		}
		//保存到缓存中
		cache('role', $data,'3600');
		return $data;
	}
}
