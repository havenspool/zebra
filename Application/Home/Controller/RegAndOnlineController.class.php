<?php

namespace Home\Controller;

use Think\Controller;

class RegAndOnlineController extends LayoutController {

	public function hero_online() {
		$this->menu ();
		$this->assign ( 'title', '实时在线' );
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/hero_online' );

		$servers=$this->get_all_server();
		$sendData = array (
						"cmd" => "sys/status",
						"user" => "hank",
						"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf");
		$status=array();
		foreach($servers as $server){
			if($this->is_choose_server($server['id'])==1){
				$stat=$this->conn_server($server['host'],$server['port'],$sendData);
				if($stat['result']==-1){
					print_r("未能连接上游戏服");
					$server['onlines']="未能连接上游戏服";
				}else{
					$server['onlines']=$stat['onlines'];

					$server['freepkNum']=$stat['freepkNum'];
					$server['sceneNum']=$stat['sceneNum'];
					$server['bossNum']=$stat['bossNum'];
					$server['pkNum']=$stat['pkNum'];
					$server['dungeonNum']=$stat['dungeonNum'];
					$server['resDungeonNum']=$stat['resDungeonNum'];
					$server['elitDungeonNum']=$stat['elitDungeonNum'];
					$server['towerNum']=$stat['towerNum'];
					$server['offlinepkNum']=$stat['offlinepkNum'];
				}
				array_push($status, $server);
			}else{
				$server['onlines']="未选择";
				array_push($status, $server);
			}
		}

		$datas=array();
		foreach($status as $stat){
			$datas['freepkNum']+=$stat['freepkNum'];
			$datas['sceneNum']+=$stat['sceneNum'];
			$datas['bossNum']+=$stat['bossNum'];
			$datas['pkNum']+=$stat['pkNum'];
			$datas['dungeonNum']+=$stat['dungeonNum'];
			$datas['resDungeonNum']+=$stat['resDungeonNum'];
			$datas['elitDungeonNum']+=$stat['elitDungeonNum'];
			$datas['towerNum']+=$stat['towerNum'];
			$datas['offlinepkNum']+=$stat['offlinepkNum'];
		}
		$this->assign ( 'datas', $datas );
		$this->assign ( 'status', $status );
		$this->display ();
	}

