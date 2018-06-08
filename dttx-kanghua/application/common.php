<?php
use think\Config;
use think\Db;
use think\Response;
use think\View as ViewTemplate;
use think\exception\HttpResponseException;
// 应用公共文件
/**
 * 用于ajax返回调用
 * @param $statusCode       int	必选。状态码(ok = 200, error = 300, timeout = 301)，可以在BJUI.init时配置三个参数的默认值。
 * @param string $message 	string	可选。信息内容。
 * @param string $tabid     string	可选。待刷新navtab id，多个id以英文逗号分隔开，当前的navtab id不需要填写，填写后可能会导致当前navtab重复刷新。
 * @param string $dialogid  string	可选。待刷新dialog id，多个id以英文逗号分隔开，请不要填写当前的dialog id，要控制刷新当前dialog，请设置dialog中表单的reload参数
 * @param string $divid     string	可选。待刷新div id，多个id以英文逗号分隔开，请不要填写当前的div id，要控制刷新当前div，请设置该div中表单的reload参数。
 * @param bool $closeCurrent 	boolean	可选。是否关闭当前窗口(navtab或dialog)。
 * @param string $forward 	string	可选。跳转到某个url
 * @param string $forwardConfirm	string	可选。跳转url前的确认提示信息。
 * @return \think\response\Json
 * @author lirong
 */
function ajaxCallBack($statusCode,$message='',$closeCurrent=false,$tabid='',$dialogid='',$divid='',$forward='',$forwardConfirm=''){

    $data['statusCode']=$statusCode;
    $data['message']=$message;
    if (!empty($closeCurrent)){
        $data['closeCurrent']=$closeCurrent;
    }
    if (!empty($tabid)){
        $data['tabid']=$tabid;
    }
    if (!empty($closeCurrent)){
        $data['closeCurrent']=$closeCurrent;
    }
    if (!empty($dialogid)){
        $data['dialogid']=$dialogid;
    }
    if (!empty($divid)){
        $data['divid']=$divid;
    }
    if (!empty($forward)){
        $data['forward']=$forward;
    }
    if (!empty($forwardConfirm)){
        $data['forwardConfirm']=$forwardConfirm;
    }
    return $data;

//    return array(
//        'statusCode'=>$statusCode,
//        'message'=>$message,
//        'tabid'=>$tabid,
//        'dialogid'=>$dialogid,
//        'divid'=>$divid,
//        'closeCurrent'=>$closeCurrent,
//        'forward'=>$forward,
//        'forwardConfirm'=>$forwardConfirm
//    );
}

/*
 * 远程调用信息返回
 * */
function ajaxRemoteMessage($type='ok',$message){
    return array($type=>$message);
}


/*
 * 生成单号
 * */
function makeOrder(){
    $order = date('YmdHis');
    $array = explode('.', microtime(true));
    return $order . end($array);
}

/**
 * array_column 替代函数
 * @param $input
 * @param $columnKey
 * @param null $indexKey
 * @return array
 */
function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
        $indexKeyIsNull            = (is_null($indexKey))?true :false;
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
        $result                         = array();
        foreach((array)$input as $key=>$row){
            if($columnKeyIsNumber){
                $tmp= array_slice($row, $columnKey, 1);
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
            }else{
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
            }
            if(!$indexKeyIsNull){
                if($indexKeyIsNumber){
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key))?current($key):null;
                    $key = is_null($key)?0:$key;
                }else{
                    $key = isset($row[$indexKey])?$row[$indexKey]:0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}


function createCode($user_id) {
    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
    $num = $user_id;
    $code = '';
    while ( $num > 0) {
        $mod = $num % 35;
        $num = ($num - $mod) / 35;
        $code = $source_string[$mod].$code;
    }

    if(strlen($code)<5){
        $code = str_pad($code,6,'0',STR_PAD_LEFT);
    }
    return $code;
}


