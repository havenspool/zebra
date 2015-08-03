<?php

namespace Home\Controller;

use Think\Controller;

class CostController extends LayoutController {
	
	public function cost_from() {		
		$this->menu();
		$this->assign('title','虚拟货币来源分析');
		
		$heroId = $_POST['heroid'];
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		
		if($_POST['heroid']==""){
			$heroId = 0;
		}
		
		if($_POST['start_date']==""){
// 			$start_date = date('Y-m-d',time()-3600*24*30);
			$start_date = 0;
		}
		
		if($_POST['end_date']==""){
// 			$end_date = date('Y-m-d',time());
			$end_date = 0;
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
				$check['cost_type']=1;
				array_push($checks, $check);
				$checkbox2="checked";
			}
			if($_POST['checkbox3']==3){
				$check=array();
				$check['cost_type']=2;
				array_push($checks, $check);
				$checkbox3="checked";
			}
			if($_POST['checkbox4']==4){
				$check=array();
				$check['cost_type']=3;
				array_push($checks, $check);
				$checkbox4="checked";
			}
		}
		$uses_num=0;
		if($_POST['uses_num']!=""){
			$uses_num=$_POST['uses_num'];
		}
		
		if($checks==null){
			$checkbox1="checked";
		}
		
		$cost_type=" (";
		foreach ($checks as $check){
			$cost_type=$cost_type."costtype=".$check['cost_type']." or ";
		}
		if($checks!=null){
			$cost_type=substr($cost_type,0,-3);
			$cost_type=$cost_type.")";
		}else{
			$cost_type=$cost_type." 1=1 )";
		}
		
		$date_sql="";
		$num_sql="";
		if($start_date!=0&&$end_date!=0){
			$num_sql="";
			$date_sql=" and time > UNIX_TIMESTAMP('".$start_date."') and time < UNIX_TIMESTAMP('".$end_date."') ";
		}else if($start_date!=0){
			$num_sql=" limit 0, 100";
			$date_sql=" and time > UNIX_TIMESTAMP('".$start_date."') ";
		}else if($end_date!=0){
			$num_sql=" limit 0, 100";
			$date_sql=" and time < UNIX_TIMESTAMP('".$end_date."') ";
		}else if($uses_num!=0){
			$date_sql="";
			$num_sql=" limit 0,".$uses_num;
		}
		
		$heroes_uses=array();
		if($heroId!=0){
			$DBuser = M('heroes_uses','', $this->get_game());
			$sql="select * from heroes_uses where ". $cost_type." and counttype=0 and heroid=".$heroId.$date_sql." order by time desc ".$num_sql;
 			//$sql="select * from heroes_uses where ". $cost_type." and counttype=0 and heroid=".$heroId." and time > UNIX_TIMESTAMP('".$start_date."') and time < UNIX_TIMESTAMP('".$end_date."') order by time desc limit 0,".$uses_num;
			$heroes_uses = $DBuser->query ($sql);
		}
				
		$heroes_uses_group=array();
		foreach ($heroes_uses as $use){
			$heroes_uses_group[date('Y-m-d', $use['time'])]['date']=date('Y-m-d', $use['time']);
			$heroes_uses_group[date('Y-m-d', $use['time'])]['gold_from']=0;
			$heroes_uses_group[date('Y-m-d', $use['time'])]['coins_from']=0;
			$heroes_uses_group[date('Y-m-d', $use['time'])]['jimmu_from']=0;
		
		}
		
		foreach ($heroes_uses as $use){			
			$heroes_uses_group[date('Y-m-d', $use['time'])]['date']=date('Y-m-d', $use['time']);
			if($use['costtype']==1) $heroes_uses_group[date('Y-m-d', $use['time'])]['gold_from']+=$use['cost'];
			if($use['costtype']==2) $heroes_uses_group[date('Y-m-d', $use['time'])]['coins_from']+=$use['cost'];
			if($use['costtype']==3) $heroes_uses_group[date('Y-m-d', $use['time'])]['jimmu_from']+=$use['cost'];
		
		}


		sort($heroes_uses_group);
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		
		$this->assign ( 'heroId', $heroId );
		$this->assign ( 'uses_num', $uses_num );

