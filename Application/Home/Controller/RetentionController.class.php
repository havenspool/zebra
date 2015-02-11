<?php
namespace Home\Controller;

use Think\Controller;

class RetentionController extends LayoutController {

	public function retention() {
		$this->menu();
		$this->assign('title','留存用户');
		$this->assign('username',$username);
		
		$DBuser = M('hero_date','','mysql://root:havens@localhost:3306/test');
		$hero_date= $DBuser->select();
		$this->assign('hero_date',$hero_date);
		$this->display();
	}
}