<?php

namespace Home\Controller;

use Think\Controller;

class HomeController extends LayoutController {

    public function daily() {
        $this->menu ();
        $url='/zebra/Home/Home/daily';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $check_date = $_POST['check_date'];
        if($_POST['check_date']==""){
            $check_date = date('Y-m-d',time());
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

        $daily=array();
        $all_user=array();
        // $daily['date']=$check_date;
        $db_user=$this->get_dbuser();
        foreach ($choose_servers as $choose_server) {
            $server=$this->get_server_from_id($choose_server);
            if(empty($server)) continue;
            foreach ($values as $value) {
//              if($server['platform']!=$value) continue;
              $db=$this->get_db_from_serverid($server['server_id']);
              if(empty($db)) continue;
              $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

              $platform=$this->get_platform($value);
              $daily[$value][$choose_server]['platform_name']=$platform['name'];
              $daily[$value][$choose_server]['server_name']=$server['server_name'];

              $all_pay_num = M ( 'payments', '', $db_user)->query ( "SELECT  count(distinct p.userid) count,sum(p.payamount) payamount from payments p,users u where p.userid=u.id and u.platform=".$value." and p.serverId=".$server['server_id']." and p.status=1 and UNIX_TIMESTAMP(from_unixtime(p.timestamp,'%Y-%m-%d')) <= UNIX_TIMESTAMP('".$check_date."')");
              if(count($all_pay_num)>0) {
                  $daily[$value][$choose_server]['all_pay_num']+=$all_pay_num[0]['count'];
                  $daily[$value][$choose_server]['all_payamount']+=$all_pay_num[0]['payamount'];
              }
              $all_user_num = M ( 'heroes', '',  $db_hero)->query ( "select count(distinct h.userid) user from ".$db['db_hero'].".heroes h,".$db['db_hero'].".heroes_stat s,".$this->get_dbuser_name().".users u where h.id=s.heroid and u.id=h.userid and h.userid!=0 and u.platform=".$value ." and s.regTime <= UNIX_TIMESTAMP('".$check_date."')+86400" );
              if(count($all_user_num)>0) {
                  $daily[$value][$choose_server]['all_user_num']=$all_user_num[0]['user'];
              }
              $pay_date = M ( 'payments', '', $db_user)->query ( "SELECT  count(distinct p.userid) pay_num,sum(p.payamount) payamount,count(p.orderid) pay_times from payments p,users u where p.userid=u.id and u.platform=".$value." and p.serverid=".$server['server_id']." and p.status=1 and UNIX_TIMESTAMP(from_unixtime(p.timestamp,'%Y-%m-%d')) = UNIX_TIMESTAMP('".$check_date."')");
              if(count($pay_date)>0) {
                  $daily[$value][$choose_server]['pay_num']+=$pay_date[0]['pay_num'];
                  $daily[$value][$choose_server]['payamount']+=$pay_date[0]['payamount'];
                  $daily[$value][$choose_server]['pay_times']+=$pay_date[0]['pay_times'];
              }
              $hero = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role from ".$db['db_hero'].".heroes_stat s,".$db['db_hero'].".heroes h ,".$this->get_dbuser_name().".users u where u.id=h.userid and u.platform=".$value." and s.heroid=h.id and UNIX_TIMESTAMP(from_unixtime(s.regTime,'%Y-%m-%d')) = UNIX_TIMESTAMP('".$check_date."')" );
              if(count($hero)>0) {
                  $daily[$value][$choose_server]['new_hero']+=$hero[0]['role'];
              }
              $user = M ( 'users', '',  $db_user)->query ( "select FROM_UNIXTIME(UNIX_TIMESTAMP(h.created), '%Y-%m-%d') day,count(h.id) user from users h where platform=".$value." and UNIX_TIMESTAMP(from_unixtime(UNIX_TIMESTAMP(h.created),'%Y-%m-%d'))= UNIX_TIMESTAMP('".$check_date."')" );
              if(count($user)>0) {
                  $all_user[$value]['new_user']=$user[0]['user'];
              }

              //gmserver
              if(strtotime($check_date)==strtotime(date ('Y-m-d', time ()))){
                  $login_hero = M ( 'heroes_stat', '', $db_hero)->query ( "select count(distinct h.id) login_hero,count(h.userid) login_user from ".$db['db_hero'].".heroes h,".$db['db_hero'].".heroes_stat s ,".$this->get_dbuser_name().".users u where u.id=h.userid and s.heroid=h.id and h.userid<>0 and u.platform=".$value." and UNIX_TIMESTAMP(from_unixtime(s.lastSaveTime,'%Y-%m-%d'))= UNIX_TIMESTAMP('".$check_date."')" );
                  $daily[$value][$choose_server]['login_hero']+=$login_hero[0]['login_hero'];
                  $daily[$value][$choose_server]['login_user']+=$login_hero[0]['login_user'];
              }else{
                  $logines = M ( 'logines_data', '',  $this->get_gmserver())->where ( " platform=".$value." and UNIX_TIMESTAMP(from_unixtime(date,'%Y-%m-%d')) = UNIX_TIMESTAMP('".$check_date."')" )->select();
                  foreach($logines as $login){
                      $daily[$value][$choose_server]['login_hero']+=$login['login_hero'];
                      $daily[$value][$choose_server]['login_user']+=$login['login_user'];
                  }
              }
            }
        }
        // print_r($daily);
        $all_daily=array();
        foreach ($daily as $tmp){
    			foreach ($tmp as $data){
            $all_daily['all_pay_num']+=$data['all_pay_num'];
            $all_daily['all_payamount']+=$data['all_payamount'];
            $all_daily['all_user_num']+=$data['all_user_num'];
            $all_daily['pay_num']+=$data['pay_num'];
            $all_daily['payamount']+=$data['payamount'];
            $all_daily['pay_times']+=$data['pay_times'];
            $all_daily['new_hero']+=$data['new_hero'];
            // $all_daily['new_user']+=$data['new_user'];
            $all_daily['login_hero']+=$data['login_hero'];
            $all_daily['login_user']+=$data['login_user'];
          }
        }
        foreach($all_user as $data){
          $all_daily['new_user_num']+=$data['new_user'];
        }

        $this->assign ( 'all_daily', $all_daily );
        $this->assign ( 'daily', $daily );
        $this->assign ( 'choose_servers', $choose_servers );
    		$this->assign ( 'servers', $servers );
    		$this->assign ( 'choose_platforms', $values );
    		$this->assign ( 'platforms', $platforms );
        $this->assign('check_date',$check_date);
        $this->display ();
    }
}