function curl_post($url,$data,$param=null){
    $curl = curl_init($url);// 要访问的地址
    //curl_setopt($curl, CURLOPT_REFERER, $param['referer']);

    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 MSIE 8.0'); // 模拟用户使用的浏览器
    //curl_setopt($curl, CURLOPT_USERAGENT, 'spider'); // 模拟用户使用的浏览器
    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    //curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    //curl_setopt($curl, CURLOPT_ENCODING, ''); // handle all encodings
    //curl_setopt($curl, CURLOPT_HTTPHEADER, $refer);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
    //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    //curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址

    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    curl_setopt($curl,CURLOPT_POSTFIELDS,$data);// post传输数据

    //是否为上传文件
    if(!is_null($param)) curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
    $res = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $res;
}

function curl_get($url){
    $curl = curl_init($url);// 要访问的地址
    //curl_setopt($curl, CURLOPT_REFERER, $param['referer']);

    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 MSIE 8.0'); // 模拟用户使用的浏览器
    //curl_setopt($curl, CURLOPT_USERAGENT, 'spider'); // 模拟用户使用的浏览器
    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    //curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    //curl_setopt($curl, CURLOPT_ENCODING, ''); // handle all encodings
    //curl_setopt($curl, CURLOPT_HTTPHEADER, $refer);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
    //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    //curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址

    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    $res = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $res;
}


// 浏览器友好的变量输出
function dump($var, $echo = true, $label = null) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    $output = print_r($var, true);
    $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
    if ($echo) {
        echo($output);
        return null;
    } else
        return $output;
}

/**
 * 生成签名
 * @param array     $data   要进行签名的数据
 * @param string    $nosign  不参与签名字段
 */
function sign($data,$nosign=''){
    $not = ['random'];
    if($nosign){
        $nosign = explode(',',$nosign);
        $not    = array_merge($not,$nosign);
    }
    $not = array_unique($not);

    foreach($data as $key => $val){
        if(in_array($key,$not)) unset($data[$key]);
    }

    ksort($data);
    //dump($data);
    $query=http_build_query($data).'&'.Config::get('api_cfg.sign_code');
    //dump($query);
    $query=urldecode($query);
    return md5($query);
}

function get_dttxLoginInfo($nick,$password){

    if (empty($nick)){
        return array('status'=>300,'data'=>false);
    }

    $apiloginurl =Config::get('dttxapi.loginUrl');
    $data['parterId'] =Config::get('dttxapi.parterId');
    $data['username']=$nick;
    $data['password']=getSuperMD5($password);
    ksort($data);
    $salt =Config::get('dttxapi.salt');
    $signValue =md5(http_build_query($data).'&'.$salt);
    $data['signValue']=$signValue;
    $dttxApiUrl =$apiloginurl.'?'.http_build_query($data);
    $begintime =getMicrotime();
    $res =curl_get($dttxApiUrl);
    $data =json_decode($res,true);

    $endtime =getMicrotime();
    $timer =$endtime-$begintime;

    $logdata['begintime']=$begintime;
    $logdata['endtime']=$endtime;
    $logdata['timer']=$timer;
    $logdata['url']=$dttxApiUrl;

    $logdata['data']=$data;
    ksort($logdata);
    \app\common\tools\Logs::writeMongodb(700000,'dttx_user',$nick,'登录接口日志',$logdata,'Ym');
    if (!empty($data)){
        return $data;
    }else{
        return false;
    }


}

/**
 * 大唐接口通用调用函数
 * @param $url
 * @param $params
 * @return array|bool|mixed
 */
function datang_interface($url,$params,$log_type_id=700000){

    if (empty($params)){
        return false;
    }
    $data['parterId'] =Config::get('dttxapi.parterId');
    $data =array_merge($data,$params);
    ksort($data);
    $salt =Config::get('dttxapi.salt');
    $signValue =md5(http_build_query($data).'&'.$salt);
    $data['signValue']=$signValue;
    $dttxApiUrl =$url.'?'.http_build_query($data);
    $begintime =getMicrotime();
    $res =curl_get($dttxApiUrl);
    $data =json_decode($res,true);

    $endtime =getMicrotime();
    $timer =$endtime-$begintime;

    $logdata['begintime']=$begintime;
    $logdata['endtime']=$endtime;
    $logdata['timer']=$timer;
    $logdata['url']=$dttxApiUrl;
    $logdata['data']=$data;
    ksort($logdata);
    $orderid=isset($params['orderID'])?$params['orderID']:"";
    $config =Config::get('logs');
    $logtile =isset($config[$log_type_id])?$config[$log_type_id]:"接口日志";
    \app\common\tools\Logs::writeMongodb($log_type_id,'',$orderid,$logtile,$logdata,'Ym');

    if (!empty($data)){
        return $data;
    }else{
        return false;
    }
}



