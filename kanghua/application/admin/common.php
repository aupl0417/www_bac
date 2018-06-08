<?php
use think\Db;

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
    $strlen = strlen($string);
    if($strlen <= $length) return $string;
    $string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    if(strtolower(CHARSET) == 'utf-8') {
        $length = intval($length-strlen($dot)-$length/3);
        $n = $tn = $noc = 0;
        while($n < strlen($string)) {
            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1; $n++; $noc++;
            } elseif(194 <= $t && $t <= 223) {
                $tn = 2; $n += 2; $noc += 2;
            } elseif(224 <= $t && $t <= 239) {
                $tn = 3; $n += 3; $noc += 2;
            } elseif(240 <= $t && $t <= 247) {
                $tn = 4; $n += 4; $noc += 2;
            } elseif(248 <= $t && $t <= 251) {
                $tn = 5; $n += 5; $noc += 2;
            } elseif($t == 252 || $t == 253) {
                $tn = 6; $n += 6; $noc += 2;
            } else {
                $n++;
            }
            if($noc >= $length) {
                break;
            }
        }
        if($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        $strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
        $replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i++) {
            $current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
            if (in_array($current_str, $search_arr)) {
                $key = $search_flip[$current_str];
                $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }
    return $strcut.$dot;
}
/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}
/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
    $pwd = array();
    $pwd['encrypt'] =  $encrypt ? $encrypt : create_randomstr();
    $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}
/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return  TRUE or FALSE
 */
function is_password($password) {
    $strlen = strlen($password);
    if($strlen >= 6 && $strlen <= 20) return true;
    return false;
}
/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
    return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}
function list_to_tree($list,$pk='id',$pid='pid',$child='_child',$root=0){
    // 创建Tree
    $tree=array();
    if(is_array($list)){
        // 创建基于主键的数组引用
        $refer=array();
        foreach($list as $key=>$data){
            $refer[$data[$pk]]=& $list[$key];
        }
        foreach($list as $key=>$data){
            // 判断是否存在parent
            $parentId=$data[$pid];
            if($root==$parentId){
                $tree[]=& $list[$key];
            }else{
                if(isset($refer[$parentId])){
                    $parent=& $refer[$parentId];
                    $parent[$child][]=& $list[$key];
                }
            }
        }
    }
    return $tree;
}
function JsJump($url,$time=0){
    $SleepTime=$time*1000;
    echo '<script language="javascript">window.setTimeout("window.location=\''.$url.'\'", '.$time.');</script>';
    exit();
}
function JsMessage($message,$URL='HISTORY',$charset='utf-8'){
    echo '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />
            <title>系统提示</title>
            </head>
        
            <body>
            <script type="text/javascript">
            alert("'.$message.'");
            '.(strtoupper($URL)=='HISTORY'?'history.back();':'location.href="'.$URL.'";').'
            </script>
            </body>
            </html>
        ';
    exit();
}

//后台验证的一些正则, 只能a-zA-Z0-9_
function checkUsername($string){
    if(preg_match("/^[a-zA-z0-9_]+$/", $string)){
        return true;
    }else{
        return false;
    }
}

//获取一个代理web
function getWebProxy(){
    //清除
    //cache('proxy_host', null);
    //cache('proxy_port', null);
    
    //获取代理列表
    $url = 'http://www.xicidaili.com/wn';
    $snoopy = new \Lain\Snoopy;
    $snoopy->fetch($url);
    $html_code = $snoopy->results;
    //使用QueryList解析html
    $query_content = \QL\QueryList::Query($html_code, array('proxy_html' => array('#ip_list tr.odd','html')))->data;
    foreach ($query_content as $proxy){
        $proxy_data = \QL\QueryList::Query($proxy['proxy_html'], array('proxy' => array('td:nth-child(3)','html'), 'port' => array('td:nth-child(4)', 'html')))->data;
        //判断IP和端口是否可以访问
        //$proxy_data = array(0 => array('proxy' => '123.138.89.130', 'port'=> '9999'));
        //var_dump($proxy_data);
        if(checkProxy($proxy_data[0]['proxy'], $proxy_data[0]['port'])){
            //保存
            cache('proxy_host', $proxy_data['proxy'], 3600*24*7);
            cache('proxy_port', $proxy_data['port'], 3600*24*7);
            
            //检测通过, 则跳出
//             echo 'keyong:';
//             var_dump($proxy_data);exit;
            break;
        }
    }
    return true;
}

function checkProxy ($proxy, $port)
{
    //使用百度来检测
    $url = 'http://www.baidu.com/';
    $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh- CN; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5 FirePHP/0.2.1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYPORT, $port); //代理服务器端口
    curl_setopt($ch, CURLOPT_URL, $url);//设置要访问的IP
    //curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);//模拟用户使用的浏览器
    //@curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
    curl_setopt($ch, CURLOPT_TIMEOUT, 3 ); //设置超时时间
    //curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    //var_dump($result);exit;
    if($result !== false && strpos($result, '百度一下') !== false)
        return true;
    else
        return false;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
function trim_script($str) {
    if(is_array($str)){
        foreach ($str as $key => $val){
            $str[$key] = trim_script($val);
        }
    }else{
        $str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
        $str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
        $str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
        $str = str_replace ( 'javascript:', 'javascript：', $str );
    }
    return $str;
}


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

