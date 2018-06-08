<?php
namespace app\job\controller;

use Pheanstalk\Pheanstalk;
use think\Config;

class Index{

    private $pheanstalk =null;


    public function __construct(){
        $this->pheanstalk =new Pheanstalk(Config::get('beanstalkd.hostname'),Config::get('beanstalkd.hostport'));

    }

    public function index(){
        $result = $this->pheanstalk->useTube('thinkjob')->put('first');
        var_dump($result);
    }

    public function index2(){

        $job =$this->pheanstalk->watch('thinkjob')->ignore('default')->peekReady();

        echo $job->getData();

    }

    public function index3(){

        $states =$this->pheanstalk->getConnection()->isServiceListening();

        var_dump($states);


    }


}

