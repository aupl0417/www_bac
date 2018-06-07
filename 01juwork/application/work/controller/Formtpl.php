<?php
namespace app\work\controller;
use app\work\controller\Common;
class Formtpl extends Common
{
    public function index()
    {
        //config('api_debug',true);
        $res = api('Formtpl/tables',[]);
        $this->assign('tables',$res['data']);

        return view();
    }

    /**
     * 数据表
     */
    public function tables(){
        $res = api('Formtpl/tables',[]);
        $this->assign('tables',$res['data']);
        $this->request->url();
        return view();
    }

    /**
     * 创建表单
     */
    public function createForm(){
        //config('api_debug',true);
        $data = $this->request->param();
        $res = api('Formtpl/fields',$data);
        //dump($res);
        $this->assign('fields',$res['data']);
        return view();
    }

    /**
     * 保存表单
     */
    public function createFormSave(){
        //config('api_debug',true);
        $data = input('post.');
        $data['field'] = json_encode($data['field']);
        //dump($data);
        $res = api('Formtpl/createForm',$data);
        return $res;
    }

    /**
     * 修改表单
     */
    public function formEdit(){
        //config('api_debug',true);
        $res = api('Formtpl/formDetail',['id' => $this->request->param('id')]);
        $this->assign('rs',$res['data']);

        //搜索字段
        $res = api('Formtpl/searchFields',['id' => $this->request->param('id')]);
        $this->assign('search_fields',$res['data']);
        return view();
    }

    /**
     * 删除字段
     */
    public function fieldsDelete(){
        //config('api_debug',true);
        $data = input('post.');
        $data['id'] = json_encode($data['id']);
        //dump($data);
        $res = api('Formtpl/fieldsDelete',$data);
        return $res;
    }

    /**
     * 从数据表结构中添加字段
     */
    public function fromTableField(){
        $data = $this->request->param();
        $res = api('Formtpl/fields',$data);
        //dump($res);
        $this->assign('fields',$res['data']);

        //config('api_debug',true);
        $res = api('Formtpl/formtplFieldsName',$data);
        $this->assign('use',$res['data']);
        //dump($res);

        return view();
    }

    /**
     * 保存从数据表结构中添加的字段
     */
    public function fromTableFieldSave(){
        //config('api_debug',true);
        $data = input('post.');
        $data['field'] = json_encode($data['field']);
        //dump($data);
        $res = api('Formtpl/fromTableFieldSave',$data);
        return $res;
    }

    /**
     * 字段排序
     */
    public function fieldsSort(){
        $data = input('post.');
        $data['id'] = json_encode($data['id']);
        //dump($data);
        $res = api('Formtpl/fieldsSort',$data);
        return $res;
    }

    /**
     * 字段转移分组
     */
    public function fieldsChangeGroup(){
        $data = input('post.');
        $data['id'] = json_encode($data['id']);
        //dump($data);
        $res = api('Formtpl/fieldsChangeGroup',$data);
        return $res;
    }


    /**
     * 设置字段状态
     */
    public function setFieldsStatus(){
        $data = input('post.');
        $data['id'] = json_encode($data['id']);
        //dump($data);
        $res = api('Formtpl/setFieldsStatus',$data);
        return $res;
    }

    /**
     * 添加分组
     */
    public function addGroup(){
        return view();
    }

    public function addGroupSave(){
        //config('api_debug',true);
        $data = input('post.');
        //dump($data);
        $res = api('Formtpl/addGroup',$data);
        return $res;
    }

    /**
     * 删除分组
     */
    public function deleteGroup(){
        $data = input('post.');
        //dump($data);
        $res = api('Formtpl/deleteGroup',$data);
        return $res;
    }

    /**
     * 获取分组信息
     */
    public function getGroup(){
        //config('api_debug',true);
        $data['id'] = $this->request->param('id');
        $res = api('Formtpl/getGroup',$data);
        $this->assign('rs',$res['data']);

        return view();
    }

    /**
     * 修改分组
     */
    public function editGroupSave(){
        //config('api_debug',true);
        $data = input('post.');
        //dump($data);
        $res = api('Formtpl/editGroup',$data);
        return $res;
    }

