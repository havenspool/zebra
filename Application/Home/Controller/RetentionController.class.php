<?php
namespace Home\Controller;

use Think\Controller;

class RetentionController extends LayoutController {

	public function retention_hero() {
		$this->menu();
		$this->assign('title','角色留存');
		
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

		$DBuser = M('hero_data','', $this->get_gmserver());
		$plat_id=" (";
		foreach ($checks as $check){
			$plat_id=$plat_id."plat_id=".$check['plat_id']." or ";
		}
		$plat_id=substr($plat_id,0,-3);
		$plat_id=$plat_id.")";
		$hero_data= $DBuser->where($plat_id." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
		
		$dates=array();
		foreach ($hero_data as $data){
			$dates[$data['date']]['date']=$data['date'];
			$dates[$data['date']]['reg']+=$data['reg'];
			$dates[$data['date']]['two']+=$data['two'];
			$dates[$data['date']]['three']+=$data['three'];
			$dates[$data['date']]['two']+=$data['four'];
			$dates[$data['date']]['five']+=$data['five'];
			$dates[$data['date']]['six']+=$data['six'];
			$dates[$data['date']]['seven']+=$data['seven'];
			$dates[$data['date']]['fourteen']+=$data['fourteen'];
			$dates[$data['date']]['twentyone']+=$data['twentyone'];
			$dates[$data['date']]['thirty']+=$data['thirty'];
			$dates[$data['date']]['sixty']+=$data['sixty'];
			$dates[$data['date']]['ninety']+=$data['ninety'];
			$dates[$data['date']]['hund180']+=$data['hund180'];
			$dates[$data['date']]['hund360']+=$data['hund360'];
		}
		
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign('hero_data',$hero_data);
		$this->assign('dates',$dates);
		$this->display();
	}
	
	public function retention_user() {
		$this->menu();
		$this->assign('title','账号留存');
	
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
	
		$DBuser = M('user_data','', $this->get_gmserver());
		$plat_id=" (";
		foreach ($checks as $check){
			$plat_id=$plat_id."plat_id=".$check['plat_id']." or ";
		}
		$plat_id=substr($plat_id,0,-3);
		$plat_id=$plat_id.")";
		$hero_data= $DBuser->where($plat_id." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
		
		$dates=array();
		foreach ($hero_data as $data){
			$dates[$data['date']]['date']=$data['date'];
			$dates[$data['date']]['reg']+=$data['reg'];
			$dates[$data['date']]['two']+=$data['two'];
			$dates[$data['date']]['three']+=$data['three'];
			$dates[$data['date']]['two']+=$data['four'];
			$dates[$data['date']]['five']+=$data['five'];
			$dates[$data['date']]['six']+=$data['six'];
			$dates[$data['date']]['seven']+=$data['seven'];
			$dates[$data['date']]['fourteen']+=$data['fourteen'];
			$dates[$data['date']]['twentyone']+=$data['twentyone'];
			$dates[$data['date']]['thirty']+=$data['thirty'];
			$dates[$data['date']]['sixty']+=$data['sixty'];
			$dates[$data['date']]['ninety']+=$data['ninety'];
			$dates[$data['date']]['hund180']+=$data['hund180'];
			$dates[$data['date']]['hund360']+=$data['hund360'];		
		}
				
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign('hero_data',$hero_data);
		$this->assign('dates',$dates);
		$this->display();
	}
}