		$this->assign('heroes_uses',$heroes_uses);
		$this->assign('heroes_uses_group',$heroes_uses_group);		
		
        $this->display();
	}
	
	public function cost_to() {
		$this->menu();
		$this->assign('title','虚拟货币消耗分析');
		
		$heroId = $_POST['heroid'];
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		
		if($_POST['heroid']==""){
			$heroId = 0;
		}
		if($_POST['start_date']==""){
// 			$start_date = date('Y-m-d',time()-3600*24*30);
			$start_date = 0;
		}
		
		if($_POST['end_date']==""){
			$end_date = 0;
// 			$end_date = date('Y-m-d',time());
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
				$check['cost_type']=1;
				array_push($checks, $check);
				$checkbox2="checked";
			}
			if($_POST['checkbox3']==3){
				$check=array();
				$check['cost_type']=2;
				array_push($checks, $check);
				$checkbox3="checked";
			}
			if($_POST['checkbox4']==4){
				$check=array();
				$check['cost_type']=3;
				array_push($checks, $check);
				$checkbox4="checked";
			}
		}
		$uses_num=0;
		if($_POST['uses_num']!=""){
			$uses_num=$_POST['uses_num'];
		}
		
		if($checks==null){
			$checkbox1="checked";
		}

		$cost_type=" (";
		foreach ($checks as $check){
			$cost_type=$cost_type."costtype=".$check['cost_type']." or ";
		}
		if($checks!=null){
			$cost_type=substr($cost_type,0,-3);
			$cost_type=$cost_type.")";
		}else{
			$cost_type=$cost_type." 1=1 )";
		}
		
		$date_sql="";
		$num_sql="";
		if($start_date!=0&&$end_date!=0){
			$num_sql="";
			$date_sql=" and time > UNIX_TIMESTAMP('".$start_date."') and time < UNIX_TIMESTAMP('".$end_date."') ";
		}else if($start_date!=0){
			$num_sql=" limit 0, 100";
			$date_sql=" and time > UNIX_TIMESTAMP('".$start_date."') ";
		}else if($end_date!=0){
			$num_sql=" limit 0, 100";
			$date_sql=" and time < UNIX_TIMESTAMP('".$end_date."') ";
		}else if($uses_num!=0){
			$date_sql="";
			$num_sql=" limit 0,".$uses_num;
		}
		
		$heroes_uses=array();
		if($heroId!=0){
			$DBuser = M('heroes_uses','', $this->get_game());
			$sql="select * from heroes_uses where ". $cost_type." and counttype=1 and heroid=".$heroId.$date_sql." order by time desc ".$num_sql;
			$heroes_uses = $DBuser->query ($sql);
		}	
		
		$heroes_uses_group=array();
		foreach ($heroes_uses as $use){
			$heroes_uses_group[date('Y-m-d', $use['time'])]['date']=date('Y-m-d', $use['time']);
			$heroes_uses_group[date('Y-m-d', $use['time'])]['gold_to']=0;
			$heroes_uses_group[date('Y-m-d', $use['time'])]['coins_to']=0;
			$heroes_uses_group[date('Y-m-d', $use['time'])]['jimmu_to']=0;
		
		}
		
		foreach ($heroes_uses as $use){			
			$heroes_uses_group[date('Y-m-d', $use['time'])]['date']=date('Y-m-d', $use['time']);
			if($use['costtype']==1) $heroes_uses_group[date('Y-m-d', $use['time'])]['gold_to']+=$use['cost'];
			if($use['costtype']==2) $heroes_uses_group[date('Y-m-d', $use['time'])]['coins_to']+=$use['cost'];
			if($use['costtype']==3) $heroes_uses_group[date('Y-m-d', $use['time'])]['jimmu_to']+=$use['cost'];
		
		}

		sort($heroes_uses_group);
		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );
		
		$this->assign ( 'heroId', $heroId );
		$this->assign ( 'uses_num', $uses_num );
		
		$this->assign('heroes_uses',$heroes_uses);
		$this->assign('heroes_uses_group',$heroes_uses_group);	
		$this->display();
	}
}
