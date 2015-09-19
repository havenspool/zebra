<?php

namespace Home\Controller;

use Think\Controller;

class CustomController extends LayoutController {

	public function punish_relieve() {
		$this->menu ();
		$url='/zebra/Home/Custom/punish_relieve';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		$platforms=$this->get_all_platforms();
		//servers
		$values=$_POST['checkbox'];
		if(!empty($values)){
			F($url.'values',$values);
		}else{
			$values = F($url.'values');//从缓存获取已选择服务器
		}
		$db_user=$this->get_dbuser();

		$heroids = $_POST['punish_heroids'];

		$punish_submit = $_POST['punish_submit_btn'];//10惩罚20解除
		$punish_type = $_POST['punish_type_radio'];//1封号2禁言3下线
		$punish_time = $_POST['punish_time_radio'];
		$fbc_day = $_POST['fbc_day'];
		$fbc_hour = $_POST['fbc_hour'];
		$fbc_min = $_POST['fbc_min'];
		$fbc_sec = $_POST['fbc_sec'];

		$fbc_time=0;//惩罚时长 单位为毫秒,踢他下线时间长设为1ms  forbidType=2
		$type=0;//惩罚时长类型 type=2永久封号 type=1限定时间
		$fbc_type=0;//forbidType=1 禁言 forbidType=2封号并踢他下线
		$flag=true;//是否惩罚
		if($punish_type==0){
        	$flag=false;
        }
        if($punish_type!=3&&$punish_time==0){
            $flag=false;
        }
        if($punish_type!=3&&$punish_time==1&&($fbc_day==0&&$fbc_hour==0&&$fbc_min==0&&$fbc_sec==0)){
        	$flag=false;
        }

        if($heroids==0){
            $flag=false;
        }

		//确定惩罚时间
        if($fbc_day!=0){
        	$fbc_time+=$fbc_day*3600*24;
        }

        if($fbc_hour!=0){
           	$fbc_time+=$fbc_hour*3600;
        }
        if($fbc_min!=0){
			$fbc_time+=$fbc_min*60;
		}
        if($fbc_sec!=0){
			$fbc_time+=$fbc_sec;
		}

		//确定惩罚时长类型，永久或者暂时
		if ($punish_time==2){
			$fbc_type = 2;
			$fbc_time=0;
		} else if ($punish_time==1){
			$fbc_type = 1;
		}

		//确定惩罚类型
		if ($punish_type==1){
			$type=2;
			//$result = "封号";
		} else if ($punish_type==2){
			$type=1;
			//$result = "禁言";
		} else if ($punish_type==3){
			$type=2;
			$fbc_type=1;
			$fbc_time = 1;
			//$result = "下线";
		}

		//解除惩罚
		if($punish_submit==20){
			$type = 1;
			$fbc_time = 0;
		}

		$punish_list = F('punish_list');//从缓存获取数据
		if(empty($punish_list)){
			$punish_list=array();
		}
        if($flag){
			$heroidstr = explode(';',$heroids);
			//每次只能传一个英雄Id
			while(count($punish_list)+count($heroidstr)>10){
				array_shift($punish_list);
			}

			for($index=0;$index<count($heroidstr);$index++){
				$punish=array();
				$sendData = array (
							"cmd" => "sys/forbid",  //sys/forbid  sys/searchUser
							"user" => "hank",
							"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
							"heroId" => $heroidstr[$index],
							"type" => $type,
							"forbidType" => $fbc_type,
							"forbidTime" => $fbc_time);
				$status = $this->conn_server($sendData);
				$punish['hero_id']=$heroidstr[$index];
				$punish['flog']=$status['forbid']['flog'];
				$punish['flogTime']=$status['forbid']['flogTime']/1000;
				$punish['fbc']=$status['forbid']['fbc'];
				$punish['fbcTime']=$status['forbid']['fbcTime']/1000;
				array_push($punish_list, $punish);
			}
        }

		F('punish_list',$punish_list);//保存数据到缓存
		//print_r($punish_list);


		$this->assign ( 'punish_list', $punish_list );
		$this->assign ( 'choose_servers', $values );
		$this->assign ( 'servers', $servers );
		$this->display ();
	}

	public function punish_query() {
		$this->menu ();
		$url='/zebra/Home/Custom/punish_query';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$heroids = $_POST['punish_heroids'];

		$heroidstr = explode(';',$heroids);

		$punish_list=array();
		for($index=0;$index<count($heroidstr);$index++){
			$punish=array();
			$sendData = array (
							"cmd" => "sys/searchUser",  //sys/forbid  sys/searchUser
							"user" => "hank",
							"heroId" => $heroidstr[$index],
							"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf");
			$status = $this->conn_server($sendData);
			$punish['hero_id']=$heroidstr[$index];
			$punish['flog']=$status['forbid']['flog'];
			$punish['flogTime']=$status['forbid']['flogTime']/1000;
			$punish['fbc']=$status['forbid']['fbc'];
			$punish['fbcTime']=$status['forbid']['fbcTime']/1000;
			array_push($punish_list, $punish);
		}
		$this->assign ( 'punish_list', $punish_list );
		$this->display ();
	}

	public function delete_hero() {
		$this->menu ();
		$url='/zebra/Home/Custom/delete_hero';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$heroids = $_POST['delete_hero_heroids'];

		$heroidstr = explode(';',$heroids);

		$delete_hero_list=array();
		for($index=0;$index<count($heroidstr);$index++){
			$delete_hero=array();
			$sendData = array (
							"cmd" => "sys/delete_hero",
							"user" => "hank",
							"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
							"heroId" => $heroidstr[$index]);
			$status = $this->conn_server($sendData);

			$delete_hero['hero_id']=$heroidstr[$index];
			$delete_hero['result']=$status['result'];
			$delete_hero['comments']=$status['comments'];
			array_push($delete_hero_list, $delete_hero);
		}
		$this->assign ( 'delete_hero_list', $delete_hero_list );
		$this->display ();
	}

    public function recharge_export() {
        $this->menu ();
		$url='/zebra/Home/Custom/recharge_export';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

        $recharge=array();

        $this->assign ( 'recharge', $recharge);
        $this->display ();
    }
}
