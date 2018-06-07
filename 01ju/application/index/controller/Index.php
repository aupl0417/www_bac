<?php
namespace app\index\controller;
use think\Controller;
use think\Request;

class Index extends Controller
{

    public function _initialize()
    {
        parent::_initialize();
        $bottom = $this->bottom_menu();
        $seo    = db('config_category')->where(['id' => '100330602'])->value('config');
        $seo    = unserialize($seo);

        $this->assign('seo', $seo);
        $this->assign('action', Request::instance()->action());
        $this->assign('bottom_menu',$bottom);
    }

    public function index()
    {
		$list = db('channel_category')->where(['id' => '100330599','status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        !$list && abort(404);
		$list['child'] = db('channel_category')->where(['upid' => '100330599','status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        foreach($list['child'] as $key => $val){
            $list['child'][$key]['child'] = db('channel')->where(['status' => 1,'category_id' => $val['id']])->field('id,name,atime,images,wap_images,content,tag,hit')->order('atime asc,id asc,sort asc')->select();
        }

		$this->assign('data',$list);
        $this->assign('title', '专业定制程序开发，一站式互联网+技术服务');
		return $this->fetch();
    }

    public function app()
    {
		$list = db('channel_category')->where(['id' => '100330603','status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        !$list && abort(404);
		$list['child'] = db('channel_category')->where(['upid' => '100330603','status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();

        foreach($list['child'] as $key => $val){
            $list['child'][$key]['child'] = db('channel')->where(['status' => 1,'category_id' => $val['id']])->field('id,name,atime,images,wap_images,content,tag,hit')->order('atime asc,id asc,sort asc')->select();
			foreach($list['child'][$key]['child'] as $vk=>$val){
				$list['child'][$key]['child'][$vk]['images'] = explode(",",$list['child'][$key]['child'][$vk]['images']);
			}
        }

		$this->assign('data',$list);
		$this->assign('title','APP开发');
		return $this->fetch();
    }


    public function shop()
    {
        $list  = $this->get_channel_category(100330611);

        foreach($list['child'] as $key => $val){
            $list['child'][$key]['child'] = db('channel')->where(['status' => 1,'category_id' => $val['id']])->field('id,name,atime,images,wap_images,content,tag,hit')->order('atime asc,id asc,sort asc')->select();
        }

		$this->assign('data',$list);
        $this->assign('title','商城开发');
		return $this->fetch();
    }

    public function case_app()
    {
		$list = db('channel_category')->where(['id' => '100330618','status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        !$list && abort(404);
		$example = db('example')->where(['status' => 1])->field('id,name,title,atime,images,detail_images,wap_images,wap_detail_images,tag,content,category_id,platform_id,application_id')->order('atime asc,id asc,sort asc')->select();

		$this->assign('data',$list);
		$this->assign('example',$example);
        $this->assign('title','经典案例');
		return $this->fetch();
    }

    public function about()
    {
		$list = db('channel_category')->where(['id' => '100330616','status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        $list['child'] = db('channel_category')->where(['upid' => '100330616','status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        foreach($list['child'] as $key => $val){
            $list['child'][$key]['child'] = db('channel')->where(['status' => 1,'category_id' => $val['id']])->field('id,name,atime,images,wap_images,content,tag,hit')->order('atime asc,id asc,sort asc')->select();
        }

		$this->assign('data',$list);
        $this->assign('title','关于我们');
		return $this->fetch();
    }

    private function bottom_menu()
    {	
		$bottom_menu = db('bottom_menu')->where(['upid' => 0,'status' => 1])->order('atime asc,id asc')->field('id,atime,name,images,url')->select();
		if(count($bottom_menu)>0){
			foreach($bottom_menu as $key => $val){
				$bottom_menu[$key]['child'] = db('bottom_menu')->where(['status' => 1,'upid' => $bottom_menu[$key]['id']])->field('id,atime,name,images,url')->order('atime asc,id asc')->select();
			} 
		}
		foreach($bottom_menu as $key=>&$val){
			foreach($val['child'] as $k=>&$v){
				if($v['url'] && strpos($v['url'], $_SERVER['HTTP_HOST'])=== false && strpos($v['url'], 'www')=== false){
					// $bottom[$key]['child'][$k]['url'] = url($v['url']);
					$v['url'] = url($v['url'],'','');
				}
			}
		}
		return $bottom_menu;
    }

    public function news()
    {
		$list = db('channel_category')->where(['id' => '100330621','status' => 1])->order('atime asc,id asc,sort asc')->field('id,atime,category_name,images,url,sub_title,content,wap_images')->select();
        !$list && abort(404);
		$list['child'] = db('channel')->where(['category_id' => '100330621','status' => 1])->order('atime asc,id asc,sort asc')->select();

		$this->assign('data',$list);
        $this->assign('title','新闻中心');
		return $this->fetch();
    }

	//案例详情
	public function case_data(){
		$id   = input('id', 973, 'intval');
        $list = db('example')->where(['id' => $id,'status' => 1])->field('id,atime,name,title,hit,keywords,description,category_id,detail_images,content,wap_detail_images,tag')->find();
        !$list && abort(404);
        $list['prev'] = db('example')->where(['id' => array('lt',$id),'status' => 1])->field('id,name')->order('id desc')->find();
        $list['next'] = db('example')->where(['id' => array('gt',$id),'status' => 1])->field('id,name')->find();
        db('example')->where(['id' => $id,'status' => 1])->setInc('hit');

		$this->assign('data',$list);
        $this->assign('title',$list['name']);
        $this->assign('keywords',$list['keywords'] ?: $list['name']);
        $this->assign('description',$list['description'] ?: $list['name']);
		return $this->fetch();
    }

	//案例详情
	public function news_deta(){
		$id   = input('id', 1047, 'intval');
		$list = db('channel')->where(['id' => $id,'status' => 1])->field('id,atime,name,hit,keywords,description,category_id,images,content,wap_images,tag')->find();
        !$list && abort(404);
        $list['prev'] = db('channel')->where(['category_id'=>'100330621','id' => array('lt',$id),'status' => 1])->field('id,name')->order('id desc')->find();
        $list['next'] = db('channel')->where(['category_id'=>'100330621','id' => array('gt',$id),'status' => 1])->field('id,name')->find();
        db('channel')->where(['id' => $id,'status' => 1])->setInc('hit');

		$lists= db('channel')->where(['category_id' => '100330621','status' => 1])->field('id,atime,name,hit,category_id,images,content,wap_images,tag')->order('atime desc')->limit(10)->select();

		$this->assign('data',$list);
		$this->assign('lists',$lists);
        $this->assign('title',$list['name'] . '-零壹聚');
        $this->assign('keywords',$list['keywords'] ?: $list['name']);
        $this->assign('description',$list['description'] ?: $list['name']);
		return $this->fetch();
    }

    private function get_channel_category($id){
        $field = 'id,atime,category_name,images,url,sub_title,content,wap_images';
        $order = 'atime asc,id asc,sort asc';
        $list  = db('channel_category')->where(['id' => $id,'status' => 1])->order($order)->field($field)->select();
        !$list && abort(404);
        $list['child'] = db('channel_category')->where(['upid' => $id,'status' => 1])->order($order)->field($field)->select();

        return $list;
    }
}
