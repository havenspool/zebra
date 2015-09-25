<?php

namespace Home\Controller;

use Think\Controller;

class RMBPlayerController extends LayoutController {

    public function recharge_query() {
        $this->menu ();
        $url='/zebra/Home/RMBPlayer/recharge_query';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $servers=$this->get_all_server();

        $flag=true;
        $userName = $_POST['userName'];
        $heroId = $_POST['heroId'];
        $heroName = $_POST['heroName'];
        $server_id = $_POST['server_select'];
        $orderId= $_POST['orderId'];

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $date_sql="";
        if($_POST['start_date']!=""){
            $date_sql=" and timestamp >= UNIX_TIMESTAMP('".$start_date."')";
        }
        if($_POST['end_date']!=""){
            $date_sql=$date_sql." and timestamp <= UNIX_TIMESTAMP('".$end_date."')";
        }

        if($_POST['userName']==""&&$_POST['heroId']==""&&$_POST['heroName']==""&&$_POST['orderId']==""){
            $flag=false;
        }
        if($_POST['heroId']==""){
            $heroId=0;
        }

        $hero_datas=array();
        $db_user=$this->get_dbuser();
        $db=$this->get_db_from_serverid($server_id);
        if(!empty($db)){
          $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

          if($userName!=""){
              //根据平台账号查询
              $users=M ('users', '', $db_user)->query("select * from users where name='".$userName."'" );
              if(count($users)>0){
                  $pays = M ('payments', '', $db_user)->query("select * from payments where userId=".$users[0]['id'].$date_sql );
                  foreach($pays as $pay){
                      $hero = M ('heroes', '', $db_hero)->query("select * from heroes where id=".$pay['heroid'] );
                      $hero_datas[$pay['orderid']]['timestamp']=date('Y-m-d',$pay['timestamp']);
                      $hero_datas[$pay['orderid']]['orderid']=$pay['orderid'];
                      $hero_datas[$pay['orderid']]['payamount']=$pay['payamount'];
                      $hero_datas[$pay['orderid']]['paytype']=$pay['payid'];
                      $hero_datas[$pay['orderid']]['channel']=$pay['type'];
                      $hero_datas[$pay['orderid']]['status']=$pay['status'];

                      $hero_datas[$pay['orderid']]['username']=$users[0]['name'];
                      $platform=$this->get_platform($hero[0]['platform']);
                      $hero_datas[$pay['orderid']]['platform']=$platform['name'];
                      $hero_datas[$pay['orderid']]['heroid']=$hero[0]['id'];
                      $hero_datas[$pay['orderid']]['heroname']=$hero[0]['name'];
                      $hero_datas[$pay['orderid']]['type']=$this->getType($hero[0]['type']);
                      $hero_datas[$pay['orderid']]['vip']=$hero[0]['vip'];
                      $hero_datas[$pay['orderid']]['level']=$hero[0]['level'];
                  }

              }
          }else if($orderId!=0){
              //根据订单号查询
              $pays=M ('payments', '', $db_user)->query("select * from payments where orderId='".$orderId."'" .$date_sql);
              $users=M ('users', '', $db_user)->query("select name from users where id='".$pays[0]['userid']."'" );
              if(count($pays)>0){
                  $hero = M ('heroes', '', $db_hero)->query("select * from heroes where userId=".$pays[0]['userid'] );

                  $hero_datas[$pays[0]['orderid']]['timestamp']=date('Y-m-d',$pays[0]['timestamp']);
                  $hero_datas[$pays[0]['orderid']]['orderid']=$pays[0]['orderid'];
                  $hero_datas[$pays[0]['orderid']]['payamount']=$pays[0]['payamount'];
                  $hero_datas[$pays[0]['orderid']]['paytype']=$pays[0]['payid'];
                  $hero_datas[$pays[0]['orderid']]['channel']=$pays[0]['type'];
                  $hero_datas[$pays[0]['orderid']]['status']=$pays[0]['status'];

                  $hero_datas[$pays[0]['orderid']]['username']=$users[0]['name'];
                  $platform=$this->get_platform($hero[0]['platform']);
                  $hero_datas[$pays[0]['orderid']]['platform']=$platform['name'];
                  $hero_datas[$pays[0]['orderid']]['heroid']=$hero[0]['id'];
                  $hero_datas[$pays[0]['orderid']]['heroname']=$hero[0]['name'];
                  $hero_datas[$pays[0]['orderid']]['type']=$this->getType($hero[0]['type']);
                  $hero_datas[$pays[0]['orderid']]['vip']=$hero[0]['vip'];
                  $hero_datas[$pays[0]['orderid']]['level']=$hero[0]['level'];
              }
          }else if($heroId!=0||$heroName!=""){
              //根据英雄UID查询
              $hero=array();
              if($heroId!=0)
                  $hero = M ('heroes', '', $db_hero)->query("select * from heroes where id=".$heroId);
              else if($heroName!="")
                  $hero = M ('heroes', '', $db_hero)->query("select * from heroes where name='".$heroName."'" );
              if(count($hero)>0){
                  $pays = M ('payments', '', $db_user)->query("select * from payments where heroid=".$hero[0]['id'] .$date_sql);
                  $users=M ('users', '', $db_user)->query("select name from users where id='".$pays[0]['userid']."'" );
                  foreach($pays as $pay){
                      $hero_datas[$pay['orderid']]['timestamp']=date('Y-m-d',$pay['timestamp']);
                      $hero_datas[$pay['orderid']]['orderid']=$pay['orderid'];
                      $hero_datas[$pay['orderid']]['payamount']=$pay['payamount'];
                      $hero_datas[$pay['orderid']]['paytype']=$pay['payid'];
                      $hero_datas[$pay['orderid']]['channel']=$pay['type'];
                      $hero_datas[$pay['orderid']]['status']=$pay['status'];

                      $hero_datas[$pay['orderid']]['username']=$users[0]['name'];
                      $platform=$this->get_platform($hero[0]['platform']);
                      $hero_datas[$pay['orderid']]['platform']=$platform['name'];
                      $hero_datas[$pay['orderid']]['heroid']=$hero[0]['id'];
                      $hero_datas[$pay['orderid']]['heroname']=$hero[0]['name'];
                      $hero_datas[$pay['orderid']]['type']=$this->getType($hero[0]['type']);
                      $hero_datas[$pay['orderid']]['vip']=$hero[0]['vip'];
                      $hero_datas[$pay['orderid']]['level']=$hero[0]['level'];
                  }
              }
          }
        }

        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        $this->assign ( 'hero_datas', $hero_datas );
        $this->assign ( 'servers', $servers );
        $this->display ();
    }

