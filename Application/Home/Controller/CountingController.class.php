<?php

namespace Home\Controller;

use Think\Controller;

class CountingController extends LayoutController {
	
	public function timesandtime() {
		$this->menu();
		$this->assign('title','竞技统计');
	
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
	
		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
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
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
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
		if($checks==null){
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
			$checkbox1="checked";
		}
		
		$DBuser = M('statistic_time','',$this->get_gmserver());
		$plat_id=" (";
		foreach ($checks as $check){
			$plat_id=$plat_id."plat_id=".$check['plat_id']." or ";
		}
		$plat_id=substr($plat_id,0,-3);
		$plat_id=$plat_id.")";
		$statistic_data= $DBuser->where($plat_id." and date > UNIX_TIMESTAMP('".$start_date."') and date < UNIX_TIMESTAMP('".$end_date."')")->select();
		
		
		$dates=array();
		foreach ($statistic_data as $data){
			$dates[$data['date']]['date']=$data['date'];
			$dates[$data['date']]['sboss']+=$data['sboss'];
			$dates[$data['date']]['sfreepk']+=$data['sfreepk'];
			$dates[$data['date']]['sonlinepk']+=$data['sonlinepk'];
			$dates[$data['date']]['sofflinpk']+=$data['sofflinpk'];
			$dates[$data['date']]['sbosstime']+=$data['sbosstime'];
			$dates[$data['date']]['sfreepktime']+=$data['sfreepktime'];
			$dates[$data['date']]['sonlinepktime']+=$data['sonlinepktime'];
			$dates[$data['date']]['sofflinpktime']+=$data['sofflinpktime'];
		}
	
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign('dates',$dates);
		$this->display();
	}
	
	public function sboss() {
		$this->menu();
		$this->assign('title','世界BOSS参与时长统计');
	
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
	
		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
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
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
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
		if($checks==null){
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
			$checkbox1="checked";
		}
	
		$DBuser = M('statistic_period','',$this->get_gmserver());
		$plat_id=" (";
		foreach ($checks as $check){
			$plat_id=$plat_id."plat_id=".$check['plat_id']." or ";
		}
		$plat_id=substr($plat_id,0,-3);
		$plat_id=$plat_id.")";
		$statistic_data= $DBuser->where($plat_id." and type=2 and date>UNIX_TIMESTAMP('".$start_date."') and date<UNIX_TIMESTAMP('".$end_date."')")->select();
	
// 		$types=array(
// 				'1' => "Sfreepk",
// 				'2' => 'Sboss',
// 				'3' => 'Sonlinepk',
// 				'4' => 'Sofflinpk'); //统计类型 武神野望参与次数和参与时间*2
		$dates=array();
		foreach ($statistic_data as $data){
			$dates[$data['date']]['date']=$data['date'];
			$dates[$data['date']]['t1']+=$data['t1'];
			$dates[$data['date']]['t2']+=$data['t2'];
			$dates[$data['date']]['t3']+=$data['t3'];
			$dates[$data['date']]['t4']+=$data['t4'];
			$dates[$data['date']]['t5']+=$data['t5'];
			$dates[$data['date']]['t6']+=$data['t6'];
			$dates[$data['date']]['t7']+=$data['t7'];
		}
	
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign('dates',$dates);
		$this->display();
	}
	
