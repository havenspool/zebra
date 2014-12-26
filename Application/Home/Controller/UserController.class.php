<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller{ 
	
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
		if(!empty($_POST['username'])){
			$username = empty($_POST['username'])? '' : trim($_POST['username']); //你要获取的性别
		}
		
		if(!empty($_POST['password'])){
			$password = empty($_POST['password'])? '' : trim($_POST['password']); //你要获取的性别
		}

        $DBuser = M('User');
        $res = $DBuser->where("username='" . $username . "' AND password='" . md5($password) . "'")->find();
        if ($res) {
            session('admin', array(
                'uid' => $res['uid'],
                'username' => $res['username'],
                'roleid' => $res['roleid']
            ));
            $this->success('登录成功', '/zebra/index.php'); //zebra/Home/Index/index
        } else {
            $this->error('用户名或密码错误', 'login');
        }
	}
	
	public function send_email(){
		$this->display();
	}
}