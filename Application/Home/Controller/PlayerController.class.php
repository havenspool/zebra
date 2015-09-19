<?php

namespace Home\Controller;

use Think\Controller;

class PlayerController extends LayoutController {

    public function hero_query() {
        $this->menu ();
        $url='/zebra/Home/Player/hero_query';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $servers=$this->get_all_server();
        $platforms=$this->get_all_platforms();

        $hero=array();
        $materials=array();

        $flag=true;
        $heroid = $_POST['heroId'];
        $heroname = $_POST['heroName'];

  			$server_id = $_POST['server_select'];
        if($_POST['server_select']=="") $flag=false;
        if($heroid==""&&$heroname=="") $flag=false;

        if($flag){
            if($heroid=="") $heroid=0;
            $db=$this->get_db_from_serverid($server_id);
            if(!empty($db)){
              $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

              $hero = M ('heroes', '', $db_hero)->query("select * from heroes where (id=".$heroid." or name='".$heroname."')" );
              if(count($hero)>0)
                  $materials = M ('heroes_bag', '', $db_hero)->query("select * from heroes_bag where heroId=".$hero[0]['id'] );
            }
        }

        $items=$this->get_items();
        $materials_bless=$this->get_material_bless();

        $materials_all=array();
        foreach($materials as $material){
            $material['materialname']=$items[$material['materialid']]['name'];
            if($materials_bless[$material['bless']]!=0) $material['bless']=$materials_bless[$material['bless']]['name'];
            array_push ($materials_all, $material);
        }

        $this->assign ( 'hero', $hero[0]);
        $this->assign ( 'materials', $materials_all);
        $this->assign ( 'servers', $servers);
        $this->assign ( 'platforms', $platforms );
        $this->display ();
    }

    public function hero_list_player() {
        $this->menu ();
        $url='/zebra/Home/Player/hero_list_player';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $flag=true;
        $userId = $_POST['userId'];
        $userName = $_POST['userName'];
        if($_POST['userId']==""&&$_POST['userName']==""){
            $flag=false;
        }

        if($_POST['userId']==""){
            $userId=0;
        }

        $hero_datas=array();
        if($flag){
            $servers=$this->get_all_server();
            $db_user=$this->get_dbuser();
            $datas=array();
            foreach($servers as $server){
                $db=$this->get_db_from_serverid($server['server_id']);
                if(empty($db)) continue;
                $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

                $users=array();
                if($userId!=0) $users=M ('users', '', $db_user)->query("select * from users where id=".$userId );
                else if($userName!="") $users=M ('users', '', $db_user)->query("select * from users where name='".$userName."'" );
                if(count($users)>0){
                    $heroes = M ('heroes', '', $db_hero)->query("select * from heroes where userId=".$users[0]['id'] );
                    array_push ($datas, $heroes);
                }
            }
            foreach($datas as $tmp){
                foreach($tmp as $data){
                    $data['platform']=$this->get_platform($data['platform'])['name'];
                    $data['type']=$this->getType($data['type']);
                    $hero_datas[$data['id']]=$data;
                }
            }
        }

        $this->assign ( 'hero_datas', $hero_datas);
        $this->display ();
    }

    public function user_rank() {
        $this->menu ();
        $url='/zebra/Home/Player/user_rank';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $flag=true;
        $userId = $_POST['userId'];
        $userName = $_POST['userName'];
        if($_POST['userId']==""&&$_POST['userName']==""){
            $flag=false;
        }

        if($_POST['userId']==""){
            $userId=0;
        }

        $hero_datas=array();
        if($flag) {
            $servers = $this->get_all_server();
            $db_user=$this->get_dbuser();
            foreach ($servers as $server) {
                $db=$this->get_db_from_serverid($server['server_id']);
                if(empty($db)) continue;
                $db_hero = "mysql://" . $db['user'] . ":" . $db['pwd'] . "@" . $db['host'] . ":" . $db['port'] . "/" . $db['db_hero'];

                $users = M('users', '', $db_user)->query("select id,name from users where (id=" . $userId . " or name='" . $userName . "')");
                if (count($users) > 0) {
                    $heroes = M('heroes', '', $db_hero)->query("select * from heroes where userId=" . $users[0]['id']);
                    foreach ($heroes as $hero) {
                        $sendData = array(
                            "cmd" => "sys/get_rank",
                            "user" => "hank",
                            "pwd" => "b6dfea72ba631c88abe4a1d17114bfcf",
                            "heroId" => $hero['id']);
                        $stat = $this->conn_server($server['host'], $server['port'], $sendData);
                        $hero_datas[$hero['id']]['userid'] = $users[0]['id'];
                        $hero_datas[$hero['id']]['username'] = $users[0]['name'];
                        $hero_datas[$hero['id']]['platform'] =$this->get_platform($hero['platform'])['name'];
                        $hero_datas[$hero['id']]['id'] = $hero['id'];
                        $hero_datas[$hero['id']]['name'] = $hero['name'];
                        $hero_datas[$hero['id']]['type'] =$this->getType($hero['type']);
                        $hero_datas[$hero['id']]['vip'] = $hero['vip'];
                        $hero_datas[$hero['id']]['level'] = $hero['level'];
                        //type : 1: 等级排行 2: 战力排行 3: 财富排行 4: 武神榜  5: 竞技榜  6: 乱斗榜 7: 成就榜 8: 公会榜
                        if($stat['ranks'][0]==0) $hero_datas[$hero['id']]['level_rank'] = '未上榜';
                        else $hero_datas[$hero['id']]['level_rank'] = $stat['ranks'][0];
                        if($stat['ranks'][1]==0) $hero_datas[$hero['id']]['power_rank'] = '未上榜';
                        else $hero_datas[$hero['id']]['power_rank'] = $stat['ranks'][1];
                        if($stat['ranks'][3]==0) $hero_datas[$hero['id']]['jimmu_rank'] = '未上榜';
                        else $hero_datas[$hero['id']]['jimmu_rank'] = $stat['ranks'][3];
                        if($stat['ranks'][4]==0) $hero_datas[$hero['id']]['offline_pk_rank'] = '未上榜';
                        else $hero_datas[$hero['id']]['offline_pk_rank'] = $stat['ranks'][4];
                        if($stat['ranks'][5]==0) $hero_datas[$hero['id']]['free_pk_rank'] = '未上榜';
                        else $hero_datas[$hero['id']]['free_pk_rank'] = $stat['ranks'][5];
                        if($stat['ranks'][6]==0) $hero_datas[$hero['id']]['achieve_rank'] = '未上榜';
                        else $hero_datas[$hero['id']]['achieve_rank'] = $stat['ranks'][6];
                        if($stat['ranks'][7]==0) $hero_datas[$hero['id']]['guild_rank'] = '未上榜';
                        else $hero_datas[$hero['id']]['guild_rank'] = $stat['ranks'][7];
                    }
                }
            }
        }
        $this->assign ( 'hero_datas', $hero_datas);
        $this->display ();
    }

