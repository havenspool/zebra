<?php
namespace Home\Model;
use Think\Model;
class ServerModel extends Model {
	// 定义自动验证
	protected $_validate    =   array(
			array('server_id','require','服务器ID必须'),array('server_name','require','服务器名称必须'),
			array('host','require','服务器IP必须'),array('port','require','服务器端口必须'),
			array('db_id','require','数据库必须')

	);
}
