<?php
/**
 *
 * User: lirong
 * Date: 2017/5/31
 * Time: 8:48
 */
namespace app\index\controller;
use app\common\controller\Common;
use app\payment\model\UserPlatform;

class Index extends Common {

    public function index(){
        $this->redirect('/');
    }
}