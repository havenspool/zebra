<?php

namespace Home\Controller;

use Think\Controller;

class AnalysisController extends LayoutController {
	public function newuser() {
		$this->menu ();
		$this->assign ( 'title', '新增用户' );
		$this->assign ( 'username', $username );
		
		$DBuser = M ( 'heroes_stat', '', 'mysql://root:hank@192.168.2.135:3306/game' );
		$stats = $DBuser->query ( "select FROM_UNIXTIME(regTime, '%Y-%m-%d') day,count(heroid) role from heroes_stat group by day" );
		
		$DBuser2 = M ( 'users', '', 'mysql://root:hank@192.168.2.135:3306/game_user' );
		$users = $DBuser2->query ( "select FROM_UNIXTIME(UNIX_TIMESTAMP(created), '%Y-%m-%d') day,count(id) user from users group by day" );
		
		$newusers = array ();
		foreach ( $stats as $stat ) {
			$newuser = array ();
			$newuser ['day'] = $stat ['day'];
			$newuser ['role'] = $stat ['role'];
			$newuser ['user'] = 0;
			$newusers [$stat ['day']] = $newuser;
		}
		
		foreach ( $users as $user ) {
			if (array_key_exists ( $user ['day'], $newusers )) {
				$newusers [$user ['day']] ['user'] = $user ['user'];
			} else {
				$newuser = array ();
				$newuser ['day'] = $user ['day'];
				$newuser ['role'] = 0;
				$newuser ['user'] = $user ['user'];
				$newusers [$user ['day']] = $newuser;
			}
		}
		
		$this->assign ( 'newusers', $newusers );
		$this->display ();
	}
}
