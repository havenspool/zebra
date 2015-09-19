<?php

namespace Home\Controller;

use Think\Controller;

class OperationController extends LayoutController {

	public function send_mail() {
		$this->menu ();
		$this->assign ( 'title', '点击发送邮件' );
		$url='/zebra/Home/Operation/send_mail';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'active_open_id', 11);
		$this->assign ( 'url', $url);

		$all_heroids = $_POST['all_heroids'];
		$heroids = $_POST['send_mail_heroids'];
		$mids = $_POST['send_mail_mids'];
		$nums = $_POST['send_mail_nums'];
		$title = $_POST['send_mail_title'];
		$content = $_POST['send_mail_content'];

		$heroidsarr = explode(';',$heroids);
		$midsarr = explode(';',$mids);
		$numsarr = explode(';',$nums);

		$send_mail_list = F('send_mail_list');//从缓存获取数据
		if(empty($send_mail_list)){
			$send_mail_list=array();
		}

		$flag=true;
		if($heroids==0&&$all_heroids!="on") $flag=false;
		if($mids==0) $flag=false;
		if($nums==0) $flag=false;
		if($_POST['send_mail_title']=="") $flag=false;
		if($_POST['send_mail_content']=="") $flag=false;

		$servers=$this->get_all_server();
		$db_gm=$this->get_gmserver();
		//servers
		$choose_servers=$_POST['checkbox_servers'];
		if(empty($choose_servers)) $flag=false;
		$server=$this->get_server_from_id($choose_servers[0]);
		if(empty($server)) $flag=false;

		if($flag){
			while(count($send_mail_list)+count($heroidsarr)>50){
				array_shift($send_mail_list);
			}
			//全服或指定英雄
			if($all_heroids=="on"){
				$send_mail=array();
				$sendData = array (
					"cmd" => "sys/sendMail",
					"user" => "hank",
					"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
					"heroId" => 0,
					"rewardTitle" => $title,
					"rewardContent" => $content,
					"rewardId" => trim($midsarr[0]),
					"rewardNum" => trim($numsarr[0]),
					"attach2" => trim($midsarr[1]),
					"num2" => trim($numsarr[1]),
					"attach3" => trim($midsarr[2]),
					"num3" => trim($numsarr[2]),
					"attach4" => trim($midsarr[3]),
					"num4" => trim($numsarr[3]),
					"attach5" => trim($midsarr[4]),
					"num5" => trim($numsarr[4])
				);
				$status=array();
				$this->conn_server($server['host'],$server['port'],$sendData);
				$status['name']=$status['name'].$server['server_name'].",";

				$send_mail['hero_id']=" 游戏服: ".$status['name']." 全服发送 ";
				if($midsarr[0]!=0&&$numsarr[0]!=0)	$send_mail['mid1']="物品MID:".$midsarr[0]." 数量".$numsarr[0];
				if($midsarr[1]!=0&&$numsarr[1]!=0)	$send_mail['mid2']="物品MID:".$midsarr[1]." 数量".$numsarr[1];
				if($midsarr[2]!=0&&$numsarr[2]!=0)	$send_mail['mid3']="物品MID:".$midsarr[2]." 数量".$numsarr[2];
				if($midsarr[3]!=0&&$numsarr[3]!=0)	$send_mail['mid4']="物品MID:".$midsarr[3]." 数量".$numsarr[3];
				if($midsarr[4]!=0&&$numsarr[4]!=0)	$send_mail['mid5']="物品MID:".$midsarr[4]." 数量".$numsarr[4];
				$send_mail['mail']="　邮件标题:".$title." 邮件内容:".$content;
				$send_mail['time']=date ( 'Y-m-d H:i:s', time ());

				array_push($send_mail_list, $send_mail);
			}else{
				for($index=0;$index<count($heroidsarr);$index++){
					$send_mail=array();
					$sendData = array (
						"cmd" => "sys/sendMail",
						"user" => "hank",
						"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
						"heroId" => trim($heroidsarr[$index]),
						"rewardTitle" => $title,
						"rewardContent" => $content,
						"rewardId" => trim($midsarr[0]),
						"rewardNum" => trim($numsarr[0]),
						"attach2" => trim($midsarr[1]),
						"num2" => trim($numsarr[1]),
						"attach3" => trim($midsarr[2]),
						"num3" => trim($numsarr[2]),
						"attach4" => trim($midsarr[3]),
						"num4" => trim($numsarr[3]),
						"attach5" => trim($midsarr[4]),
						"num5" => trim($numsarr[4])
					);

					$this->conn_server($server['host'],$server['port'],$sendData);

					$send_mail['hero_id']=" 英雄UID: ".$heroidsarr[$index];
					//$send_mail['hero_id']=$heroidsarr[$index];
					if($midsarr[0]!=0&&$numsarr[0]!=0)	$send_mail['mid1']="物品MID:".$midsarr[0]." 数量".$numsarr[0];
					if($midsarr[1]!=0&&$numsarr[1]!=0)	$send_mail['mid2']="物品MID:".$midsarr[1]." 数量".$numsarr[1];
					if($midsarr[2]!=0&&$numsarr[2]!=0)	$send_mail['mid3']="物品MID:".$midsarr[2]." 数量".$numsarr[2];
					if($midsarr[3]!=0&&$numsarr[3]!=0)	$send_mail['mid4']="物品MID:".$midsarr[3]." 数量".$numsarr[3];
					if($midsarr[4]!=0&&$numsarr[4]!=0)	$send_mail['mid5']="物品MID:".$midsarr[4]." 数量".$numsarr[4];
					$send_mail['mail']="　邮件标题:".$title." 邮件内容:".$content;
					$send_mail['time']=date ( 'Y-m-d H:i:s', time ());

					array_push($send_mail_list, $send_mail);
				}
			}

		}

