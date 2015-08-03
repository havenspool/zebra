<?php

namespace Home\Controller;

use Think\Controller;

class RMBPlayerController extends LayoutController {

    public function recharge_query() {
        $this->menu ();
        $this->assign ( 'title', '充值&查询' );
        $this->assign ( 'url', '/zebra/Home/RMBPlayer/recharge_query' );
        $servers=$this->get_all_server();

        $flag=true;
        $userName = $_POST['userName'];
        $heroId = $_POST['heroId'];
        $heroName = $_POST['heroName'];
        $db_id= $_POST['db_select'];
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

        $db=$this->get_db_from_id($db_id);
        $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
        $db_user="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_user'];

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
                    $hero_datas[$pay['orderid']]['platform']=$this->get_platfrom_name_from_platfrom($hero[0]['platform']);
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
                $hero_datas[$pays[0]['orderid']]['platform']=$this->get_platfrom_name_from_platfrom($hero[0]['platform']);
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
                $hero_datas[$pay['orderid']]['platform']=$this->get_platfrom_name_from_platfrom($hero[0]['platform']);
                $hero_datas[$pay['orderid']]['heroid']=$hero[0]['id'];
                $hero_datas[$pay['orderid']]['heroname']=$hero[0]['name'];
                $hero_datas[$pay['orderid']]['type']=$this->getType($hero[0]['type']);
                $hero_datas[$pay['orderid']]['vip']=$hero[0]['vip'];
                $hero_datas[$pay['orderid']]['level']=$hero[0]['level'];
            }
        }

        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        $this->assign ( 'hero_datas', $hero_datas );
        $this->assign ( 'servers', $servers );
        $this->display ();
    }

    public function recharge_statistic() {
        $this->menu ();
        $this->assign ( 'title', '充值分布' );
        $this->assign ( 'url', '/zebra/Home/RMBPlayer/recharge_statistic' );
        $servers=$this->get_all_server();
        $values=$_POST['checkbox'];
        $checks=$this->choose_server($values);

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        if($_POST['start_date']==""){
            $start_date = date('Y-m-d',time()-3600*24*30);
        }
        if($_POST['end_date']==""){
            $end_date = date('Y-m-d',time()+86400);
        }

        $pay_statistics=array();
        $pay_times=array();
        $all_pay_statistics=array();
        $all_pay_times=array();

        foreach ($checks as $check){
            //channel
            $server=$this->get_server_from_id($check['plat_id']);

            $pay_statistic= M('pay_statistic','', $this->get_gmserver())->where(" platform=".$server['channel']." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
            foreach($pay_statistic as $pay){
                //pay_statistic	platform	server_id	date	pay_part	num	pay
                $pay_statistics[$pay['date']][$pay['pay_part']]['platform']=$this->get_platfrom_name_from_platfrom($pay['platform']);
                $pay_statistics[$pay['date']][$pay['pay_part']]['server_id']=$pay['server_id'];
                $pay_statistics[$pay['date']][$pay['pay_part']]['date']=date("Y-m-d",$pay['date']);
                $pay_statistics[$pay['date']][$pay['pay_part']]['pay_part']=$this->get_pay_part($pay['pay_part']);
                $pay_statistics[$pay['date']][$pay['pay_part']]['num']=$pay['num'];
                $pay_statistics[$pay['date']][$pay['pay_part']]['pay']=$pay['pay'];

                $all_pay_statistics[$pay['pay_part']]['pay_part']=$this->get_pay_part($pay['pay_part']);
                $all_pay_statistics[$pay['pay_part']]['num']+=$pay['num'];
                $all_pay_statistics[$pay['pay_part']]['pay']+=$pay['pay'];
            }

            $pay_time= M('pay_time','', $this->get_gmserver())->where(" platform=".$server['channel']." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
            foreach($pay_time as $pay){
                //pay_statistic	platform	server_id	date	period	num	pay
                $pay_times[$pay['date']][$pay['period']]['platform']=$this->get_platfrom_name_from_platfrom($pay['platform']);
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
        $this->assign ( 'all_pay_statistics', $all_pay_statistics );
        $this->assign ( 'all_pay_times', $all_pay_times );
        $this->assign ( 'pay_statistics', $pay_statistics );
        $this->assign ( 'pay_times', $pay_times );
        $this->assign ( 'checks', $checks );
        $this->assign ( 'servers', $servers );
        $this->display ();
    }

    public function recharge_time() {
        $this->menu ();
        $this->assign ( 'title', '充值时间统计' );
        $this->assign ( 'url', '/zebra/Home/RMBPlayer/recharge_time' );
        $servers=$this->get_all_server();
        $values=$_POST['checkbox'];
        $checks=$this->choose_server($values);

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        if($_POST['start_date']==""){
            $start_date = date('Y-m-d',time()-3600*24*30);
        }
        if($_POST['end_date']==""){
            $end_date = date('Y-m-d',time()+86400);
        }

        $pay_times=array();
        $all_pay_times=array();

        foreach ($checks as $check){
            //channel
            $server=$this->get_server_from_id($check['plat_id']);
            $pay_time= M('pay_time','', $this->get_gmserver())->where(" platform=".$server['channel']." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
            foreach($pay_time as $pay){
                //pay_statistic	platform	server_id	date	period	num	pay
                $pay_times[$pay['date']][$pay['period']]['platform']=$this->get_platfrom_name_from_platfrom($pay['platform']);
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
        $this->assign ( 'checks', $checks );
        $this->assign ( 'servers', $servers );
        $this->display ();
    }

    public function recharge_amount() {
        $this->menu ();
        $this->assign ( 'title', '充值金额统计' );
        $this->assign ( 'url', '/zebra/Home/RMBPlayer/recharge_amount' );
        $servers=$this->get_all_server();
        $values=$_POST['checkbox'];
        $checks=$this->choose_server($values);

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        if($_POST['start_date']==""){
            $start_date = date('Y-m-d',time()-3600*24*30);
        }
        if($_POST['end_date']==""){
            $end_date = date('Y-m-d',time()+86400);
        }

        $pay_statistics=array();
        $all_pay_statistics=array();

        foreach ($checks as $check){
            //channel
            $server=$this->get_server_from_id($check['plat_id']);

            $pay_statistic= M('pay_statistic','', $this->get_gmserver())->where(" platform=".$server['channel']." and date >= UNIX_TIMESTAMP('".$start_date."') and date <= UNIX_TIMESTAMP('".$end_date."')")->select();
            foreach($pay_statistic as $pay){
                //pay_statistic	platform	server_id	date	pay_part	num	pay
                $pay_statistics[$pay['date']][$pay['pay_part']]['platform']=$this->get_platfrom_name_from_platfrom($pay['platform']);
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
        $this->assign ( 'checks', $checks );
        $this->assign ( 'servers', $servers );
        $this->display ();
    }

    public function recharge_rank() {
        $this->menu ();
        $this->assign ( 'title', '充值排行榜' );
        $this->assign ( 'url', '/zebra/Home/RMBPlayer/recharge_rank' );

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $date_sql="";
        if($_POST['start_date']!=""){
            $date_sql=" and timestamp >= UNIX_TIMESTAMP('".$start_date."')";
        }
        if($_POST['end_date']!=""){
            $date_sql=$date_sql." and timestamp <= UNIX_TIMESTAMP('".$end_date."')";
        }

        $servers=$this->get_all_server();
        $values=$_POST['checkbox'];
        $checks=$this->choose_server($values);
        $pay_orders=array();
        $pay_ranks=array();

        $server_ids=array();
        foreach ($checks as $check){
            $server=$this->get_server_from_id($check['plat_id']);
            $server_ids[$server['server_id']]=$server['server_id'];
        }
        $server_id_sql="";
        foreach($server_ids as $server_id){
            $server_id_sql=$server_id_sql." serverid=".$server_id." or";
        }
        $server_id_sql=substr($server_id_sql,0,-2);


        foreach ($checks as $check){
            $db=$this->get_db_from_id($check['plat_id']);
            if(empty($db)) continue;
            $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
            $db_user="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_user'];

            $pays = M ('payments', '', $db_user)->query("SELECT * FROM payments where 1=1 ".$date_sql."  order by payamount desc limit 0,20 ");
            foreach($pays as $pay){
                $hero = M ('heroes', '', $db_hero)->query("select * from heroes where userId=".$pay['userid'] );
                $user=M ('users', '', $db_user)->query("select * from users where id=".$pay['userid']);


                $pay_orders[$pay['orderid']]['timestamp']=date('Y-m-d',$pay['timestamp']);
                $pay_orders[$pay['orderid']]['orderid']=$pay['orderid'];
                $pay_orders[$pay['orderid']]['payamount']=round($pay['payamount'],2);
                $pay_orders[$pay['orderid']]['paytype']=$pay['payid'];
                $pay_orders[$pay['orderid']]['channel']=$pay['type'];
                $pay_orders[$pay['orderid']]['status']=$pay['status'];

                $pay_orders[$pay['orderid']]['username']=$user[0]['name'];
                $pay_orders[$pay['orderid']]['platform']=$this->get_platfrom_name_from_platfrom($hero[0]['platform']);
                $pay_orders[$pay['orderid']]['heroid']=$hero[0]['id'];
                $pay_orders[$pay['orderid']]['heroname']=$hero[0]['name'];
                $pay_orders[$pay['orderid']]['type']=$this->getType($hero[0]['type']);
                $pay_orders[$pay['orderid']]['vip']=$hero[0]['vip'];
                $pay_orders[$pay['orderid']]['level']=$hero[0]['level'];
            }

            $pays_data = M ('payments', '', $db_user)->query("SELECT *,sum(payamount*100) payamount FROM payments  where 1=1 and (".$server_id_sql.")".$date_sql." group by heroid order by payamount desc limit 0,20 ");
            foreach($pays_data as $pay){
                $hero = M ('heroes', '', $db_hero)->query("select * from heroes where id=".$pay['heroid'] );
                $user=M ('users', '', $db_user)->query("select * from users where id=".$pay['userid']);


                $pay_ranks[$pay['heroid']]['timestamp']=date('Y-m-d',$pay['timestamp']);
                $pay_ranks[$pay['heroid']]['orderid']=$pay['orderid'];
                $pay_ranks[$pay['heroid']]['payamount']=round($pay['payamount']/100,2);
                $pay_ranks[$pay['heroid']]['paytype']=$pay['payid'];
                $pay_ranks[$pay['heroid']]['channel']=$pay['type'];
                $pay_ranks[$pay['heroid']]['status']=$pay['status'];

                $pay_ranks[$pay['heroid']]['username']=$user[0]['name'];
                $pay_ranks[$pay['heroid']]['platform']=$this->get_platfrom_name_from_platfrom($hero[0]['platform']);
                $pay_ranks[$pay['heroid']]['heroid']=$hero[0]['id'];
                $pay_ranks[$pay['heroid']]['heroname']=$hero[0]['name'];
                $pay_ranks[$pay['heroid']]['type']=$this->getType($hero[0]['type']);
                $pay_ranks[$pay['heroid']]['vip']=$hero[0]['vip'];
                $pay_ranks[$pay['heroid']]['level']=$hero[0]['level'];
            }
        }

        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        $this->assign ( 'pay_orders', $pay_orders );
        $this->assign ( 'pay_ranks', $pay_ranks );
        $this->assign ( 'checks', $checks );
        $this->assign ( 'servers', $servers );
        $this->display ();
    }

    public function first_recharge() {
        $this->menu ();
        $this->assign ( 'title', '首充等级统计' );
        $this->assign ( 'url', '/zebra/Home/RMBPlayer/first_recharge' );

        $servers=$this->get_all_server();
        $values=$_POST['checkbox'];
        $checks=$this->choose_server($values);
        $pays=array();

        $server_ids=array();
        foreach ($checks as $check){
            $server=$this->get_server_from_id($check['plat_id']);
            $server_ids[$server['server_id']]=$server['server_id'];
        }
        $server_id_sql="";
        foreach($server_ids as $server_id){
            $server_id_sql=$server_id_sql." serverid=".$server_id." or";
        }
        $server_id_sql=substr($server_id_sql,0,-2);

        $total_payamout=0;
        $total_heroid=0;
        foreach ($checks as $check){
            $db=$this->get_db_from_id($check['plat_id']);
            if(empty($db)) continue;
            //$db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];
            $db_user="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_user'];

            $pay_data = M ('payments', '', $db_user)->query("select level,count(heroid) count,sum(pay) pay from (SELECT *,min(heroLevel) as level,sum(payamount*100) pay FROM payments where payAmount*100>0 group by heroid) pays group by level");
            foreach($pay_data as $pay){
                $pays[$pay['level']]['level']=$pay['level'];
                $pays[$pay['level']]['count']=$pay['count'];
                $pays[$pay['level']]['pay']=$pay['pay']/100;

                $total_heroid+=$pay['count'];
                $total_payamout+=$pay['pay']/100;
            }
        }

        $this->assign ( 'pays', $pays );
        $this->assign ( 'total_payamout', $total_payamout );
        $this->assign ( 'total_heroid', $total_heroid );
        $this->assign ( 'checks', $checks );
        $this->assign ( 'servers', $servers );
        $this->display ();
    }

}