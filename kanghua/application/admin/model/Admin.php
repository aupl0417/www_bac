<?php 
/*
 * 后台管理员模型
 */
namespace app\admin\model;

use think\Db;
use think\Model;
class Admin extends Model {
    private $randomKey;     //验证随机码
    public $setting = array(
            'enableCodeCheck' 	=>	false,	//开启登录验证码
            'errorTime'			=>	1, 		//错误次数, 达到错误次数后, 则需要验证码
            'errorMaxTime'		=>	2,		//允许的最大错误次数, 达到后, 则需要等待时间后, 才能登录
            'waitTime'			=> 20,		//需要等待的时间(分钟)
    );
	// //管理员账号验证规则
	// protected $_validate = array(
	// 		//array('username','require','用户名必须！'), //默认情况下用正则进行验证
	// 		//array('username','checkUsername', '只能输入a-zA-Z0-9_的组合', 1, 'function', 1),
	// 		array('username', 'checkUsername', '只能输入a-zA-Z0-9_的组合', self::EXISTS_VALIDATE, 'callback'),
	// 		array('username', '3,12', '用户名长度须在3-12个字符之间', self::EXISTS_VALIDATE, 'length', 1),
	// 		array('username', '', '用户名被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用
	// );
	/*
	 * 登录验证部分
	 * @garam 验证码/密码
	 * @return
	 *  1用户名错误 
	 *  2账号禁用
	 *  3密码错误
	 *  
	 */
	protected function checkUsername($username){
	
		//如果用户名中有空格，不允许注册
		if (strpos($username, ' ') !== false) {
			return false;
		}
		preg_match("/^[a-zA-Z0-9_]{0,64}$/", $username, $result);
	
		if (!$result) {
			return false;
		}
		return true;
	}
			
    public function setRandomKey($randomKey){
        $this->randomKey = $randomKey;
    }
	
    public function login(){
        $username = input('post.username');
        $passwordhash = input('post.passwordhash');
        //$demo = hash_hmac('sha256',password, username);
        $setting = $this->setting;
        //如果开启验证码, 检测错误次数
        if($setting['enableCodeCheck']){
            $rtime = model('Times')->where(array('username'=>$username,'isadmin'=>1))->find();
        
            //密码错误10次，则需要等待1小时后，才能再次登录
            if($rtime['times'] >= $setting['errorMaxTime']) {
                $minute = $setting['waitTime'] - floor((time()-$rtime['logintime'])/60);
                if($minute > 0){
                    $this->error = lang('locked', array('minute' => $minute));
                    return false;
                    //$this->ajaxReturn(array('flag'=>false, 'errorCode'=> 1, 'message'=>'请等待'.$minute.'分钟后再次登录'));	//$this->error('请等待'.$minute.'分钟后再次登录');
                }
            }
            //如果登录密码错误次数大于， 则检测验证码
            if($rtime['times'] >= $setting['errorTime']){
                $code = input('post.code');
                $verify = new \Think\Verify();
                if(!$code){
                    $this->error = lang('please_enter_code');
                    return false;
                    //$this->ajaxReturn(array('flag'=>false, 'errorCode'=> 2));	//$this->error('请输入验证码');
                }
                if(!$verify->check($code)){
                    $this->error = lang('checkcode_wrong');
                    return false;
                    //$this->ajaxReturn(array('flag'=>false, 'errorCode'=> 3));	//$this->error('验证码输入有误');
                }
            }
        }
        
        $map['username'] = $username;
        $detail = $this->where($map)->find();
        if (! $detail){
            $this->error = lang('loginFailed');
            return false;
            //$this->ajaxReturn(array('flag'=>false, 'errorCode'=> 10));		//$this->error('用户名，密码错误');
        }
        	
        //用户组是否被禁用
        	
        //验证是否被禁用
        if($detail['status'] != 1){
            $this->error = lang('forbid');
            return false;
            //$this->ajaxReturn(array('flag'=>false, 'errorCode'=> 6));		//$this->error('此账号已被禁用');
        }
        
        //验证密码
        $randomKey = $this->randomKey;
        $ip = get_client_ip(0, true);
        if (hash_hmac('sha256', $detail['password'], $randomKey) != $passwordhash){
            //如果开启验证码, 则增加错误次数
            if($setting['enableCodeCheck']){
                //小于最大允许错误次数, 则增加错误计数
                if($rtime && $rtime['times'] < $setting['errorMaxTime']) {
                    $times = 8-intval($rtime['times']);
                    $result = model('Times')->where(array('username'=>$username))->save(array('ip'=>$ip,'isadmin'=>1,'times'=>array('exp', 'times+1')));
                } else {
                    //如果超过最大计数后, 则重新开始计数
                    model('Times')->where(array('username'=>$username,'isadmin'=>1))->delete();
                    model('Times')->add(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>time(),'times'=>1));
                }
            }
            $this->error = lang('loginFailed');
            return false;
            //$this->ajaxReturn(array('flag'=>false, 'errorCode'=> 20));		//$this->error('密码错误');
        } else{
            //更新登录信息
            $this->where('userid='.$detail['userid'])->update(array('lastloginip'=>$ip, 'lastlogintime'=>time()));
        
            //删除登录错误次数
            if($setting['enableCodeCheck']){
                model('Times')->where(array('username' => $username,'isadmin' => 1))->delete();
            }
            //记录session
            session('admin_userid',$detail['userid']);
            session('admin_roleid',$detail['roleid']);
            if($detail['roleid'] == '1'){
                session('admin_identify',1);
            }
            	
            $cookie_time = 86400*30;
            cookie('admin_username',$username,$cookie_time);
            cookie('admin_userid', $detail['userid'],$cookie_time);
            return true;
            //$this->ajaxReturn(array('flag'=>true));
        
        }
        
    }
	
    /**
     * 检查用户登录是否需要验证码
     * @param unknown $username
     */
    public function check_username_for_code($username){
        $setting = $this->setting;
        if(!$setting['enableCodeCheck'])
            return false;
        
        //检测次数表，是否有当前用户的信息， 如果次数〉指定次数 ， 则返回假。
        $times = model('Times')->check_username($username);
        if($times >= $setting['errorTime']){
            return true;
        }else{
            return false;
        }
    }
	/**
	 * 修改密码
	 * @param unknown $userid 	用户ID
	 * @param unknown $password	密码
	 * @return boolean
	 */
	public function edit_password($userid, $password){

		$userid = intval($userid);
		if($userid < 1) return false;
		if(!is_password($password))
		{
			return false;
		}
		$passwordinfo = password($password);
		return $this->where('userid='.$userid)->save($passwordinfo);
		
	}
	
	public function deleteUser($userid){
		$map['userid'] = $userid;
		return $this->where($map)->delete();
	}

    /**
     * 根据权限id查找对应用户列表
     * @param $roid
     * @return bool
     */
	public function findUserListByRoleId($roid){

	    if (empty($roid)){
	        return false;
        }

        $data =Db::name('admin')->where(['status'=>1,'roleid'=>$roid])->field('userid,nickname')->select();
        return $data;

    }





}