    public function recharge_time() {
        $this->menu ();
        $url='/zebra/Home/RMBPlayer/recharge_time';
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
            $end_date = date('Y-m-d',time()+86400);
        }
        $servers=$this->get_all_server();
        $db_gm=$this->get_gmserver();
        $platforms=$this->get_all_platforms();
        $values=$_POST['checkbox'];
        if(!empty($values)){
          F($url.'values',$values);
        }else{
          $values = F($url.'values');//从缓存获取已选择服务器
        }
        $db_user=$this->get_dbuser();

        $pay_times=array();
        $all_pay_times=array();

        foreach ($values as $value) {
          $pay_time= M('pay_time','', $this->get_gmserver())->where(" platform=".$value." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
          foreach($pay_time as $pay){
              //pay_statistic	platform	server_id	date	period	num	pay
              $platform=$this->get_platform($pay['platform']);
              $pay_times[$pay['date']][$pay['period']]['platform']=$platform['name'];
              $pay_times[$pay['date']][$pay['period']]['server_id']=$pay['server_id'];
              $pay_times[$pay['date']][$pay['period']]['date']=date("Y-m-d",$pay['date']);
              $pay_times[$pay['date']][$pay['period']]['period']=$this->get_period($pay['period']);
              $pay_times[$pay['date']][$pay['period']]['num']=$pay['num'];
              $pay_times[$pay['date']][$pay['period']]['pay']=$pay['pay'];

              $all_pay_times[$pay['period']]['period']=$this->get_period($pay['period']);
              $all_pay_times[$pay['period']]['num']+=$pay['num'];
              $all_pay_times[$pay['period']]['pay']+=$pay['pay'];
          }
        }


        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        $this->assign ( 'all_pay_times', $all_pay_times );
        $this->assign ( 'pay_times', $pay_times );
        $this->assign ( 'choose_platforms', $values );
        $this->assign ( 'platforms', $platforms );
        $this->display ();
    }

