<?php

namespace Home\Controller;

use Think\Controller;

class AnalysisController extends LayoutController {

	public function newuser() {
		$this->menu ();
		$this->assign ( 'title', '新增用户' );
		
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		
		//echo $start_date; 2015-03-10
		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*10);
		}
		
		if($_POST['end_date']==""){
			$end_date = date('Y-m-d',time());
		}

		$checks=array();
		$checkbox1="";
		$checkbox2="";
		$checkbox3="";
		$checkbox4="";
		if($_POST['checkbox1']==1){
			$checkbox1="checked";
		}else{
			if($_POST['checkbox2']==2){
				$check=array();
				$check['plat_id']=13;
				array_push($checks, $check);
				$checkbox2="checked";
			}
			if($_POST['checkbox3']==3){
				$check=array();
				$check['plat_id']=0;
				array_push($checks, $check);
				$checkbox3="checked";
			}
			if($_POST['checkbox4']==4){
				$check=array();
				$check['plat_id']=-1;
				array_push($checks, $check);
				$checkbox4="checked";
			}
		}

		$plat_id=" (";
		if($checks==null){
			$checkbox1="checked";
			$plat_id=$plat_id."1=1";
		}
		foreach ($checks as $check){
			$plat_id=$plat_id."h.platform=".$check['plat_id']." or ";
		}
		if($checks!=null){
			$plat_id=substr($plat_id,0,-3);
		}
		$plat_id=$plat_id.")";
		

		$DBuser = M ( 'heroes_stat', '', $this->get_game());
		$stats = $DBuser->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role from heroes_stat s,heroes h where ".$plat_id." and s.heroid=h.id and  s.regTime > UNIX_TIMESTAMP('".$start_date."') and s.regTime < UNIX_TIMESTAMP('".$end_date."') group by day" );
		
		$DBuser2 = M ( 'users', '',  $this->get_game_user());
		$users = $DBuser2->query ( "select FROM_UNIXTIME(UNIX_TIMESTAMP(h.created), '%Y-%m-%d') day,count(h.id) user from users h where ".$plat_id." and UNIX_TIMESTAMP(h.created) > UNIX_TIMESTAMP('".$start_date."') and UNIX_TIMESTAMP(h.created) < UNIX_TIMESTAMP('".$end_date."') group by day" );
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
		
		sort($newusers);
		
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		
		$this->assign ( 'newusers', $newusers );
		$this->display ();
	}

	public function onlines() {
		$this->menu ();
		$this->assign ( 'title', '实时在线' );
		$sendData = array (
						"cmd" => "sys/status",
						"user" => "hank",
						"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf");
		$status=$this->conn_server($sendData);

		$this->assign ( 'onlines', $status['onlines'] );
		$this->display ();
	}
}