		F('send_mail_list',$send_mail_list);//保存数据到缓存
		//$this->assign ('send_mail_list', $send_mail_list );
		if($flag) header("Location:/zebra/Home/Operation/send_mail_list");
		$this->assign ( 'choose_servers', $values );
		$this->assign ( 'servers', $servers );
		$this->display ();
    }

    public function send_mail_list() {
		$this->menu ();
		$url='/zebra/Home/Operation/send_mail_list';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$send_mail_list = F('send_mail_list');//从缓存获取数据
			if(empty($send_mail_list)){
				$send_mail_list=array();
		}
		$this->assign ('send_mail_list', $send_mail_list );
		$this->display ();
    }

	public function send_board() {
		$this->menu ();
		$url='/zebra/Home/Operation/send_board';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$flag=true;
		$content = $_POST['send_board_content'];
		if($content=="") $flag=false;

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		if($start_date==0) $start_date=date('Y-m-d',time());
		if($end_date==0) $end_date=date('Y-m-d',time());

		$start_hour = $_POST['start_hour'];
		$start_min = $_POST['start_min'];
		$start_sec = $_POST['start_sec'];
		if($start_hour==0) $start_hour=0;
		if($start_min==0) $start_min=0;
		if($start_sec==0) $start_sec=0;

		$end_hour = $_POST['end_hour'];
		$end_min = $_POST['end_min'];
		$end_sec = $_POST['end_sec'];
		if($end_hour==0) $end_hour=0;
		if($end_min==0) $end_min=0;
		if($end_sec==0) $end_sec=0;

		//if($start_date==$end_date&&$start_hour==$end_hour&&$start_min==$end_min&&$start_sec==$end_sec) $flag=false;

		$fqc_day = $_POST['fqc_day'];
		$fqc_hour = $_POST['fqc_hour'];
		$fqc_min = $_POST['fqc_min'];
		$fqc_sec = $_POST['fqc_sec'];
		if($fqc_day==0) $fqc_day=0;
		if($fqc_hour==0) $fqc_hour=0;
		if($fqc_min==0) $fqc_min=0;
		if($fqc_sec==0) $fqc_sec=0;

		$times=0;
		$fqc_time=$fqc_day*86400+$fqc_hour*3600+$fqc_min*60+$fqc_sec;
		if($fqc_day==0&&$fqc_hour==0&&$fqc_min==0&&$fqc_sec==0) $times=1;

		$start=strtotime($start_date." ".$start_hour.":".$start_min.":".$start_sec);
		$end=strtotime($end_date." ".$end_hour.":".$end_min.":".$end_sec);

		if($end<time()) $flag=false;
		if($start==0&&$end==0) $flag=false;
		if($start!=0&&$end!=0) $times=floor(($end-$start)/$fqc_time);

//		echo $end-$start.":".$fqc_time;
//		echo "flag:".$flag.":".$times;
		if($flag&&$times>0){
			//加入定时发送
			$sendData = array (
							"cmd" => "sys/announcement",
							"user" => "hank",
							"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
							"comment" => $content);
			$this->create_process($start,$end,$fqc_time,$sendData,$times);

//			$url='http://localhost/app/zebra_timer.php';
//			$post_data = array ("start" => $start,"end" => $end,"_times" => $times
//						,"interval" => $fqc_time,"host" => $this->get_host(),"port" => $this->get_port(),"comment" => $content);
			//$this->curl_get($url,$post_data);
		}

		if($flag&&$times>0) header("Location:/zebra/Home/Operation/send_board_list");
		$this->display ();
	}

	function curl_get($url,$post_data){
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_exec($ch);
		curl_close($ch);
	}

	public function send_board_online() {
		$this->menu ();
		$this->assign ( 'title', '公告发布记录' );
		$url='/zebra/Home/Operation/send_board_online';
		$this->assign ( 'active_open_id', 11);
		$this->assign ( 'url', $url);

		$flag=true;
		$content = $_POST['send_board_online_content'];
		if($content=="") $flag=false;

		$servers=$this->get_all_server();
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}
		if(empty($choose_servers)) $flag=false;
		if($flag){
			$sendData = array (
							"cmd" => "sys/announcement",
							"user" => "hank",
							"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
							"comment" => $content);
			$status=array();
			foreach ($choose_servers as $value) {
				// $value as server_id
				$server=$this->get_server_from_id($value);
				if(empty($server)) continue;
				$stat=$this->conn_server($server['host'],$server['port'],$sendData);
				$stat['name']=$server['server_name'];
				array_push($status, $stat);
			}

			$result=array();
			foreach($status as $stat){
				$result['onlines']+=$stat['onlines'];
				$result['name']=$result['name'].$stat['name'].",";
			}
			$result['comment']=$content;
			//2015-06-30 15:36:03:游戏服：平台_游戏服 总在线人数：2 内容:hello,test;
			//把发布放入缓存
			$send_board_list = F('send_board_list');//从缓存获取数据
			if(empty($send_board_list)){
				$send_board_list=array();
			}
			while(count($send_board_list)+1>50){
				array_shift($send_board_list);
			}
			$send_board=array();
			$send_board['comment']=$result['comment'];
			$send_board['onlines']=$result['onlines'];
			$send_board['name']=$result['name'];
			$send_board['time']=date ( 'Y-m-d H:i:s', time ());
			array_push($send_board_list, $send_board);
			F('send_board_list',$send_board_list);//保存数据到缓存
		}
		if($flag) header("Location:/zebra/Home/Operation/send_board_list");
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->display ();
	}

	public function send_board_list() {
		$this->menu ();
		$url='/zebra/Home/Operation/send_board_list';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$send_board_list = F('send_board_list');//从缓存获取数据
		if(empty($send_board_list)){
			$send_board_list=array();
		}
		$this->assign ('send_board_list', $send_board_list );
		$this->display ();
	}

	public function create_process($start,$end,$interval,$sendData,$times){
		$pid = pcntl_fork();
		$this->assign ('send_board_ing', "公告发布中..." );
		$this->assign ('send_board_content', "公告内容: ".$sendData['comment'] );
		$this->display ();

		if($pid == -1){
			 //创建失败
			 die('could not fork');
		}else{

			if($pid){
				//从这里开始写的代码是父进程的
				pcntl_wait($status); //等待子进程中断，防止子进程成为僵尸进程。
				//return;
				exit(0);
			}else{
				//子进程代码，为防止不停的启用子进程造成系统资源被耗尽的情况，一般子进程代码运行完成后，加入exit来确保子进程正常退出。
				//$this->get_task($start,$end,$interval,$sendData,$times);
				sleep(5);
				//echo "child";
				exit(0);
			}
		}
    }

	public function get_task($start,$end,$interval,$sendData,$times){
		//$start开始时间 $end结束时间 $interval每隔*秒运行
		$is_task=true;
		ignore_user_abort(); //关掉浏览器，PHP脚本也可以继续执行.
		set_time_limit($end-$start); // 通过set_time_limit(0)可以让程序无限制的执行下去，如果为大于零的数字，则不管程序是否执行完成，到了设定的秒数，程序结束。

		if(time()<$start){
			sleep(time()-$start); // 等待$time-$start秒钟
		}
		if(time()>$end){
			$is_task=false; // 等待$time-$start秒钟
		}
		$count=0;
		while($is_task&&$count<=$times) {
			$time=time();
			if($time>$start&&$time<$end){
				//这里是你要执行的代码
				$status = $this->conn_server($sendData);
				//把发布放入缓存
				$send_board_list = F('send_board_list');//从缓存获取数据
				if(empty($send_board_list)){
					$send_board_list=array();
				}
				while(count($send_board_list)+1>20){
					array_shift($send_board_list);
				}
				$send_board=array();
				$send_board['comment']=$sendData['comment'];
				$send_board['onlines']=$status['onlines'];
				$send_board['time']=date ( 'Y-m-d H:i:s', time ());
				array_push($send_board_list, $send_board);
				F('send_board_list',$send_board_list);//保存数据到缓存
				$count++;
			}
			sleep($interval); // 等待*秒钟
		}
	}

  public function currency_analysis() {
    $this->menu ();
		$url='/zebra/Home/Operation/currency_analysis';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];

		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
		}
		if($_POST['end_date']==""){
			$end_date = date('Y-m-d',time());
		}

		$currency_type = $_POST['currency_radio'];
		if($_POST['currency_radio']=='') $currency_type=0;

		$servers=$this->get_all_server();
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}

		$source_types=array();
		$use_types=array();
		$dates=array();
		$periods=array();

		$currency_datas=array();
		foreach ($choose_servers as $value) {
			// $value as server_id
			$server=$this->get_server_from_id($value);
			if(empty($server)) continue;
			$currency_data= M('currency_data','', $this->get_gmserver())->where(" platform=".$server['platform']." and server_id=".$server['server_id']." and currency_type=".$currency_type." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
			array_push($currency_datas, $currency_data);
		}

		$currencys=$this->get_currency_types();
		$currency_type_str="";
		if($currency_type==0) $currency_type_str="gold";
		else if($currency_type==1) $currency_type_str="coins";
		foreach($currency_datas as $tmp_data){
			foreach($tmp_data as $currency_data){
				//Array ( [platform] => 0 [server_id] => 9999 [date] => 1437667200 [period] => 10
				// [currency_type] => 0 [type] => 1 [use_type] => 4 [amount] => 80000 )
				//$currency_type 0金币１银币
				//type 1增加2消耗
				//use_type　增加或消耗类型
				$dates[$currency_data['date']]['date']=$currency_data['date'];
				$periods[$currency_data['period']]['date']=$currency_data['period'];
				if($currency_data['type']==1){
					$source_types[$currency_data['use_type']]['name']=$currencys[$currency_type_str]['source'][$currency_data['use_type']];
					$source_types[$currency_data['use_type']]['num']+=$currency_data['amount'];
					$dates[$currency_data['date']]['source']+=$currency_data['amount'];
					$periods[$currency_data['period']]['source']+=$currency_data['amount'];
				}else if($currency_data['type']==2){
					$use_types[$currency_data['use_type']]['name']=$currencys[$currency_type_str]['use'][$currency_data['use_type']];
					$use_types[$currency_data['use_type']]['num']+=$currency_data['amount'];
					$dates[$currency_data['date']]['use']+=$currency_data['amount'];
					$periods[$currency_data['period']]['use']+=$currency_data['amount'];
				}
			}
		}

		foreach($dates as $date){
			$dates[$date['date']]['sum']=$dates[$date['date']]['source']-$dates[$date['date']]['use'];
		}

		foreach($periods as $period){
			$periods[$period['date']]['sum']=$periods[$period['date']]['source']-$periods[$period['date']]['use'];
		}

		$this->assign ( 'type', $currency_type);

		$this->assign ( 'source_types', $source_types );
		$this->assign ( 'use_types', $use_types );
		$this->assign ( 'dates', $dates );
		$this->assign ( 'periods', $periods );
		$this->assign('start_date',$start_date);
		$this->assign('end_date',$end_date);
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
    $this->display ();
  }

    public function hero_list() {
        $this->menu ();
		$url='/zebra/Home/Operation/hero_list';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$servers=$this->get_all_server();
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}

		$vip_start = $_POST['vip_start'];
		$vip_end = $_POST['vip_end'];
		if($_POST['vip_start']==""){
			$vip_start = 0;
		}
		if($_POST['vip_end']==""){
			$vip_end=10;
		}

		$level_start = $_POST['level_start'];
		$level_end = $_POST['level_end'];
		if($_POST['level_start']==""){
			$level_start = 1;
		}
		if($_POST['level_end']==""){
			$level_end=60;
		}
		$page_no = $_POST['page_no'];
		if($_POST['page_no']==""){
			$page_no = 1;
		}
		$page_size = $_POST['page_size'];
		if($_POST['page_size']==""){
			$page_size=50;
		}

		$hero_datas=array();
		$db_user=$this->get_dbuser();
		foreach($choose_servers as $choose_server){
			$server=$this->get_server_from_id($choose_server);
			if(empty($server)) continue;
			$db=$this->get_db_from_serverid($choose_server);
			if(empty($db)) continue;
			$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

			$hero_data = M('heroes','',$db_hero)->query("select * from heroes where vip >=".$vip_start." and vip <=".$vip_end." and level >=".$level_start." and level <=".$level_end." order by level desc,vip desc,_power desc limit ".($page_no-1)*$page_size.",".$page_no*$page_size);
			foreach($hero_data as $data){
				$data['platform']=$this->get_platform($data['platform'])['name'];
				$data['type']=$this->getType($data['type']);
				$pays = M ('payments', '', $db_user)->query("select count(orderid) times,sum(payamount*100)/100 pay from payments where heroid=".$data['id']);
				if(count($pays)>0){
					$data['pay']=$pays[0]['pay'];
					$data['times']=$pays[0]['times'];
				}
				array_push($hero_datas, $data);
			}
		}

		$this->assign ( 'vip_start', $vip_start );
		$this->assign ( 'vip_end', $vip_end );
		$this->assign ( 'level_start', $level_start );
		$this->assign ( 'level_end', $level_end );
		$this->assign ( 'page_no', $page_no );
		$this->assign ( 'page_size', $page_size );

		$this->assign ( 'hero_datas', $hero_datas );
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
        $this->display ();
    }

	public function battle_statistic() {
		$this->menu();
		$url='/zebra/Home/Operation/battle_statistic';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];

		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
		}

		if($_POST['end_date']==""){
			$end_date = date('Y-m-d',time());
		}
		$servers=$this->get_all_server();
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}

		$statistic_data=array();
		foreach ($choose_servers as $value) {
			// $value as server_id
			$server=$this->get_server_from_id($value);
			if(empty($server)) continue;
			$data= M('statistics_time','', $this->get_gmserver())->where(" platform=".$server['platform']." and server_id=".$server['server_id']." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
			array_push($statistic_data, $data);
		}
		//sfreepk:1sboss:2sonlinepk:3sofflinpk:4
		$dates=array();
		foreach ($statistic_data as $tmp_data) {
			foreach ($tmp_data as $data) {
				$dates[$data['date']]['date'] = $data['date'];
				if ($data['type'] == 1) {
					$dates[$data['date']]['sfreepk'] += $data['times'];
					$dates[$data['date']]['sfreepktime'] += $data['time'];
					$dates[$data['date']]['sfreepknum'] += $data['num'];
				} else if ($data['type'] == 2) {
					$dates[$data['date']]['sboss'] += $data['times'];
					$dates[$data['date']]['sbosstime'] += $data['time'];
					$dates[$data['date']]['sbossnum'] += $data['num'];
				} else if ($data['type'] == 3) {
					$dates[$data['date']]['sonlinepk'] += $data['times'];
					$dates[$data['date']]['sonlinepktime'] += $data['time'];
					$dates[$data['date']]['sonlinepknum'] += $data['num'];
				} else if ($data['type'] == 4) {
					$dates[$data['date']]['sofflinpk'] += $data['times'];
					$dates[$data['date']]['sofflinpktime'] += $data['time'];
					$dates[$data['date']]['sofflinpknum'] += $data['num'];
				}
			}
		}

		$this->assign('start_date',$start_date);
		$this->assign('end_date',$end_date);
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->assign('dates',$dates);
		$this->display();
	}

	public function time_statistic() {
		$this->menu();
		$url='/zebra/Home/Operation/time_statistic';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

		$servers=$this->get_all_server();
		$choose_servers=$_POST['checkbox_servers'];
		if(!empty($choose_servers)){
			F($url.'choose_servers',$choose_servers);
		}else{
			$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
		}

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];

		if($_POST['start_date']==""){
			$start_date = date('Y-m-d',time()-3600*24*30);
		}

		if($_POST['end_date']==""){
			$end_date = date('Y-m-d',time());
		}

		$type_radio = $_POST['type_radio'];
		if($_POST['type_radio']=="") $type_radio=1;
		//sfreepk:1sboss:2sonlinepk:3sofflinpk:4
		$statistic_data=array();
		foreach ($choose_servers as $value) {
			// $value as server_id
			$server=$this->get_server_from_id($value);
			if(empty($server)) continue;
			$data= M('statistics_time','', $this->get_gmserver())->where(" platform=".$server['platform']." and server_id=".$server['server_id']." and type=".$type_radio." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
			array_push($statistic_data, $data);
		}

		$dates=array();
		foreach ($statistic_data as $tmp_data){
			foreach ($tmp_data as $data){
				$dates[$data['date']]['date']=$data['date'];
				$dates[$data['date']][$data['time_type']]['num']+=$data['num'];  //人数
				$dates[$data['date']][$data['time_type']]['times']+=$data['times']; //次数
			}
		}

		$this->assign('start_date',$start_date);
		$this->assign('end_date',$end_date);
		$this->assign ( 'type_radio', $type_radio);
		$this->assign ( 'choose_servers', $choose_servers );
		$this->assign ( 'servers', $servers );
		$this->assign('dates',$dates);
		$this->display();
	}

	public function code_gen() {
        $this->menu ();
		$url='/zebra/Home/Operation/code_gen';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

        $recharge=array();

        $this->assign ( 'recharge', $recharge);
        $this->display ();
    }

    public function code_list() {
        $this->menu ();
		$url='/zebra/Home/Operation/code_list';
		$menu=$this->get_menu_from_url($url);
		$this->assign ( 'title', $menu['title']);
		$this->assign ( 'active_open_id', $menu['pid']);
		$this->assign ( 'url', $url);

        $recharge=array();

        $this->assign ( 'recharge', $recharge);
        $this->display ();
    }

		public function hero_remainder() {
			$this->menu ();
			$url='/zebra/Home/Operation/hero_remainder';
			$menu=$this->get_menu_from_url($url);
			$this->assign ( 'title', $menu['title']);
			$this->assign ( 'active_open_id', $menu['pid']);
			$this->assign ( 'url', $url);

			$servers=$this->get_all_server();
			$platforms=$this->get_all_platforms();

			$userName = $_POST['userName'];
			$heroId = $_POST['heroId'];
			$heroName = $_POST['heroName'];
			$platform_id = $_POST['platform_select'];
			$server_id = $_POST['server_select'];
			$page_size = $_POST['table_report_length'];

			$page=$_POST['hidevalue'];
			if($page<=0){
				$page=1;
			}
			if($page_size<=0){
				$page_size=10;
			}

			$server=$this->get_db_from_serverid($server_id);
			$hero_datas=array();
			$size=0;
			$db_user=$this->get_dbuser();
			if(!empty($server)){
				$db=$this->get_db_from_serverid($server['server_id']);
				$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

				if($_POST['userName']==''&&$_POST['heroId']==''&&$_POST['heroName']==''){
					//list
					$all_heroids=M ('heroes', '', $db_hero)->query("select count(id) size from heroes where userid!=0 " );
					if(count($all_heroids)>0){
						$size=$all_heroids[0]['size'];
					}

					$heroes=M ('heroes', '', $db_hero)->query("select * from heroes where userid!=0 ORDER BY heroes.gold+heroes.bindGold desc LIMIT ".(($page-1)*$page_size)." , ".$page_size);
					// print_r("select * from heroes where userid!=0 ORDER BY heroes.gold+heroes.bindGold desc LIMIT ".(($page-1)*$page_size)." , ".$page_size);
					if(count($heroes)>0){
							foreach($heroes as $hero){
									$hero_datas[$hero['id']]['heroid']=$hero['id'];
									$hero_datas[$hero['id']]['heroname']=$hero['name'];
									$hero_datas[$hero['id']]['type']=$this->getType($hero['type']);
									$hero_datas[$hero['id']]['vip']=$hero['vip'];
									$hero_datas[$hero['id']]['level']=$hero['level'];
									$hero_datas[$hero['id']]['platform']=$this->get_platform($hero['platform'])['name'];
									$hero_datas[$hero['id']]['remainder_gold']=$hero['gold']+$hero['bindGold'];

									$user = M ('users', '', $db_user)->query("select * from users where id=".$hero['userid'] );
									if(count($user)>0)
										$hero_datas[$hero['id']]['username']=$user[0]['name'];

									$payment = M ('payments', '', $db_user)->query("select sum(payamount) pay FROM payments where status=1 and heroid=".$hero['id'] );
									if(count($payment)>0)
										$hero_datas[$hero['id']]['isrmb']='是';
									else
										$hero_datas[$hero['id']]['isrmb']='否';

										$heroes_uses = M ('heroes_uses', '', $db_hero)->query("select sum(cost) cost from heroes_uses where costType=1 and countType=0 and type=1 and heroid=".$hero['id'] );
										if(count($heroes_uses)>0)
											$hero_datas[$hero['id']]['recharge_gold']=$heroes_uses[0]['cost'];

										$heroes_uses = M ('heroes_uses', '', $db_hero)->query("select sum(cost) cost from heroes_uses where costType=1 and countType=0 and heroid=".$hero['id'] );
										if(count($heroes_uses)>0)
											$hero_datas[$hero['id']]['get_gold']=$heroes_uses[0]['cost'];


										$heroes_stat = M ('heroes_stat', '', $db_hero)->query("select *,FROM_UNIXTIME( regtime, '%Y-%m-%d %H:%i:%S' ) regtime,FROM_UNIXTIME( lastlogintime, '%Y-%m-%d %H:%i:%S' ) lastlogintime from heroes_stat where heroid=".$hero['id'] );
										if(count($heroes_stat)>0){
											$hero_datas[$hero['id']]['regtime']=$heroes_stat[0]['regtime'];
											$hero_datas[$hero['id']]['lastlogin']=$heroes_stat[0]['lastlogintime'];
										}
							}
					}
				}else{
					//one
					if($heroId!=0){
						$heroes=M ('heroes', '', $db_hero)->query("select * from heroes where userid!=0 and id=".$heroId );
						if(count($heroes)>0){
								foreach($heroes as $hero){
										$hero_datas[$hero['id']]['heroid']=$hero['id'];
										$hero_datas[$hero['id']]['heroname']=$hero['name'];
										$hero_datas[$hero['id']]['type']=$this->getType($hero['type']);
										$hero_datas[$hero['id']]['vip']=$hero['vip'];
										$hero_datas[$hero['id']]['level']=$hero['level'];
										$hero_datas[$hero['id']]['platform']=$this->get_platform($hero['platform'])['name'];
										$hero_datas[$hero['id']]['remainder_gold']=$hero['gold']+$hero['bindGold'];

										$user = M ('users', '', $db_user)->query("select * from users where id=".$hero['userid'] );
										if(count($user)>0)
											$hero_datas[$hero['id']]['username']=$user[0]['name'];

										$payment = M ('payments', '', $db_user)->query("select sum(payamount) pay FROM payments where status=1 and heroid=".$hero['id'] );
										if(count($payment)>0)
											$hero_datas[$hero['id']]['isrmb']='是';
										else
											$hero_datas[$hero['id']]['isrmb']='否';

											$heroes_uses = M ('heroes_uses', '', $db_hero)->query("select sum(cost) cost from heroes_uses where costType=1 and countType=0 and type=1 and heroid=".$hero['id'] );
											if(count($heroes_uses)>0)
												$hero_datas[$hero['id']]['recharge_gold']=$heroes_uses[0]['cost'];

											$heroes_uses = M ('heroes_uses', '', $db_hero)->query("select sum(cost) cost from heroes_uses where costType=1 and countType=0 and heroid=".$hero['id'] );
											if(count($heroes_uses)>0)
												$hero_datas[$hero['id']]['get_gold']=$heroes_uses[0]['cost'];

											$heroes_stat = M ('heroes_stat', '', $db_hero)->query("select *,FROM_UNIXTIME( regtime, '%Y-%m-%d %H:%i:%S' ) regtime,FROM_UNIXTIME( lastlogintime, '%Y-%m-%d %H:%i:%S' ) lastlogintime from heroes_stat where heroid=".$hero['id'] );
											if(count($heroes_stat)>0){
												$hero_datas[$hero['id']]['regtime']=$heroes_stat[0]['regtime'];
												$hero_datas[$hero['id']]['lastlogin']=$heroes_stat[0]['lastlogintime'];
											}
								}
						}
					}else if($heroName!=""){
						$heroes=M ('heroes', '', $db_hero)->query("select * from heroes where userid!=0 and name='".$heroName."'" );
						if(count($heroes)>0){
								foreach($heroes as $hero){
										$hero_datas[$hero['id']]['heroid']=$hero['id'];
										$hero_datas[$hero['id']]['heroname']=$hero['name'];
										$hero_datas[$hero['id']]['type']=$this->getType($hero['type']);
										$hero_datas[$hero['id']]['vip']=$hero['vip'];
										$hero_datas[$hero['id']]['level']=$hero['level'];
										$hero_datas[$hero['id']]['platform']=$this->get_platform($hero['platform'])['name'];
										$hero_datas[$hero['id']]['remainder_gold']=$hero['gold']+$hero['bindGold'];

										$user = M ('users', '', $db_user)->query("select * from users where id=".$hero['userid'] );
										if(count($user)>0)
											$hero_datas[$hero['id']]['username']=$user[0]['name'];

										$payment = M ('payments', '', $db_user)->query("select sum(payamount) pay FROM payments where status=1 and heroid=".$hero['id'] );
										if(count($payment)>0)
											$hero_datas[$hero['id']]['isrmb']='是';
										else
											$hero_datas[$hero['id']]['isrmb']='否';

											$heroes_uses = M ('heroes_uses', '', $db_hero)->query("select sum(cost) cost from heroes_uses where costType=1 and countType=0 and type=1 and heroid=".$hero['id'] );
											if(count($heroes_uses)>0)
												$hero_datas[$hero['id']]['recharge_gold']=$heroes_uses[0]['cost'];

											$heroes_uses = M ('heroes_uses', '', $db_hero)->query("select sum(cost) cost from heroes_uses where costType=1 and countType=0 and heroid=".$hero['id'] );
											if(count($heroes_uses)>0)
												$hero_datas[$hero['id']]['get_gold']=$heroes_uses[0]['cost'];

											$heroes_stat = M ('heroes_stat', '', $db_hero)->query("select *,FROM_UNIXTIME( regtime, '%Y-%m-%d %H:%i:%S' ) regtime,FROM_UNIXTIME( lastlogintime, '%Y-%m-%d %H:%i:%S' ) lastlogintime from heroes_stat where heroid=".$hero['id'] );
											if(count($heroes_stat)>0){
												$hero_datas[$hero['id']]['regtime']=$heroes_stat[0]['regtime'];
												$hero_datas[$hero['id']]['lastlogin']=$heroes_stat[0]['lastlogintime'];
											}
								}
						}
					}else if($userName!=""){
						$users = M ('users', '', $db_user)->query("select * from users where name='".$userName."'" );
						if(count($users)>0){
							$heroes=M ('heroes', '', $db_hero)->query("select * from heroes where userid!=0 and userid=".$users[0]['id'] );
							if(count($heroes)>0){
									foreach($heroes as $hero){
										$hero_datas[$hero['id']]['username']=$users[0]['name'];
										$hero_datas[$hero['id']]['heroid']=$hero['id'];
										$hero_datas[$hero['id']]['heroname']=$hero['name'];
										$hero_datas[$hero['id']]['type']=$this->getType($hero['type']);
										$hero_datas[$hero['id']]['vip']=$hero['vip'];
										$hero_datas[$hero['id']]['level']=$hero['level'];
										$hero_datas[$hero['id']]['platform']=$this->get_platform($hero['platform'])['name'];
										$hero_datas[$hero['id']]['remainder_gold']=$hero['gold']+$hero['bindGold'];

										$payment = M ('payments', '', $db_user)->query("select sum(payamount) pay FROM payments where status=1 and heroid=".$hero['id'] );
										if(count($payment)>0)
											$hero_datas[$hero['id']]['isrmb']='是';
										else
											$hero_datas[$hero['id']]['isrmb']='否';

											$heroes_uses = M ('heroes_uses', '', $db_hero)->query("select sum(cost) cost from heroes_uses where costType=1 and countType=0 and type=1 and heroid=".$hero['id'] );
											if(count($heroes_uses)>0)
												$hero_datas[$hero['id']]['recharge_gold']=$heroes_uses[0]['cost'];

											$heroes_uses = M ('heroes_uses', '', $db_hero)->query("select sum(cost) cost from heroes_uses where costType=1 and countType=0 and heroid=".$hero['id'] );
											if(count($heroes_uses)>0)
												$hero_datas[$hero['id']]['get_gold']=$heroes_uses[0]['cost'];

											$heroes_stat = M ('heroes_stat', '', $db_hero)->query("select *,FROM_UNIXTIME( regtime, '%Y-%m-%d %H:%i:%S' ) regtime,FROM_UNIXTIME( lastlogintime, '%Y-%m-%d %H:%i:%S' ) lastlogintime from heroes_stat where heroid=".$hero['id'] );
											if(count($heroes_stat)>0){
												$hero_datas[$hero['id']]['regtime']=$heroes_stat[0]['regtime'];
												$hero_datas[$hero['id']]['lastlogin']=$heroes_stat[0]['lastlogintime'];
											}
									}
							}
						}
					}
				}
			}

			$this->assign ( 'page_size', $page_size );
			$this->assign ( 'page', $page );
			$this->assign ( 'size', $size );
			$this->assign ( 'hero_datas', $hero_datas );
			$this->assign ( 'platforms', $platforms );
			$this->assign ( 'servers', $servers );
	    $this->display ();
		}

		public function all_remainder() {
			$this->menu ();
			$url='/zebra/Home/Operation/all_remainder';
			$menu=$this->get_menu_from_url($url);
			$this->assign ( 'title', $menu['title']);
			$this->assign ( 'active_open_id', $menu['pid']);
			$this->assign ( 'url', $url);
			$servers=$this->get_all_server();
			$choose_servers=$_POST['checkbox_servers'];
			if(!empty($choose_servers)){
				F($url.'choose_servers',$choose_servers);
			}else{
				$choose_servers = F($url.'choose_servers');//从缓存获取已选择服务器
			}

			$pie_datas=array();
			$datas=array();
			foreach ($choose_servers as $value) {
				// $value as server_id
				$server=$this->get_server_from_id($value);
				if(empty($server)) continue;
				$db=$this->get_db_from_serverid($server['server_id']);
				if(empty($db)) continue;
				$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

				$heroes = M ( 'heroes', '', $db_hero)->query ( "SELECT count(id) count FROM heroes where userid!=0 and gold+bindgold<1000 and platform=".$server['platform'] );
				if(count($heroes)>0){
					$pie_datas[1]+=$heroes[0]['count'];
					$datas[$server['server_id']]['platform_name']=$this->get_platform($server['platform'])['name'];
					$datas[$server['server_id']]['server_name']=$server['server_name'];
					$datas[$server['server_id']][1]+=$heroes[0]['count'];
				}
				$heroes = M ( 'heroes', '', $db_hero)->query ( "SELECT count(id) count FROM heroes where userid!=0 and gold+bindgold between 1000 and 5000 and platform=".$server['platform'] );
				if(count($heroes)>0){
					$pie_datas[2]+=$heroes[0]['count'];
					$datas[$server['server_id']]['platform_name']=$this->get_platform($server['platform'])['name'];
					$datas[$server['server_id']]['server_name']+=$server['server_name'];
					$datas[$server['server_id']][2]+=$heroes[0]['count'];
				}
				$heroes = M ( 'heroes', '', $db_hero)->query ( "SELECT count(id) count FROM heroes where userid!=0 and gold+bindgold between 5000 and 10000 and platform=".$server['platform'] );
				if(count($heroes)>0){
					$pie_datas[3]+=$heroes[0]['count'];
					$datas[$server['server_id']]['platform_name']=$this->get_platform($server['platform'])['name'];
					$datas[$server['server_id']]['server_name']=$server['server_name'];
					$datas[$server['server_id']][3]+=$heroes[0]['count'];
				}
				$heroes = M ( 'heroes', '', $db_hero)->query ( "SELECT count(id) count FROM heroes where userid!=0 and gold+bindgold between 10000 and 100000 and platform=".$server['platform'] );
				if(count($heroes)>0){
					$pie_datas[4]+=$heroes[0]['count'];
					$datas[$server['server_id']]['platform_name']=$this->get_platform($server['platform'])['name'];
					$datas[$server['server_id']]['server_name']=$server['server_name'];
					$datas[$server['server_id']][4]+=$heroes[0]['count'];
				}
				$heroes = M ( 'heroes', '', $db_hero)->query ( "SELECT count(id) count FROM heroes where userid!=0 and gold+bindgold>100000 and platform=".$server['platform'] );
				if(count($heroes)>0){
					$pie_datas[5]+=$heroes[0]['count'];
					$datas[$server['server_id']]['platform_name']=$this->get_platform($server['platform'])['name'];
					$datas[$server['server_id']]['server_name']=$server['server_name'];
					$datas[$server['server_id']][5]+=$heroes[0]['count'];
				}
			}

			$this->assign ( 'pie_datas', $pie_datas );
			$this->assign ( 'datas', $datas );
			$this->assign ( 'choose_servers', $choose_servers );
			$this->assign ( 'servers', $servers );
			$this->display ();
		}

}
