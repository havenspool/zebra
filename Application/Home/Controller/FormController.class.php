<?php

namespace Home\Controller;

use Think\Controller;

class FormController extends Controller {
	
	public function index($id = 0) {
		$Form = M ( 'Form' );
		// 读取数据
		$data = $Form->select();
		if ($data) {
			$this->data = $data; // 模板变量赋值
		} else {
			$this->error ( '数据错误' );
		}
		$this->display ();
	}
		
	public function insert() {
		$Form = D ( 'Form' );
		if ($Form->create ()) {
			$result = $Form->add ();
			if ($result) {
				$this->success ( '操作成功！' );
			} else {
				$this->error ( '写入错误！' );
			}
		} else {
			$this->error ( $Form->getError () );
		}
	}
	public function read($id = 0) {
		$Form = M ( 'Form' );
		// 读取数据
		$data = $Form->find ( $id );
		if ($data) {
			$this->data = $data; // 模板变量赋值
		} else {
			$this->error ( '数据错误' );
		}
		$this->display ();
	}
	
	public function edit($id = 0) {
		$Form = M ( 'Form' );
		$this->vo = $Form->find ( $id );
		$this->display ();
	}
	
	public function update() {
		$Form = D ( 'Form' );
		if ($Form->create ()) {
			$result = $Form->save ();
			if ($result) {
				$this->success ( '操作成功！' );
			} else {
				$this->error ( '写入错误！' );
			}
		} else {
			$this->error ( $Form->getError () );
		}
	}
	public function delete($id = 0) {
		$User = M ( "Form" ); // 实例化User对象
		$User->where ( 'id='.$id )->delete (); // 删除id为5的用户数据
	}
}
