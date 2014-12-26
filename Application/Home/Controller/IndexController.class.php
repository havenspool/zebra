<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends MenuController {
    
	public function index(){
		$this->meta_title = '管理首页';
		$this->display('menu/index');
        $this->display();
    }
}