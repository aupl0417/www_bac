<?php

namespace app\admin\controller;

use app\common\controller\Admin;
use think\Request;

class System extends Admin {
	public function _initialize(){
    parent::_initialize();
    // B('Admin\\Behaviors\\Authenticate', '', $action);

}

	/**
	 * 修改个人资料
	 * 		-只有修改昵称部分
	 */
	public function profile(){
		$userid = session('admin_userid');
		if(Request::instance()->isPost()){
			$data['nickname'] = input('post.nickname');
			
			model('Admin')->where('userid', $userid)->update($data);
			//更新成功后， 重新登录
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','message'=>'操作成功'));
		}else{
			
			$detail = model('Admin')->where('userid', session('admin_userid'))->find();
			$this->assign('Detail', $detail);
			return $this->fetch();
		}
	}
	/**
	 * 修改密码
	 */
	public function changePassword(){
		$userid = session('admin_userid');
		if(Request::instance()->isPost()){
			
			$detail = model('Admin')->where('userid', $userid)->field('username, password')->find();
			if ( hash_hmac('sha256', input('post.password_old'), $detail['username']) != $detail['password'] )
				$this->ajaxReturn(array('statusCode'=>300,'message'=>'旧密码输入错误'));
			$password_new = input('post.password_new');
			if(isset($password_new) && !empty($password_new)) {
				$data['password'] = hash_hmac('sha256', $password_new, $detail['username']);
				model('Admin')->where('userid', $userid)->update($data);
			}
			//更新成功后， 重新登录
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','message'=>'密码更改成功','forwardUrl'=>'/admin.php/Publics/Logout/'));
		}else{
			return $this->fetch();
		}
	}
	
	public function adminManage(){
		//检索条件
        $map = array();
		if(input('post.username')){
			$username = input('post.username');
			$map['username'] = array('like', "%$username%");
            $this->assign('username', $username);
		}
		if(input('post.roleid')){
			$roleid = input('post.roleid');
			$map['roleid'] = $roleid;
            $this->assign('roleid', $roleid);
		}
		//排序
		if(input('post.orderField')){
			$orderField = input('post.orderField');
			$orderDirection = input('post.orderDirection') ? input('post.orderDirection') : 'asc';
		}else{
			//默认排序
            $orderField = 'userid';
            $orderDirection = 'asc';
		}
		
		//分页相关
		$page['pageCurrent'] = max(1 , input('post.pageCurrent'));
		$page['pageSize']= input('post.pageSize') ? input('post.pageSize') : 30 ;
		
		$totalCount = model('admin')->where($map)->count();
		$page['totalCount']=$totalCount ? $totalCount : 0;
		 
		$page_list = model('admin')->order('userid')->where($map)->order($orderField, $orderDirection)->page($page['pageCurrent'], $page['pageSize'])->select();
		//获取角色
		$roles = cache('role') ? cache('role') : model('AdminRole')->get_role_list();

        // var_dump($roles);exit;
        $this->assign('roles', $roles);
        $this->assign('page_list', $page_list);
        $this->assign('page', $page);
		
		return $this->fetch();
	}

