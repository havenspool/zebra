<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller{ 

	public function reg(){
		$this->display();
	}

	public function doreg() {
		$DBuser = D('User');		
		$res = $DBuser->create();		
		if ($res) {		
			if ($DBuser->add()) {		
				$this->success('注册成功，请返回首页登录', '__APP__/Home/user/login');		
			} else {		
				$this->error('注册失败', '__APP__/Home/user/reg');		
			}		
		} else {		
			$this->error($DBuser->getError(), '__APP__/Home/user/reg');		
		}		
	}
	
	public function login(){
		$this->display();
	}
	
	public function send_email(){
		$this->display();
	}
}