<?php

namespace Home\Controller;

use Think\Controller;

class CustomerController extends LayoutController {

	public function heroinfo() {
		$this->menu ();
		$this->assign ( 'title', '英雄基本信息' );
		
		$flag = true;
		if (! extension_loaded ( 'amf3' ))
			$flag = false;
		
		$heroid = $_POST['heroid'];
		$heroname = $_POST['heroname'];
// 		if($_POST['heroid']==""&&$_POST['heroiname']==""){
// 			return 1;
// 		}
		if($_POST['heroid']==""){
			$heroid = 0;
		}

		$status = Array ();
		if($heroid!=0||$heroname!=""){
			$host = $this->get_host();
			$port = $this->get_port();
			$socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP ) or $flag = false;
			$conn = socket_connect ( $socket, $host, $port ) or $flag = false;
			if ($conn) {
				$sendData = array (
						"cmd" => "sys/playerinfo",
						"heroId" => $heroid,
						"name" => $heroname,
						"user" => "hank",
						"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf"
				);
				socket_write ( $socket, amf3_encode ( $sendData ) ) or $flag = false;
				$buffer = socket_read ( $socket, 2048 );
				$status = amf3_decode ( $buffer );
			}
			socket_close ( $socket );
		}
	
		if ($flag) {
			$this->assign ( 'id', $status ['id'] );
			$this->assign ( 'name', $status ['name'] );
			$this->assign ( 'type',$this->getType($status ['type'])  );
			$this->assign ( 'vip', $status ['vip'] );
			$this->assign ( 'level', $status ['level'] );
			
			$this->assign ( '_health', $status ['health'] );
			$this->assign ( '_attack', $status ['attack'] );
			$this->assign ( '_cstrike', $status ['cstrike'] );
			$this->assign ( '_armor', $status ['armor'] );
			$this->assign ( '_wreck', $status ['wreck'] );
			$this->assign ( '_power', $status ['power'] );
			
			$this->assign ( 'rank', $status ['rank'] );
			$this->assign ( 'physical', $status ['physical'] );
			$this->assign ( 'gold', $status ['gold'] );
			$this->assign ( 'coins', $status ['coins'] );
			$this->assign ( 'pay', $status ['pay'] );
			$this->assign ( 'bagLimit', $status ['bagLimit'] );
			$this->assign ( 'soul', $status ['soul'] );
			$this->assign ( 'experience', $status ['experience'] );
		} else {
			$DBuser = M ( 'heroes', '',  $this->get_game());
			$heroes = $DBuser->query("select * from heroes where id=".$heroid." or name='".$heroname."'" );
			//where ( "id=".$heroid." or name=".$heroname )->select ();
			
			$this->assign ( 'id', $heroes [0] ['id'] );
			$this->assign ( 'name', $heroes [0] ['name'] );
			$this->assign ( 'type', $this->getType($heroes [0] ['type']));
			$this->assign ( 'vip', $heroes [0] ['vip'] );
			$this->assign ( 'level', $heroes [0] ['level'] );
			$this->assign ( '_health', $heroes [0] ['_health'] );
			$this->assign ( '_attack', $heroes [0] ['_attack'] );
			$this->assign ( '_cstrike', $heroes [0] ['_cstrike'] );
			$this->assign ( '_armor', $heroes [0] ['_armor'] );
			$this->assign ( '_wreck', $heroes [0] ['_wreck'] );
			$this->assign ( '_power', $heroes [0] ['_power'] );
		}
		
