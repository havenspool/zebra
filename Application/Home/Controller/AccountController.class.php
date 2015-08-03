<?php

namespace Home\Controller;

use Think\Controller;

class AccountController extends LayoutController {

    public function add_account() {
        $this->menu ();
        $this->assign ( 'title', '新增账号' );
        $this->assign ( 'url', '/zebra/Home/Account/add_account' );
        $roles= M('Role','', $this->get_gmserver())->select();

        $flag=true;
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        $comfirm_pwd = $_POST['comfirm_pwd'];
        $mail = $_POST['mail'];
        $status = $_POST['status_select'];
        $role_id= $_POST['role_select'];
        $md5_pwd=md5($pwd);
        $md5_cpwd=md5($comfirm_pwd);
        if($md5_pwd!=$md5_cpwd){
            $flag=false;
        }

        if($_POST['username']==""||$_POST['pwd']==""||$_POST['mail']==""
            ||$_POST['status_select']==""||$_POST['role_select']==""||$_POST['comfirm_pwd']=="")
            $flag=false;

        $user=array('username'=>$username,'password'=>$md5_pwd,'email'=>$mail,
            'roleid'=>$role_id,'status'=>$status);

        if($flag){
            M('User','', $this->get_gmserver())->add($user);
        }

        if($flag) header("Location:/zebra/Home/Account/account_list");

        $this->assign ( 'roles', $roles);
        $this->display ();
    }

    public function modify_account() {
        $this->menu ();
        $this->assign ( 'title', '权限设置' );
        $this->assign ( 'url', '/zebra/Home/Account/modify_account' );
        $flag=true;
        $uid = $_GET['uid'];
        if($_GET['uid']=="") $flag=false;

        $save=true;
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        $comfirm_pwd = $_POST['comfirm_pwd'];
        $mail = $_POST['mail'];
        $status = $_POST['status_select'];
        $role_id= $_POST['role_select'];
        $_id = $_POST['_id'];


        if($_POST['_id']==""||$_POST['username']==""||$_POST['pwd']==""||$_POST['mail']==""
            ||$_POST['status_select']==""||$_POST['role_select']==""||$_POST['comfirm_pwd']=="")
            $save=false;

        if($flag) $save=false;

        $md5_pwd=md5($pwd);
        $md5_cpwd=md5($comfirm_pwd);
        if($md5_pwd!=$md5_cpwd){
            $flag=false;
        }
        $user=array();
        if($flag){
            $user=M('User','', $this->get_gmserver())->where(' uid='.$uid)->select();
        }
        if($save){
            $user=array('username'=>$username,'password'=>$md5_pwd,'email'=>$mail,
                'roleid'=>$role_id,'status'=>$status);
            M('User','', $this->get_gmserver())->where(' uid='.$_id)->save($user);
        }

        if($save) header("Location:/zebra/Home/Account/account_list");

        $roles= M('Role','', $this->get_gmserver())->select();
        $this->assign ('user', $user[0]);
        $this->assign ( 'roles', $roles);
        $this->display ();
    }

    public function account_list() {
        $this->menu ();
        $this->assign ( 'title', '账号列表' );
        $this->assign ( 'url', '/zebra/Home/Account/account_list' );

        $users=M('User','', $this->get_gmserver())->select();
        $users_all=array();

        foreach($users as $user){
            $role=$this->get_role_from_roleid($user['roleid']);
            $user['roleid']=$role['desc'];
            array_push($users_all,$user);
        }

        $this->assign ( 'users', $users_all);
        $this->display ();
    }

    public function delete_account() {
        $this->menu ();
        $this->assign ( 'title', '删除账号' );
        $this->assign ( 'url', '/zebra/Home/Account/delete_account' );

        $flag=true;
        $uid = $_GET['uid'];
        if($_GET['uid']=="") $flag=false;

        if($flag){
            M('User','', $this->get_gmserver())->where('uid='.$uid)->delete();
        }

        if($flag) header("Location:/zebra/Home/Account/account_list");
        $this->display ();
    }

}