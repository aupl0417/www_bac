<?php
namespace app\common\controller;
use think\Request;
use think\Hook;
/**
 *
 * User: lirong
 * Date: 2017/6/26
 * Time: 10:28
 */
abstract class Admin extends Common{

    public function _initialize()
    {
        parent::checkAdmin();
        $action = array(
            'permission'=>array('profile', 'changepassword', 'ajax_checkusername'),
            //'allow'=>array('index')
        );

        $request =Request::instance();
        $controller =$request->controller();
        $novalidate=config('novaildate_controller');
        if (!in_array(strtolower($controller),$novalidate)){
            Hook::exec('app\\common\\behavior\\CheckAdminAuth', 'run', $action);
        }
    }

    /**
     * 用于首页数据展示
     * @return mixed
     */
    public abstract function index();

    /**
     * 增加数据
     * @return mixed
     */
    public abstract function create();

    /**
     *修改数据
     * @return mixed
     */
    public abstract function edit();

    /**
     * 移除数据
     * @return mixed
     */
    public abstract function remove();



}