    public function recharge_amount() {
        $this->menu ();
        $url='/zebra/Home/RMBPlayer/recharge_amount';
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
            $end_date = date('Y-m-d',time()+86400);
        }
        $servers=$this->get_all_server();
        $db_gm=$this->get_gmserver();
        $platforms=$this->get_all_platforms();
        $values=$_POST['checkbox'];
        if(!empty($values)){
          F($url.'values',$values);
        }else{
          $values = F($url.'values');//从缓存获取已选择服务器
        }
        $db_user=$this->get_dbuser();

        $pay_statistics=array();
        $all_pay_statistics=array();
        foreach ($values as $value) {
          $pay_statistic= M('pay_statistic','', $this->get_gmserver())->where(" platform=".$value." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
          foreach($pay_statistic as $pay){
              //pay_statistic	platform	server_id	date	pay_part	num	pay
              $platform=$this->get_platform($pay['platform']);
              $pay_statistics[$pay['date']][$pay['pay_part']]['platform']=$platform['name'];
              $pay_statistics[$pay['date']][$pay['pay_part']]['server_id']=$pay['server_id'];
              $pay_statistics[$pay['date']][$pay['pay_part']]['date']=date("Y-m-d",$pay['date']);
              $pay_statistics[$pay['date']][$pay['pay_part']]['pay_part']=$this->get_pay_part($pay['pay_part']);
              $pay_statistics[$pay['date']][$pay['pay_part']]['num']=$pay['num'];
              $pay_statistics[$pay['date']][$pay['pay_part']]['pay']=$pay['pay'];

              $all_pay_statistics[$pay['pay_part']]['pay_part']=$this->get_pay_part($pay['pay_part']);
              $all_pay_statistics[$pay['pay_part']]['num']+=$pay['num'];
              $all_pay_statistics[$pay['pay_part']]['pay']+=$pay['pay'];
          }
        }

        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        $this->assign ( 'all_pay_statistics', $all_pay_statistics );
        $this->assign ( 'pay_statistics', $pay_statistics );
        $this->assign ( 'choose_platforms', $values );
        $this->assign ( 'platforms', $platforms );
        $this->display ();
    }

