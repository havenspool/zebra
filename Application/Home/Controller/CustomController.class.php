<?php

namespace Home\Controller;

use Think\Controller;

class CustomController extends LayoutController {

	public function punish() {
		$this->menu ();
		$url='/zebra/Home/Custom/punish';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$flag=true;//是否惩罚
		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		$platforms=$this->get_all_platforms();
		//servers
		$choose_server=$_POST['checkbox_servers'];
		if($_POST['checkbox_servers']=='') $flag=false;

		$db_user=$this->get_dbuser();

		$heroids = $_POST['punish_heroids'];

		$punish_type = $_POST['punish_type_radio'];//1封号2禁言3下线
		$punish_time = $_POST['punish_time_radio'];
		$fbc_day = $_POST['fbc_day'];
		$fbc_hour = $_POST['fbc_hour'];
		$fbc_min = $_POST['fbc_min'];
		$fbc_sec = $_POST['fbc_sec'];

		$page_size = $_POST['table_report_length'];
		$page=$_POST['hidevalue'];
		if($page<=0){
			$page=1;
		}
		$size=0;  //总页数
		if($page_size<=0){
			$page_size=10;
		}

		$fbc_time=0;//惩罚时长 单位为毫秒,踢他下线时间长设为1ms  forbidType=2
		$type=0;//惩罚时长类型 type=2永久封号 type=1限定时间
		$fbc_type=0;//forbidType=1 禁言 forbidType=2封号并踢他下线
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

		$server=$this->get_server_from_id($choose_server);
		if(empty($server)) $flag=false;
		if($_POST['punish_heroids']=="")  $flag=false;

    if($flag){
			$heroidstr = explode(';',$heroids);
			//每次只能传一个英雄Id
			while(count($punish_list)+count($heroidstr)>50){
				array_shift($punish_list);
			}
			for($index=0;$index<count($heroidstr);$index++){
				$sendData = array (
							"cmd" => "sys/forbid",  //sys/forbid  sys/searchUser
							"user" => "hank",
							"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
							"heroId" => $heroidstr[$index],
							"type" => $type,
							"forbidType" => $fbc_type,
							"forbidTime" => $fbc_time);
				$status = $this->conn_server($server['host'],$server['port'],$sendData);
				//插入记录保存
				// print_r($status);
				if($status!=-1){
					if($type==1) $time=0;
					else $time=$fbc_time;
					$punish=array('heroid'=>$heroidstr[$index],'server_id'=>$choose_server,'type'=>$fbc_type,'time'=>$time,'created'=>time(),'username'=>$this->getUsername());
					M('punlist_record','', $this->get_gmserver())->add($punish);
				}
			}
		}

		$punish_list = array();
		$punish_size = M ('punlist_record', '', $this->get_gmserver())->query("SELECT count(*) size FROM punlist_record");
		if(count($punish_size)>0){
			$size=$punish_size[0]['size'];
		}
		$punishs = M ('punlist_record', '', $this->get_gmserver())->query("SELECT * FROM punlist_record order by created desc LIMIT ".(($page-1)*$page_size)." , ".$page_size);
		if(count($punishs)>0){
			$index=1;
			foreach ($punishs as $punish) {
				$tmp_server=$this->get_server_from_id($punish['server_id']);
				if(!empty($tmp_server)) {
					$punish_list[$index]['server_id']=$punish['server_id'];
					$punish_list[$index]['server_name']=$tmp_server['server_name'];
				}
				$db=$this->get_db_from_serverid($punish['server_id']);
				if(!empty($db)) {
					$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
						$hero= M ('heroes', '', $db_hero)->query("SELECT name FROM heroes where id=".$punish['heroid']);
						if(count($hero)>0){
							$punish_list[$index]['heroname']=$hero[0]['name'];
						}
				}
				$punish_list[$index]['heroid']=$punish['heroid'];
				if($punish['type']==1){
					$punish_list[$index]['type']="封号";
				}else if($punish['type']==2){
					$punish_list[$index]['type']="禁言";
				}else if($punish['type']==3){
					$punish_list[$index]['type']="下线";
				}
				if($punish['time']==0){
					$punish_list[$index]['time']='永久';
				}else{
					$punish_list[$index]['time']=$punish['time'].'秒';
				}
				$punish_list[$index]['username']=$punish['username'];
				$punish_list[$index]['created']=date("Y-m-d H:i:s", $punish['created']);
				$index++;
			}
		}

		$db=$this->get_db_from_serverid($choose_server);
		if(!empty($db)) {
			$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
			foreach ($punish_list as $punish) {
				$index=1;
				$hero= M ('heroes', '', $db_hero)->query("SELECT name FROM heroes where id=".$punish['heroid']);
				if(count($hero)>0){
					$punish_list[$index]['heroname']=$hero[0]['name'];
				}
				$index++;
			}
		}

		// $choose_servers=array();
		// array_push($choose_servers, $choose_server);
		$this->assign ( 'punish_list', $punish_list );
		// $this->assign ( 'choose_servers',$choose_servers);
		$this->assign ( 'servers', $servers );

		$this->assign ( 'page_size', $page_size );
		$this->assign ( 'page', $page );
		$this->assign ( 'size', $size );
		$this->display ();
	}