		$this->display ();
	}
	
	public function heromaterial() {
		$this->menu ();
		$this->assign ( 'title', '英雄道具信息' );
		
		$heroid = $_POST['heroid'];
		$heroname = $_POST['heroname'];
		if($_POST['heroid']==""){
			$heroid = 0;
		}
		$DBuser = M ( 'heroes', '',  $this->get_game());
		$heroes = $DBuser->query("select * from heroes where id=".$heroid." or name='".$heroname."'" );
		
		$this->assign ( 'id', $heroes [0] ['id'] );
		$this->assign ( 'name', $heroes [0] ['name'] );
		$this->assign ( 'type',$this->getType($heroes [0] ['type']));
		$this->assign ( 'vip', $heroes [0] ['vip'] );
		$this->assign ( 'level', $heroes [0] ['level'] );

		$DBuser = M ( 'materials', '',  $this->get_game());
		$materials = $DBuser->query ( "SELECT @rownum := @rownum +1 AS no, b.materialid,b.materialnum,b.binding,b.wear,m.name FROM `heroes_bag` b,materials m,heroes h, (SELECT@rownum :=0) r where m.id=b.materialid and h.id=b.heroid and (h.id=".$heroid." or h.name='".$heroname."') ORDER BY `no` ASC" );
		$this->assign ( 'materials', $materials);		
		$this->display ();
	}

	public function deletehero() {
		$this->menu ();
		$this->assign ( 'title', '删除英雄' );
		$this->assign ( 'username', $username );

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
	
	public function punish() {
		$this->menu ();
		$this->assign ( 'title', '惩罚英雄' );
		$this->assign ( 'username', $username );

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
		$this->display ();
	}

	public function punish_query() {
		$this->menu ();
		$this->assign ( 'title', '英雄惩罚查询' );
		$this->assign ( 'username', $username );

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

	public function send_mail() {
		$this->menu ();
		$this->assign ( 'title', '发送邮件' );
		$this->assign ( 'username', $username );

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
		if($heroids==0) $flag=false;
		if($mids==0) $flag=false;
		if($nums==0) $flag=false;
		if($title==0) $flag=false;
		if($content==0) $flag=false;

		if($flag){
			while(count($send_mail_list)+count($heroidsarr)>20){
				array_shift($send_mail_list);
			}

			for($index=0;$index<count($heroidsarr);$index++){
				$send_mail=array();
				$sendData = array (
								"cmd" => "sys/sendMail",
								"user" => "hank",
								"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
								"heroId" => $heroidsarr[$index],
								"rewardTitle" => $title,
								"rewardContent" => $content,
								"rewardId" => $midsarr[0],
								"rewardNum" => $numsarr[0],
								"attach2" => $midsarr[1],
								"num2" => $numsarr[1],
								"attach3" => $midsarr[2],
								"num3" => $numsarr[2],
								"attach4" => $midsarr[3],
								"num4" => $numsarr[3],
								"attach5" => $midsarr[4],
								"num5" => $numsarr[4]
								);
				$status = $this->conn_server($sendData);
				$send_mail['hero_id']=$heroidsarr[$index];
				$send_mail['onlines']=$status['onlines'];
				if($midsarr[0]!=0&&$numsarr[0]!=0)	$send_mail['mid1']="物品MID:".$midsarr[0]." 数量".$numsarr[0];
				if($midsarr[1]!=0&&$numsarr[1]!=0)	$send_mail['mid2']="物品MID:".$midsarr[1]." 数量".$numsarr[1];
				if($midsarr[2]!=0&&$numsarr[2]!=0)	$send_mail['mid3']="物品MID:".$midsarr[2]." 数量".$numsarr[2];
				if($midsarr[3]!=0&&$numsarr[3]!=0)	$send_mail['mid4']="物品MID:".$midsarr[3]." 数量".$numsarr[3];
				if($midsarr[4]!=0&&$numsarr[4]!=0)	$send_mail['mid5']="物品MID:".$midsarr[4]." 数量".$numsarr[4];
				$send_mail['time']=date ( 'Y-m-d H:i:s', time ());

				array_push($send_mail_list, $send_mail);
			}
		}

		F('send_mail_list',$send_mail_list);//保存数据到缓存
		//$this->assign ('send_mail_list', $send_mail_list );
		if($flag) header("Location:/zebra/Home/customer/success_send_mail");
		$this->display ();
    }

    public function success_send_mail() {
		$this->menu ();
		$this->assign ( 'title', '发送邮件记录' );
		$this->assign ( 'username', $username );

		$send_mail_list = F('send_mail_list');//从缓存获取数据
			if(empty($send_mail_list)){
				$send_mail_list=array();
		}
		$this->assign ('send_mail_list', $send_mail_list );
		$this->display ();
    }

	public function send_board() {
		$this->menu ();
		$this->assign ( 'title', '发布公告' );
		$this->assign ( 'username', $username );

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

		if($flag&&$times>0) header("Location:/zebra/Home/customer/success_send_board");
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
		$this->assign ( 'username', $username );

		$flag=true;
		$content = $_POST['send_board_online_content'];
		if($content=="") $flag=false;

		if($flag){
			$sendData = array (
							"cmd" => "sys/announcement",
							"user" => "hank",
							"pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
							"comment" => $content);
			$status=$this->conn_server($sendData);

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

		}
		if($flag) header("Location:/zebra/Home/customer/success_send_board");
		$this->display ();
	}

	public function success_send_board() {
		$this->menu ();
		$this->assign ( 'title', '公告发布记录' );
		$this->assign ( 'username', $username );

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
}
