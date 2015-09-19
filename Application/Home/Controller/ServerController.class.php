<?php

namespace Home\Controller;

use Think\Controller;

class ServerController extends LayoutController {

    public function add_server() {
        $this->menu ();
        $url='/zebra/Home/Server/add_server';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $platforms=$this->get_all_platforms();

        $flag=true;
        $server_id = $_POST['server_id'];
        $server_name = $_POST['server_name'];
        $host = $_POST['host'];
        $port = $_POST['port'];
        $platform = $_POST['platform_select'];

        if($_POST['server_id']==""||$_POST['server_name']==""||$_POST['host']==""
            ||$_POST['port']==""||$_POST['platform_select']=="")
            $flag=false;

        $server=array('server_id'=>$server_id,'server_name'=>$server_name,'host'=>$host,
                    'port'=>$port,'platform'=>$platform);

        if($flag){
            $Server = M('Server','', $this->get_gmserver());
            $Server->add($server);
        }

        if($flag) header("Location:/zebra/Home/Server/choose_server");

        $this->assign ('platforms', $platforms);
        $this->display ();
    }

    public function modify_server() {
        $this->menu ();
        $this->assign ( 'title', '修改服务器' );
        $url='/zebra/Home/Server/modify_server';
        $this->assign ( 'active_open_id', 12);
        $this->assign ( 'url', $url);

        $flag=true;
        $id = $_GET['id'];
        if($_GET['id']=="") $flag=false;
        $platforms=$this->get_all_platforms();
        $save=true;
        $server_id = $_POST['server_id'];
        $server_name = $_POST['server_name'];
        $host = $_POST['host'];
        $port = $_POST['port'];
        $_id = $_POST['_id'];
        $platform= $_POST['platform_select'];

        if($_POST['_id']==""||$_POST['server_id']==""||$_POST['server_name']==""||$_POST['host']==""
            ||$_POST['port']==""||$_POST['platform_select']=="")
            $save=false;

        if($flag) $save=false;

        $server=array();
        if($flag){
            $Server = M('Server','', $this->get_gmserver());
            $server=$Server->where('id='.$id)->select();
        }

        if($save){
            $server=array('server_id'=>$server_id,'server_name'=>$server_name,'host'=>$host,
                        'port'=>$port,'platform'=>$platform);
            $Server = M('Server','', $this->get_gmserver());
            // $Server->where(' id='.$_id)->delete();
            // $Server->add($server);
            $Server->where(' id='.$_id)->save($server);
            $this->reload_server();
        }

        if($save) header("Location:/zebra/Home/Server/choose_server");
        $this->assign ('server', $server[0]);
        $this->assign ('platforms', $platforms);
        $this->display ();
    }

    public function delete_server() {
        $this->menu ();
        $this->assign ( 'title', '删除服务器' );
        $url='/zebra/Home/Server/delete_server';
        $this->assign ( 'active_open_id', 12);
        $this->assign ( 'url', $url);

        $flag=true;
        $id = $_GET['id'];
        if($_GET['id']=="") $flag=false;

        if($flag){
            $Server = M('Server','', $this->get_gmserver());
            $Server->where('id='.$id)->delete();
        }

        if($flag) header("Location:/zebra/Home/Server/choose_server");
        $this->display ();
    }

    public function choose_server() {
        $this->menu ();
        $url='/zebra/Home/Server/choose_server';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $servers=$this->get_all_server();
        $servers_all=array();
        foreach($servers as $server){
            $server['platform_name']=$this->get_platform($server['platform'])['name'];
            array_push ($servers_all, $server);
        }

        $this->assign ('servers', $servers_all);
        $this->display ();
    }

