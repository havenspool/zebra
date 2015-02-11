<?php
namespace Home\Controller;

use Think\Controller;

class LayoutController extends Controller {

	public function menu() {
		//列表采用的是前台分页
		$DBuser = M('Menu');
		$menus = $DBuser->select();
		$this->assign('menus',$menus);

		$admin = session('admin');
		$username=$admin['username'];
	}
}
