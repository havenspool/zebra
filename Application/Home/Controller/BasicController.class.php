<?php

namespace Home\Controller;

use Think\Controller;

class BasicController extends LayoutController {
	
	public function statistic() {		
		$this->menu();
		$this->assign('title','实时统计');
		$this->assign('username',$username);
		
		$DBuser = M('heroes_uses','','mysql://root:hank@192.168.2.135:3306/game');
		$heroes_uses= $DBuser->where("heroid=1133004")->select();
		$this->assign('heroid',113304);
		$this->assign('heroes_uses',$heroes_uses);
        $this->display();
	}
		
	public function overall() {
		$this->menu();
		$this->assign('title','整体趋势');
		$this->assign('username',$username);
        $this->display();
	}

}