<?php

namespace Home\Controller;

use Think\Controller;

class RegAndOnlineController extends LayoutController {

	public function hero_online() {
		$this->menu ();
		$url='/zebra/Home/RegAndOnline/hero_online';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$servers=$this->get_all_server();
		$choose_servers=$_POST['checkbox'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}

		$sendData = array (
						"cmd" => "sys/status",
						"user" => "hank",
						"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf");
		$status=array();
		foreach($servers as $server){
			$status[$server['server_id']]['server_id']=$server['server_id'];
			$status[$server['server_id']]['server_name']=$server['server_name'];
			$status[$server['server_id']]['host']=$server['host'];
			$status[$server['server_id']]['port']=$server['port'];

			$flag=0;
			foreach($choose_servers as $choose_server){
				if($choose_server==$server['server_id']){
					$flag=1;
					break;
				}
			}
			if($flag==1){
				$stat=$this->conn_server($server['host'],$server['port'],$sendData);
				if($stat['result']==-1){
					print_r("未能连接上游戏服");
					// $server['onlines']="未能连接上游戏服";
					$status[$server['server_id']]['onlines']="未能连接上游戏服";
				}else{
					$status[$server['server_id']]['onlines']+=$stat['onlines'];
					$status[$server['server_id']]['freepkNum']+=$stat['freepkNum'];
					$status[$server['server_id']]['sceneNum']+=$stat['sceneNum'];
					$status[$server['server_id']]['bossNum']+=$stat['bossNum'];
					// $status[$server['server_id']]['pkNum']+=$stat['pkNum'];
					$status[$server['server_id']]['dungeonNum']+=$stat['dungeonNum'];
					// $status[$server['server_id']]['resDungeonNum']+=$stat['resDungeonNum'];
					// $status[$server['server_id']]['elitDungeonNum']+=$stat['elitDungeonNum'];
					// $status[$server['server_id']]['towerNum']+=$stat['towerNum'];
					$status[$server['server_id']]['offlinepkNum']+=$stat['offlinepkNum'];
				}
				// print_r($stat);
			}else{
				$status[$server['server_id']]['onlines']="未选择";
			}
		}

		$datas=array();
		foreach($status as $stat){
			$datas['freepkNum']+=$stat['freepkNum'];
			$datas['sceneNum']+=$stat['sceneNum'];
			$datas['bossNum']+=$stat['bossNum'];
			// $datas['pkNum']+=$stat['pkNum'];
			$datas['dungeonNum']+=$stat['dungeonNum'];
			// $datas['resDungeonNum']+=$stat['resDungeonNum'];
			// $datas['elitDungeonNum']+=$stat['elitDungeonNum'];
			// $datas['towerNum']+=$stat['towerNum'];
			$datas['offlinepkNum']+=$stat['offlinepkNum'];
		}
		$this->assign ( 'datas', $datas );
		$this->assign ( 'status', $status );

	  $this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->display ();
	}