	public function sfree() {
		$this->menu();
		$this->assign('title','大乱斗参与时长统计');
	
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
	
		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
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
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
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
		if($checks==null){
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
			$checkbox1="checked";
		}
	
		$DBuser = M('statistic_period','',$this->get_gmserver());
		$plat_id=" (";
		foreach ($checks as $check){
			$plat_id=$plat_id."plat_id=".$check['plat_id']." or ";
		}
		$plat_id=substr($plat_id,0,-3);
		$plat_id=$plat_id.")";
		$statistic_data= $DBuser->where($plat_id." and type=1 and date > UNIX_TIMESTAMP('".$start_date."') and date < UNIX_TIMESTAMP('".$end_date."')")->select();
	
		// 		$types=array(
		// 				'1' => "Sfreepk",
		// 				'2' => 'Sboss',
		// 				'3' => 'Sonlinepk',
		// 				'4' => 'Sofflinpk'); //统计类型 武神野望参与次数和参与时间*2
		$dates=array();
		foreach ($statistic_data as $data){
			$dates[$data['date']]['date']=$data['date'];
			$dates[$data['date']]['t1']+=$data['t1'];
			$dates[$data['date']]['t2']+=$data['t2'];
			$dates[$data['date']]['t3']+=$data['t3'];
			$dates[$data['date']]['t4']+=$data['t4'];
			$dates[$data['date']]['t5']+=$data['t5'];
			$dates[$data['date']]['t6']+=$data['t6'];
			$dates[$data['date']]['t7']+=$data['t7'];
		}
	
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign('dates',$dates);
		$this->display();
	}
	
	public function sonline() {
		$this->menu();
		$this->assign('title','世界BOSS参与时长统计');
	
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
	
		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
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
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
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
		if($checks==null){
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
			$checkbox1="checked";
		}
	
		$DBuser = M('statistic_period','',$this->get_gmserver());
		$plat_id=" (";
		foreach ($checks as $check){
			$plat_id=$plat_id."plat_id=".$check['plat_id']." or ";
		}
		$plat_id=substr($plat_id,0,-3);
		$plat_id=$plat_id.")";
		$statistic_data= $DBuser->where($plat_id." and type=3 and date > UNIX_TIMESTAMP('".$start_date."') and date < UNIX_TIMESTAMP('".$end_date."')")->select();
	
		// 		$types=array(
		// 				'1' => "Sfreepk",
		// 				'2' => 'Sboss',
		// 				'3' => 'Sonlinepk',
		// 				'4' => 'Sofflinpk'); //统计类型 武神野望参与次数和参与时间*2
		$dates=array();
		foreach ($statistic_data as $data){
			$dates[$data['date']]['date']=$data['date'];
			$dates[$data['date']]['t1']+=$data['t1'];
			$dates[$data['date']]['t2']+=$data['t2'];
			$dates[$data['date']]['t3']+=$data['t3'];
			$dates[$data['date']]['t4']+=$data['t4'];
			$dates[$data['date']]['t5']+=$data['t5'];
			$dates[$data['date']]['t6']+=$data['t6'];
			$dates[$data['date']]['t7']+=$data['t7'];
		}
	
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign('dates',$dates);
		$this->display();
	}
	
	public function soffonline() {
		$this->menu();
		$this->assign('title','世界BOSS参与时长统计');
	
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
	
		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
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
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
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
		if($checks==null){
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
			$checkbox1="checked";
		}
	
		$DBuser = M('statistic_period','',$this->get_gmserver());
		$plat_id=" (";
		foreach ($checks as $check){
			$plat_id=$plat_id."plat_id=".$check['plat_id']." or ";
		}
		$plat_id=substr($plat_id,0,-3);
		$plat_id=$plat_id.")";
		$statistic_data= $DBuser->where($plat_id." and type=4 and date > UNIX_TIMESTAMP('".$start_date."') and date < UNIX_TIMESTAMP('".$end_date."')")->select();
	
		// 		$types=array(
		// 				'1' => "Sfreepk",
		// 				'2' => 'Sboss',
		// 				'3' => 'Sonlinepk',
		// 				'4' => 'Sofflinpk'); //统计类型 武神野望参与次数和参与时间*2
		$dates=array();
		foreach ($statistic_data as $data){
			$dates[$data['date']]['date']=$data['date'];
			$dates[$data['date']]['t1']+=$data['t1'];
			$dates[$data['date']]['t2']+=$data['t2'];
			$dates[$data['date']]['t3']+=$data['t3'];
			$dates[$data['date']]['t4']+=$data['t4'];
			$dates[$data['date']]['t5']+=$data['t5'];
			$dates[$data['date']]['t6']+=$data['t6'];
			$dates[$data['date']]['t7']+=$data['t7'];
		}
	
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign('dates',$dates);
		$this->display();
	}
	
}
?>