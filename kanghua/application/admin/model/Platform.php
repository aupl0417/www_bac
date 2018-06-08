<?php
namespace app\admin\model;
use think\Db;
use think\Model;

/**
 *
 * User: lirong
 * Date: 2017/6/27
 * Time: 22:18
 */
class Platform extends Model{

    protected $name ='platform';

    /**
     * 查找项目列表用于搜索条件
     * @param $input
     * @return bool
     */
    public function findPlatFormList($input){
//        print_r($input);
        if (!is_array($input)){
            return false;
        }
        $map['pl_isDelete']=0;
        if (isset($input['name']) && !empty($input['name'])){
            $map['pl_name']=['like',"%{$input['name']}%"];
        }
        if (isset($input['companyname']) && !empty($input['companyname'])){
            $map['pl_company_name'] =['like',"%{$input['companyname']}%"];
        }

        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);

        $order =isset($input['order'])?$input['order']:"pl_create_time asc";

        $data['list'] =Db::name($this->name)->where($map)->page($page,$pagesize)->order($order)->select();
        $data['count']=Db::name($this->name)->where($map)->count();
        if (!empty($data)){
            return $data;
        }else{
            return false;
        }

    }

    /**
     * 批量更新平台状态
     * @param $ids
     * @param $status
     * @return boo
     */
    public function changeStatus($ids,$status){
        if (empty($ids)){
            return false;
        }
        $result =Db::name($this->name)->where(['pl_id'=>['in',$ids]])->update([
            'pl_states'=>$status
        ]);
        if ($result){
            return true;
        }else{
            return false;
        }

    }

    /**
     * 批量删除项目
     * @param $ids
     * @return bool
     */
    public function remove($ids){
        if (empty($ids)){
            return false;
        }
        Db::startTrans();
        try{
            Db::name($this->name)->where(['pl_id'=>['in',$ids]])->update(['pl_isDelete'=>1]);
            return true;
        }catch (\Exception $e){
            Db::rollback();
            return false;
        }
    }


    /**
     * 根据ID查找信息
     * @param $id
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function findDetailByid($id,$field='*'){

        if (empty($id)){
            return false;
        }

        $data =Db::name($this->name)->field($field)->find($id);

        if (!empty($data)){
            return $data;
        }else{
            return false;
        }
    }

    /**
     * 获取所有项目列表
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function findAllPlatform(){
         $data = Db::name($this->name)->where(['pl_states'=>1,'pl_isDelete'=>0])->order(['pl_id'=>'asc'])->select();
         return $data;
    }



}