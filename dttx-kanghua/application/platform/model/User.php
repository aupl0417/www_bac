<?php
namespace app\platform\model;
use think\Model;
use think\Db;
use think\Session;

/**
 *
 * User: lirong
 * Date: 2017/6/29
 * Time: 16:27
 */
class User extends Model{

    protected $name ='user';
    protected $othertable ='db_user_platform';
    protected $debug=false;

    /**
     * 会员资料列表读取使用
     * @param $input
     * @return bool
     */
    public function findUserList($input){

        if (empty($input) || !is_array($input)){
            return false;
        }

        $map=[];

        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId'])?$input['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }

        $map['up_plateform_id']=$platformId;
        if (!empty($input['nick'])){
            $map['u.u_nick']=['like',"%{$input['nick']}%"];
        }

        if (!empty($input['name'])){
            $map['u.u_name']=['like',"%{$input['name']}%"];
        }

        if (!empty($input['tel'])){
            $map['u.u_tel']=$input['tel'];
        }
        if (!empty($input['provinceId'])){
            $map['p.up_provinceId']=$input['provinceId'];
        }
        if (!empty($input['cityId'])){
            $map['p.up_cityId']=$input['cityId'];
        }

        if (!empty($input['roleid'])){
            $map['p.up_roleid']=$input['roleid'];
        }

        $beginDate =strtotime($input['beginDate']);
        $endDate =strtotime($input['endDate']);