function getDttxUserInfo($nick,$encrypt=true){
    if (empty($nick)){
        return array('status'=>1002,'data'=>false);
    }

    $cacheId =md5('dttxgetUserinfo_'.$nick);
    if (!($data = \think\Cache::get($cacheId))){
        $apiurl = Config::get('dttxapi.getUserInforUrl');
        $data['parterId'] =Config::get('dttxapi.parterId');
        $data['username']=$nick;
        ksort($data);
        $salt =Config::get('dttxapi.salt');
        $signValue =md5(http_build_query($data).'&'.$salt);
        $data['signValue']=$signValue;

        $dttxApiUrl =$apiurl.'?'.http_build_query($data);
        $begintime =getMicrotime();
        $res =curl_get($dttxApiUrl);
        $data =json_decode($res,true);
        if (!empty($data)){
            \think\Cache::set($cacheId,$data,3600);
        }
        $endtime =getMicrotime();
        $timer =$endtime-$begintime;
        $logdata['timer']=$timer;
        $logdata['begintime']=$begintime;
        $logdata['endtime']=$endtime;
        $logdata['url']=$dttxApiUrl;
        $logdata['data']=$data;
        \app\common\tools\Logs::writeMongodb(700005,'',$nick,'获取用户信息日志',$logdata,'Ym');
    }

    if ($data['id']=='1001'){
        if ($encrypt){
            $data['info']['realName']= "*".mb_substr($data['info']['realName'],1, mb_strlen($data['info']['realName'], 'utf-8'), 'utf-8');
            $data['info']['tel'] =mb_substr($data['info']['tel'],0,3).'****'.mb_substr($data['info']['tel'],7);
        }
    }

    return array('status'=>$data['id'],'data'=>$data['info']);
}

/**
 * create crc code
 * @param $uid
 * @param $time
 * @param string $type
 * @return string
 */
function createCrcCode($string,$time,$type='1'){
    $sercrt_key =Config::get('account_secret_key');
    if ($type=='1'){
        return md5($string.$time.$sercrt_key);
    }else{
        return sha1($string.$time,$sercrt_key);
    }
}

/*  * ********************************************************************************************************
    *
    *
    * 基本功能的函数
    *
    *
    * ********************************************************************************************************** */

//得到当前时间
function mytime($mode = 'Y-m-d H:i:s') {
    return date($mode, time());
}

// 日期减1天
function SubDay($ntime, $ctime) {
    $dayst = 86400;
    $oktime = $ntime - $ctime * $dayst;
    return $oktime;
}

//日期加1天
function AddDay($ntime, $aday) {
    $dayst = 86400;
    $oktime = $ntime + $aday * $dayst;
    return $oktime;
}

//得到毫\微秒级时间
function getMicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return sprintf('%s%03d', date('YmdHis',$sec),$usec * 1000);
}

//得到毫秒级时间戳(12位)
function getMtID() {
    return sprintf('%012o', self::getMicrotime());
}

//得到唯一id
function getGID($len = 32) {
    return substr(md5(self::getMtID() . rand(0, 1000)), 0, $len);
}

//得到流水25位时间戳
function getTimeMarkID() {
    list($usec, $sec) = explode(" ", microtime());
    return sprintf('%s%06d%03d', date('YmdHis',$sec),$usec * 1000000,rand(10000, 99999));
}

//得到MD5
function getMD5($str, $len = 32) {
    return substr(md5($str), 0, $len);
}

//得到高强度不可逆的加密字串
function getSuperMD5($str) {
    return MD5(SHA1($str) . '@$^^&!##$$%%$%$$^&&asdtans2g234234HJU');
}

//账号校验码
function getSupersha1($str){
    return sha1(getSuperMD5($str.config('account_secret_key')),false);
}

//得到指定分隔符分割的子串数量（例如: '1|2|3|4|5'，分隔符为'|'，子串数为竖线的出现次数+1）
function getSubStrCountByDim($str, $dim = '|') {
    return substr_count($str, $dim) + 1;
}

