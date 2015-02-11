<?php

namespace Home\Controller;

use Think\Controller;

class LevelController extends LayoutController {
	
	public function levels() {
		$this->menu ();
		$this->assign ( 'title', '等级分布' );
		$this->assign ( 'username', $username );
		
		$DBuser = M ( 'heroes', '', 'mysql://root:hank@192.168.2.135:3306/game' );
		$type1 = $DBuser->query ( "select level,count(id) role from heroes where type=1 group by level" );
		$type2 = $DBuser->query ( "select level,count(id) role from heroes where type=2 group by level" );
		$type3 = $DBuser->query ( "select level,count(id) role from heroes where type=3 group by level" );
		$type = $DBuser->query ( "select level,count(id) role from heroes group by level" );
		$total = $DBuser->query ( "select count(id) total from heroes" );
	
		$levels = array ();
		foreach ( $type as $tmp ) {
			$level = array ();
			$level ['level'] = $tmp ['level'];
			$level ['type'] = $tmp ['role'];
			$level ['type1'] = 0;
			$level ['type2'] = 0;
			$level ['type3'] = 0;
			$level ['per'] =round ( (100*$tmp ['role']) / $total[0]['total'], 2 );
			$levels [$tmp ['level']] = $level;
		}
		
		foreach ( $type1 as $tmp ) {
			if ($levels [$tmp ['level']] ['level'] == $tmp ['level'])
				$levels [$tmp ['level']] ['type1'] = $tmp ['role'];
		}
		foreach ( $type2 as $tmp ) {
			if ($levels [$tmp ['level']] ['level'] == $tmp ['level'])
				$levels [$tmp ['level']] ['type2'] = $tmp ['role'];
		}
		foreach ( $type3 as $tmp ) {
			if ($levels [$tmp ['level']] ['level'] == $tmp ['level'])
				$levels [$tmp ['level']] ['type3'] = $tmp ['role'];
		}
		
		$this->assign ( 'levels', $levels );
		$this->display ();
	}
}