        if (!empty($beginDate) && empty($endDate)){
            $map['up_create_time']=['>= time',$beginDate];
        }elseif (empty($beginDate) && !empty($endDate)){
            $map['up_create_time']=['<= time',$endDate];
        }elseif (!empty($beginDate) && !empty($endDate)){
            $map['up_create_time'] =['between time',[$beginDate,$endDate]];
        }

        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);
        $order =$input['orderField'] .' '.$input['orderDirection'];

        $data['list'] =Db::name($this->name)->field('u.*,p.*,a.a_name provinceName,b.a_name cityName,us.u_name fname,us.u_nick fnick,ur.ur_rolename,ul.ul_name as ulname,cl.c_name as clname')
            ->alias('u')
            ->join( 'user_platform p','u.u_id=p.up_uid','left')
            ->join('area a','p.up_provinceId=a.a_id','left')
            ->join('area b','p.up_cityId=b.a_id','left')
            ->join('user us','p.up_fcode=us.u_code','left')
            ->join('user_role ur','p.up_roleid=ur.ur_roleid','left')
            ->join('user_level ul','p.up_user_level_id=ul.ul_id','left')
            ->join('channel cl','p.up_user_agent_level=cl.c_id','left')
            ->where($map)
            ->page($page,$pagesize)
            ->order($order)
            ->fetchSql(false)
            ->select();

        foreach ($data['list'] as &$item){
            $item['u_auth'] =get_auth($item['u_auth']);
        }

        $data['count']=Db::name($this->name)->alias('u')
            ->join( 'db_user_platform p','u.u_id=p.up_uid','left')
            ->where($map)
            ->fetchSql(false)
            ->count();

        if (!empty($data)){
            return $data;
        }else{
            return false;
        }


    }

    /**
     * 推荐人关系树
     * @param $uid
     * @param $paltformId
     * @return bool
     */
    public function findrelationalByPlatFromIdAndUid($uid,$paltformId){

        if (empty($paltformId)){
            $paltformId =Session::get('user.platformId');
        }

        if (empty($uid)){
            return false;
        }

        $code = Db::name('user_platform')
            ->field('up_uid as id,up_fcode,u_code,u_name,u_nick')
            ->alias('up')->join('db_user u','u.u_id=up.up_uid','left')
            ->where(['up_uid'=>$uid,'up_plateform_id'=>$paltformId])
            ->fetchSql($this->debug)
            ->find();
            $list =array();
            if (empty($code)){
                return false;
            }else{

            $parent = Db::name($this->name)
                ->alias('u')
                ->join('user_platform up','u.u_id=up.up_uid','left')
                ->where(['up_plateform_id'=>$paltformId,'u_code'=>$code['up_fcode']])
                ->field('up_uid,up_fcode,u_code,u_name,u_nick')
                ->fetchSql($this->debug)
                ->find();
                $parentlist=array();
                if (!empty($parent)){
                    $parentlist['id']=$parent['u_code'];
                    $parentlist['pid']=$parent['up_fcode'];
                    $parentlist['name']=$parent['u_nick'].'('.($parent['u_name']==""?"--":$parent['u_name']).').【上级】';
                }else{
                    $parentlist['id']=$code['u_code'];
                    $parentlist['pid']=0;
                    $parentlist['name']='无上级';
                }

                $list['id']=$code['u_code'];
                $list['pid']=$code['up_fcode'];
                $list['name']=$code['u_nick'].'('.($code['u_name']==""?"--":$code['u_name']).')【本人】';
                $child = $this->list_tree_bycode($code['u_code'],$paltformId);
                if (!empty($child)){
                    $list['children']=$child;
                }
                $parentlist['children'][]=$list;
                return $parentlist;
        }

    }


    private function list_tree_bycode($code,$paltformId){

        $list =[];
        $codedata =Db::name('user_platform')->alias('up')->join('user u','up.up_uid=u.u_id')->field('up_uid,up_fcode,u_code,u_name,u_nick')->where(['up_fcode'=>$code,'up_plateform_id'=>$paltformId])->select();
        if (!empty($codedata)){
            foreach ($codedata as $key =>$item){
                $list[$key]['id']=$item['u_code'];
                $list[$key]['pid']=$item['up_fcode'];
                $list[$key]['name']=$item['u_nick'].'('.($item['u_name']==''?"--":$item['u_name']).')';

                $child = $this->list_tree_bycode($item['u_code'],$paltformId);
                if (!empty($child)){
                    $list[$key]['children']=$child;
                }
            }
        }

        return $list;
    }

    /**
     * 查找带角色用户列表for系统管理用户列表
     * @param $input
     * @return bool
     */
    public function findRoleUserList($input){

        if (empty($input) || !is_array($input)){
            return false;
        }

        $map=[];

        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId'])?$input['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }
        $map['up_plateform_id']=$platformId;

        if (isset($input['nick']) && !empty($input['nick'])){
            $map['u_nick']=['like',"%{$input['nick']}%"];
        }

        if (isset($input['name']) && !empty($input['name'])){
            $map['u_name']=['like',"%{$input['name']}%"];
        }

        if (isset($input['tel']) && !empty($input['tel'])){
            $map['u_tel']=$input['tel'];
        }
        if (isset($input['provinceId']) && !empty($input['provinceId'])){
            $map['u_provinceId']=$input['provinceId'];
        }
        if (isset($input['cityId']) && !empty($input['cityId'])){
            $map['u_cityId']=$input['cityId'];
        }

        if (isset($input['roleid']) && !empty($input['roleid'])){
            $map['up_roleid']=$input['roleid'];
        }else{
            $map['up_roleid']=['>',0];
        }

        $beginDate =isset($input['beginDate']) ? strtotime($input['beginDate']):"";
        $endDate =isset($input['endDate'])?strtotime($input['endDate']):"";

        if (!empty($beginDate) && empty($endDate)){
            $map['up_create_time']=['>= time',$beginDate];
        }elseif (empty($beginDate) && !empty($endDate)){
            $map['up_create_time']=['<= time',$endDate];
        }elseif (!empty($beginDate) && !empty($endDate)){
            $map['up_create_time'] =['between time',[$beginDate,$endDate]];
        }

        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);
        $order =$input['orderField'] .' '.$input['orderDirection'];

        $data['list'] =Db::name($this->name)->field('u.*,p.*,a.a_name provinceName,b.a_name cityName,ur.ur_rolename')
            ->alias('u')
            ->join( 'user_platform p','u.u_id=p.up_uid','left')
            ->join('area a','p.up_provinceId=a.a_id','left')
            ->join('area b','p.up_cityId=b.a_id','left')
            ->join('user_role ur','p.up_roleid=ur.ur_roleid','left')
            ->where($map)
            ->page($page,$pagesize)
            ->order($order)
            ->fetchSql($this->debug)
            ->select();
        $data['count']=Db::name($this->name)->alias('u')
            ->join( 'user_platform p','u.u_id=p.up_uid','left')
            ->where($map)
            ->fetchSql($this->debug)
            ->count();

        if (!empty($data)){
            return $data;
        }else{
            return false;
        }
    }

    /**
     * 判断账号在该平台下是否存在,可用于获取账号的详细信息
     * @param $unick
     * @param $platformId
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function findUserByNickAndPlatFormid($unick,$platformId){
        if (empty($unick)){
            return false;
        }
        $data= Db::name($this->name)
            ->alias('u')->field('u.*,up.*,a.a_name provinceName,b.a_name cityName,ur.ur_rolename,cl.c_name')
            ->join('user_platform up','u.u_id=up.up_uid','left')
            ->join('area a','up.up_provinceId=a.a_id','left')
            ->join('area b','up.up_cityId=b.a_id','left')
            ->join('user_role ur','up.up_roleid=ur.ur_roleid','left')
            ->join('user_level ul','up.up_user_level_id=ul.ul_id','left')
            ->join('channel cl','up.up_user_agent_level=cl.c_id','left')
            ->where(['up_plateform_id'=>$platformId,'up_isDelete'=>0])->where('u_nick|u_tel','eq',$unick)->find();

        return $data;
    }



}