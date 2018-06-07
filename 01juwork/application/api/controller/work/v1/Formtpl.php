<?php
namespace app\api\controller\work\v1;
use app\api\controller\work\v1\Init;
use think\Db;
use think\Exception;
use think\exception\ErrorException;

class Formtpl extends Init
{
    /**
     * 数据表
     * 2017-06-05
     */
    public function tables($check=1){
        $tables = Db::getTables();
        return $this->ret(['code' => 1,'data' => $tables]);
    }

    /**
     * 读取数据表字段
     */
    public function fields($check=1){
        if($check == 1) {
            $res = $this->check('table',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $fields = Db::getFields($this->post['table']);
        return $this->ret(['code' => 1,'data' => $fields]);
    }

    /**
     * 创建表单
     */
    public function createForm($check=1){
        if($check == 1) {
            $this->post['field'] = html_entity_decode($this->post['field']);
            $res = $this->check('tpl_name,table,field');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['field'] = json_decode($this->post['field'],true);
        }

        $data = [
            'tpl_name'  => $this->post['tpl_name'],
            'remark'    => $this->post['remark'],
            'tables'    => $this->post['table'],
        ];

        Db::startTrans();
        try{
            db('formtpl')->insert($data);
            $insid = db('formtpl')->getLastInsID();
            if(!$insid) throw new \Exception('创建表单记录失败！');

            $group  = [
                'formtpl_id'    => $insid,
                'group_name'    => '默认分组',
                'tables'        => $data['tables'],
                'is_lock'       => 1,
            ];

            db('formtpl_group')->insert($group);
            $gid = db('formtpl_group')->getLastInsID();
            if(!$gid) throw new \Exception('创建字段分组失败！');

            $fields = $fields = Db::getFields($data['tables']);
            foreach($fields as $val){
                $tmp[$val['name']] = $val;
            }
            $fieldAll = [];
            foreach($this->post['field'] as $val){
                $fieldAll[] = [
                    'formtpl_id'    => $insid,
                    'group_id'      => $gid,
                    'tables'        => $data['tables'],
                    'name'          => $tmp[$val]['name'],
                    'label'         => $tmp[$val]['comment'],
                    'formtype'      => 'text',
                ];
            }

            if(!db('formtpl_fields')->insertAll($fieldAll)) throw new \Exception('插入字段失败！');

            Db::commit();
        }catch(\Exception $e){
            $msg = $e->getMessage();
            Db::rollback();

            return $this->ret(['code' => 0,'msg' => $msg]);
        }

        return $this->ret(['code' => 1,'data' => ['id' => $insid]]);
    }

    /**
     * 表单模板详情
     */
    public function formDetail($check=1){
        if($check == 1) {
            $res = $this->check('id',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $rs = db('formtpl')->where(['id' => $this->post['id']])->find();
        if(!$rs) return $this->ret(['code' => 3]);

        $rs['group']    = db('formtpl_group')->where(['formtpl_id' => $rs['id']])->order('sort asc,id asc')->select();
        $tmp = [];
        foreach($rs['group'] as &$val){
            $val['fields']  = db('formtpl_fields')->where(['group_id' => $val['id']])->order('sort asc,id asc')->select();
            $tmp = array_merge($tmp,$val['fields']);
        }

        //列表字段格式化
        if($rs['list_fields']){
            $rs['list_fields'] = json_decode(html_entity_decode($rs['list_fields']),true);
        }

        if(empty($rs['list_fields'])) $rs['list_fields'] = $tmp;

        return $this->ret(['code' => 1,'data' => $rs]);
    }

    /**
     * 模板所有字段
     */
    public function formtplFields($check=1){
        if($check == 1) {
            $res = $this->check('id',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $list  = db('formtpl_fields')->where(['formtpl_id' => $this->post['id']])->order('sort asc,id asc')->select();

        if($list){
            return $this->ret(['code' => 1,'data' => $list]);
        }

        return $this->ret(['code' => 3]);
    }

    /**
     * 删除字段
     */
    public function fieldsDelete($check=1){
        if($check == 1) {
            $this->post['id'] = html_entity_decode($this->post['id']);
            $res = $this->check('id');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['id'] = json_decode($this->post['id'],true);
        }


        if(db('formtpl_fields')->where(['id' => ['in',$this->post['id']],'is_lock' => 0])->delete()){
            return $this->ret(['code' => 1]);
        }

        return $this->ret(['code' => 0]);
    }

    /**
     * 取某表单模板字段
     */
    public function formtplFieldsName($check=1){
        if($check == 1) {
            $res = $this->check('id',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $list = db('formtpl_fields')->where(['formtpl_id' => $this->post['id']])->field('name')->select();
        foreach($list as $val){
            $name[] = $val['name'];
        }
        return $this->ret(['code' => 1,'data' => $name]);
    }

    /**
     * 保存从数据表结构中添加的字段
     */
    public function fromTableFieldSave($check=1){
        $this->post['field'] = html_entity_decode($this->post['field']);
        if($check == 1) {
            $res = $this->check('group_id,field');
            if($res['code'] != 1) return $this->ret($res);
        }
        $this->post['field'] = json_decode($this->post['field'],true);
        $rs = db('formtpl_group')->where(['id' => $this->post['group_id']])->field('id,formtpl_id,tables')->find();

        $fields = $fields = Db::getFields($rs['tables']);
        foreach($fields as $val){
            $tmp[$val['name']] = $val;
        }
        $fieldAll = [];
        foreach($this->post['field'] as $val){
            $fieldAll[] = [
                'formtpl_id'    => $rs['formtpl_id'],
                'group_id'      => $rs['id'],
                'tables'        => $rs['tables'],
                'name'          => $tmp[$val]['name'],
                'label'         => $tmp[$val]['comment'],
                'formtype'      => 'text',
            ];
        }

        if(db('formtpl_fields')->insertAll($fieldAll)) {
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 字段转移分组
     */
    public function fieldsChangeGroup($check=1){
        if($check == 1) {
            $this->post['id'] = html_entity_decode($this->post['id']);
            $res = $this->check('id,group_id');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['id'] = json_decode($this->post['id'],true);
        }

        if(false !== db('formtpl_fields')->where(['id' => ['in',$this->post['id']]])->update(['group_id' => $this->post['group_id']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 批量设置字段状态
     */
    public function setFieldsStatus($check=1){
        if($check == 1) {
            $this->post['id'] = html_entity_decode($this->post['id']);
            $res = $this->check('id,field,value');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['id'] = json_decode($this->post['id'],true);
        }

        if(false !== db('formtpl_fields')->where(['id' => ['in',$this->post['id']]])->update([$this->post['field'] => $this->post['value']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 字段排序
     */
    public function fieldsSort($check=1){
        if($check == 1) {
            $this->post['id'] = html_entity_decode($this->post['id']);
            $res = $this->check('id');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['id'] = json_decode($this->post['id'],true);
        }

        foreach($this->post['id'] as $key => $val){
            db('formtpl_fields')->where(['id' => $val])->update(['sort' => ($key+1)]);
        }

        return $this->ret(['code' => 1]);
    }

    /**
     * 删除分组
     */
    public function deleteGroup($check=1){
        if($check == 1) {
            $res = $this->check('id');
            if($res['code'] != 1) return $this->ret($res);
        }

        if(db('formtpl_group')->where(['id' => $this->post['id'],'is_lock' => 0])->delete()){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 添加分组
     */
    public function addGroup($check=1){
        if($check == 1) {
            $res = $this->check('group_name,status,formtpl_id,tables');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'FormtplGroup');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('FormtplGroup')->allowField(true)->save($this->post)){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 获取分组信息
     */
    public function getGroup($check=1){
        if($check == 1) {
            $res = $this->check('id',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $rs = db('formtpl_group')->where(['id' => $this->post['id']])->field('atime,etime,ip',true)->find();
        if($rs){
            return $this->ret(['code' => 1,'data' => $rs]);
        }

        return $this->ret(['code' => 3]);
    }

    /**
     * 修改分组
     */
    public function editGroup($check=1){
        if($check == 1) {
            $res = $this->check('group_name,status,id');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'FormtplGroup');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('FormtplGroup')->allowField(true)->save($this->post,['id' => $this->post['id']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 分组排序
     */
    public function groupSort($check=1){
        if($check == 1) {
            $this->post['id'] = html_entity_decode($this->post['id']);
            $res = $this->check('id');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['id'] = json_decode($this->post['id'],true);
        }

        foreach($this->post['id'] as $key => $val){
            db('formtpl_group')->where(['id' => $val])->update(['sort' => ($key+1)]);
        }

        return $this->ret(['code' => 1]);
    }

    /**
     * 字段详情
     */
    public function getField($check=1){
        if($check == 1) {
            $res = $this->check('id',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $rs = db('formtpl_fields')->where(['id' => $this->post['id']])->field('atime,etime',true)->find();
        if($rs){
            return $this->ret(['code' => 1,'data' => $rs]);
        }

        return $this->ret(['code' => 3]);
    }

    /**
     * 保存字段设置
     */
    public function fieldSave($check=1){
        if($check == 1) {
            $res = $this->check('id,group_id,label,name,formtype');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'FormtplFields');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('FormtplFields')->allowField(true)->save($this->post,['id' => $this->post['id']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }


    /**
     * 模板基本设置
     */
    public function formtplSave($check=1){
        if($check == 1) {
            $res = $this->check('id,tpl_name,tables');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'Formtpl.base');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('Formtpl')->allowField(true)->save($this->post,['id' => $this->post['id']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 保存列表设置
     */
    public function listFieldsSave($check=1){
        if($check == 1) {
            $this->post['list_fields'] = html_entity_decode($this->post['list_fields']);
            $res = $this->check('id,list_fields');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'Formtpl.listfields');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('Formtpl')->allowField(true)->save($this->post,['id' => $this->post['id']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 保存视图模型设置
     */
    public function viewSave($check=1){
        if($check == 1) {
            $res = $this->check('id,view_model');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'Formtpl.view');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('Formtpl')->allowField(true)->save($this->post,['id' => $this->post['id']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 保存关联模型
     */
    public function relationSave($check=1){
        if($check == 1) {
            $res = $this->check('id,relation_model');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'Formtpl.relation');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('Formtpl')->allowField(true)->save($this->post,['id' => $this->post['id']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 搜索字段
     */
    public function searchFields($check=1){
        if($check == 1) {
            $res = $this->check('id',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $list = db('formtpl_search_fields')->where(['formtpl_id' => $this->post['id']])->order('sort asc,id asc')->select();
        if($list){
            $fields = [];
            foreach($list as $val){
                $fields[] = $val['name'];
            }
            return $this->ret(['code' => 1,'data' => $list,'fields' => $fields]);
        }

        return $this->ret(['code' => 3]);
    }

    /**
     * 新增搜索字段
     */
    public function addSearchFields($check=1){
        if($check == 1) {
            $this->post['id'] = html_entity_decode($this->post['id']);
            $res = $this->check('id,formtpl_id,tables');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['id'] = json_decode($this->post['id'],true);
        }

        $list = db('formtpl_fields')->where(['id' => ['in',$this->post['id']]])->field('id,atime,etime',true)->select();
        if(db('formtpl_search_fields')->insertAll($list)){
            return $this->ret(['code' => 1]);
        }

        return $this->ret(['code' => 0]);
    }

    /**
     * 删除搜索字段
     */
    public function deleteSearchField($check=1){
        if($check == 1) {
            $this->post['id'] = html_entity_decode($this->post['id']);
            $res = $this->check('id');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['id'] = json_decode($this->post['id'],true);
        }

        if(db('formtpl_search_fields')->where(['id' => ['in',$this->post['id']],'is_lock' => 0])->delete()){
            return $this->ret(['code' => 1]);
        }

        return $this->ret(['code' => 0]);
    }


    /**
     * 搜索字段排序
     */
    public function searchFieldsSort($check=1){
        if($check == 1) {
            $this->post['id'] = html_entity_decode($this->post['id']);
            $res = $this->check('id');
            if($res['code'] != 1) return $this->ret($res);
            $this->post['id'] = json_decode($this->post['id'],true);
        }

        foreach($this->post['id'] as $key => $val){
            db('formtpl_search_fields')->where(['id' => $val])->update(['sort' => ($key+1)]);
        }

        return $this->ret(['code' => 1]);
    }

    /**
     * 搜索字段详情
     */
    public function searchFieldDetail($check=1){
        if($check == 1) {
            $res = $this->check('id',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $rs = db('formtpl_search_fields')->where(['id' => $this->post['id']])->find();
        if($rs){
            return $this->ret(['code' => 1,'data' => $rs]);
        }

        return $this->ret(['code' => 3]);
    }

    /**
     * 保存字段设置
     */
    public function searchFieldSave($check=1){
        if($check == 1) {
            $res = $this->check('id,label,name,formtype');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'FormtplSearchFields');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('FormtplSearchFields')->allowField(true)->save($this->post,['id' => $this->post['id']])){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 保存字段设置
     */
    public function addSearchField($check=1){
        if($check == 1) {
            $res = $this->check('label,name,formtype,formtpl_id,tables');
            if($res['code'] != 1) return $this->ret($res);
        }

        $res = $this->validate($this->post,'FormtplSearchFields');
        if(true !== $res){
            return $this->ret(['code' => 0,'msg' => $res]);
        }

        if(model('FormtplSearchFields')->allowField(true)->save($this->post)){
            return $this->ret(['code' => 1]);
        }
        return $this->ret(['code' => 0]);
    }

    /**
     * 生成模型文件
     * @param int $type 1=验证,2=数据,3=关联,4=视图
     */
    public function createModelFile($check=1){
        if($check == 1) {
            $res = $this->check('type,formtpl_id');
            if($res['code'] != 1) return $this->ret($res);
        }

        $formtpl = db('formtpl')->where(['id' => $this->post['formtpl_id']])->field('id,tables')->find();

        switch($this->post['type']){
            case 1:
                $model_file = format_model_name($formtpl['tables'],$formtpl['id']);
                $res = $this->_createModelFile1($model_file,$formtpl);
                return $this->ret($res);
                break;
            case 3:
                return $this->ret(['code' => 0,'msg' => '暂未支持创建该类型模型文件！']);
                break;
            case 4:
                return $this->ret(['code' => 0,'msg' => '暂未支持创建该类型模型文件！']);
                break;
            default:
                $model_file = format_model_name($formtpl['tables'],$formtpl['id']);
                $res = $this->_createModelFile2($model_file,$formtpl);
                return $this->ret($res);
                break;
        }

        return $this->ret(['code' => 0,'msg' => '创建模型文件失败！']);
    }

    public function _createModelFile1($model,$formtpl){ //验证模型文件
        $file = file_get_contents(APP_PATH.'work/templates/validate/Model.php');

        //验证字段
        $list   = db('formtpl_fields')->where(['status' => 1,'is_verify' => 1,'formtpl_id' => $formtpl['id']])->field('label,name,scene,rule,msg')->order('sort asc,id asc')->select();
        $rule   = [];
        $msg    = [];
        $scene  = [];
        foreach($list as $val){
            $val['rule']    = $val['rule'] ? $val['rule'] : 'require';
            $val['msg']     = $val['msg'] ? $val['msg'] : $val['label'].'必填';
            $rule[$val['name']] = str_replace(PHP_EOL,'|',trim($val['rule']));

            $rule_tmp       = explode(PHP_EOL,trim($val['rule']));
            $msg_tmp        = explode(PHP_EOL,trim($val['msg']));
            foreach($rule_tmp as $k => $v){
                $v = explode(':',$v);
                $msg[$val['name'].'.'.$v[0]] = $msg_tmp[$k];
            }

            switch($val['scene']){
                case 2:
                    $scene['add'][] = $val['name'];
                    break;
                case 3:
                    $scene['edit'][] = $val['name'];
                    break;
                default:
                    $scene['add'][] = $val['name'];
                    $scene['edit'][] = $val['name'];
            }
        }

        $rule   = 'protected $rule = '.var_export($rule,true).';';
        $msg    = 'protected $message = '.var_export($msg,true).';';
        $scene  = 'protected $scene = '.var_export($scene,true).';';

        $file = str_replace(['{day}','{model}','{rule}','{msg}','{scene}'],[date('Y-m-d H:i:s'),$model,$rule,$msg,$scene],$file);
        file_put_contents(APP_PATH.'work/validate/'.$model.'.php',$file);
        return ['code' => 1,'msg' => '创建成功！'];
    }

    public function _createModelFile2($model,$formtpl){ //数据模型文件
        $file = file_get_contents(APP_PATH.'work/templates/model/Model.php');

        $file = str_replace(['{day}','{model}','{table}'],[date('Y-m-d H:i:s'),$model,$formtpl['tables']],$file);
        file_put_contents(APP_PATH.'work/model/'.$model.'.php',$file);
        return ['code' => 1,'msg' => '创建成功！'];
    }

    /**
     * 创建控制器文件
     */
    public function createController($check=1){
        if($check == 1) {
            $res = $this->check('controller,controller_name,type,formtpl_id');
            if($res['code'] != 1) return $this->ret($res);
        }

        $this->post['controller'] = ucfirst(strtolower($this->post['controller']));

        $data = [
            'controller'        => $this->post['controller'],
            'controller_name'   => $this->post['controller_name'],
            'type'              => $this->post['type'],
            'formtpl_id'        => $this->post['formtpl_id'],
            'action'            => '{"index":["R"],"deleteCategorySelect":["D"],"changeCategory":["U"],"setStatus":["U"],"setSort":["U"],"edit":["R"],"edit_save":["U"],"add":["R"],"add_save":["C"],"id":"199"}',
        ];

        if(in_array($data['type'],['CategoryMore'])) return $this->ret(['code' => 0,'msg' => '暂不支持创建该类型控制器文件！']);

        $res = $this->validate($data,'Controller');
        if(false === $res) $this->ret(['code' => 0,'msg' => $res]);

        if(!db('controller')->where($data)->find()){
            if(!model('Controller')->allowField(true)->save($data)) return $this->ret(['code' => 0,'msg' => '创建控制器记录失败！']);
        }

        switch($this->post['type']){
            case 'CategoryMore':

                break;
            default:
                $cdir = APP_PATH.'work/controller';
                $vdir = APP_PATH.'work/view/'.strtolower($this->post['controller']);
                if(!is_dir($vdir)){
                    @mkdir($vdir);
                    @chmod(0777,$vdir);
                }

                //控制器文件
                $cfile = file_get_contents(APP_PATH.'work/templates/controller/'.$this->post['type'].'/controller.php');
                $cfile = str_replace(['{controller}','{controller_name}','{formtpl_id}'],[$data['controller'],$data['controller_name'],$data['formtpl_id']],$cfile);
                file_put_contents($cdir.'/'.$data['controller'].'.php',$cfile);

                //模板文件
                $file = file_get_contents(APP_PATH.'work/templates/controller/'.$this->post['type'].'/view/index.html');
                $file = str_replace(['{controller}','{controller_name}','{formtpl_id}'],[strtolower($data['controller']),$data['controller_name'],$data['formtpl_id']],$file);
                file_put_contents($vdir.'/index.html',$file);

                $file = file_get_contents(APP_PATH.'work/templates/controller/'.$this->post['type'].'/view/add.html');
                $file = str_replace(['{controller}','{controller_name}','{formtpl_id}'],[strtolower($data['controller']),$data['controller_name'],$data['formtpl_id']],$file);
                file_put_contents($vdir.'/add.html',$file);

                $file = file_get_contents(APP_PATH.'work/templates/controller/'.$this->post['type'].'/view/edit.html');
                $file = str_replace(['{controller}','{controller_name}','{formtpl_id}'],[strtolower($data['controller']),$data['controller_name'],$data['formtpl_id']],$file);
                file_put_contents($vdir.'/edit.html',$file);

                $file = file_get_contents(APP_PATH.'work/templates/controller/'.$this->post['type'].'/view/nav.html');
                $file = str_replace(['{controller}','{controller_name}','{formtpl_id}'],[strtolower($data['controller']),$data['controller_name'],$data['formtpl_id']],$file);
                file_put_contents($vdir.'/nav.html',$file);

                $file = file_get_contents(APP_PATH.'work/templates/controller/'.$this->post['type'].'/view/search.html');
                $file = str_replace(['{controller}','{controller_name}','{formtpl_id}'],[strtolower($data['controller']),$data['controller_name'],$data['formtpl_id']],$file);
                file_put_contents($vdir.'/search.html',$file);

        }


        //生成模型文件
        $this->post = ['type' => 1,'formtpl_id' => $this->post['formtpl_id']];
        $this->createModelFile(0);  //生成验证模型

        $this->post['type'] = 2;
        $this->createModelFile(0);  //生成数据模型

        $this->post['type'] = 3;
        $this->createModelFile(0);  //生成关联模型

        //$this->post['type'] = 4;
        //$this->createModelFile(0);  //生成视图模型

        return $this->ret(['code' => 1,'msg' => '创建控制器文件成功！']);
    }
}
