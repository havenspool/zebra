<?php

namespace Home\Controller;

use Think\Controller;

class HomeController extends LayoutController {

    public function daily() {
        $this->menu ();
        $this->assign ( 'title', '每日报表' );
        $this->assign ( 'url', '/zebra/Home/Home/daily' );

        $check_date = $_POST['check_date'];
        if($_POST['check_date']==""){
            $check_date = date('Y-m-d',time());
        }
        $servers=$this->get_all_server();
        $values=$_POST['checkbox'];
        $checks=$this->choose_server($values);

        $daily=array();
        $daily['date']=$check_date;
        foreach ($checks as $check) {
            $db=$this->get_db_from_id($check['plat_id']);
            if(empty($db)) continue;
            $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
            $db_user="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_user'];
            //channel
            $server=$this->get_server_from_id($check['plat_id']);

            $all_pay_num = M ( 'payments', '', $db_user)->query ( "SELECT  count(p.userid) count,sum(p.payamount) payamount from payments p,users u where p.userid=u.id and u.platform=".$server['channel']." and p.status=1 and UNIX_TIMESTAMP(from_unixtime(p.timestamp,'%Y-%m-%d')) <= UNIX_TIMESTAMP('".$daily['date']."')");
            if(count($all_pay_num)>0) {
                $daily['all_pay_num']+=$all_pay_num[0]['count'];
                $daily['all_payamount']+=$all_pay_num[0]['payamount'];
            }
            $all_user_num = M ( 'users', '', $db_user)->query ( "SELECT  count(id) user from users where platform=".$server['channel'] );
            if(count($all_user_num)>0) {
                $daily['all_user_num']=$all_user_num[0]['user'];
            }
            $pay_date = M ( 'payments', '', $db_user)->query ( "SELECT  count(p.userid) pay_num,sum(p.payamount) payamount,count(p.orderid) pay_times from payments p,users u where p.userid=u.id and u.platform=".$server['channel']." and p.status=1 and UNIX_TIMESTAMP(from_unixtime(p.timestamp,'%Y-%m-%d')) = UNIX_TIMESTAMP('".$daily['date']."')");
            if(count($pay_date)>0) {
                $daily['pay_num']+=$pay_date[0]['pay_num'];
                $daily['payamount']+=$pay_date[0]['payamount'];
                $daily['pay_times']+=$pay_date[0]['pay_times'];
            }
            $hero = M ( 'heroes_stat', '', $db_hero)->query ( "select FROM_UNIXTIME(s.regTime, '%Y-%m-%d') day,count(s.heroid) role from heroes_stat s,heroes h where h.platform=".$server['channel']." and s.heroid=h.id and UNIX_TIMESTAMP(from_unixtime(s.regTime,'%Y-%m-%d')) = UNIX_TIMESTAMP('".$daily['date']."')" );
            if(count($hero)>0) {
                $daily['new_hero']+=$hero[0]['role'];
            }
            $user = M ( 'users', '',  $db_user)->query ( "select FROM_UNIXTIME(UNIX_TIMESTAMP(h.created), '%Y-%m-%d') day,count(h.id) user from users h where platform=".$server['channel']." and UNIX_TIMESTAMP(from_unixtime(UNIX_TIMESTAMP(h.created),'%Y-%m-%d'))= UNIX_TIMESTAMP('".$daily['date']."')" );
            if(count($user)>0) {
                $daily['new_user']+=$user[0]['user'];
            }

            //gmserver
            if(strtotime($daily['date'])==strtotime(date ('Y-m-d', time ()))){
                $login_hero = M ( 'heroes_stat', '', $db_hero)->query ( "select count(h.id) login_hero,count(h.userid) login_user from game.heroes h,game.heroes_stat s where s.heroid=h.id and h.userid<>0 and h.platform=".$server['channel']." and UNIX_TIMESTAMP(from_unixtime(s.lastSaveTime,'%Y-%m-%d'))= UNIX_TIMESTAMP('".$daily['date']."')" );
                $daily['login_hero']+=$login_hero[0]['login_hero'];
                $daily['login_user']+=$login_hero[0]['login_user'];
            }else{
                $logines = M ( 'logines_data', '',  $this->get_gmserver())->where ( " platform=".$server['channel']." and UNIX_TIMESTAMP(from_unixtime(date,'%Y-%m-%d')) = UNIX_TIMESTAMP('".$daily['date']."')" )->select();
                foreach($logines as $login){
                    $daily['login_hero']+=$login['login_hero'];
                    $daily['login_user']+=$login['login_user'];
                }
            }
        }

        $this->assign ( 'daily', $daily );
        $this->assign ( 'checks', $checks );
        $this->assign ( 'servers', $servers );
        $this->assign('check_date',$check_date);
        $this->display ();
    }