    public function new_user() {
        $this->menu ();
        $this->assign ( 'title', '新增玩家' );
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/new_user' );

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if($_POST['start_date']==""){
            $start_date = date('Y-m-d',time()-3600*24*10);
        }

        if($_POST['end_date']==""){
            $end_date = date('Y-m-d',time()+86400);
        }

		$servers=$this->get_all_server();
		$values=$_POST['checkbox'];
		$checks=$this->choose_server($values);

        $stats=array();
        $users=array();
		foreach ($checks as $check){
			$db=$this->get_db_from_id($check['plat_id']);
			if(empty($db)) continue;
			$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
			$db_user="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_user'];
			//channel
			$server=$this->get_server_from_id($check['plat_id']);

			$stat = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role,0 type from heroes_stat s,heroes h where h.platform=".$server['channel']." and s.heroid=h.id and  s.regTime >= UNIX_TIMESTAMP('".$start_date."') and s.regTime <= UNIX_TIMESTAMP('".$end_date."') group by day" );
			$user = M ( 'users', '',  $db_user)->query ( "select FROM_UNIXTIME(UNIX_TIMESTAMP(h.created), '%Y-%m-%d') day,count(h.id) user from users h where platform=".$server['channel']." and UNIX_TIMESTAMP(h.created) >= UNIX_TIMESTAMP('".$start_date."') and UNIX_TIMESTAMP(h.created) <= UNIX_TIMESTAMP('".$end_date."') group by day" );
			array_push($stats, $stat);
			array_push($users, $user);
			$stat = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role,h.type type from heroes_stat s,heroes h where h.platform=".$server['channel']." and s.heroid=h.id and h.type=1 and  s.regTime >= UNIX_TIMESTAMP('".$start_date."') and s.regTime <= UNIX_TIMESTAMP('".$end_date."') group by day" );
			array_push($stats, $stat);
			$stat = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role,h.type type from heroes_stat s,heroes h where h.platform=".$server['channel']." and s.heroid=h.id and h.type=2 and s.regTime >= UNIX_TIMESTAMP('".$start_date."') and s.regTime <= UNIX_TIMESTAMP('".$end_date."') group by day" );
			array_push($stats, $stat);
			$stat = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role,h.type type from heroes_stat s,heroes h where h.platform=".$server['channel']." and s.heroid=h.id and h.type=3 and s.regTime >= UNIX_TIMESTAMP('".$start_date."') and s.regTime <= UNIX_TIMESTAMP('".$end_date."') group by day" );
			array_push($stats, $stat);
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
		$this->assign ( 'checks', $checks );
        $this->assign ( 'servers', $servers );
        $this->display ();
    }

	public function hero_retention() {
		$this->menu();
		$this->assign('title','角色留存');
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/hero_retention' );

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
		}
		if($_POST['end_date']==""){
			$end_date = date('Y-m-d',time());
		}

		$servers=$this->get_all_server();
		$values=$_POST['checkbox'];
		$checks=$this->choose_server($values);

		$dates=array();
		$all_hero_datas=array();
		$days=$this->get_retention_day();

		foreach ($checks as $check){
			$server=$this->get_server_from_id($check['plat_id']);
			for($tmp_date=strtotime($start_date);$tmp_date<=strtotime($end_date);$tmp_date+=86400){
				foreach($days as $day){
					$heroes_data= M('heroes_data','', $this->get_gmserver())->where(" platform=".$server['channel']." and reg_date = ".$tmp_date." and login_date = ".($tmp_date+$day*86400))->select();
					if(count($heroes_data)>0)
						array_push($all_hero_datas, $heroes_data);
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
		$this->assign ( 'checks', $checks );
		$this->assign ( 'servers', $servers );
		$this->display();
	}

	public function user_retention() {
		$this->menu();
		$this->assign('title','玩家留存');
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/user_retention' );

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];

		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
		}

		if($_POST['end_date']==""){
			$end_date = date('Y-m-d',time());
		}

		$servers=$this->get_all_server();
		$values=$_POST['checkbox'];
		$checks=$this->choose_server($values);

		$dates=array();
		$all_hero_datas=array();
		$days=$this->get_retention_day();

		foreach ($checks as $check){
			$server=$this->get_server_from_id($check['plat_id']);
			for($tmp_date=strtotime($start_date);$tmp_date<=strtotime($end_date);$tmp_date+=86400){
				foreach($days as $day){
					$heroes_data= M('users_data','', $this->get_gmserver())->where(" platform=".$server['channel']." and reg_date = ".$tmp_date." and login_date = ".($tmp_date+$day*86400))->select();
					if(count($heroes_data)>0)
						array_push($all_hero_datas, $heroes_data);
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
		$this->assign ( 'checks', $checks );
		$this->assign ( 'servers', $servers );
		$this->display();
	}

	public function level_statistic() {
		$this->menu ();
		$this->assign ( 'title', '等级分布' );
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/level_statistic' );


		$check_date = $_POST['check_date'];

		if($_POST['check_date']==""){
			$check_date = date('Y-m-d',time());
		}

		$flag=true;
		if($check_date ==date('Y-m-d',time())){
			$flag=false;
		}

		$servers=$this->get_all_server();
		$values=$_POST['checkbox'];
		$checks=$this->choose_server($values);

		$levels = array ();
		$level_all = array ();
		$total=0;
		if($flag){
			//过去某天的角色等级分布
			$hero_data=array();
			foreach ($checks as $check){
				$server=$this->get_server_from_id($check['plat_id']);

				$data= M('level_data','', $this->get_gmserver())->where(" platform=".$server['channel']." and date=UNIX_TIMESTAMP('".$check_date."')")->select();
				array_push($hero_data, $data);
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
			}
		}else{
			//当前角色等级分布
			$hero_data=array();
			foreach ($checks as $check){
				$db=$this->get_db_from_id($check['plat_id']);
				if(empty($db)) continue;
				$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
				//channel
				$server=$this->get_server_from_id($check['plat_id']);

				$DBuser = M ( 'heroes', '',  $db_hero);
				$type1 = $DBuser->query ( "select level,count(id) role1 from game.heroes where type=1 and platform=".$server['channel']." group by level" );
				$type2 = $DBuser->query ( "select level,count(id) role2 from game.heroes where type=2 and platform=".$server['channel']." group by level" );
				$type3 = $DBuser->query ( "select level,count(id) role3 from game.heroes where type=3 and platform=".$server['channel']." group by level" );

				array_push($hero_data, $type1);
				array_push($hero_data, $type2);
				array_push($hero_data, $type3);
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
		$this->assign ( 'checks', $checks );
		$this->assign ( 'servers', $servers );
		$this->display ();
	}

    public function dungeon_statistic() {
        $this->menu ();
        $this->assign ( 'title', '关卡统计' );
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/dungeon_statistic' );

		$servers=$this->get_all_server();
		$values=$_POST['checkbox'];
		$checks=$this->choose_server($values);

		$dungeons_data=array();

		$all_times=array();
		$all_enter_hero=array();
		$all_hero=array();
		foreach ($checks as $check){
			$db=$this->get_db_from_id($check['plat_id']);
			if(empty($db)) continue;
			$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
			//channel
			$server=$this->get_server_from_id($check['plat_id']);

			$DBuser = M ( 'dungeons_clearance', '',  $db_hero);
			$times = $DBuser->query ( "SELECT d.dungeonid dungeonid,count(d.heroid) count,sum(d.all_times) all_times,sum(d.enter_times) enter_times FROM dungeons_clearance d,heroes h where d.heroid=h.id and h.platform=".$server['channel']."  group by d.dungeonid" );
			$enter_hero_times = $DBuser->query ( "SELECT d.dungeonid dungeonid,count(d.heroid) enter_count FROM dungeons_clearance d,heroes h where d.heroid=h.id and d.enter_times>0 and h.platform=".$server['channel']."  group by d.dungeonid" );
			$all_hero_times = $DBuser->query ( "SELECT d.dungeonid dungeonid,count(d.heroid) all_count FROM dungeons_clearance d,heroes h where d.heroid=h.id and d.all_times>0 and h.platform=".$server['channel']."  group by d.dungeonid" );

			array_push($all_times, $times);
			array_push($all_enter_hero, $enter_hero_times);
			array_push($all_hero, $all_hero_times);
		}

		$dungeons=$this->get_Dungeons();
		foreach($all_times as $tmp){
			foreach($tmp as $clean){
				if($dungeons[$clean['dungeonid']]['name']!=""){
					$dungeons_data[$clean['dungeonid']]['dungeonid']=$clean['dungeonid'];
					$dungeons_data[$clean['dungeonid']]['name']=$dungeons[$clean['dungeonid']]['name'];
					$dungeons_data[$clean['dungeonid']]['all_times']=$clean['all_times'];
					$dungeons_data[$clean['dungeonid']]['enter_times']=$clean['enter_times'];
				}
			}
		}

		foreach($all_enter_hero as $tmp){
			foreach($tmp as $clean){
				if($dungeons[$clean['dungeonid']]['name']!="") {
					$dungeons_data[$clean['dungeonid']]['dungeonid'] = $clean['dungeonid'];
					$dungeons_data[$clean['dungeonid']]['name'] = $dungeons[$clean['dungeonid']]['name'];
					$dungeons_data[$clean['dungeonid']]['enter_count'] = $clean['enter_count'];
				}
			}
		}

		foreach($all_hero as $tmp){
			foreach($tmp as $clean){
				if($dungeons[$clean['dungeonid']]['name']!="") {
					$dungeons_data[$clean['dungeonid']]['dungeonid'] = $clean['dungeonid'];
					$dungeons_data[$clean['dungeonid']]['name'] = $dungeons[$clean['dungeonid']]['name'];
					$dungeons_data[$clean['dungeonid']]['all_count'] = $clean['all_count'];
				}
			}
		}

        $this->assign ( 'dungeons_data', $dungeons_data);
        $this->display ();
    }

    public function online_statistic() {
        $this->menu ();
        $this->assign ( 'title', '在线统计' );
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/online_statistic' );

		$check_date = $_POST['check_date'];
		if($_POST['check_date']==""){
			$check_date = date('Y-m-d',time()-86400);
		}

		$servers=$this->get_all_server();
		$datas=array();
		$values=$_POST['checkbox'];
		$checks=$this->choose_server($values);

		$online_data=array();
		foreach ($checks as $check){
			$server=$this->get_server_from_id($check['plat_id']);

			$data= M('statistics_online','', $this->get_gmserver())->where(" platform=".$server['channel']." and server_id=".$server['server_id']." and date=UNIX_TIMESTAMP('".$check_date."')")->select();
			array_push($online_data, $data);
		}

		$pie_datas=array();
		foreach($online_data as $online){
			foreach($online as $data){
				$server=$this->get_server_from_serverid($data['server_id'],$data['platform']);
				$datas[$data['platform']][$data['server_id']]['server_name']=$server['server_name'];
				$datas[$data['platform']][$data['server_id']]['channel_name']=$server['channel_name'];
				$datas[$data['platform']][$data['server_id']][$data['time_type']]+=$data['time_num'];
				$pie_datas[$data['time_type']]+=$data['time_num'];
			}
		}

		$this->assign('check_date',$check_date);
		$this->assign ( 'checks', $checks );
		$this->assign ( 'servers', $servers );
		$this->assign ( 'datas', $datas );
		$this->assign('pie_datas',$pie_datas);
		$this->assign ( 'date', $check_date );
        $this->display ();
    }

    public function login_statistic() {
        $this->menu ();
        $this->assign ( 'title', '登陆统计' );
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/login_statistic' );

		$check_date = $_POST['check_date'];

		if($_POST['check_date']==""){
			$check_date = date('Y-m-d',time()-86400);
		}

		$servers=$this->get_all_server();
		$datas=array();
		$values=$_POST['checkbox'];
		$checks=$this->choose_server($values);

		$dbs=$this->get_db();
		$datas=array();

		$online_data=array();
		foreach ($checks as $check){
			$server=$this->get_server_from_id($check['plat_id']);

			$data= M('logines_data','', $this->get_gmserver())->where(" platform=".$server['channel']." and server_id=".$server['server_id']." and date=UNIX_TIMESTAMP('".$check_date."')")->select();
			array_push($online_data, $data);
		}

		foreach($online_data as $online) {
			foreach ($online as $data) {
				$server=$this->get_server_from_serverid($data['server_id'],$data['platform']);

				$datas[$data['platform']][$data['server_id']][$data['period']]['server_name']=$server['server_name'];
				$datas[$data['platform']][$data['server_id']][$data['period']]['channel_name']=$server['channel_name'];

				$datas[$data['platform']][$data['server_id']][$data['period']]['period']= $data['period'];
				$datas[$data['platform']][$data['server_id']][$data['period']]['login_times']+= $data['login_times'];
				$datas[$data['platform']][$data['server_id']][$data['period']]['login_num']+= $data['login_num'];
			}
		}

		$this->assign('check_date',$check_date);
		$this->assign ( 'checks', $checks );
		$this->assign ( 'servers', $servers );
		$this->assign ( 'datas', $datas );
		$this->assign ( 'date', $check_date );
		$this->display ();
    }

    public function phone_statistic() {
        $this->menu ();
        $this->assign ( 'title', '机型分布' );
		$this->assign ( 'url', '/zebra/Home/RegAndOnline/phone_statistic' );

        $phones=array();

        $this->assign ( 'phones', $phones);
        $this->display ();
    }
}