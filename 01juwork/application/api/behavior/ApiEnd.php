<?php

namespace app\api\behavior;
use app\api\controller\work\v1\Init;
use think\Request;
class ApiEnd extends Init
{
    //定义此预加载，即可直接跳过Init中的_initialize()
    public function _initialize()
    {

    }

    public function run(&$params)
    {
        $this->log_write($params);
    }

    public function log_write($params){
        if(config('api_log') !== true) return;
        $handle = $params['handle'];
        $logs   = [
            'atime'         => date('Y-m-d H:i:s'),
            'dotime'        => $handle->dotime,
            //'controller'    => request()->controller(),
            //'action'        => request()->action(),
            'url'           => request()->url(),
            'post'          => request()->post(),
            'result'        => $handle->result,
            'sw'            => $handle->sw,
        ];

        $content = var_export($logs,true).PHP_EOL.'--------------------------------------------------------'.PHP_EOL;

        $path = LOG_PATH . DS . 'api_logs_' . date('Ym') . '.log';
        file_put_contents($path,$content,FILE_APPEND);
    }

}