    public function new_user() {
		    $this->menu ();
				$url='/zebra/Home/RegAndOnline/new_user';
				$menu=$this->get_menu_from_url($url);
				$this->assign ( 'title', $menu['title']);
				$this->assign ( 'active_open_id', $menu['pid']);
				$this->assign ( 'url', $url);

				$start_date = $_POST['start_date'];
		    $end_date = $_POST['end_date'];

		    if($_POST['start_date']==""){
		        $start_date = date('Y-m-d',time()-3600*24*7);
		    }

		    if($_POST['end_date']==""){
		        $end_date = date('Y-m-d',time()+86400);
		    }

				$servers=$this->get_all_server();
				$db_gm=$this->get_gmserver();
				$platforms=$this->get_all_platforms();
				//platform
    		$values=$_POST['checkbox'];
    		if(!empty($values)){
    			F($url.'values',$values);
    		}else{
    			$values = F($url.'values');//从缓存获取已选择服务器
    		}
    		//servers
    		$choose_servers=$_POST['checkbox_servers'];
    		if(!empty($choose_servers)){
    			F($url.'choose_servers',$choose_servers);
    		}else{
    			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
    		}
				$stats=array();
				$users=array();
				$db_user=$this->get_dbuser();

				foreach ($choose_servers as $choose_server) {
						$server=$this->get_server_from_id($choose_server);
						if(empty($server)) continue;
						foreach ($values as $value) {
							$db=$this->get_db_from_serverid($server['server_id']);
							if(empty($db)) continue;
							$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

							$user = M ( 'users', '',  $db_user)->query ( "select FROM_UNIXTIME(UNIX_TIMESTAMP(u.created), '%Y-%m-%d') day,count(u.id) user from ".$this->get_dbuser_name().".users u,".$db['db_hero'].".heroes h where u.id=h.userid and h.userid!=0 and u.platform=".$value." and UNIX_TIMESTAMP(u.created) >= UNIX_TIMESTAMP('".$start_date."') and UNIX_TIMESTAMP(u.created) <= UNIX_TIMESTAMP('".$end_date."') group by day" );
							array_push($users, $user);

							$stat = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role,0 type from ".$db['db_hero'].".heroes_stat s,".$db['db_hero'].".heroes h, ".$this->get_dbuser_name().".users u where u.id=h.userid and u.platform=".$value." and s.heroid=h.id and  s.regTime >= UNIX_TIMESTAMP('".$start_date."') and s.regTime <= UNIX_TIMESTAMP('".$end_date."') group by day" );
							array_push($stats, $stat);
							$stat = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role,h.type type from ".$db['db_hero'].".heroes_stat s,".$db['db_hero'].".heroes h, ".$this->get_dbuser_name().".users u where u.id=h.userid and h.platform=".$value." and s.heroid=h.id and h.type=1 and  s.regTime >= UNIX_TIMESTAMP('".$start_date."') and s.regTime <= UNIX_TIMESTAMP('".$end_date."') group by day" );
							array_push($stats, $stat);
							$stat = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role,h.type type from ".$db['db_hero'].".heroes_stat s,".$db['db_hero'].".heroes h, ".$this->get_dbuser_name().".users u where u.id=h.userid and h.platform=".$value." and s.heroid=h.id and h.type=2 and s.regTime >= UNIX_TIMESTAMP('".$start_date."') and s.regTime <= UNIX_TIMESTAMP('".$end_date."') group by day" );
							array_push($stats, $stat);
							$stat = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role,h.type type from ".$db['db_hero'].".heroes_stat s,".$db['db_hero'].".heroes h, ".$this->get_dbuser_name().".users u where u.id=h.userid and h.platform=".$value." and s.heroid=h.id and h.type=3 and s.regTime >= UNIX_TIMESTAMP('".$start_date."') and s.regTime <= UNIX_TIMESTAMP('".$end_date."') group by day" );
							array_push($stats, $stat);
						}
				}

    		$new_users = array ();
		    foreach ( $stats as $stat ) {
		    		foreach ( $stat as $sta ) {
		        		$new_users [$sta ['day']]['day'] = $sta ['day'];
								if($sta ['type']==0){
									$new_users [$sta ['day']]['role'] += $sta ['role'];
								}else if($sta ['type']==1){
									$new_users [$sta ['day']]['role1'] += $sta ['role'];
								}else if($sta ['type']==2){
									$new_users [$sta ['day']]['role2'] += $sta ['role'];
								}else if($sta ['type']==3){
									$new_users [$sta ['day']]['role3'] += $sta ['role'];
								}
				        		$new_users [$sta ['day']]['user'] = 0;
		    		}
		    }

        foreach ( $users as $user ) {
					foreach ( $user as $us ) {
						$new_users [$us ['day']]['role']+=0;
						$new_users [$us ['day']]['day'] = $us ['day'];
						$new_users [$us ['day']]['user'] += $us ['user'];
					}
        }

        sort($new_users);

			$this->assign('start_date',$start_date);
			$this->assign('end_date',$end_date);
	    $this->assign ( 'new_users', $new_users );
			$this->assign ( 'choose_servers', $choose_servers );
			$this->assign ( 'servers', $servers );
			$this->assign ( 'choose_platforms', $values );
			$this->assign ( 'platforms', $platforms );
	    $this->display ();
    }

