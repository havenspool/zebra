<?php

namespace Home\Controller;

use Think\Controller;

class CostController extends LayoutController {
	
	public function cost_from() {		
		$this->menu();
		$this->assign('title','游戏币来源分析');
		$this->assign('username',$username);
		
		$DBuser = M('heroes_uses','','mysql://root:hank@192.168.2.135:3306/game');
		$heroes_uses= $DBuser->where("counttype=0")->select();
		$this->assign('heroes_uses',$heroes_uses);
        $this->display();
	}
	
	public function cost_to() {
		$this->menu();
		$this->assign('username',$username);
	
		$DBuser = M('heroes_uses','','mysql://root:hank@192.168.2.135:3306/game');
		$heroes_uses= $DBuser->where("counttype=1")->select();
		$this->assign('heroes_uses',$heroes_uses);
		$this->display();
	}
}
