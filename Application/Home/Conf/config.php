<?php
return array(
	/* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'test',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'havens',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    =>  false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    
	'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
	'URL_MODEL' => 2, //URL模式 0:普通模式 1：PATHINFO模式 2：REWRITE模式 3：兼容模式
	
	//'MODULE_DENY_LIST' => array('Common','User','Admin','Install'),
	'MODULE_ALLOW_LIST' => array('Home'),
);