	public function hero_retention() {
		$this->menu();
		$url='/zebra/Home/RegAndOnline/hero_retention';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*7);
		}
		if($_POST['end_date']==""){
			$end_date = date('Y-m-d',time());
		}

		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		$platforms=$this->get_all_platforms();
		//platform
		$values=$_POST['checkbox'];
		if(!empty($values)){
			F($url.'values',$values);
		}else{
			$values = F($url.'values');//从缓存获取已选择服务器
		}
		//servers
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}
		$db_user=$this->get_dbuser();

		$dates=array();
		$all_hero_datas=array();
		$days=$this->get_retention_day();

		foreach ($choose_servers as $choose_server) {
			$server=$this->get_server_from_id($choose_server);
			if(empty($server)) continue;
			foreach ($values as $value) {
				$db=$this->get_db_from_serverid($server['server_id']);
				if(empty($db)) continue;
				for($tmp_date=strtotime($start_date);$tmp_date<=strtotime($end_date);$tmp_date+=86400){
					foreach($days as $day){
						$heroes_data= M('heroes_data','', $this->get_gmserver())->where(" platform=".$value." and server_id=".$choose_server." and reg_date = ".$tmp_date." and login_date = ".($tmp_date+$day*86400))->select();
						if(count($heroes_data)>0)
							array_push($all_hero_datas, $heroes_data);
					}
				}
			}
		}

		$retentions=array();
		foreach ($all_hero_datas as $tmp_data){
			foreach ($tmp_data as $data){
				$retentions[$data['reg_date']]['date']=$data['reg_date'];
				$retentions[$data['reg_date']][($data['login_date']-$data['reg_date'])/86400]+=$data['login'];
			}
		}
		$this->assign('start_date',$start_date);
		$this->assign('end_date',$end_date);
		$this->assign('retentions',$retentions);
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->assign ( 'choose_platforms', $values );
		$this->assign ( 'platforms', $platforms );
		$this->display();
	}

	public function user_retention() {
		$this->menu();
		$url='/zebra/Home/RegAndOnline/user_retention';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];

		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*7);
		}

		if($_POST['end_date']==""){
			$end_date = date('Y-m-d',time());
		}
		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		$platforms=$this->get_all_platforms();
		//platform
		$values=$_POST['checkbox'];
		if(!empty($values)){
			F($url.'values',$values);
		}else{
			$values = F($url.'values');//从缓存获取已选择服务器
		}
		//servers
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}
		$db_user=$this->get_dbuser();
		$dates=array();
		$all_hero_datas=array();
		$days=$this->get_retention_day();
		foreach ($choose_servers as $choose_server) {
			$server=$this->get_server_from_id($choose_server);
			if(empty($server)) continue;
			foreach ($values as $value) {
				$db=$this->get_db_from_serverid($server['server_id']);
				if(empty($db)) continue;
				for($tmp_date=strtotime($start_date);$tmp_date<=strtotime($end_date);$tmp_date+=86400){
					foreach($days as $day){
						$heroes_data= M('users_data','', $this->get_gmserver())->where(" platform=".$value." and server_id=".$choose_server." and reg_date = ".$tmp_date." and login_date = ".($tmp_date+$day*86400))->select();
						if(count($heroes_data)>0)
							array_push($all_hero_datas, $heroes_data);
					}
				}

			}
		}

		$retentions=array();
		foreach ($all_hero_datas as $tmp_data){
			foreach ($tmp_data as $data){
				$retentions[$data['reg_date']]['date']=$data['reg_date'];
				$retentions[$data['reg_date']][($data['login_date']-$data['reg_date'])/86400]+=$data['login'];
			}
		}

		$this->assign('start_date',$start_date);
		$this->assign('end_date',$end_date);
		$this->assign('retentions',$retentions);
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->assign ( 'choose_platforms', $values );
		$this->assign ( 'platforms', $platforms );
		$this->display();
	}

	public function level_statistic() {
		$this->menu ();
		$url='/zebra/Home/RegAndOnline/level_statistic';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$check_date = $_POST['check_date'];

		if($_POST['check_date']==""){
			$check_date = date('Y-m-d',time());
		}

		$flag=true;
		if($check_date ==date('Y-m-d',time())){
			$flag=false;
		}

		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		$platforms=$this->get_all_platforms();
		//platform
		$values=$_POST['checkbox'];
		if(!empty($values)){
			F($url.'values',$values);
		}else{
			$values = F($url.'values');//从缓存获取已选择服务器
		}
		//servers
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}
		$db_user=$this->get_dbuser();

		$levels = array ();
		$level_all = array ();
		$total=0;
		if($flag){
			//过去某天的角色等级分布
			$hero_data=array();
			foreach ($choose_servers as $choose_server) {
				$server=$this->get_server_from_id($choose_server);
				if(empty($server)) continue;
				foreach ($values as $value) {
					$db=$this->get_db_from_serverid($server['server_id']);
					if(empty($db)) continue;
					$data= M('level_data','', $this->get_gmserver())->where(" platform=".$value." and server_id=".$choose_server." and date=UNIX_TIMESTAMP('".$check_date."')")->select();
					array_push($hero_data, $data);
				}
			}


			$count=false;
			foreach ( $hero_data as $data ) {
				if(count($data)>0) $count=true;
			}

			if($count){
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

				foreach ( $hero_data as $tmp_data ) {
					foreach ( $tmp_data as $data ) {
						for($index=1;$index<=60;$index++){
							$levels [$index] ['plat_id'] = $data ['platform'];
							$levels [$index] ['type'] = $data ['type'];
							$levels [$index] ['date'] = $data ['date'];
							if($data['type']==1) $levels [$index] ['type1'] += $data ['lv'.$index];
							else if($data['type']==2) $levels [$index] ['type2'] += $data ['lv'.$index];
							else if($data['type']==3) $levels [$index] ['type3'] += $data ['lv'.$index];
							$levels [$index] ['level']=$index;
							$total+=$data ['lv'.$index];
						}
					}
				}
				// Array (
				// [platform] => 0 [server_id] => 10001 [date] => 1442937600 [type] => 1 [all_user] => 1 [lv1] => 8 [lv2] => 7 [lv3] => 14 [lv4] => 15
				// [lv5] => 1 [lv6] => 5 [lv7] => 14 [lv8] => 6 [lv9] => 4 [lv10] => 9 [lv11] => 5 [lv12] => 4 [lv13] => 2 [lv14] => 4 [lv15] => 2
				// [lv16] => 1 [lv17] => 0 [lv18] => 0 [lv19] => 0 [lv20] => 0 [lv21] => 2 [lv22] => 1 [lv23] => 0 [lv24] => 1 [lv25] => 0
				// [lv26] => 0 [lv27] => 3 [lv28] => 2 [lv29] => 1 [lv30] => 0 [lv31] => 0 [lv32] => 1 [lv33] => 0 [lv34] => 0 [lv35] => 1
				// [lv36] => 1 [lv37] => 1 [lv38] => 1 [lv39] => 1 [lv40] => 0 [lv41] => 0 [lv42] => 1 [lv43] => 0 [lv44] => 0 [lv45] => 1
				// [lv46] => 1 [lv47] => 0 [lv48] => 2 [lv49] => 0 [lv50] => 0 [lv51] => 0 [lv52] => 0 [lv53] => 2 [lv54] => 0 [lv55] => 1
				//  [lv56] => 0 [lv57] => 1 [lv58] => 0 [lv59] => 0 [lv60] => 0 )
				// print_r($levels);
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
			}
		}else{
			//当前角色等级分布
			$hero_data=array();
			foreach ($choose_servers as $choose_server) {
				$server=$this->get_server_from_id($choose_server);
				if(empty($server)) continue;
				foreach ($values as $value) {
					$db=$this->get_db_from_serverid($server['server_id']);
					if(empty($db)) continue;
					$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

					$DBuser = M ( 'heroes', '',  $db_hero);


					$type1 = $DBuser->query ( "select level,count(h.id) role1 from ".$db['db_hero'].".heroes h, ".$this->get_dbuser_name().".users u where h.userid=u.id and h.type=1 and u.platform=".$value." group by h.level" );
					$type2 = $DBuser->query ( "select level,count(h.id) role2 from ".$db['db_hero'].".heroes h, ".$this->get_dbuser_name().".users u where h.userid=u.id and h.type=2 and u.platform=".$value." group by h.level" );
					$type3 = $DBuser->query ( "select level,count(h.id) role3 from ".$db['db_hero'].".heroes h, ".$this->get_dbuser_name().".users u where h.userid=u.id and h.type=3 and u.platform=".$value." group by h.level" );

					array_push($hero_data, $type1);
					array_push($hero_data, $type2);
					array_push($hero_data, $type3);

					// $data= M('level_data','', $this->get_gmserver())->where(" platform=".$value." and server_id=".$choose_server." and date=UNIX_TIMESTAMP('".$check_date."')")->select();
					// array_push($hero_data, $data);
				}
			}

			foreach ( $hero_data as $tmp_data ) {
				foreach ( $tmp_data as $tmp ) {
					$total += $tmp ['role1'];
					$total += $tmp ['role2'];
					$total += $tmp ['role3'];
				}
			}

			foreach ( $hero_data as $tmp_data ) {
				foreach ( $tmp_data as $tmp ) {
					$levels [$tmp ['level']]['date'] = strtotime ($check_date);
					$levels [$tmp ['level']]['level'] = $tmp ['level'];
					$levels [$tmp ['level']]['type1'] += $tmp ['role1'];
					$levels [$tmp ['level']]['type2'] += $tmp ['role2'];
					$levels [$tmp ['level']]['type3'] += $tmp ['role3'];
					$levels [$tmp ['level']]['type'] += $tmp ['role1'];
					$levels [$tmp ['level']]['type'] += $tmp ['role2'];
					$levels [$tmp ['level']]['type'] += $tmp ['role3'];

					$levels [$tmp ['level']]['per'] =round ((100*$levels [$tmp ['level']]['type']) / $total, 2 );
        }
			}

			sort($levels);

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
		}

		$this->assign ( 'levels', $levels );
		$this->assign ( 'level_all', $level_all);
		$this->assign ( 'total', $total);
		$this->assign('check_date',$check_date);
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->assign ( 'choose_platforms', $values );
		$this->assign ( 'platforms', $platforms );
		$this->display ();
	}

    public function dungeon_statistic() {
    $this->menu ();
		$url='/zebra/Home/RegAndOnline/dungeon_statistic';
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

		$hidevalue=$_POST['hidevalue'];

		$dungeons_data=array();

		$all_times=array();
		$all_enter_hero=array();
		$all_hero=array();
		foreach ($values as $value) {
			// $value as server_id
			$server=$this->get_server_from_id($value);
			$db=$this->get_db_from_serverid($server['server_id']);
			if(empty($db)) continue;
			$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
			$DBuser = M ( 'dungeons_clearance', '',  $db_hero);
			$times = $DBuser->query ( "SELECT d.dungeonid dungeonid,count(d.heroid) count,sum(d.all_times) all_times,sum(d.enter_times) enter_times FROM dungeons_clearance d,heroes h where d.heroid=h.id   group by d.dungeonid" );
			$enter_hero_times = $DBuser->query ( "SELECT d.dungeonid dungeonid,count(d.heroid) enter_count FROM dungeons_clearance d,heroes h where d.heroid=h.id and (d.enter_times>0 or d.all_times>0)   group by d.dungeonid" );
			$all_hero_times = $DBuser->query ( "SELECT d.dungeonid dungeonid,count(d.heroid) all_count FROM dungeons_clearance d,heroes h where d.heroid=h.id and d.all_times>0   group by d.dungeonid" );

			array_push($all_times, $times);
			array_push($all_enter_hero, $enter_hero_times);
			array_push($all_hero, $all_hero_times);
		}

		$dungeons=$this->get_Dungeons();
		while ($dungeon=current($dungeons)) {
				$dungeoid=key($dungeons);
				if($dungeons_data[$dungeoid]['dungeonid']==""){
					if($hidevalue==2){
						if($dungeon['type']==""){
							$dungeons_data[$dungeoid]['dungeonid'] = $dungeoid;
							$dungeons_data[$dungeoid]['type'] = $dungeons[$dungeoid]['type'];
							$dungeons_data[$dungeoid]['name'] = $dungeons[$dungeoid]['name'];
							$dungeons_data[$dungeoid]['all_count'] = 0;
							$dungeons_data[$dungeoid]['enter_count'] = 0;
							$dungeons_data[$dungeoid]['all_times']=0;
							$dungeons_data[$dungeoid]['enter_times']=0;
						}
					}else if($hidevalue==3){
						if($dungeon['type']!=""){
							$dungeons_data[$dungeoid]['dungeonid'] = $dungeoid;
							$dungeons_data[$dungeoid]['type'] = $dungeons[$dungeoid]['type'];
							$dungeons_data[$dungeoid]['name'] = $dungeons[$dungeoid]['name'];
							$dungeons_data[$dungeoid]['all_count'] = 0;
							$dungeons_data[$dungeoid]['enter_count'] = 0;
							$dungeons_data[$dungeoid]['all_times']=0;
							$dungeons_data[$dungeoid]['enter_times']=0;
						}
					}else{
						$dungeons_data[$dungeoid]['dungeonid'] = $dungeoid;
						$dungeons_data[$dungeoid]['type'] = $dungeons[$dungeoid]['type'];
						$dungeons_data[$dungeoid]['name'] = $dungeons[$dungeoid]['name'];
						$dungeons_data[$dungeoid]['all_count'] = 0;
						$dungeons_data[$dungeoid]['enter_count'] = 0;
						$dungeons_data[$dungeoid]['all_times']=0;
						$dungeons_data[$dungeoid]['enter_times']=0;
					}
				}
				next($dungeons);
		}

		foreach($all_times as $tmp){
			foreach($tmp as $clean){
				if($dungeons[$clean['dungeonid']]['name']!=""){
					if($hidevalue==2){
						if($dungeons[$clean['dungeonid']]['type']==""){
							$dungeons_data[$clean['dungeonid']]['dungeonid']=$clean['dungeonid'];
							$dungeons_data[$clean['dungeonid']]['name']=$dungeons[$clean['dungeonid']]['name'];
							$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
							$dungeons_data[$clean['dungeonid']]['all_times']=$clean['all_times'];
							$dungeons_data[$clean['dungeonid']]['enter_times']=$clean['enter_times'];
						}
					}else if($hidevalue==3){
						if($dungeons[$clean['dungeonid']]['type']!=""){
							$dungeons_data[$clean['dungeonid']]['dungeonid']=$clean['dungeonid'];
							$dungeons_data[$clean['dungeonid']]['name']=$dungeons[$clean['dungeonid']]['name'];
							$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
							$dungeons_data[$clean['dungeonid']]['all_times']=$clean['all_times'];
							$dungeons_data[$clean['dungeonid']]['enter_times']=$clean['enter_times'];
						}
					}else{
						$dungeons_data[$clean['dungeonid']]['dungeonid']=$clean['dungeonid'];
						$dungeons_data[$clean['dungeonid']]['name']=$dungeons[$clean['dungeonid']]['name'];
						$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
						$dungeons_data[$clean['dungeonid']]['all_times']=$clean['all_times'];
						$dungeons_data[$clean['dungeonid']]['enter_times']=$clean['enter_times'];
					}
				}
			}
		}

		foreach($all_enter_hero as $tmp){
			foreach($tmp as $clean){
				if($dungeons[$clean['dungeonid']]['name']!="") {
					if($hidevalue==2){
						if($dungeons[$clean['dungeonid']]['type']==""){
							$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
							$dungeons_data[$clean['dungeonid']]['dungeonid'] = $clean['dungeonid'];
							$dungeons_data[$clean['dungeonid']]['name'] = $dungeons[$clean['dungeonid']]['name'];
							$dungeons_data[$clean['dungeonid']]['enter_count'] = $clean['enter_count'];
						}
					}else if($hidevalue==3){
						if($dungeons[$clean['dungeonid']]['type']!=""){
							$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
							$dungeons_data[$clean['dungeonid']]['dungeonid'] = $clean['dungeonid'];
							$dungeons_data[$clean['dungeonid']]['name'] = $dungeons[$clean['dungeonid']]['name'];
							$dungeons_data[$clean['dungeonid']]['enter_count'] = $clean['enter_count'];
						}
					}else{
						$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
						$dungeons_data[$clean['dungeonid']]['dungeonid'] = $clean['dungeonid'];
						$dungeons_data[$clean['dungeonid']]['name'] = $dungeons[$clean['dungeonid']]['name'];
						$dungeons_data[$clean['dungeonid']]['enter_count'] = $clean['enter_count'];
					}
				}
			}
		}

		foreach($all_hero as $tmp){
			foreach($tmp as $clean){
				if($dungeons[$clean['dungeonid']]['name']!="") {
					if($hidevalue==2){
						if($dungeons[$clean['dungeonid']]['type']==""){
							$dungeons_data[$clean['dungeonid']]['dungeonid'] = $clean['dungeonid'];
							$dungeons_data[$clean['dungeonid']]['name'] = $dungeons[$clean['dungeonid']]['name'];
							$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
							$dungeons_data[$clean['dungeonid']]['all_count'] = $clean['all_count'];
						}
					}else if($hidevalue==3){
						if($dungeons[$clean['dungeonid']]['type']!=""){
							$dungeons_data[$clean['dungeonid']]['dungeonid'] = $clean['dungeonid'];
							$dungeons_data[$clean['dungeonid']]['name'] = $dungeons[$clean['dungeonid']]['name'];
							$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
							$dungeons_data[$clean['dungeonid']]['all_count'] = $clean['all_count'];
						}
					}else{
						$dungeons_data[$clean['dungeonid']]['dungeonid'] = $clean['dungeonid'];
						$dungeons_data[$clean['dungeonid']]['name'] = $dungeons[$clean['dungeonid']]['name'];
						$dungeons_data[$clean['dungeonid']]['type']=$dungeons[$clean['dungeonid']]['type'];
						$dungeons_data[$clean['dungeonid']]['all_count'] = $clean['all_count'];
					}
				}
			}
		}

		$this->assign ( 'choose_servers', $values );
		$this->assign ( 'servers', $servers );
    $this->assign ( 'dungeons_data', $dungeons_data);
    $this->display ();
    }

    public function online_statistic() {
        $this->menu ();
		$url='/zebra/Home/RegAndOnline/online_statistic';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);
		$check_date = $_POST['check_date'];
		if($_POST['check_date']==""){
			$check_date = date('Y-m-d',time()-86400);
		}
		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		$platforms=$this->get_all_platforms();
		//platforms
		$values=$_POST['checkbox'];
		if(!empty($values)){
			F($url.'values',$values);
		}else{
			$values = F($url.'values');//从缓存获取已选择服务器
		}
		//servers
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}
		$db_user=$this->get_dbuser();

		$datas=array();
		$online_data=array();
		foreach ($choose_servers as $choose_server) {
			$server=$this->get_server_from_id($choose_server);
			if(empty($server)) continue;
			foreach ($values as $value) {
				$db=$this->get_db_from_serverid($server['server_id']);
				if(empty($db)) continue;
				$data= M('statistics_online','', $this->get_gmserver())->where(" platform=".$value." and server_id=".$server['server_id']." and date=UNIX_TIMESTAMP('".$check_date."')")->select();
				array_push($online_data, $data);
			}
		}

		$pie_datas=array();
		foreach($online_data as $online){
			foreach($online as $data){
				$server=$this->get_server_from_id($data['server_id']);
				$datas[$data['platform']][$data['server_id']]['server_name']=$server['server_name'];
				$platform=$this->get_platform($data['platform']);
				$datas[$data['platform']][$data['server_id']]['channel_name']=$platform['name'];
				$datas[$data['platform']][$data['server_id']][$data['time_type']]+=$data['time_num'];
				$pie_datas[$data['time_type']]+=$data['time_num'];
			}
		}

		$this->assign('check_date',$check_date);
		$this->assign ( 'checks', $checks );
		$this->assign ( 'datas', $datas );
		$this->assign('pie_datas',$pie_datas);
		$this->assign ( 'date', $check_date );
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->assign ( 'choose_platforms', $values );
		$this->assign ( 'platforms', $platforms );
        $this->display ();
    }

    public function login_statistic() {
        $this->menu ();
		$url='/zebra/Home/RegAndOnline/login_statistic';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$check_date=$_POST['check_date'];
		if($_POST['check_date']==""){
			$check_date = date('Y-m-d',time()-86400);
		}
		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		$platforms=$this->get_all_platforms();
		//platforms
		$values=$_POST['checkbox'];
		if(!empty($values)){
			F($url.'values',$values);
		}else{
			$values = F($url.'values');//从缓存获取已选择服务器
		}
		//servers
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}
		$db_user=$this->get_dbuser();

		$datas=array();
		$online_data=array();
		foreach ($choose_servers as $choose_server) {
			$server=$this->get_server_from_id($choose_server);
			if(empty($server)) continue;
			foreach ($values as $value) {
				$db=$this->get_db_from_serverid($server['server_id']);
				if(empty($db)) continue;
				$data= M('logines_data','', $this->get_gmserver())->where(" platform=".$value." and server_id=".$server['server_id']." and date=UNIX_TIMESTAMP('".$check_date."')")->select();
				array_push($online_data, $data);
			}
		}

		foreach($online_data as $online) {
			foreach ($online as $data) {
				$server=$this->get_server_from_id($data['server_id']);
				$platform=$this->get_platform($data['platform']);
				// $server=$this->get_server_from_serverid($data['server_id'],$data['platform']);

				$datas[$data['platform']][$data['server_id']][$data['period']]['server_name']=$server['server_name'];
				$datas[$data['platform']][$data['server_id']][$data['period']]['platform_name']=$platform['name'];

				$datas[$data['platform']][$data['server_id']][$data['period']]['period']= $data['period'];
				$datas[$data['platform']][$data['server_id']][$data['period']]['login_times']+= $data['login_times'];
				$datas[$data['platform']][$data['server_id']][$data['period']]['login_num']+= $data['login_num'];
			}
		}


		$this->assign('check_date',$check_date);
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->assign ( 'choose_platforms', $values );
		$this->assign ( 'platforms', $platforms );
		$this->assign ( 'datas', $datas );
		$this->assign ( 'date', $check_date );
		$this->display ();
    }

    public function phone_statistic() {
        $this->menu ();
		$url='/zebra/Home/RegAndOnline/phone_statistic';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);
        $phones=array();

        $this->assign ( 'phones', $phones);
        $this->display ();
    }
}