//屏蔽电话号码中间的四位数字
function hidtel($phone) {
    $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
    if ($IsWhat == 1) {
        return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
    } else {
        return preg_replace('/(1[34578]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
    }
}

function GetIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $cip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $cip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (!empty($_SERVER['REMOTE_ADDR'])) {
        $cip = $_SERVER['REMOTE_ADDR'];
    } else {
        $cip = '-';
    }
    return $cip;
}

//屏蔽身份证号码中的四位生日数字
function hidIDCnum($idcnum) {
    switch (strlen($idcnum)) {
        case 15:
            $cardnum = substr_replace($idcnum, "****", 8, 4);
            break;
        case 18:
            $cardnum = substr_replace($idcnum, "****", 10, 4);
            break;
        default:
            $cardnum = $idcnum;
    }
    return $cardnum;
}

//屏蔽邮箱号码中部分字符
function hidEmail($email) {
    $arr = explode('@', $email);
    $num = substr_replace($arr[0], '***', 1, 3);
    //strlen($num)
    $mail = $num . '@' . $arr[1];
    return $mail;
}

/**
 * 获取字典表信息
 * @param $typeid
 * @return false|PDOStatement|string|\think\Collection
 */
function get_dict($typeid){
    $data = Db::name('dictionary')->where('dt_typeid',$typeid)->field('dt_key,dt_value')->order('dt_sort','asc')->cache(true,3600)->select();
    return i_array_column($data,'dt_value','dt_key');
}

function isPhone($value) {
    return (preg_match('/13[0-9]\d{8}|14[0-9]\d{8}|15[0-9]\d{8}|17[0-9]\d{8}|18[0-9]\d{8}/', $value) && is_numeric($value));
}


//大唐身份认证
function get_auth($auth){
    if (intval($auth)<0){
        return false;
    }
    $string =[];
    if (substr($auth,0,1)==1){
        array_push($string,'[手机]');
    }

    if (substr($auth,1,1)==1){
        array_push($string,'[邮箱]');
    }

    if (substr($auth,2,1)==1){
        array_push($string,'[实名]');
    }

    if (substr($auth,3,1)){
        array_push($string,'[行业]');
    }

    return implode(',',$string);
}

/**
 * 系统邮件发送函数
 * @param string $tomail     接收邮件者邮箱
 * @param string $name       接收邮件者名称
 * @param string $subject    邮件主题
 * @param string $body       邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 */
function sendMail($mailTo, $body, $name = '', $subject = '', $attachment = null){
    $mail = new \PHPMailer();           //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码

    $mail->setLanguage('zh_cn');
    $mail->isSMTP();
    $mail->Host     = 'smtp.dttx.com';
    $mail->SMTPAuth = true;
    $mail->Username = "yunshang@dttx.com";    // SMTP服务器用户名
    $mail->Password = "yunshang#123";     // SMTP服务器密码
    $mail->From     = "yunshang@dttx.com";
    $mail->FromName = '大唐云商';

    if (is_array($mailTo) && !empty($mailTo)) {
        foreach ($mailTo as $item) {
            $mail->addAddress($item);
        }
    } else {
        $mail->addAddress($mailTo, $name);
    }

    if (is_array($attachment) && !empty($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }

    $mail->WordWrap = 50;
    $mail->isHTML(true);
    $mail->Subject  = $subject ?: '大唐云商邮件提醒';
    $mail->Body     = $body;
    $mail->AltBody  = "这是一封HTML邮件，请用HTML方式浏览!";

    return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * @param $type 跳转类型  success：操作成功跳转   error：操作失败跳转
 * @param $msg  提示信息
 * @param $url  跳转地址
 * @return void
 * */
function dispatchJump($type, $msg, $url = ''){
    $result = [
        'msg'   => $msg,
        'title' => $type == 'success' ? '操作成功' : '操作失败',
        'url'   => $url ?: 'javascript:window.history.back();'
    ];

    $template = $type == 'success' ? 'wap_dispatch_success_tmpl' : 'wap_dispatch_error_tmpl';
    $result   = ViewTemplate::instance(Config::get('template'), Config::get('view_replace_str'))->fetch(Config::get($template), $result);
    $response = Response::create($result, 'html')->header([]);
    throw new HttpResponseException($response);
}