    public function platform_manager() {
        $this->menu ();
        $url='/zebra/Home/Server/platform_manager';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);
        $platforms = M ( 'platforms', '', $this->get_gmserver())->query ( "SELECT  *  from platforms");
        $this->assign ('platforms', $platforms);
        $this->display ();
    }

    public function modify_platform() {
        $this->menu ();
        $url='/zebra/Home/Server/modify_platform';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $flag=true;
        $id = $_GET['id'];
        if($_GET['id']=="") $flag=false;
        $save=true;
        $platform_id = $_POST['platform'];
        $platform_name = $_POST['platform_name'];
        $enable = $_POST['enable'];

        if($_POST['platform']==""||$_POST['platform_name']==""||$_POST['enable']=="")
            $save=false;

        if($flag) $save=false;

        $platform=array();
        if($flag){
            $Platform = M('Platforms','', $this->get_gmserver());
            $platform=$Platform->where(' platform='.$id)->select();
        }

        if($save){
            $platform=array('name'=>$platform_name,'enable'=>$enable);
            $Platform = M('Platforms','', $this->get_gmserver());
            $Platform->where(' platform='.$platform_id)->save($platform);
        }

        if($save) header("Location:/zebra/Home/Server/platform_manager");
        $this->assign ('platform', $platform[0]);
        $this->display ();
    }

    public function add_platform() {
        $this->menu ();
        $url='/zebra/Home/Server/add_platform';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $platforms=$this->get_all_platforms();

        $flag=true;
        $platform_id = $_POST['platform'];
        $platform_name = $_POST['platform_name'];
        $enable = $_POST['enable'];

        if($_POST['platform']==""||$_POST['platform_name']==""||$_POST['enable']=="")
            $flag=false;

        $platform=array('platform'=>$platform_id,'server_id'=>$server_id,'name'=>$platform_name,'enable'=>$enable);

        if($flag){
            $Platform = M('Platforms','', $this->get_gmserver());
            $Platform->add($platform);
        }

        if($flag) header("Location:/zebra/Home/Server/platform_manager");

        $this->assign ('platforms', $platforms);
        $this->display ();
    }

    public function delete_platform() {
        $this->menu ();
        $this->assign ( 'title', '删除平台' );
        $url='/zebra/Home/Server/delete_platform';
        $this->assign ( 'active_open_id', 12);
        $this->assign ( 'url', $url);

        $flag=true;
        $id = $_GET['id'];
        if($_GET['id']=="") $flag=false;

        if($flag){
            $Platform = M('Platforms','', $this->get_gmserver());
            $Platform->where('platform='.$id)->delete();
        }

        if($flag) header("Location:/zebra/Home/Server/platform_manager");
        $this->display ();
    }

    public function db_manager() {
        $this->menu ();
        $url='/zebra/Home/Server/db_manager';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);
        $dbs = M ( 'db', '', $this->get_gmserver())->query ( "SELECT  *  from db");
        $this->assign ('dbs', $dbs);
        $this->display ();
    }

    public function modify_db() {
        $this->menu ();
        $url='/zebra/Home/Server/modify_db';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $flag=true;
        $id = $_GET['id'];
        if($_GET['id']=="") $flag=false;
        $save=true;
        $server_id = $_POST['server_select'];
        $user = $_POST['user'];
        $pwd = $_POST['pwd'];
        $host = $_POST['host'];
        $port = $_POST['port'];
        $db_hero = $_POST['db_hero'];

        if($_POST['server_select']==""||$_POST['user']==""||$_POST['pwd']==""||$_POST['host']==""||$_POST['port']==""||$_POST['db_hero']=="")
            $save=false;

        if($flag) $save=false;

        $db=array();
        if($flag){
          $Db = M('Db','', $this->get_gmserver());
          $db=$Db->where(' server_id='.$id)->select();
        }

        if($save){
          $db=array('server_id'=>$server_id,'user'=>$user,'pwd'=>$pwd,'host'=>$host,'port'=>$port,'db_hero'=>$db_hero);
          $Db = M('Db','', $this->get_gmserver());
          $Db->where(' server_id='.$server_id)->save($db);
        }

        if($save) header("Location:/zebra/Home/Server/db_manager");
        $servers=$this->get_all_server();
        $this->assign ('db', $db[0]);
        $this->assign ('servers', $servers);
        $this->display ();
    }

    public function add_db() {
        $this->menu ();
        $url='/zebra/Home/Server/add_db';
        $menu=$this->get_menu_from_url($url);
        $this->assign ( 'title', $menu['title']);
        $this->assign ( 'active_open_id', $menu['pid']);
        $this->assign ( 'url', $url);

        $servers=$this->get_all_server();

        $flag=true;
        $server_id = $_POST['server_select'];
        $user = $_POST['user'];
        $pwd = $_POST['pwd'];
        $host = $_POST['host'];
        $port = $_POST['port'];
        $db_hero = $_POST['db_hero'];

        if($_POST['server_select']==""||$_POST['user']==""||$_POST['pwd']==""||$_POST['host']==""||$_POST['port']==""||$_POST['db_hero']=="")
            $flag=false;

        $db=array('server_id'=>$server_id,'user'=>$user,'pwd'=>$pwd,'host'=>$host,'port'=>$port,'db_hero'=>$db_hero);

        if($flag){
            $Db = M('Db','', $this->get_gmserver());
            $db_=$Db->where(' server_id='.$server_id)->select();
            if(empty($db_))
              $Db->add($db);
        }

        if($flag) header("Location:/zebra/Home/Server/db_manager");

        $this->assign ('servers', $servers);
        $this->display ();
    }

    public function delete_db() {
        $this->menu ();
        $this->assign ( 'title', '删除数据库' );
        $url='/zebra/Home/Server/delete_db';
        $this->assign ( 'active_open_id', 12);
        $this->assign ( 'url', $url);

        $flag=true;
        $id = $_GET['id'];
        if($_GET['id']=="") $flag=false;

        if($flag){
            $Db = M('db','', $this->get_gmserver());
            $Db->where('server_id='.$id)->delete();
        }

        if($flag) header("Location:/zebra/Home/Server/db_manager");
        $this->display ();
    }
}
