<?php
namespace app\admin\controller;
use think\Controller;

/**
 *
 * Author: lirong
 * Date: 2017/8/18
 * Time: 15:06
 */
class Index extends Controller{

    public function index(){
        $this->redirect('/','',404);
    }

}