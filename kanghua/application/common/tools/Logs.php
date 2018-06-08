<?php
namespace app\common\tools;
use think\Config;
use think\Exception;
use think\Session;

/**
 *
 * Author: lirong
 * Date: 2017/7/19
 * Time: 11:04
 */
class Logs{

    private static $_instance =null;

    private function __construct(){
    }

    private function __clone(){
    }

    public static function getInstance(){
        if (is_null(self::$_instance) || isset(self::$_instance)){
            self::$_instance =new MongdbHelper();
        }
        return self::$_instance;
    }

    /**
     * 写入mongodb日志
     * @param string $log_type_id  日志类型编码
     * @param string $table   操作的数据库表名
     * @param $logInfo          写入的操作日志
     * @param string $connections   欲写入的collection
     * @param string $model   是否按日期进行分collection存储
     * @param string $user   操作者的编号
     * @param string $user_type   操作者的类型
     * @param string $level     操作日志级别
     * @return bool             返回是否为成功
     * @throws Exception
     */
    public static function writeMongodb($log_type_id ='',$table='',$orderId='',$logTitle='',$logData='',$model='',$collections='',$user='',$user_type='',$level='log'){

            $data = [
                'log_type_id'	   => $log_type_id - 0,
                'log_table'	 	   => $table,
                'log_user'		   => Session::has('user.userId') ? Session::get('user.userId').'-'.Session::get('user.username') : $user,//操作者id
                'log_time'		   => mytime(),//时间
                'log_microTime'	   => getMicrotime(),//时间
                'log_ip'		   => GetIP(),//ip
                'log_code'		   => getTimeMarkID(),//编号,用于排序
                'log_orderID'      =>$orderId,
                'log_title'         =>$logTitle,
                'log_info'         =>$logData,
                'level'=>$level
            ];

         if (empty($collections)){
             $num =substr($log_type_id,0,3);
             $collection =Config::get('logs.collections');
             if (isset($collection[$num])){
                 $collections = $collection[$num];
             }else{
                 $collections ='fenxiao';
             }
         }


         if (!empty($model)){
             $collections.='_'.mytime($model);
         }
         $mongodb =self::getInstance();
         $res = $mongodb->name($collections)->insert($data);

         return $res;
    }

    /**
     * 日志写入
     * @param $log_type_id   日志编码 logs.php
     * @param string $table  所操作数据表
     * @param string $tagId  订单ID或当前操作ID标识符
     * @param string $logData 日志数据
     * @param string $model  写入collections 时间格式 Ymd
     * @param string $logTitle  可选 日志标题
     * @param string $collections 可选mongodb表名
     * @param $user
     * @param $level
     * @return bool
     */
    public static function write($log_type_id,$logData,$tagId='',$table='',$model='',$logTitle='',$collections='',$user='',$level=''){

        $config =Config::get('logs');
        if (empty($logTitle)){
            $logTitle =isset($config[$log_type_id])?$config[$log_type_id]:$log_type_id;
        }
        $data = [
            'log_type_id'	   => $log_type_id + 0,
            'log_table'	 	   => $table,
            'log_user'		   => Session::has('user.userId') ? Session::get('user.userId').'-'.Session::get('user.username') : $user,//操作者id
            'log_time'		   => mytime(),//时间
            'log_microTime'	   => getMicrotime(),//时间
            'log_ip'		   => GetIP(),//ip
            'log_code'		   => getTimeMarkID(),//编号,用于排序
            'log_orderID'      =>$tagId,
            'log_title'         =>$logTitle,
            'log_info'         =>$logData,
            'level'=>$level
        ];

        if (empty($collections)){
            $num =substr($log_type_id,0,3);
            $collection =Config::get('logs.collections');
            if (isset($collection[$num])){
                $collections = $collection[$num];
            }else{
                $collections ='fenxiao';
            }
        }

        if (!empty($model)){
            $collections.='_'.mytime($model);
        }
        $mongodb =self::getInstance();
        $res = $mongodb->name($collections)->insert($data);

        return $res;

    }


}