	public function relieve() {
		$this->menu ();
		$url='/zebra/Home/Custom/relieve';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$flag=true;//是否惩罚
		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		$platforms=$this->get_all_platforms();
		//servers
		$choose_server=$_POST['checkbox_servers'];
		if($_POST['checkbox_servers']=='') $flag=false;

		$db_user=$this->get_dbuser();

		$page_size = $_POST['table_report_length'];
		$page=$_POST['hidevalue'];
		if($page<=0){
			$page=1;
		}
		$size=0;  //总页数
		if($page_size<=0){
			$page_size=10;
		}

		$heroids = $_POST['punish_heroids'];

		$punish_type = $_POST['punish_type_radio'];//1封号2禁言3下线

		$fbc_time=1;//解除惩罚把时长设为1ms,惩罚时长 单位为毫秒,踢他下线时间长设为1ms  forbidType=2
		$type=0;//惩罚时长类型 type=2永久封号 type=1限定时间
		$fbc_type=1;//forbidType=1 禁言 forbidType=2封号并踢他下线
// [type] => 2 [forbidType] => 1 [forbidTime] => 1  解除封号
//[type] => 1 [forbidType] => 1 [forbidTime] => 1  解除禁言

		if($punish_type==1){
			$type=2;
		}elseif ($punish_type==2) {
			$type=1;
		}

		$relieve_list = F('relieve_list');//从缓存获取数据
		if(empty($relieve_list)){
			$relieve_list=array();
		}
		$server=$this->get_server_from_id($choose_server);
		if(empty($server)) $flag=false;
		if($_POST['punish_heroids']=="")  $flag=false;
		if($flag){
			$heroidstr = explode(';',$heroids);
			//每次只能传一个英雄Id
			while(count($relieve_list)+count($heroidstr)>50){
				array_shift($relieve_list);
			}
			for($index=0;$index<count($heroidstr);$index++){
				$relieve=array();
				$sendData = array (
							"cmd" => "sys/forbid",  //sys/forbid  sys/searchUser
							"user" => "hank",
							"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
							"heroId" => $heroidstr[$index],
							"type" => $type,
							"forbidType" => $fbc_type,
							"forbidTime" => $fbc_time);
				$status = $this->conn_server($server['host'],$server['port'],$sendData);
				//插入记录保存
				// print_r($status);
				if($status!=-1){
					$relieve=array('heroid'=>$heroidstr[$index],'server_id'=>$choose_server,'type'=>$fbc_type,'created'=>time(),'username'=>$this->getUsername());
					 M('relieve_record','', $this->get_gmserver())->add($relieve);
				}
			}
		}

		$relieve_list = array();
		$relieve_size = M ('relieve_record', '', $this->get_gmserver())->query("SELECT count(*) size FROM relieve_record");
		if(count($relieve_size)>0){
			$size=$relieve_size[0]['size'];
		}
		$relieve_records = M ('relieve_record', '', $this->get_gmserver())->query("SELECT * FROM relieve_record order by created desc LIMIT ".(($page-1)*$page_size)." , ".$page_size);
		if(count($relieve_records)>0){
			$index=1;
			foreach ($relieve_records as $relieve) {
				$tmp_server=$this->get_server_from_id($relieve['server_id']);
				if(!empty($tmp_server)) {
					$relieve_list[$index]['server_id']=$relieve['server_id'];
					$relieve_list[$index]['server_name']=$tmp_server['server_name'];
				}
				$db=$this->get_db_from_serverid($relieve['server_id']);
				if(!empty($db)) {
					$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
						$hero= M ('heroes', '', $db_hero)->query("SELECT name FROM heroes where id=".$relieve['heroid']);
						if(count($hero)>0){
							$relieve_list[$index]['heroname']=$hero[0]['name'];
						}
				}
				$relieve_list[$index]['heroid']=$relieve['heroid'];
				if($relieve['type']==1){
					$relieve_list[$index]['type']="封号";
				}else if($relieve['type']==2){
					$relieve_list[$index]['type']="禁言";
				}
				$relieve_list[$index]['username']=$relieve['username'];
				$relieve_list[$index]['created']=date("Y-m-d H:i:s", $relieve['created']);
				$index++;
			}
		}

		$db=$this->get_db_from_serverid($choose_server);
		if(!empty($db)) {
			$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
			foreach ($relieve_list as $relieve) {
				$index=1;
				$hero= M ('heroes', '', $db_hero)->query("SELECT name FROM heroes where id=".$relieve['heroid']);
				if(count($hero)>0){
					$relieve_list[$index]['heroname']=$hero[0]['name'];
				}
				$index++;
			}
		}

		// $choose_servers=array();
		// array_push($choose_servers, $choose_server);
		$this->assign ( 'relieve_list', $relieve_list );
		// $this->assign ( 'choose_servers',$choose_servers);
		$this->assign ( 'servers', $servers );

		$this->assign ( 'page_size', $page_size );
		$this->assign ( 'page', $page );
		$this->assign ( 'size', $size );
		$this->display ();
	}

