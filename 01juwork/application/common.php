<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Config;
use enhong\BuildForm;
use think\Db;
/**
 * 接口请求
 * Create by lazycat
 * 2017-06-01
 * @param string $api      接口地址
 * @param string $data     提交数据
 * @param string $nosign    不签名字段
 */
function api($api,$data,$nosign=''){
    $apiurl     = Config::get('apiurl');
    $api_cfg    = Config::get('api_cfg');
    $token      = isset($data['token']) && $data['token'] ? $data['token'] : Config::get('token.token');
    $apiurl     = preg_match("/^(http:\/\/|https:\/\/).*$/",$api) ? $api : $apiurl . $api;
    if(strstr(strtolower($apiurl),'auth/token') == false) $data['token'] = $token;
    $data['sign']       =   sign($data,$nosign);
    $data['random']     =   isset($data['random']) && $data['random'] ? $data['random'] : session_id();
    //dump($apiurl);
    //dump($data);
    $res=curl_post($apiurl,$data);
    if(Config::get('api_debug')) print_r($res);
    $res=json_decode($res,true);
    if(Config::get('api_debug')) dump($res);

    return $res;
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


/**
 * 表单生成器
 * @param array $param 字段选项
 * @param array $data 值
 */
function buildform($param,$data=''){
    $html = array();
    $form = new BuildForm();
    $form->value = $data;
    foreach($param as $key=>$val){
        if(substr($key,0,5)=='field'){
            foreach($val as $vkey=>$val){
                $html[]=$form->$val['formtype']($val)->create();
            }
        }
    }
    $html = @implode('',$html);
    echo $html;
}

/**
 * 创建单个表单项
 */
function form_item($param,$data=''){
    $form = new BuildForm();
    $form->value = $data;

    $html = $form->$param['formtype']($param)->item();
    return $html;
}

/**
 * 状态格式成按钮
 */
function status($code,$btn=''){
    if($btn == '') $btn = [['停用',''],['启用','text-success']];

    $html = '<div class="'.$btn[$code][1].'">'. $btn[$code][0].'</div>';
    return $html;
}

/**
 * 格式模型名称
 */
function format_model_name($tables,$id=''){
    $model = substr($tables,strlen(config('database.prefix')));
    $model = explode('_',$model);
    foreach($model as &$val){
        $val = ucfirst($val);
    }
    $model = implode('',$model).$id;
    return $model;
}

/**
 * 视图排序时字段自动加上表名
 * @param $order
 * @param $view_model
 * @return string
 */
function order_conver($order,$view_model){
    if(strstr($order,'.') || substr($order,0,strlen(config('database.prefix'))) == config('database.prefix')) return $order;
    $orders  = explode(',',$order);
    foreach($orders as $key => &$val){
        $val = explode(' ',$val);
    }
    if(isset($val)) unset($val);
    //dump($orders);
    $fields = [];
    foreach($view_model as $val){
        $tmp[$val[0]] = Db::getFields($val[0]);
        $fields = array_merge($fields,$tmp);
    }

    $tmp = [];
    foreach($orders as $val){
        foreach($fields as $key => $vl){
            foreach($vl as $v){
                if($v['name'] == $val[0] && !isset($tmp[$v['name']])){
                    $tmp[$v['name']] = $key.'.'.$v['name'].' '.$val[1];
                }
            }
        }
    }

    //dump($tmp);
    return implode(',',$tmp);
    //dump($fields);
}

/**
 * 分页
 */
function pagelist($param){
    if(!isset($param['table'])) return ['code' => 0,'msg' => '缺少要操作的数据表或模型！'];
    $table      = $param['table'];
    $pagesize   = isset($param['pagesize']) ? $param['pagesize'] : 20;
    $order      = isset($param['order']) ? $param['order'] : 'id desc';
    $p          = isset($param['p']) ? $param['p'] : 1;
    $where      = isset($param['where']) ? $param['where'] : [];
    $action_type= isset($param['action_type']) ? $param['action_type'] : 'default';
    $cache      = isset($param['cache']) ? $param['cache'] : false;
    $field      = isset($param['field']) ? $param['field'] : '*';
    $view_model = isset($param['view_model']) ? $param['view_model'] : '';
    //dump($param);
    switch($action_type){
        case 'view':    //视图查询
            if(!isset($view_model) || empty($view_model)) return ['code' => 0,'msg' => '未设置视图参数！','data' => ['list' => '','pageinfo' => '']];
            $order = order_conver($order,$view_model);
            $do = db();
            foreach($view_model as $val){
                $on     = isset($val[2])&& $val[2] ? $val['2'] : '';
                $type   = isset($val[3])&& $val[3] ? $val['3'] : 'INNER';
                $do->view($val[0],$val[1],$on,$type);
            }
            $count = $do->where($where)->count();
            $page   = ceil($count/$pagesize);
            $p      = $p > $page ? $page : $p;

            foreach($view_model as $val){
                $on     = isset($val[2])&& $val[2] ? $val['2'] : '';
                $type   = isset($val[3])&& $val[3] ? $val['3'] : 'INNER';
                $do->view($val[0],$val[1],$on,$type);
            }
            $list   = $do->where($where)->page($p)->limit($pagesize)->order($order)->select();
            //dump($list);
            //dump($do->getLastSQL());
            break;
        case 'relation':
            break;
        default:
            $count  = db($table)->where($where)->count();
            $page   = ceil($count/$pagesize);
            $p      = $p > $page ? $page : $p;
            $list   = db($table)->cache($cache)->where($where)->field($field)->page($p)->limit($pagesize)->order($order)->select();
    }

    if($list){
        $pageinfo = [
            'count'     => $count,
            'pagesize'  => $pagesize,
            'p'         => $p,
            'page'     => $page,
            //'sql'       => db($table)->getLastSql(),
        ];
        if(isset($param['item_function']) && $param['item_function']){
            foreach($list as &$val){
                $val = eval($param['item_function']($val));
            }
        }
        return ['code' => 1,'data' => ['list' => $list,'pageinfo' => $pageinfo]];
    }
    return ['code' => 3,'data' => ['list' => '','pageinfo' => '']];
}

/**
 * 将数据记录生成列表管理
 * @param array     $data   数据
 * @param array     $th     列标题
 * @param string    $btn   按钮
 * @param int       $colspan    是否扩展合并行
 */
function html_table($data,$th,$btn='',$colspan=0){
    if(empty($data)) {
        $res['html']    = '<div class="text-center nors">暂无记录！</div>';
        return $res;
    }

    if(empty($th)){
        $res['html']    = '<div class="text-center nors">缺少输出字段！</div>';
        return $res;
    }

    $id     = 'id'; //行标记
    $col    = count($th) + 1;   //列数
    $btns   = '<a href="'.url(request()->controller().'/edit','',false).'/'.$id.'/['.$id.']" class="btn blue btn-outline btn-block">修改</a>';   //操作按钮
    if($btn === false)  {
        $btns = '';
    }else{
        $col++;
        $btns = $btn ? $btn : $btns;
    }
    $base_url = request()->controller().'/'.request()->action();    //当前方法

    //dump($th);

    $html   = '<table class="table table-bordered table-hover valign-middle">';
    $thead  = '<thead>';
    $thead  .= '<th class="text-center" width="60">选择</th>';
    $field  = [];
    foreach($th as $key => $val){
        $field[] = $val['name'];
        $attr_th = [];
        $attr_th[]  = $val['attr'] ? html_entity_decode($val['attr']) : '';
        //排序按钮
        $order_url = url($base_url,array_merge(request()->param(),['order' => (request()->param('order') == $val['name'].'-asc' ? $val['name'].'-desc' : $val['name'].'-asc')]));
        $sort    = ' <a href="'.$order_url.'"><i class="fa fa-angle-'.(request()->param('order') == $val['name'].'-asc' ? 'up' : 'down').'"></i></a>';
        $thead  .= '<th '.implode(' ',$attr_th).'>'.$val['label'].$sort.'</th>';
    }
    if($btn !== false) {
        $thead  .= '<th class="text-center" width="100">操作</th>';
    }
    $thead  .= '</thead>';
    if(!in_array($id,$field)) $id = $field[0];

    $tbody  = '<tbody>';
    foreach($data as $key => $val){
        $attr_tr = [];
        $attr_tr[] = 'id="'.$val[$id].'"';
        $tbody  .= '<tr '.implode(' ',$attr_tr).'>';
        $tbody  .= '<td class="text-center" width="60">
                        <label class="mt-checkbox mt-checkbox-outline">
						    <input type="checkbox" id="'.$id.'[]" name="'.$id.'[]" value="'.$val[$id].'">
						    <span></span>
					    </label>
					</td>';

        foreach($th as $k => $v){
            $attr_td = [];
            $attr_td[]  = 'data-field="'.$v['name'].'"';
            $attr_td[]  = $v['attr'] ? html_entity_decode($v['attr']) : '';
            //dump($attr_td);
            $tbody  .= '<td '.implode(' ',$attr_td).'>';
            $tbody  .= $v['function'] ? eval(html_entity_decode($v['function'])) : $val[$v['name']];
            $tbody  .= '</td>';
        }
        if($btn !== false) {
            $tbody  .= '<td class="text-center" width="100">'.url_conver($btns,$val).'</td>';
        }
        $tbody  .= '</tr>';

        if(isset($val['sublist']) && $val['sublist']['count'] > 0){ //子级
            //dump($val['sublist']);
            $tbody  .= '<tr data-id="ext-sub-'.$val[$id].'-'.$val['sublist']['depth'].'" class="table-sublist"><td colspan="'.$col.'" style="padding:0;padding-left:60px">'.html_table($val['sublist']['data'],$th,$btns,$colspan)['html'].'</td></tr>';
        }

        if($colspan == 1){
            $tbody  .= '<tr class="hide" data-id="ext-row-'.$val[$id].'"><td colspan="'.$col.'"></td></tr>';
        }
    };

    $tbody  .= '</tbody>';

    $html .= $thead . $tbody .'</table>';
    $res['html']    = $html;

    return $res;
}

/**
 * 格式化Url
 * @param $url
 * @param $arr
 * @return mixed
 */
function url_conver($url,$arr){
    foreach($arr as $key => $val){
        if(!is_array($val)) $url = str_replace('['.$key.']',$val,$url);
    }
    return $url;
}
/**
 * 生成分页html
 * @param int $param['page'] 总页数
 * @param int $param['p']   当前页码
 * @param int $param['count'] 总记录数
 * @param int $param['pagesize'] 每页数量
 */
function page_html($param){
    if(empty($param)) return '';
    $allpage = $param['page'];
    if(isset($param['max']) && $param['max'] < $param['page']) $param['page'] =  $param['max'];
    $base_url   = request()->controller().'/'.request()->action();    //当前方法
    $vars       = request()->param();
    if(isset($vars['p'])) unset($vars['p']);

    if($param['page'] > 1) {
        $first = '<a class="btn-p page-s ' . ($param['p'] < 2 ? 'disabled' : '') . '" ' . ($param['p'] > 1 ? ' href="' . url($base_url, array_merge($vars, array('p' => $param['p'] - 1))) . '"' : '') . '>上一页</a>';
        $first .= '<a class="btn-p page-no ' . ($param['p'] == 1 ? 'active' : '') . '" ' . ($param['p'] != 1 ? ' href="' . url($base_url, array_merge($vars, array('p' => 1))) . '"' : '') . '>1</a>';
        $last = '<a class="btn-p page-no ' . ($param['p'] == $param['page'] ? 'active' : '') . '" ' . ($param['p'] != $param['page'] ? ' href="' . url($base_url, array_merge($vars, array('p' => $param['page']))) . '"' : '') . '>' . $param['page'] . '</a>';
        $last .= '<a class="btn-p page-s ' . ($param['p'] >= $param['page'] ? 'disabled' : '') . '" ' . ($param['p'] < $param['page'] ? ' href="' . url($base_url, array_merge($vars, array('p' => $param['p'] + 1))) . '"' : '') . '>下一页</a>';



        $page_num = [];
        if ($param['page'] < 9) {
            for ($i = 2; $i < $param['page']; $i++) {
                $page_num[] = $i;
            }
        } elseif ($param['p'] >= 6 && $param['p'] + 2 < $param['page']) {
            $page_num = [
                '',
                $param['p'] - 2,
                $param['p'] - 1,
                $param['p'],
                $param['p'] + 1,
                $param['p'] + 2,
                ''
            ];
        } elseif ($param['p'] <= 5 && $param['page'] >= 8) {
            for ($i = 2; $i <= 7; $i++) {
                $page_num[] = $i;
            }
            $page_num[] = '';
        } elseif ($param['page'] - $param['p'] <= 4) {
            $page_num[] = '';
            for ($i = $param['page'] - 7; $i < $param['page']; $i++) {
                $page_num[] = $i;
            }
        }

        $middle = '';
        foreach ($page_num as $val) {
            if ($val == '') $middle .= '<a class="page-nobox">…</a>';
            else $middle .= '<a class="btn-p page-no ' . ($param['p'] == $val ? 'active' : '') . '" ' . ($param['p'] != $val ? ' href="' . url($base_url, array_merge($vars, array('p' => $val))) . '"' : '') . '>' . $val . '</a>';
        }

        $total = '<div class="page-total">'.$param['count'].'条记录/共'.$allpage.'页</div>';

        $goto = '';
        if($allpage > 1){
            $goto = '<div class="input-group" style="width:300px;float:right">
                        <input type="text" id="custom_pagesize" value="'.$param['pagesize'].'" class="form-control" style="text-align:center">
                        <span class="input-group-addon">条/页，跳至</span>
                        <input type="text" id="custom_goto_page" value="'.$param['p'].'" class="form-control" style="text-align:center">
                        <span class="input-group-addon">页</span>
                        <span class="input-group-btn">
                            <button class="btn blue" type="button" onclick="gopage(\''.url($base_url,$vars,false).'\',$(this))">Go</button>
                        </span>
                     </div>';
        }

        echo $first . $middle . $last . $total . $goto;
    }elseif($param['page'] == 1){
        $total = '<div class="page-total">'.$param['count'].'条记录/共'.$allpage.'页</div>';
        echo $total;
    }
}

/**
 * 图片缩略图
 * @param String $url 图片地址
 * @param integer $param['w']  	宽度
 * @param integer $param['h']	高度
 * @param integer $param['t']	剪裁类型 1(等比)|2(按尺寸)|3
 * @param integer $h 			等同于$param['h']
 * @param integer $t 			等同于$param['t']
 * @param string  $nopic 		当图片不存在时默认显示的图片
 * @param integer $type 			七牛的另一种缩略图方式
 */
function thumb($url,$param=null,$h='',$t=2,$nopic='',$type=''){
    $nopic = $nopic ? $nopic : 'http://7xvbop.com1.z0.glb.clouddn.com/1469193729000511.png';
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $scheme='https://';
    else $scheme='http://';

    if(is_array($url)){
        $tmp_url=$url[0]?$url[0]:$url[1];
        $url=$tmp_url;
    }

    if(isset($param) && !is_array($param)) {
        $cfg['w']=$param;
        $cfg['h']=$h;
        $cfg['t']=$t;
        $param=$cfg;
    }

    if(empty($url)) $url = isset($param['nopic']) && $param['nopic'] ? $param['nopic'] : $nopic;

    $tmp=parse_url($url);

    $param['t']=$param['t']?$param['t']:2;
    $param['h']=$param['h']?$param['h']:$param['w'];


    if(isset($tmp['scheme']) && ($tmp['scheme']=='http' || $tmp['scheme']=='https')){
        if($param['t'] && $param['w'] && $param['h']){
            if(strpos($tmp['host'],'.qiniucdn.com') || strpos($tmp['host'],'.clouddn.com') || strstr($tmp['host'],'pic.tangmall.net') || strstr($tmp['host'],'img.tangmall.net') || strstr($tmp['host'],'img.trj.cc')){
                //$url=$url.'?imageView2/'.$param['t'].'/w/'.$param['w'].'/h/'.$param['h'];
                if($type==1) $url=$url.'?imageView2/'.$param['t'].'/w/'.$param['w'].'/h/'.$param['h'];
                else $url=$url.'?imageMogr2/thumbnail/!'.$param['w'].'x'.$param['h'].'r/gravity/Center/crop/'.$param['w'].'x'.$param['h'];

                //?imageMogr2/thumbnail/!300x300r/gravity/Center/crop/300x300
            }elseif(strpos($tmp['host'],'dttx.com')){
                //return $url;
            }else{
                $url=$scheme.'work.'.config('url_domain_root').'/Thumb/index?src='.$url.'&w='.$param['w'].'&h='.$param['h'].'&zc='.$param['t'];
            }
        }

    }else{
        if($param['t'] && $param['w'] && $param['h']){
            $url = $scheme.'work.'.config('url_domain_root').'/Thumb/index?src='.$url.'&w='.$param['w'].'&h='.$param['h'].'&zc='.$param['t'];
        }
    }

    return $url;
}

/**
 * 图片输出
 * @param string $url 图片url
 * @param integer $width 图片宽度
 * @param integer $height 图片高度
 */
function imgwh($url,$width=80,$height='',$type=''){
    return '<a class="image-zoom" href="'.$url.'" title="大图"><img src="'.thumb($url,$width,$height,2,'',$type).'" alt="图片"></a>';
}

/**
 * 终端格式为图片输出
 * @param $val
 * @param int $w
 * @return string
 */
function terminal_conver($val,$w=100){
    $url = '/images/work/icon-t-'.$val.'.png';
    return imgwh($url,$w);
}

/**
 * 格式化为连接输出
 * @param $title
 * @param $url
 * @param string $target
 * @return string
 */
function href($title,$url,$target="_self"){
    return '<a href="'.$url.'" target="'.$target.'">'.$title.'</a>';
}

/**
 * 取无限级分类
 */
function get_category($param){
    $table = isset($param['table']) ? $param['table'] : '';                         //读取数据表
    if(empty($table)) return ['code' => 0,'msg' => '未设置要读取的数据表！'];

    $field  = isset($param['field']) && $param['field'] ? $param['field'] : '*';    //获取字段
    $where  = isset($param['where']) ? $param['where'] : [];                        //条件
    $order  = isset($param['order']) ? $param['order'] : 'sort asc,id asc';         //排序
    $limit  = isset($param['limit']) ? $param['limit'] : '';                        //获取数量
    $cache  = isset($param['cache']) ? $param['cache'] : false;                     //是否启用缓存
    $depth  = isset($param['depth']) ? $param['depth'] : 0;                         //层级
    $max_depth  = isset($param['max_depth']) ? $param['max_depth'] : 0;             //最多获取层级
    $depth++;   //当前层级

    if($max_depth > $depth) goto end;

    $upid   = isset($param['upid']) && $param['upid'] > 0 ? $param['upid'] : 0;     //父级ID
    $where['upid']  = $upid;

    $list   = db($table)->cache($cache)->where($where)->field($field)->order($order)->limit($limit)->select();
    if($list) {
        foreach ($list as $key => $val) {
            $options = [
                'table'     => $table,
                'where'     => $where,
                'field'     => $field,
                'limit'     => $limit,
                'depth'     => $depth,
                'max_depth' => $max_depth,
                'upid'      => $val['id'],
            ];

            $list[$key]['sublist'] = get_category($options);
        }
        return ['code' => 1, 'data' => $list,'count' => count($list),'depth' => $depth];
    }

    end:
    return ['code' => 3,'data' => [],'count' => 0,'depth' => $depth];
}

/**
 * 获取所有子级ID
 * @param $table
 * @param $id
 * @param array $where
 * @return array
 */
function sortid($table,$id,$where=[]){
    $ids[] = $id;
    $where['upid'] = $id;
    $list = db($table)->where($where)->field('id')->order('id asc')->select();
    if($list){
        foreach($list as $val){
            $ids[] = $val['id'];
            $tmp = sortid($table,$val['id'],$where);
            $ids = array_merge($ids,$tmp);
        }
    }

    $ids = array_unique($ids);
    return $ids;
}

/**
 * 生成select 的option选项，支持无限级，配合get_category使用
 * @param array     $data   选项数据
 * @param array     $field  option的value和text的字段键名
 * @param string    $value  默认值
 * @return string
 */
function create_option($data,$field,$value=''){
    $html = '';
    if($data['count'] > 0){
        foreach($data['data'] as $val){
            $selected = (string)$val[$field[0]] === $value ? ' selected' : '';
            $str = '';
            if($data['depth'] > 1){
                for($i=1;$i<$data['depth'];$i++){
                    $str .= '　';
                }
                $str .= '|— ';
            }
            $html .= '<option value="'.$val[$field[0]].'"'.$selected.'>'.$str.$val[$field[1]].'</option>';
            if(isset($val['sublist']) && $val['sublist']['count'] > 0) $html .= create_option($val['sublist'],$field,$value);
        }
    }
    return $html;
}

/**
 * 高并发下创建不重复流水号
 * @param string $prefix
 * @param string $uid
 * @return string
 */
function create_no($prefix='',$uid=''){
    if(empty($uid)) $uid = date('His');
    $str    = $prefix.session_id().microtime(true).uniqid(md5(microtime(true)),true);
    $str    = md5($str);
    $prefix = $prefix.date('YmdH').$uid;
    $code   = $prefix.substr(uniqid($str,true),-8,8);
    return $code;
}

function tree($res,$data=[],$field='name'){
    if(!is_array($data)) $data = explode(',',$data);
    $tree = [];
    if($res['count'] > 0){
        foreach($res['data'] as $val){
            $tree[] = [
                'text'      => '<span class="hide" data-id="'.$val['id'].'"></span>'.$val[$field],
                'state'     => ['opened' => true,'selected' => in_array($val['id'],$data) && $val['sublist']['count'] == 0 ? true : false ],
                'children'  => $val['sublist']['count'] > 0 ? tree($val['sublist'],$data) : [],
            ];
        }
    }
    return $tree;
}

/**
 * 返回第一张图片
 * @param $str
 * @param int $w
 * @return string
 */
function first_images($str,$w=100){
    $url = '';
    if($str){
        $str = explode(',',$str);
        $url = $str[0];
    }
    return imgwh($url,$w);
}