    public function daily_bak() {
        $this->menu ();
        $this->assign ( 'title', '每日报表' );
        $this->assign ( 'url', '/zebra/Home/Home/daily' );

        $check_date = $_POST['check_date'];
        if($_POST['check_date']==""){
            $check_date = date('Y-m-d',time());
        }
        $servers=$this->get_all_server();
        $values=$_POST['checkbox'];
        $checks=$this->choose_server($values);
        $daily=array();
        $daily['day_time']=date ( 'Y-m-d H:i:s', time ());
        $daily['date']['date']=date ('Y-m-d', time ());
        $daily['last_date']['date']=date ('Y-m-d', time ()-86400);
        $dbs=$this->get_db();

        foreach ($dbs as $db) {
            $db_hero = "mysql://" . $db['user'] . ":" . $db['pwd'] . "@" . $db['host'] . ":" . $db['port'] . "/" . $db['db_hero'];
            $db_user = "mysql://" . $db['user'] . ":" . $db['pwd'] . "@" . $db['host'] . ":" . $db['port'] . "/" . $db['db_user'];

            $pays = M ('payments', '', $db_user)->query("select count(Distinct heroid) num, count(orderid) count,sum(payamount*100)/100 pay from payments where UNIX_TIMESTAMP(from_unixtime(timestamp,'%Y%m%d')) = UNIX_TIMESTAMP('".$daily['date']['date']."')");
            if(count($pays)>0) {
                $daily['date']['pay']=$pays[0]['pay'];
                $daily['date']['pay_times']=$pays[0]['count'];
                $daily['date']['num']=$pays[0]['num'];
            }

            $last_pays = M ('payments', '', $db_user)->query("select count(orderid) num,sum(payamount*100)/100 pay from payments where UNIX_TIMESTAMP(from_unixtime(timestamp,'%Y%m%d')) = UNIX_TIMESTAMP('".$daily['last_date']['date']."')");
            if(count($last_pays)>0) {
                $daily['last_date']['pay']=$last_pays[0]['pay'];
                $daily['last_date']['pay_times']=$last_pays[0]['count'];
                $daily['last_date']['num']=$last_pays[0]['num'];
            }

            $heroes = M ('heroes_stat', '', $db_hero)->query("select count(heroid) new_hero from heroes_stat where UNIX_TIMESTAMP(FROM_UNIXTIME(regTime, '%Y-%m-%d'))=UNIX_TIMESTAMP('" . $daily['date']['date'] ."')");
            if(count($heroes)>0) $daily['date']['new_hero']=$heroes[0]['new_hero'];
            $heroes = M ('heroes_stat', '', $db_hero)->query("select count(heroid) new_hero from heroes_stat where UNIX_TIMESTAMP(FROM_UNIXTIME(regTime, '%Y-%m-%d'))=UNIX_TIMESTAMP('" . $daily['last_date']['date'] ."')");
            if(count($heroes)>0) $daily['last_date']['new_hero']=$heroes[0]['new_hero'];
            $heroes = M ('heroes_stat', '', $db_hero)->query("select count(heroid) login from heroes_stat where UNIX_TIMESTAMP(FROM_UNIXTIME(lastSaveTime, '%Y-%m-%d')) >= UNIX_TIMESTAMP('" . $daily['date']['date'] ."')");
            if(count($heroes)>0) $daily['date']['login']=$heroes[0]['login'];

            $users = M ('users', '', $db_user)->query("select count(id) new_user from users where UNIX_TIMESTAMP(FROM_UNIXTIME(created, '%Y-%m-%d'))=UNIX_TIMESTAMP('" . $daily['date']['date'] ."')");
            if(count($users)>0) $daily['date']['new_user']=$users[0]['new_user'];
            $users = M ('users', '', $db_user)->query("select count(id) new_user from users where UNIX_TIMESTAMP(FROM_UNIXTIME(created, '%Y-%m-%d'))=UNIX_TIMESTAMP('" . $daily['last_date']['date'] ."')");
            if(count($users)>0) $daily['last_date']['new_user']=$users[0]['new_user'];
        }

        $this->assign ( 'daily', $daily );

        $this->assign ( 'checks', $checks );
        $this->assign ( 'servers', $servers );
        $this->assign('check_date',$check_date);
        $this->display ();
    }
}