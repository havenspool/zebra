<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model {
	// 定义自动验证
	protected $_validate    =   array(
			array('username','require','用户名必须'),array('password','require','密码必须'),
	);
	
	protected $_auto = array(	
			array('password', 'md5', 1, 'function') // 对password字段在新增的时候使md5函数处理
	);
}