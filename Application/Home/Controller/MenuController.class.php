<?php
namespace Home\Controller;
use Think\Controller;
class MenuController extends Controller {
	
	public function _initialize(){
		//列表采用的是前台分页
		$DBuser = M('Menu');
		$menus = $DBuser->select();
		$this->assign('menus',$menus);
		
		$admin = session('admin');
		$username=$admin['username'];
		$this->assign('username',$username);
	}

    public function index(){
        //列表采用的是前台分页
        $DBuser = M('Menu');
        $menus = $DBuser->select();
        $this->assign('menus',$menus);

        $admin = session('admin');
		$username=$admin['username'];
		$this->assign('title','菜单列表');
		$this->assign('username',$username);
        $this->display();
    }
    
    public function add(){
    	if(IS_POST){
    		$Menu = D('Menu');
    		$data = $Menu->create();
    		if($data){
    			$id = $Menu->add();
    			if($id){
    				// S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('update_menu', 'Menu', $id, UID);
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($Menu->getError());
    		}
    	} else {
    		$this->assign('info',array('pid'=>I('pid')));
    		$menus = M('Menu')->field(true)->select();
    		$menus = D('Tree')->toFormatTree($menus);
    		$menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
    		$this->assign('Menus', $menus);
    		$this->meta_title = '新增菜单';
    		$this->display('edit');
    	}
    }
    
    public function edit($id = 0){
    	if(IS_POST){
    		$Menu = D('Menu');
    		$data = $Menu->create();
    		if($data){
    			if($Menu->save()!== false){
    				// S('DB_CONFIG_DATA',null);
    				//记录行为
    				action_log('update_menu', 'Menu', $data['id'], UID);
    				$this->success('更新成功', Cookie('__forward__'));
    			} else {
    				$this->error('更新失败');
    			}
    		} else {
    			$this->error($Menu->getError());
    		}
    	} else {
    		$info = array();
    		/* 获取数据 */
    		$info = M('Menu')->field(true)->find($id);
    		$menus = M('Menu')->field(true)->select();
    		$menus = D('Tree')->toFormatTree($menus);
    
    		$menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
    		$this->assign('Menus', $menus);
    		if(false === $info){
    			$this->error('获取后台菜单信息错误');
    		}
    		$this->assign('info', $info);
    		$this->meta_title = '编辑后台菜单';
    		$this->display();
    	}
    }
    
    public function del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('Menu')->where($map)->delete()){
    		// S('DB_CONFIG_DATA',null);
    		//记录行为
    		action_log('update_menu', 'Menu', $id, UID);
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
}