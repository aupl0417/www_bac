<?php 
/*
 * 后台管理员模型
 */
namespace app\admin\model;

use think\Model;
class AdminMenu extends Model {
	
	public function nodeDrag($move_type, $parentid, $ids, $target_id = null){
		$menu_list = array();
		//获取该父级下的现有排序
		$map['parentid'] = $parentid;
		$map['id']	= array('notin', $ids);
		$menu_list_res = $this->where($map)->order('listorder, id')->field('id')->select();

		$array_ids = explode(',', $ids);
		if($menu_list_res){
			foreach ($menu_list_res as $key => $value) {
				if($target_id && $value->id == $target_id){
					if($move_type == 'prev'){
						//前面插入
						// $menu_list + $array_ids
						$menu_list = array_merge($menu_list, $array_ids);
						$menu_list[] = $value->id;
					}elseif($move_type == 'next'){
						//后面插入
						$menu_list[] = $value->id;
						$menu_list = array_merge($menu_list, $array_ids);
					}
				}else{
					$menu_list[] = $value->id;
				}
			}
		}
		if($move_type == 'inner'){
			//尾部插入
			$menu_list = array_merge($menu_list, $array_ids);
		}
		return $menu_list;
	}

}
