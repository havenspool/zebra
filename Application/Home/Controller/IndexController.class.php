<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    
	public function index(){
       	$Data = M('Data'); // 实例化Data数据模型
        $this->data = $Data->select();
        $this->display();
    }
}