	/**
	 * 禁用,启用账号
	 */
	public function adminChangeStatus($userid){
		//只有学校管理员有权限
		if($userid == 1){
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'该对象不可更改'));
		}
		//判断权限, 只能禁用和启用教师角色
		$map['userid'] = $userid;
		$detail = model('Admin')->where($map)->find();
		if(!$detail)
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
	
		//修改状态
		$status = $detail['status'] == 0 ? 1 : 0;
		model('Admin')->where($map)->update(array('status' => $status));
		$this->ajaxReturn(array('statusCode'=>200,'message' => '操作成功'));
	}
	
    //判断用户名是否重复
    public function ajax_checkUsername(){
        	$username = input('get.username');
        	
        	$exist_username = model('Admin')->where(array('username' => $username))->find();
        	if($exist_username){
        		echo '{"error":"用户名已存在"}';
        	}else {
        		echo '{"ok":""}';
        	}
        	exit;
    }
    
    /**
     * 管理员编辑
     */
	public function adminEdit($userid){
		if(Request::instance()->isPost()){
			$info['nickname'] 	= input('post.nickname');
			$info['roleid'] 	= input('post.roleid');
            $result = db('Admin')->where('userid', $userid)->update($info);
			if($result){
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'Admin_System_adminManage'));
			}else{
				$this->ajaxReturn(array('statusCode'=>300,'message' => model('Admin')->getError()));
			}
		}else{
	
			$Detail = model('Admin')->where('userid', $userid)->find();
			//获取角色
			$roles = cache('role') ? cache('role') : model('AdminRole')->get_role_list();

            $this->assign('Detail', $Detail);
            $this->assign('roles', $roles);
            $this->assign('userid', $userid);
			return $this->fetch();
		}
	}
	
	/**
	 * 管理员添加
	 */
	public function adminAdd(){
		if(Request::instance()->isPost()){
			
			$info['username'] 	= input('post.username');
			$info['nickname'] 	= input('post.nickname');
			$info['roleid'] 	= input('post.roleid');
			$info['password'] 	= hash_hmac('sha256', 'md1q2w3e4r', $info['username']);	//生成默认密码

            //验证
            $result = $this->validate($info,'Admin');
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->ajaxReturn(array('statusCode'=>300,'message' => $result));
            }

            $result = model('Admin')->save($info);
			if($result){
				$this->ajaxReturn(array('statusCode'=>200,'message'=>'保存成功!','closeCurrent'=>'true','tabid'=>'System_adminManage'));
			}else{
				$this->ajaxReturn(array('statusCode'=>300,'message' => '保存失败!'));
			}
			
		}else{
	
			//获取角色
			$roles = cache('role') ? cache('role') : model('AdminRole')->get_role_list();
            $this->assign('roles', $roles);

            return $this->fetch('adminEdit');
		}
	}
	
	/**
	 * 默认角色admin不能删除
	 */
	public function adminDelete($userid){
		// $userids = input('get.userid');
        // var_dump($userid);exit;

		$userids = explode(',', $userid);
		foreach ($userids as $userid){
			//过滤不需要删除的 角色 ID
			if($userid == 1)
				continue;
			//判断权限
			
			//删除角色,
			model('admin')->deleteUser($userid);
		}
	
		$this->ajaxReturn(array('statusCode'=>200,'message'=>'用户删除成功'));
	}
	
	/**
	 * 系统设置-管理员设置-重置密码   
	 */
	public function adminResetPassword($userid){
		
		// $userid = input('get.userid','','intval');
		//不能修改超级管理员
		if($userid == 1){
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'该对象不可更改'));
		}
		//自己不能修改自己的角色
		if($userid == session('admin_userid')){
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'重置自己的密码有啥意思嘛！'));
		}
		//修改规则
		$username = model('Admin')->where('userid', $userid)->value('username');
		//设置默认密码
