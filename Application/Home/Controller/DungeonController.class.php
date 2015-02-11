<?php
namespace Home\Controller;

use Think\Controller;

class DungeonController extends LayoutController {

	public function details() {
		$this->menu();
		$this->assign('title','关卡详情');
		$this->assign('username',$username);
		
		$DBuser = M('dungeons','','mysql://root:hank@192.168.2.135:3306/game');
		$dungeons= $DBuser->query('SELECT c.dungeonid dungeonid,d.name name,count(c.heroid) as count FROM dungeons_clearance c,dungeons d where c.dungeonid=d.id group BY c.dungeonId');
		$this->assign('dungeons',$dungeons);
		$this->display();
	}
}