	public function punish_query() {
		$this->menu ();
		$url='/zebra/Home/Custom/punish_query';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$servers=$this->get_all_server();
		//servers
		$choose_server=$_POST['checkbox_servers'];
		if(!empty($choose_server)){
			F($url.'choose_server',$choose_server);
		}else{
			$choose_server = F($url.'choose_server');//从缓存获取已选择服务器
		}

		$heroids = $_POST['punish_heroids'];

		$heroidstr = explode(';',$heroids);

		$punish_list=array();
		$server=$this->get_server_from_id($choose_server);

		if(!empty($server)&&!$_POST['punish_heroids']==""){
			for($index=0;$index<count($heroidstr);$index++){
				$punish=array();
				$sendData = array (
								"cmd" => "sys/searchUser",  //sys/forbid  sys/searchUser
								"user" => "hank",
								"heroId" => $heroidstr[$index],
								"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf");
				$status = $this->conn_server($server['host'],$server['port'],$sendData);
				$punish['hero_id']=$heroidstr[$index];
				$punish['flog']=$status['forbid']['flog'];
				$punish['flogTime']=$status['forbid']['flogTime']/1000;
				$punish['fbc']=$status['forbid']['fbc'];
				$punish['fbcTime']=$status['forbid']['fbcTime']/1000;
				array_push($punish_list, $punish);
			}
		}

		$choose_servers=array();
		array_push($choose_servers, $choose_server);
		$this->assign ( 'choose_servers',$choose_servers);
		$this->assign ( 'servers', $servers );
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

		$servers=$this->get_all_server();
		//servers
		$choose_server=$_POST['checkbox_servers'];
		if(!empty($choose_server)){
			F($url.'choose_server',$choose_server);
		}else{
			$choose_server = F($url.'choose_server');//从缓存获取已选择服务器
		}
		$page_size = $_POST['table_report_length'];
		$page=$_POST['hidevalue'];
		if($page<=0){
			$page=1;
		}
		$size=0;  //总页数
		if($page_size<=0){
			$page_size=10;
		}

		$heroids = $_POST['delete_hero_heroids'];
		$flag=true;
		if($_POST['delete_hero_heroids']=="")  $flag=false;
		$heroidstr = explode(';',$heroids);

		$server=$this->get_server_from_id($choose_server);
		if(!empty($server)&&$flag){
			for($index=0;$index<count($heroidstr);$index++){
				$sendData = array (
								"cmd" => "sys/delete_hero",
								"user" => "hank",
								"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
								"heroId" => $heroidstr[$index]);
				$status = $this->conn_server($server['host'],$server['port'],$sendData);
				if($status!=-1){
					$delete=array('heroid'=>$heroidstr[$index],'server_id'=>$choose_server,'created'=>time(),'username'=>$this->getUsername());
					$Db = M('delete_heroes','', $this->get_gmserver());
					$db_=$Db->where(' heroid='.$heroidstr[$index]." and server_id=".$choose_server)->select();
					if(empty($db_)){
						$Db->add($delete);
					}
					M('delete_heroes','', $this->get_gmserver())->add($delete);
				}
			}
		}

		$delete_hero_list=array();
		$delete_size = M ('delete_heroes', '', $this->get_gmserver())->query("SELECT count(*) size FROM delete_heroes");
		if(count($delete_size)>0){
			$size=$delete_size[0]['size'];
		}
		$delete_heroes = M ('delete_heroes', '', $this->get_gmserver())->query("SELECT * FROM delete_heroes order by created desc LIMIT ".(($page-1)*$page_size)." , ".$page_size);
		if(count($delete_heroes)>0){
			$index=1;
			foreach ($delete_heroes as $delete_hero) {
				$tmp_server=$this->get_server_from_id($delete_hero['server_id']);
				if(!empty($tmp_server)) {
					$delete_hero_list[$index]['server_id']=$delete_hero['server_id'];
					$delete_hero_list[$index]['server_name']=$tmp_server['server_name'];
				}
				$db=$this->get_db_from_serverid($delete_hero['server_id']);
				if(!empty($db)) {
					$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
						$hero= M ('heroes', '', $db_hero)->query("SELECT name FROM heroes where id=".$delete_hero['heroid']);
						if(count($hero)>0){
							$delete_hero_list[$index]['heroname']=$hero[0]['name'];
						}
				}
				$delete_hero_list[$index]['username']=$delete_hero['username'];
				$delete_hero_list[$index]['heroid']=$delete_hero['heroid'];
				$delete_hero_list[$index]['created']=date("Y-m-d H:i:s", $delete_hero['created']);
				$index++;
			}
		}

		$db=$this->get_db_from_serverid($choose_server);
		if(!empty($db)) {
			$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
			foreach ($delete_hero_list as $delete_hero) {
				$index=1;
				$hero= M ('heroes', '', $db_hero)->query("SELECT name FROM heroes where id=".$delete_hero['heroid']);
				if(count($hero)>0){
					$delete_hero_list[$index]['heroname']=$hero[0]['name'];
				}
				$index++;
			}
		}

		$choose_servers=array();
		array_push($choose_servers, $choose_server);
		$this->assign ( 'choose_servers',$choose_servers);
		$this->assign ( 'servers', $servers );
		$this->assign ( 'delete_hero_list', $delete_hero_list );

		$this->assign ( 'page_size', $page_size );
		$this->assign ( 'page', $page );
		$this->assign ( 'size', $size );
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