//		$password = 'md123456';
		$password = 'md'.create_randomstr(6);
		$data['password'] = hash_hmac('sha256', $password, $username);
		$result = model('admin')->where('userid', $userid)->update($data);
		if($result){
			$this->ajaxReturn(array('statusCode'=>200,'message'=>'重置密码为:'.$password,'tabid'=>'System_adminUserLists'));
		}else{
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'重置失败,可能密码重置前就是:'.$password));
		}
		
	}
    /**
     * 系统设置-角色列表 
     */
    public function adminRoleList(){
    	$DB=db('admin_role');
    	//检索条件
    	
    	//分页相关
    	$page['pageCurrent']=max(1,input('post.pageCurrent',0,'intval'));
    	$page['pageSize']=input('post.pageSize',20,'intval');
    	$totalCount = $DB->count();
    	$page['totalCount']= $totalCount ? $totalCount : 0;
    	
    	//取数据
    	$str=intval($page['pageCurrent']-1)*$page['pageSize'];
    	$roleList = $DB->page($page['pageCurrent'], $page['pageSize'])->order('roleid asc')->select();
    	
    	$this->assign('page_list', $roleList);
    	$this->assign('page', $page);
    	return $this->fetch();
    }
    /**
     * 系统设置-角色列表-添加角色 
     */
    public function adminRoleAdd(){
    	if(Request::instance()->isPost()){
    		$input = input('');
            $info  = $input['info'];
    		
    		$info['status'] = 1;
    		$result = model('AdminRole')->data($info)->save();
    		if(!$result){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>'添加角色失败，请重试。ErrorNo:0001'));
    		}
    		//更新角色缓存
    		model('AdminRole')->get_role_list();
    		$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminRoleList'));
    	}
    	return $this->fetch();
    }
    /**
     * 系统设置-角色列表-编辑角色 
     */
    public function adminRoleEdit($roleid){
    	if(Request::instance()->isPost()){
            $input = input('');
            $info = $input['info'];

    		$result = model('AdminRole')->where('roleid', $roleid)->update($info);
    		if(!$result){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存角色信息失败，请重试。ErrorNo:0001'));
    		}

    		//更新角色缓存
    		model('AdminRole')->get_role_list();
    		$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminRoleList'));
    	}else{
	    	$Detail = model('AdminRole')->where('roleid', $roleid)->find();
            $this->assign('Detail', $Detail);
            $this->assign('roleid', $roleid);
	    	return $this->fetch();
    	}
    }
    /**
     * 系统设置-角色列表-删除角色 
     */
    public function adminRoleDelete($roleid){

    	//不允许删除超级管理员
    	if($roleid == 1)
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'不允许删除超级管理员'));
    	$result = model('AdminRole')->where('roleid', $roleid)->delete();
    	
    	if(!$result){
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'删除角色失败，请重试。ErrorNo:0001'));
    	}

    	//删除权限表
    	db('admin_role_priv')->where('roleid', $roleid)->delete();

    	//更新角色缓存
    	model('AdminRole')->get_role_list();
    	$this->ajaxReturn(array('statusCode'=>200,'tabid'=>'System_adminRoleList'));
    }
    
    /**
     * 系统设置-角色列表-禁用角色 
     */
    public function adminRoleForbid(){
    	$DB = db('admin_role');
    	$roleid = input('get.roleid','','intval');
    	$detail_role = model('AdminRole')->where('roleid', $roleid)->find();
    	
    	if(!$roleid)
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误，请重试'));
    	$status = $detail_role['status'] ? 0 : 1;
    	//更新状态
    	$result = $DB->where('roleid', $roleid)->save(array('status'=>$status));

    	if(!$result){
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'变更状态失败'));
    	}
    	$this->ajaxReturn(array('statusCode'=>200,'tabid'=>'System_adminRoleList'));
    }
    
    
    /**
     * 角色权限设置
     */
    public function adminPrivSetting($roleid){
    	$array_menu_res = db('admin_menu')->order('listorder, id')->select();
        if($array_menu_res){
            foreach ($array_menu_res as $value) {
                $array_menu[$value['id']] = $value;
            }
        }
    	if(Request::instance()->isPost()){
    		//删除旧权限
			db('admin_role_priv')->where('roleid', $roleid)->delete();
			$ids = input('post.ids');
			$data['roleid'] = $roleid;
			if($ids){
				$ids = explode(',', $ids);
				foreach ($ids as $menu_id){
					//取出id的设置
					$detail = $array_menu[$menu_id];
					$data['menuid'] = $detail['id'];
					$data['module'] = strtolower($detail['module']);
					$data['controller'] = strtolower($detail['controller']);
					$data['action'] = strtolower($detail['action']);
					db('admin_role_priv')->insert($data);
				}
			}
			//这里用html里写的js来post， 结果也是在js里写的。
		//	$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','message'=> 'haha', 'tabid'=>'System_adminRoleList'));
    	}else{

    		
    		//$menus = list_to_tree($array_menu, 'id' ,'parentid', 'children');
    		$map['roleid'] = $roleid;
    		foreach ($array_menu as $menu_id => $menu){
    		    unset($menu['icon']);
    			$json_priv[$menu_id] = $menu;
    			//判断该权限是否拥有
    			$map['menuid'] = $menu['id'];
    			$exist_priv = db('admin_role_priv')->where($map)->find();
    			if($exist_priv){
    				$json_priv[$menu_id]['checked'] = true;
    			}
    		}
    		$menus = list_to_tree($json_priv, 'id' ,'parentid', 'children');
            $this->assign('json_priv', json_encode($menus));
            $this->assign('roleid', $roleid);
    		
    		return $this->fetch();
    	}
    }
    
    /**
     * 菜单显示列表
     */
    public function adminNodeLists(){
		$DB = db();
    	$tree = new \Lain\Phpcms\tree();
    	$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
    	$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
    	
    	$result = $DB->table('db_admin_menu')->order('listorder ASC,id ASC')->select();
    	$array = array();
    	foreach($result as $r) {
    		$r['icon'] = $r['icon'] ? '<i class="fa fa-'.$r['icon'].'"></i>' : '';
    		$r['cname'] = $r['name'];
    		$r['display_icon'] = $r['display'] ? '' : ' <img src ="/Public/images/gear_disable.png" title="不在菜单显示">';
    		$r['str_manage'] = '<a href="'.url('System/adminNodeAdd?parentid='.$r['id']).' " class="btn btn-green" data-toggle="dialog" data-width="520" data-height="290" data-id="dialog-mask" data-mask="true">添加下级菜单</a> <a class="btn btn-green" href="'.U('System/adminNodeEdit?id='.$r['id']).'" data-toggle="dialog" data-width="520" data-height="290" data-id="dialog-mask" data-mask="true">修改</a> <a href="'.U('System/adminNodeDelete?id='.$r['id']).'" class="btn btn-red" data-toggle="doajax" data-confirm-msg="确定要删除该行信息吗？">删</a> ';
    		$array[] = $r;
    	}
    	
    	$str  = "<tr data-id='\$id'>
			    	<td>\$id</td>
			    	<td>\$spacer\$cname \$display_icon</td>
			    	<td>\$listorder</td>
			    	<td>\$icon</td>
    				<td align='center'>\$str_manage</td>
    	</tr>";
    	$tree->init($array);
    	$this->categorys = $tree->get_tree(0, $str);
    	return $this->fetch();
    }
    
    public function adminNodeListsNew(){
    	//取出菜单列表
    	$menu_list = model('AdminMenu')->field('id, name, parentid, module,controller, action, icon, display,outside, listorder')->order('listorder ASC,id ASC')->select();
    	$node_lists[0] = array('id' => 0, 'name' => '后台菜单', 'font' => array('color' => 'red'), 'open'=>true);
    	if($menu_list){
    		foreach ($menu_list as $key => $menu){
    			//第一级展开, 其余的收起
    			if($menu['parentid'] == 0){
    				$menu['open'] = true;
    			}else{
    				$menu['open'] = false;
    			}
    			$menu['id'] = (int)$menu['id'];
    			$menu['pId'] = (int)$menu['parentid'];
    			$menu['faicon'] = $menu['icon'] ? $menu['icon'] : 'cog';
    			//隐藏的显示灰色
    			if($menu['display'] != 1)
    				$menu['font'] = array('color' => '#999999');	
    			$node_lists[] = $menu;
    		}
    	}
    	$this->assign('js_node_lists', json_encode($node_lists));
    	return $this->fetch();
    }
    
    //菜单编辑
    public function ajax_nodeEdit(){
    	if(Request::instance()->isPost()){
    		$id = input('post.id', '', 'intval');
    		
    		$info['name'] 		= input('post.name');
    		$info['module'] 		= input('post.module');
    		$info['controller'] 			= input('post.controller');
    		$info['action'] 			= input('post.action');
    		$info['icon'] 		= input('post.icon');
    		$info['listorder'] 	= input('post.listorder', '', 'intval');
    		$info['display'] 	= input('post.display');
    		$info['outside'] 	= input('post.outside');

    		$result = model('AdminMenu')->where('id', $id)->update($info);
    		if($result){
    			$this->ajaxReturn(ajaxCallBack(200,'更新成功!'));
    		}else {
    			$this->ajaxReturn(ajaxCallBack(300,'更新失败!'));
    		}
    	}
    }
    
    //菜单添加
    public function ajax_nodeAdd(){
    	if(Request::instance()->isPost()){
    		$parentid = input('post.pid', '', 'intval');
    		
    		//添加菜单
    		$data['parentid'] 	= $parentid;
    		$data['name'] 		= '新增菜单';
    		$data['controller'] 			= input('post.c');
    		$data['module'] 			= input('post.module');
    		$data['display'] 	= 1;
    		$data['listorder']	= 0;
    		$id = db('AdminMenu')->insertGetId($data);
    		
    		if($id){
    			$data['id'] = $id;
    			$data['faicon'] = 'cog';	//默认图标
    			$return['flag'] = true;
    			$return['data'] = $data;
    		}else{
    			$return['flag'] = false;
    		}
    		
    		$this->ajaxReturn($return);
    	}
    }
    
    //菜单拖拽
    public function ajax_nodeDrag(){
    	if(Request::instance()->isPost()){
    		$input = input('');
            //操作的节点
            $nodes = $input['treeNodes']; //暂时操作一个节点
            //目标节点
            $target_node = $input['targetNode'];
            $move_type = $input['moveType'];

            // var_dump($nodes[0]['id']);
            // var_dump($target_node['id']);
            // var_dump($move_type);
            // var_dump($menu_list);exit;
            $ids = '';
            if($nodes){
                foreach ($nodes as $value) {
                    $ids .= $ids ? ','.$value['id'] : $value['id'];
                }
            }
            $return['flag'] = false;
            switch ($move_type) {
                case 'prev':
                case 'next':
                    //更改排序
                    $menu_list = model('AdminMenu')->nodeDrag($move_type, $target_node['parentid'], $ids, $target_node['id']);
                    if($menu_list){
                        foreach ($menu_list as $key => $value) {
                            db('AdminMenu')->where(array('id' => $value))->update(array('listorder' => $key, 'parentid' => $target_node['parentid']));
                        }
                    }
                    $return['flag'] = true;
                    break;
                case 'inner':
                    $menu_list = model('AdminMenu')->nodeDrag($move_type, $target_node['id'], $ids);
                    if($menu_list){
                        foreach ($menu_list as $key => $value) {
                            db('AdminMenu')->where(array('id' => $value))->update(array('listorder' => $key, 'parentid' => $target_node['id']));
                        }
                    }
                    $return['flag'] = true;
                    break;
                default:
                    //# code...
                    break;
            }
    		
    		$this->ajaxReturn($return);
    	}
    }
    
    //菜单删除
    public function ajax_nodeDelete(){
        
    	if(Request::instance()->isPost()){
    		$id = input('post.id', '', 'intval');
    		
    		$result = model('AdminMenu')->where('id', $id)->delete();
    		if($result){
    			$this->ajaxReturn(array('flag' => true));
    		}else {
    			$this->ajaxReturn(array('flag' => false));
    		}
    	}
    }
    
    //图标,查找带回
    public function adminNodeIcon(){

        $map = array();
    	//取出图标集
    	//检索条件
		if(input('post.name')){
			$this->name = $name = input('post.name','','trim');
			$map['name'] = array('like', "%$name%");
		}
		
		//分页相关
		$page['pageCurrent'] = max(1 , input('post.pageCurrent'));
		$page['pageSize']= input('post.pageSize') ? input('post.pageSize') : 30 ;
		$page['totalCount'] = db('admin_icon')->where($map)->count();
    	$icons = db('admin_icon')->where($map)->select();
    	
    	$this->assign('page', $page);
    	$this->assign('page_list', $icons);
    	return $this->fetch();
    }
    /**
     * 系统设置-节点设置-增加节点  
     */
    public function adminNodeAdd(){
    	if(Request::instance()->isPost()){
            $input = input('');
            $info = $input['info'];
    		$info['icon'] = input('post.icon');
    		
    		if(!model('AdminMenu')->create($info)){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>model('AdminMenu')->getError()));
    		}else{
	    		model('AdminMenu')->add($info);
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminNodeLists'));
    		}
    	}else{
    		//取出父级菜单信息
    		$parentid = input('get.parentid','','intval');
    		if($parentid)
    			$this->Detail = model('AdminMenu')->where('id', $parentid)->field('c, display, icon')->find();
	    	$tree = new \Lain\Phpcms\tree();
	    	$result = model('AdminMenu')->select();
	    	$array = array();
	    	foreach($result as $r) {
	    		$r['cname'] = $r['name'];
	    		$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
	    		$array[] = $r;
	    	}
	    	$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
	    	$tree->init($array);
	    	$this->select_categorys = $tree->get_tree(0, $str);
	    	$this->display('adminNodeEdit');
    	}
    }
    /**
     * 系统设置-节点设置-编辑节点 
     */
    public function adminNodeEdit(){
    	$DB = db('admin_menu');
    	$id = input('get.id','','intval');
    	if(Request::instance()->isPost()){
            $input = input('');
            $info = $input['info'];
    		//新增图标
    		if(input('post.icon')){
    			$info['icon'] = input('post.icon');
    		}
    		if(!$DB->create($info)){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>$DB->getError()));
    		}else{
	    		$DB->where('id', $id)->update($info);
	    		$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminNodeLists'));
    		}
    		
    	}else{
	    	$this->Detail = $DB->where('id', $id)->find();
	    	$tree = new \Lain\Phpcms\tree();
	    	$result = $DB->select();
	    	foreach($result as $r) {
	    		$r['cname'] = $r['name'];
	    		$r['selected'] = $r['id'] == $this->Detail['parentid'] ? 'selected' : '';
	    		$array[] = $r;
	    	}
	    	$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
	    	$tree->init($array);
	    	$this->select_categorys = $tree->get_tree(0, $str);
	    	return $this->fetch();
    	}
    }
    /**
     * 系统设置-节点设置-删除节点 
     */
    public function adminNodeDelete(){
    	$DB = model('AdminMenu');
    	$id = input('get.id','','intval');
    	$result = $DB->where('id', $id)->delete();
    	if(!$result){
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'删除节点失败，请重试。ErrorNo:0001'));
    	}
    	$this->ajaxReturn(array('statusCode'=>200,'tabid'=>'System_adminNodeLists'));
    }

    public function index()
    {
        // TODO: Implement index() method.
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function edit()
    {
        // TODO: Implement edit() method.
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }
}