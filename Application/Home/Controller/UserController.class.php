<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends LayoutController{ 
	
	public function reg(){
		$this->assign('title','注册');
		$this->display();
	}

	public function doreg() {
		if(!empty($_POST['email'])){
			$email = empty($_POST['email'])? '' : trim($_POST['email']); //你要获取的性别
		}
		
		if(!empty($_POST['username'])){
			$username = empty($_POST['username'])? '' : trim($_POST['username']); //你要获取的性别
		}
		
		if(!empty($_POST['password'])){
			$password = empty($_POST['password'])? '' : trim($_POST['password']); //你要获取的性别
		}
		
		if(!empty($_POST['role'])){
			$role = empty($_POST['role'])? '' : trim($_POST['role']); //你要获取的性别
		}
		
		$DBuser = D('User');
		$DBuser->email = $email;
		$DBuser->username = $username;
		$DBuser->password = $password;
		$DBuser->roleid = $role;		
		$res = $DBuser->create();		
		if ($res) {		
			if ($DBuser->add()) {		
				$this->success('注册成功，请返回首页登录', 'login');		
			} else {		
				$this->error('注册失败', 'reg');		
			}		
		} else {		
			$this->error($DBuser->getError(), 'reg');		
		}
	}
	
	
	public function login(){
		$this->assign('title','登陆');
		$this->display();
	}
	
	public function dologin(){
		global $username;
		
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$ref_url = $_GET['req_url'];
		$remember = $_POST['remember'];
		
// 		if($username==""&&$password==""){
// 			$username = cookie('username');		
// 			$password = cookie('password');		
// 		}
		
		if($username==""){
			$this->error('用户名不能为空');		
			exit;
		}
		
		if($password==""){
			$this->error('密码不能为空');
			exit;
		}

        $DBuser = M('User');
        $res = $DBuser->where("username='" . $username . "' AND password='" . md5($password) . "'")->find();
        if ($res) {
            session('admin', array(
                'uid' => $res['uid'],
                'username' => $res['username'],
                'roleid' => $res['roleid']
            ));
            
            $this->load_menu();//加载菜单目录
            $this->success('登录成功', '/zebra/Home/Home/daily'); //zebra/Home/Index/index  /zebra/index.php

            if($remember==1){
            	//存入cookie的数据一定要加密，否则能在cookie中看到用户名和密码
            	//setcookie("username", md5($username), time()+3600*24*7);
           	 	//setcookie("password", md5(password), time()+3600*24*7);
            }
        } else {
            $this->error('用户名或密码错误', 'login');
        }
	}
	
	public function send_email(){
		$this->display();
	}
	
	function logout(){
		if(!empty($_SESSION[C('USER_AUTH_KEY')])){
			unset($_SESSION[C('USER_AUTH_KEY')]);
			$_SESSION=array();
			session_destroy();
			//$this->assign('jumpUrl',/Code.'/login');
			$this->success('登出成功');
		}else{
			$this->error('已经登出了');
		}
	}

}