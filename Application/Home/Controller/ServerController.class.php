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

        $flag=true;
        $server_id = $_POST['server_id'];
        $server_name = $_POST['server_name'];
        $host = $_POST['host'];
        $port = $_POST['port'];
        $channel = $_POST['channel'];
        $channel_name = $_POST['channel_name'];
        $db_id= $_POST['db_select'];

        if($_POST['server_id']==""||$_POST['server_name']==""||$_POST['host']==""
            ||$_POST['port']==""||$_POST['channel']==""||$_POST['channel_name']==""||$_POST['db_select']=="")
            $flag=false;

        $server=array('server_id'=>$server_id,'server_name'=>$server_name,'host'=>$host,
                    'port'=>$port,'channel'=>$channel,'channel_name'=>$channel_name,'db_id'=>$db_id);

        if($flag){
            $Server = M('Server','', $this->get_gmserver());
            $Server->add($server);
        }

        if($flag) header("Location:/zebra/Home/Server/choose_server");

        $dbs=$this->get_db();
        $this->assign ('dbs', $dbs);
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

        $save=true;
        $server_id = $_POST['server_id'];
        $server_name = $_POST['server_name'];
        $host = $_POST['host'];
        $port = $_POST['port'];
        $channel = $_POST['channel'];
        $channel_name = $_POST['channel_name'];
        $_id = $_POST['_id'];
        $db_id= $_POST['db_select'];

        if($_POST['_id']==""||$_POST['server_id']==""||$_POST['server_name']==""||$_POST['host']==""
            ||$_POST['port']==""||$_POST['channel']==""||$_POST['channel_name']==""||$_POST['db_select']=="")
            $save=false;

        if($flag) $save=false;

        $server=array();
        if($flag){
            $Server = M('Server','', $this->get_gmserver());
            $server=$Server->where('id='.$id)->select();
        }

        if($save){
            $server=array('server_id'=>$server_id,'server_name'=>$server_name,'host'=>$host,
                        'port'=>$port,'channel'=>$channel,'channel_name'=>$channel_name,'db_id'=>$db_id);
            $Server =M('Server');
            $Server->where('id='.$_id)->save($server);
            //有关server的缓存要更新
            $this->reload_server();
        }

        if($save) header("Location:/zebra/Home/Server/choose_server");
        $this->assign ('server', $server[0]);

        $dbs=$this->get_db();
        $this->assign ('dbs', $dbs);
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

        $save=false;
        $value = $_POST['submit_btn'];
        if($_POST['submit_btn']!=""&&$value>0) $save=true;

        $checks=array();
        $username=$this->getUsername();
        if($save){
            //保存选择的服务器
            for($i=1;$i<$value;$i++){
                if($_POST['checkbox'.$i]!="")
                    $checks[$_POST['checkbox'.$i]]=$_POST['checkbox'.$i];
            }
            S('choose_server_list_'.$username,NULL);
        }

        $choose_server_list = S('choose_server_list_'.$username);//从缓存获取已选择服务器
        if(empty($choose_server_list)){
            $choose_server_list=array();
        }

        if(!$save){
            foreach($choose_server_list as $choose_server){
                if($choose_server['check']=='checked'){
                    $checks[$choose_server['id']]=$choose_server['id'];
                }
            }
        }

        $servers=$this->get_all_server();
        $servers_all = array ();
        foreach($servers as $server){
            if(in_array($server['id'],$checks)){
                $server['check']='checked';
                $choose_server_list[$server['id']]=$server;
            }
            array_push($servers_all, $server);
        }

        if($save){
            S('choose_server_list_'.$username,NULL);
            S('choose_server_list_'.$username,$choose_server_list,86400);
        }

        $this->assign ('servers', $servers_all);
        $this->display ();
    }
}