    /**
     * 分组排序
     */
    public function groupSort(){
        $data = input('post.');
        $data['id'] = json_encode($data['id']);
        //dump($data);
        $res = api('Formtpl/groupSort',$data);
        return $res;
    }

    /**
     * 字段设置
     */
    public function getField(){
        //config('api_debug',true);
        $data['id'] = $this->request->param('id');
        $res = api('Formtpl/getField',$data);
        //dump($res);
        $this->assign('rs',$res['data']);

        return view();
    }

    /**
     * 保存字段设置
     */
    public function fieldSave(){
        $data = input('post.');
        $res = api('Formtpl/fieldSave',$data);
        return $res;
    }

    /**
     * 保存模板基本设置
     */
    public function formtplSave(){
        $data = input('post.');
        $res = api('Formtpl/formtplSave',$data);
        return $res;
    }

    /**
     * 保存列表字段
     */
    public function listFieldsSave(){
        $data['id'] = input('post.id');
        $data['list_fields']    = [];
        $tmp = input('post.');
        if(isset($tmp['checked'])) {
            foreach ($tmp['checked'] as $key => $val) {
                $data['list_fields'][] = [
                    'label' => $tmp['label'][$val],
                    'name' => $tmp['name'][$val],
                    'function' => $tmp['function'][$val],
                    'attr' => $tmp['attr'][$val],
                ];
            }
        }
        //dump($data);
        $data['list_fields']    = json_encode($data['list_fields']);
        //dump($data);
        $res = api('Formtpl/listFieldsSave',$data);
        return $res;
    }

    /**
     * 保存视图设置
     */
    public function viewSave(){
        $data = input('post.');
        $res = api('Formtpl/viewSave',$data);
        return $res;
    }

    /**
     * 保存关联模型设置
     */
    public function relationSave(){
        //config('api_debug',true);
        $data = input('post.');
        $res = api('Formtpl/relationSave',$data);
        return $res;
    }

    /**
     * 从数据表结构中添加字段
     */
    public function searchFieldFromTable(){
        $data['id'] = $this->request->param('id');
        $res = api('Formtpl/formtplFields',$data);
        //dump($res);
        $this->assign('fields',$res['data']);

        $res = api('Formtpl/searchFields',$data);
        //dump($res);
        $use = isset($res['fields']) ? $res['fields'] : [];
        $this->assign('use',$use);

        return view();
    }

    /**
     * 新增搜索字段
     */
    public function addSearchFields(){
        //config('api_debug',true);
        $data = input('post.');
        $data['id'] = json_encode($data['id']);

        $res = api('Formtpl/addSearchFields',$data);
        return $res;
    }

    /**
     * 删除搜索字段
     */
    public function deleteSearchField(){
        $data = input('post.');
        $data['id'] = json_encode($data['id']);

        $res = api('Formtpl/deleteSearchField',$data);
        return $res;
    }

    /**
     * 搜索字段排序
     */
    public function searchFieldsSort(){
        $data = input('post.');
        $data['id'] = json_encode($data['id']);
        //dump($data);
        $res = api('Formtpl/searchFieldsSort',$data);
        return $res;
    }

    /**
     * 搜索字段详情
     */
    public function searchFieldDetail(){
        $data['id'] = $this->request->param('id');
        $res = api('Formtpl/searchFieldDetail',$data);
        //dump($res);
        $this->assign('rs',$res['data']);

        return view();
    }

    /**
     * 保存搜索字段设置
     */
    public function searchFieldSave(){
        $data = input('post.');
        $res = api('Formtpl/searchFieldSave',$data);
        return $res;
    }

    /**
     * 添加搜索字段
     */
    public function addSearchField(){
        return view();
    }

    /**
     * 保存-添加搜索字段
     */
    public function addSearchFieldSave(){
        $data = input('post.');
        $res = api('Formtpl/addSearchField',$data);
        return $res;
    }

    /**
     * 创建模型文件
     */
    public function createModelFile(){
        $res = api('Formtpl/createModelFile',$this->post);
        return $res;
    }

    /**
     * 创建控制器文件
     */
    public function createController(){
        //config('api_debug',true);
        $res = api('Formtpl/createController',$this->post);
        return $res;
    }
}
