<?php
namespace app\common\controller;

class Index
{
    public function index()
    {
        $action =config('novalidate_platform_action');
        $novalidate_platform_controller =config('novalidate_platform_controller');
        dump($action);
        dump($novalidate_platform_controller);
        echo "11111111111111";

    }
}
