<?php
namespace app\platform\controller;

use app\common\controller\Platform;
use app\platform\model\Area;
use think\Exception;
use think\Request;
use think\db;
use think\Session;

class Shopkeeper extends Platform
{
    public function _initialize(){
        parent::_initialize();
        $this->goodsModel = model('ShopKeeper');
    }

    public function index(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 30, 'intval');

        $id        = input('post.id', '', 'intval');
        $name      = input('post.name', '', 'htmlspecialchars,strip_tags,trim');
        $provinceCode  = input('post.provinceCode', '', 'intval');
        $cityCode      = input('post.cityCode', '', 'intval');
        $beginTime = input('post.beginTime', '', 'htmlspecialchars,strip_tags,trim');
        $endTime   = input('post.endTime', '', 'htmlspecialchars,strip_tags,trim');
        $where     = array('s_isDelete' => 0, 's_projectId' => session('user.platformId'), 's_state' => 'pass');

        !empty($name) && $where['s_userDttxNick']    = array('LIKE', '%' . $name .'%');
        !empty($id)   && $where['s_id']              = array('LIKE', '%' . $id .'%');
        !empty($beginTime) && $where['s_createTime'] = array('>=', strtotime($beginTime));
        !empty($endTime)   && $where['s_createTime'] = array('<=', strtotime($endTime));
        !empty($provinceCode)  && $where['s_provinceCode'] = array('=', $provinceCode);
        !empty($cityCode)      && $where['s_cityCode']     = array('=', $cityCode);

        $page['totalCount']  = $this->goodsModel->getShopKeeperCount($where);
        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];

        $field = 's_id as id,s_userDttxNick as nickName,s_provinceCode,s_cityCode,s_regionCode,s_streetCode,s_createTime as createTime,s_address as address,s_type as type,s_isBlocked as isBlocked';
        $shop  = $this->goodsModel->getShopKeeperAll($field, $where, '', 's_id desc', $limit);

        if($shop){
            $areaModel = model('common/Area');
            foreach ($shop as &$val){
                $province   = $areaModel->getAreaById('a_name', $val['s_provinceCode']);
                $city       = $areaModel->getAreaById('a_name', $val['s_cityCode']);
                $val['area'] = ($province ? $province['a_name'] : '') . '-' . ($city ? $city['a_name'] : '');
            }
        }

        $areaModel = new Area();
        $area =$areaModel->findListByParentId(0);

        $this->assign('area',$area);
        $this->assign('goodsList', $shop);
        $this->assign('page',      $page);
        $this->assign('id',        $id ?: '');
        $this->assign('name',      $name);
        $this->assign('beginTime', $beginTime);
        $this->assign('endTime',   $endTime);
        return $this->fetch();
    }

    /*
     * 添加经销商
     * */
    public function create(){
        if(Request::instance()->isPost()){

            $userinfo['state']='pass';
            $userinfo['createId']=Session::get('user.userId');
            $userinfo['operateId']=Session::get('user.userId');
            $res = model('ShopKeeper')->createShopKeeper($userinfo);
            $res && $res['code'] == 300 && $this->ajaxReturn(ajaxCallBack(300, $res['message']));
            $this->ajaxReturn(ajaxCallBack(200, '添加经销商成功', true, 'platform_shopkeeper_create'));
        }else{
            $areaModel = new Area();
            $area =$areaModel->findListByParentId(0);
            $this->assign('area',$area);
            return $this->fetch();
        }
    }


    public function edit(){

    }

    /*
     * 查看
     * */
    public function view(){
        $input = input('');

        !isset($input['id']) && empty($input['id']) && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));

        $id    = $input['id'] + 0;
        $shop = model('ShopKeeper')->getShopKeeperOne('*', ['s_id' => $id]);

        $areaModel = new Area();
        $area      = $areaModel->findListByParentId(0);

        $areaModel = model('common/Area');
        $province  = $areaModel->getAreaById('a_name', $shop['s_provinceCode']);
        $city      = $areaModel->getAreaById('a_name', $shop['s_cityCode']);
        $region    = $areaModel->getAreaById('a_name', $shop['s_regionCode']);
//        $street    = $areaModel->getAreaById('a_name', $shop['s_streetCode']);

        $this->assign('province',$province ? $province['a_name'] : '');
        $this->assign('city',    $city ? $city['a_name'] : '');
        $this->assign('region',  $region ? $region['a_name'] : '');