    public function recharge_rank() {
        $this->menu ();
        $url='/zebra/Home/RMBPlayer/recharge_rank';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if($_POST['start_date']==""&&$_POST['end_date']==""){
          $_POST['end_date']==date('Y-m-d',time()+86400);
          $end_date = date('Y-m-d',time()+86400);
        }

        $date_sql="";
        if($_POST['start_date']!=""){
            $date_sql=" and timestamp >= UNIX_TIMESTAMP('".$start_date."')";
        }
        if($_POST['end_date']!=""){
            $date_sql=$date_sql." and timestamp <= UNIX_TIMESTAMP('".$end_date."')";
        }

        $page_size = $_POST['table_report_length'];
        $page=$_POST['hidevalue'];
        $hidetype=$_POST['hidetype'];
        if($page<=0){
          $page=1;
        }
        if($hidetype<=0){
          $hidetype=1;  //1 单笔排行　２累计排行
        }
  			$size=0;  //总页数
        if($page_size<=0){
          $page_size=10;
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

        $pay_orders=array();
        $pay_ranks=array();
        $all_size=0;

        $platform_str=" and u.platform in( ";
        foreach ($values as $value) {
          $platform_str=$platform_str.$value.",";
        }
        $platform_str=substr($platform_str,0,-1);
        $platform_str=$platform_str." ) ";

        $choose_servers_str=" and p.serverId in( ";
        foreach ($choose_servers as $choose_server) {
          $choose_servers_str=$choose_servers_str.$choose_server.",";
        }
        $choose_servers_str=substr($choose_servers_str,0,-1);
        $choose_servers_str=$choose_servers_str." ) ";

        if($hidetype==1){
          $pays = M ('payments', '', $db_user)->query("SELECT count(p.orderId) size FROM payments p,users u where p.userid=u.id and p.payamount*100>0 ".$platform_str.$choose_servers_str." and p.status=1 ".$date_sql);
          if(count($pays)>0){
            $size=$pays[0]['size'];
            $all_size+=$size;
          }
        }else if($hidetype==2){
          $pays_data = M ('payments', '', $db_user)->query("SELECT count(distinct heroid) size FROM  payments p,users u where p.userid=u.id and p.payamount>0 ".$platform_str.$choose_servers_str.$date_sql);
          if(count($pays_data)>0){
            $size=$pays_data[0]['size'];
            $all_size+=$size;
          }
        }
        if($all_size<=$page_size){
          $page_size=$all_size;
        }


        if($hidetype==1){
          $pays = M ('payments', '', $db_user)->query("SELECT p.* FROM payments p,users u where p.userid=u.id and p.payamount*100>0 ".$platform_str.$choose_servers_str." and p.status=1 ".$date_sql."  order by p.payamount*100 desc LIMIT ".(($page-1)*$page_size)." , ".$page_size);
          foreach($pays as $pay){
              $db=$this->get_db_from_serverid($pay['serverid']);
              if(empty($db)) continue;
              $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

              $hero = M ('heroes', '', $db_hero)->query("select * from heroes where id=".$pay['heroid'] );
              if(count($hero)>0){
                $user=M ('users', '', $db_user)->query("select * from users where id=".$pay['userid']);

                $pay_orders[$pay['orderid']]['timestamp']=date('Y-m-d',$pay['timestamp']);
                $pay_orders[$pay['orderid']]['orderid']=$pay['orderid'];
                $pay_orders[$pay['orderid']]['payamount']=round($pay['payamount'],2);
                $pay_orders[$pay['orderid']]['paytype']=$pay['payid'];
                $pay_orders[$pay['orderid']]['channel']=$pay['type'];
                $pay_orders[$pay['orderid']]['status']=$pay['status'];

                $pay_orders[$pay['orderid']]['username']=$user[0]['name'];
                $platform=$this->get_platform($user[0]['platform']);
                $pay_orders[$pay['orderid']]['platform']=$platform['name'];
                $pay_orders[$pay['orderid']]['heroid']=$hero[0]['id'];
                $pay_orders[$pay['orderid']]['heroname']=$hero[0]['name'];
                $pay_orders[$pay['orderid']]['type']=$this->getType($hero[0]['type']);
                $pay_orders[$pay['orderid']]['vip']=$hero[0]['vip'];
                $pay_orders[$pay['orderid']]['level']=$hero[0]['level'];
              }
          }
        }else if($hidetype==2){
            $pays_data = M ('payments', '', $db_user)->query("SELECT p.*,sum(p.payamount*100) payamount FROM payments p,users u where p.userid=u.id and p.payamount>0 ".$platform_str.$choose_servers_str.$date_sql." group by p.heroid order by payamount desc limit ".(($page-1)*$page_size)." , ".$page_size);
            foreach($pays_data as $pay){
                $db=$this->get_db_from_serverid($pay['serverid']);
                if(empty($db)) continue;
                $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

                $hero = M ('heroes', '', $db_hero)->query("select * from heroes where id=".$pay['heroid'] );
                $user=M ('users', '', $db_user)->query("select * from users where id=".$pay['userid']);

                $pay_ranks[$pay['heroid']]['timestamp']=date('Y-m-d',$pay['timestamp']);
                $pay_ranks[$pay['heroid']]['orderid']=$pay['orderid'];
                $pay_ranks[$pay['heroid']]['payamount']=round($pay['payamount']/100,2);
                $pay_ranks[$pay['heroid']]['paytype']=$pay['payid'];
                $pay_ranks[$pay['heroid']]['channel']=$pay['type'];
                $pay_ranks[$pay['heroid']]['status']=$pay['status'];

                $pay_ranks[$pay['heroid']]['username']=$user[0]['name'];
                $platform=$this->get_platform($user[0]['platform']);
                $pay_ranks[$pay['heroid']]['platform']=$platform['name'];
                $pay_ranks[$pay['heroid']]['heroid']=$hero[0]['id'];
                $pay_ranks[$pay['heroid']]['heroname']=$hero[0]['name'];
                $pay_ranks[$pay['heroid']]['type']=$this->getType($hero[0]['type']);
                $pay_ranks[$pay['heroid']]['vip']=$hero[0]['vip'];
                $pay_ranks[$pay['heroid']]['level']=$hero[0]['level'];
            }
          }

        $this->assign ( 'hidetype', $hidetype );
        $this->assign ( 'page_size', $page_size );
        $this->assign ( 'page', $page );
        $this->assign ( 'size', $all_size );
        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        $this->assign ( 'pay_orders', $pay_orders );
        $this->assign ( 'pay_ranks', $pay_ranks );
        $this->assign ( 'choose_servers', $choose_servers );
    		$this->assign ( 'servers', $servers );
    		$this->assign ( 'choose_platforms', $values );
    		$this->assign ( 'platforms', $platforms );
        $this->display ();
    }

    public function first_recharge() {
        $this->menu ();
        $url='/zebra/Home/RMBPlayer/first_recharge';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

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

        $pays=array();
        $total_payamout=0;
        $total_heroid=0;
        foreach ($choose_servers as $choose_server) {
          $server=$this->get_server_from_id($choose_server);
          if(empty($server)) continue;
          foreach ($values as $value) {
            $db=$this->get_db_from_serverid($server['server_id']);
            if(empty($db)) continue;

            $pay_data = M ('payments', '', $db_user)->query("select level,count(heroid) count,sum(pay) pay from (SELECT *,min(p.heroLevel) as level,sum(p.payamount*100) pay FROM payments p,users u where p.userid=u.id and p.payAmount*100>0 and p.serverId=".$choose_server." and u.platform=".$value."  group by p.heroid) pays group by level");
            foreach($pay_data as $pay){
                $pays[$pay['level']]['level']=$pay['level'];
                $pays[$pay['level']]['count']=$pay['count'];
                $pays[$pay['level']]['pay']=$pay['pay']/100;

                $total_heroid+=$pay['count'];
                $total_payamout+=$pay['pay']/100;
            }
          }
        }


        $this->assign ( 'pays', $pays );
        $this->assign ( 'total_payamout', $total_payamout );
        $this->assign ( 'total_heroid', $total_heroid );
        $this->assign ( 'choose_servers', $choose_servers );
    		$this->assign ( 'servers', $servers );
    		$this->assign ( 'choose_platforms', $values );
    		$this->assign ( 'platforms', $platforms );
        $this->display ();
    }

    public function first_recharge_time() {
        $this->menu ();
        $url='/zebra/Home/RMBPlayer/first_recharge_time';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

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

        $pays=array();
        $total_payamout=0;
        $total_heroid=0;
        foreach ($choose_servers as $choose_server) {
          $server=$this->get_server_from_id($choose_server);
          if(empty($server)) continue;
          foreach ($values as $value) {
            $db=$this->get_db_from_serverid($server['server_id']);
            if(empty($db)) continue;
            $pay_data = M ('payments', '', $db_user)->query("select date,count(heroid) count,sum(pay) pay from (SELECT *,min(UNIX_TIMESTAMP(from_unixtime(timestamp,'%Y%m%d'))) as date,sum(p.payamount*100) pay FROM payments p,users u where p.userid=u.id and p.payAmount*100>0 and p.serverId=".$choose_server." and u.platform=".$value."  group by p.heroid) pays group by date");
            foreach($pay_data as $pay){
                $pays[$pay['date']]['date']=$pay['date'];
                $pays[$pay['date']]['count']=$pay['count'];
                $pays[$pay['date']]['pay']=$pay['pay']/100;

                $total_heroid+=$pay['count'];
                $total_payamout+=$pay['pay']/100;
            }
          }
        }

        $this->assign ( 'pays', $pays );
        $this->assign ( 'total_payamout', $total_payamout );
        $this->assign ( 'total_heroid', $total_heroid );
        $this->assign ( 'choose_servers', $choose_servers );
        $this->assign ( 'servers', $servers );
        $this->assign ( 'choose_platforms', $values );
        $this->assign ( 'platforms', $platforms );
        $this->display ();
    }

    public function pay_rate() {
        $this->menu ();
        $url='/zebra/Home/RMBPlayer/pay_rate';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

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
        $pays=array();
        $date=date('Y-m-d',time()+86400);

        foreach ($choose_servers as $choose_server) {
          $server=$this->get_server_from_id($choose_server);
          if(empty($server)) continue;
          foreach ($values as $value) {
            $db=$this->get_db_from_serverid($server['server_id']);
            if(empty($db)) continue;
            $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

            $all_pay = M ( 'payments', '', $db_user)->query ( "SELECT  count(distinct p.heroid) pay_hero,count(distinct p.userid) pay_user from payments p,users u where p.userid=u.id and u.platform=".$value." and p.serverid=".$server['server_id']." and p.status=1 and p.payamount*10>0");
            if(count($all_pay)>0) {
                $platform=$this->get_platform($value);
                $pays[$server['server_id']][$value]['date']=date('Y-m-d',time());
                $pays[$server['server_id']][$value]['platform']=$platform['name'];
                $pays[$server['server_id']][$value]['pay_hero']+=$all_pay[0]['pay_hero'];
                $pays[$server['server_id']][$value]['pay_user']+=$all_pay[0]['pay_user'];
            }
            $pays[$server['server_id']][$value]['server_name']=$server['server_name'];
            $hero = M ( 'heroes_stat', '', $db_hero)->query ( "select count(distinct s.heroid) role from ".$db['db_hero'].".heroes_stat s,".$db['db_hero'].".heroes h, ".$this->get_dbuser_name().".users u where h.userid=u.id and u.platform=".$value." and s.heroid=h.id and s.regTime <= UNIX_TIMESTAMP('".$date."')+86400" );
            if(count($hero)>0) {
                $pays[$server['server_id']][$value]['hero']+=$hero[0]['role'];
            }
            $user = M ( 'heroes', '',  $db_hero)->query ( "select count(distinct h.userid) user from ".$db['db_hero'].".heroes h,".$db['db_hero'].".heroes_stat s, ".$this->get_dbuser_name().".users u where h.userid=u.id and h.id=s.heroid and h.userid!=0 and u.platform=".$value ." and s.regTime <= UNIX_TIMESTAMP('".$date."')+86400" );
            if(count($user)>0) {
                $pays[$server['server_id']][$value]['user']+=$user[0]['user'];
            }
          }
        }

        $this->assign ( 'pays', $pays );
        $this->assign ( 'choose_servers', $choose_servers );
    		$this->assign ( 'servers', $servers );
    		$this->assign ( 'choose_platforms', $values );
    		$this->assign ( 'platforms', $platforms );
        $this->display ();
    }
}