    public function currency_analysis_player() {
        $this->menu ();
        $url='/zebra/Home/Player/currency_analysis_player';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $servers=$this->get_all_server();

        $flag=true;
        $heroid = $_POST['heroId'];
        $heroname = $_POST['heroName'];
        $server_id = $_POST['server_select'];
        if($_POST['server_select']=="") $flag=false;
        if($heroid==""&&$heroname=="") $flag=false;

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        if($_POST['start_date']==""){
            $start_date = date('Y-m-d',time()-3600*24*30);
        }
        if($_POST['end_date']==""){
            $end_date = date('Y-m-d',time());
        }

        $type = $_POST['money_radio'];
        if($type==0) $type=1;


        if($flag){
            if($heroid=="") $heroid=0;
            $db=$this->get_db_from_serverid($server_id);
            if(!empty($db)){
              $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

              $hero = M ('heroes', '', $db_hero)->query("select * from heroes where (id=".$heroid." or name='".$heroname."')" );
              if(count($hero)>0)
                  $materials = M ('heroes_bag', '', $db_hero)->query("select * from heroes_bag where heroId=".$hero[0]['id'] );
            }
        }
        $source_types=array();
        $use_types=array();
        $dates=array();
        $periods=array();

        $money_datas=array();
        if($flag) {
            if ($heroid == "") $heroid = 0;
            $db=$this->get_db_from_serverid($server_id);
            if(!empty($db)){
              $db_hero="mysql://".$db['user'].":".$db['pwd']."@".$db['host'].":".$db['port']."/".$db['db_hero'];

              $hero = M ('heroes', '', $db_hero)->query("select * from heroes where (id=".$heroid." or name='".$heroname."')" );
              if(count($hero)>0)
                  $money_datas = M ('heroes_uses', '', $db_hero)->where(" heroId=".$hero[0]['id']." and costType=".$type." and time >= UNIX_TIMESTAMP('".$start_date."') and time <= UNIX_TIMESTAMP('".$end_date."')")->select();
            }
        }



        $moneys=$this->get_currency_types();
        $money_type="";
        if($type==1) $money_type="gold";
        else if($type==2) $money_type="coins";
        foreach($money_datas as $money_data){
            $tmp_date=strtotime(date("Y-m-d ", $money_data['time']));
            $dates[$tmp_date]['date']=$tmp_date;

            $tmp=$money_data['time']-strtotime(date("Y-m-d ", $money_data['time']));
            $period=(int)($tmp/3600);
            $periods[$period]['date']=$period;

            if($money_data['counttype']==0){
                $source_types[$money_data['type']]['name']=$moneys[$money_type]['source'][$money_data['type']];
                $source_types[$money_data['type']]['num']+=$money_data['cost'];
                $dates[$tmp_date]['source']+=$money_data['cost'];
                $dates[$tmp_date]['use']+=0;
                $periods[$period]['source']+=$money_data['cost'];
                $periods[$period]['use']+=0;
            }else if($money_data['counttype']==1){
                $use_types[$money_data['type']]['name']=$moneys[$money_type]['use'][$money_data['type']];
                $use_types[$money_data['type']]['num']+=$money_data['cost'];
                $dates[$tmp_date]['use']+=$money_data['cost'];
                $dates[$tmp_date]['source']+=0;
                $periods[$period]['use']+=$money_data['cost'];
                $periods[$period]['source']+=0;
            }
        }

        foreach($dates as $date){
            $dates[$date['date']]['sum']=$dates[$date['date']]['source']-$dates[$date['date']]['use'];
        }

        foreach($periods as $period){
            $periods[$period['date']]['sum']=$periods[$period['date']]['source']-$periods[$period['date']]['use'];
        }

        sort($periods);

        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        $this->assign ( 'type', $type);
        $this->assign ( 'source_types', $source_types );
        $this->assign ( 'use_types', $use_types );
        $this->assign ( 'dates', $dates );
        $this->assign ( 'periods', $periods );
        $this->assign ( 'servers', $servers);
        $this->display ();
    }


}
