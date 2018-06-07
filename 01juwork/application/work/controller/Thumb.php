<?php
namespace app\work\controller;
use think\Controller;

class Thumb extends Controller
{
    public function index(){
		require_once(APP_PATH.'extend/timthumb.php');
    }
}
