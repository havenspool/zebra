<?php

namespace Home\Controller;

use Think\Controller;

class LevelController extends LayoutController {
	
	public function levels() {
		$this->menu ();
		$this->assign ( 'title', '英雄等级分布' );
		
		
		$check_date = $_POST['check_date'];
		
		if($_POST['check_date']==""){
			$check_date = date('Y-m-d',time());			
		}
		
		$flag=true;
		if($check_date ==date('Y-m-d',time())){
			$flag=false;
		}
		$all=false;
		$checks=array();
		$checkbox1="";
		$checkbox2="";
		$checkbox3="";
		$checkbox4="";
		if($_POST['checkbox1']==1){
			$check=array();
			$check['plat_id']=-100;
			array_push($checks, $check);
			$all=true;
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
			$all=true;
		}
		$levels = array ();
		$level_all = array ();
		if($flag){
			$DBuser = M('level_data','', $this->get_gmserver());
			$plat_id=" (";
			foreach ($checks as $check){
				$plat_id=$plat_id."plat_id=".$check['plat_id']." or ";
			}
			$plat_id=substr($plat_id,0,-3);
			$plat_id=$plat_id.")";
			$hero_data= $DBuser->where($plat_id." and date=UNIX_TIMESTAMP('".$check_date."')")->select();
			//$total = $DBuser->query ( "select count(id) total from game.heroes" );
			
			
			for($index=1;$index<=60;$index++){
				$level = array ();
				$level ['plat_id'] = 0;
				$level ['date'] = 0;
				$level ['type1']=0;
				$level ['type2']=0;
				$level ['type3']=0;
				$level ['type']=0;
				$level ['level']=$index;
				$level ['per']=0;
				$levels [$index] = $level;
			}
			$total=0;
			foreach ( $hero_data as $data ) {
				for($index=1;$index<=60;$index++){
					$levels [$index] ['plat_id'] = $data ['plat_id'];
					$levels [$index] ['type'] = $data ['type'];
					$levels [$index] ['date'] = $data ['date'];
					if($data['type']==1) $levels [$index] ['type1'] = $data ['lv'.$index];
					else if($data['type']==2) $levels [$index] ['type2'] = $data ['lv'.$index];
					else if($data['type']==3) $levels [$index] ['type3'] = $data ['lv'.$index];
					$levels [$index] ['level']=$index;
					$total+=$data ['lv'.$index];
				}
			}
			
			$level_type=0;
			$level_type1=0;
			$level_type2=0;
			$level_type3=0;

			foreach ( $levels as $level ) {
				$level_type1+=$level ['type1'];
				$level_type2+=$level ['type2'];
				$level_type3+=$level ['type3'];			
				$level_type+=$level ['type1']+$level ['type2']+$level ['type3'];
				$levels[$level ['level']]['type']=$level ['type1']+$level ['type2']+$level ['type3'];
				$levels[$level ['level']]['per']=round ( (100*$levels[$level ['level']]['type']) / $total, 2 );
			}
			$level_all ['type1']=$level_type1;
			$level_all ['type2']=$level_type2;
			$level_all ['type3']=$level_type3;
			$level_all ['type']=$level_type;
			$level_all ['per']=100;
			
			$this->assign ( 'total', $total);
		}else{	

			$plat_id=" ";
			if(!$all){				
				$plat_id=$plat_id." and (";
				foreach ($checks as $check){
					$plat_id=$plat_id." platform=".$check['plat_id']." or ";
				}
				$plat_id=substr($plat_id,0,-3);
				$plat_id=$plat_id.")";
			}
			
			$DBuser = M ( 'heroes', '',  $this->get_game());
			$type1 = $DBuser->query ( "select level,count(id) role from game.heroes where type=1 ".$plat_id." group by level" );
			$type2 = $DBuser->query ( "select level,count(id) role from game.heroes where type=2 ".$plat_id." group by level" );
			$type3 = $DBuser->query ( "select level,count(id) role from game.heroes where type=3 ".$plat_id." group by level" );
			$type = $DBuser->query ( "select level,count(id) role from game.heroes where 1=1 ".$plat_id." group by level" );
			$total = $DBuser->query ( "select count(id) total from game.heroes" );
			
			$levels = array ();
			foreach ( $type as $tmp ) {
				$level = array ();
				$level ['level'] = $tmp ['level'];
				$level ['type'] = $tmp ['role'];
				$level ['date'] = strtotime ($check_date);
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
			
			$level_type=0;
			$level_type1=0;
			$level_type2=0;
			$level_type3=0;
				
			foreach ( $levels as $level ) {
				$level_type+=$level ['type'];
				$level_type1+=$level ['type1'];
				$level_type2+=$level ['type2'];
				$level_type3+=$level ['type3'];
			}
			
			$level_all ['type1']=$level_type1;
			$level_all ['type2']=$level_type2;
			$level_all ['type3']=$level_type3;
			$level_all ['type']=$level_type;
			$level_all ['per']=100;
			
			$this->assign ( 'total', $total[0]['total'] );
		}

		$this->assign ( 'checkbox1', $checkbox1 );
		$this->assign ( 'checkbox2', $checkbox2 );
		$this->assign ( 'checkbox3', $checkbox3 );
		$this->assign ( 'checkbox4', $checkbox4 );
		$this->assign ( 'checkbox4', $checkbox4 );

		$this->assign ( 'levels', $levels );
		$this->assign ( 'level_all', $level_all);
		$this->display ();
	}
}