//        $this->assign('street',  $street ? $street['a_name'] : '');
        $this->assign('area',    $area);
        $this->assign('shop',    $shop);
        return $this->fetch();
    }

    /*
     * 审核
     * */
    public function review(){
        $input = input('');

        (!isset($input['id'])|| empty($input['id'])) && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));
        $id    = $input['id'] + 0;

        if(Request::instance()->isPost()){
            (!isset($input['state'])  || empty($input['state']))  && $this->ajaxReturn(ajaxCallBack(300, '请选择审核状态'));
            (!isset($input['userId']) || empty($input['userId'])) && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));
            $state  = htmlspecialchars(strip_tags(trim($input['state'])));
            $reason = isset($input['state']) ? htmlspecialchars(strip_tags(trim($input['reason']))) : '';
            $userId = $input['userId'] + 0;

            Db::startTrans();
            try{
                $params = array(
                    's_state'  => $state,
                    's_reason' => $reason,
                    's_operateId' => session('user.userId')
                );
                $res = Db::table('db_shopkeeper')->where(array('s_id' => $id))->update($params);
                if(!$res){
                    throw new Exception('审核失败');
                }

                if($state == 'pass'){
                    $res = model('ShopItems')->addRecommendedGoods($userId, session('user.userId'));
                    if($res['statusCode'] == 300){
                        throw new Exception('添加默认推荐商品失败' . $res['message']);
                    }

                    $res = Db::name('user_platform')->where(['up_id' => $userId])->update(['up_roleid' => 3]);
                    if(!$res){
                        throw new Exception('添加默认权限失败');
                    }
                }

                // 提交事务
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200, '操作成功', true, 'platform_shopkeeper_reviewlist'));
            }catch (\Exception $e){
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300, '操作失败' . $e->getMessage()));
            }
        }else{
            $shop = model('ShopKeeper')->getShopKeeperOne('*', ['s_id' => $id]);

            $areaModel = new Area();
            $area      = $areaModel->findListByParentId(0);

            $areaModel = model('common/Area');
            $province  = $areaModel->getAreaById('a_name', $shop['s_provinceCode']);
            $city      = $areaModel->getAreaById('a_name', $shop['s_cityCode']);
            $region    = $areaModel->getAreaById('a_name', $shop['s_regionCode']);
            $street    = $areaModel->getAreaById('a_name', $shop['s_streetCode']);

            $this->assign('province',$province ? $province['a_name'] : '');
            $this->assign('city',    $city ? $city['a_name'] : '');
            $this->assign('region',  $region ? $region['a_name'] : '');
            $this->assign('street',  $street ? $street['a_name'] : '');
            $this->assign('area',    $area);
            $this->assign('shop',    $shop);
            return $this->fetch();
        }
    }

    /*
     * 入驻审核列表
     * */
    public function reviewlist(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 10, 'intval');

        $where     = array('s_isDelete' => 0, 's_projectId' => session('user.platformId'), 's_state' => array('neq', 'pass'));

        $page['totalCount']  = $this->goodsModel->getShopKeeperCount($where);
        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];

        $field = 's_id as id,s_userDttxNick as nickName,s_provinceCode,s_cityCode,s_regionCode,s_streetCode,s_createTime as createTime,s_address as address,s_type as type,s_state as state';
        $shop  = $this->goodsModel->getShopKeeperAll($field, $where, '', 's_id desc', $limit);

        if($shop){
            $areaModel = model('common/Area');
            foreach ($shop as &$val){
                $province    = $areaModel->getAreaById('a_name', $val['s_provinceCode']);
                $city        = $areaModel->getAreaById('a_name', $val['s_cityCode']);
                $val['area'] = ($province ? $province['a_name'] : '') . '-' . ($city ? $city['a_name'] : '');
            }
        }

        $areaModel = new Area();
        $area =$areaModel->findListByParentId(0);

        $this->assign('area',$area);
        $this->assign('goodsList', $shop);
        $this->assign('page',      $page);
        return $this->fetch();
    }

    /*
     * 恢复或终止
     * @params $id  type:int    shopkeeper id
     * @params $act type:string 操作类型
     * */
    public function block(){
        $input = input('');
        (!isset($input['id'])  || empty($input['id']))  && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        (!isset($input['act']) || empty($input['act'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;
        $act   = isset($input['act']) && !empty($input['act']) ? htmlspecialchars(strip_tags(trim($input['act']))) : 'recovery';
        !in_array($act, [ 'block', 'recovery']) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));

        $res   = $this->goodsModel->blockShopKeeper($id, $act);
        $msg   = ($act == 'block' ? '终止': '恢复');
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => $msg . '失败'));
        $this->ajaxReturn(ajaxCallBack(200, $msg . '成功'));
    }

    public function remove(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $res   = $this->goodsModel->deleteBaseGoods($id);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '删除失败'));

        $this->ajaxReturn(array('statusCode' => 200, 'message' => '删除成功'));
    }
}
