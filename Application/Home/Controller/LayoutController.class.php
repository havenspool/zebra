<?php
namespace Home\Controller;

use Think\Controller;

class LayoutController extends Controller {

//	public function get_game(){
//		return "mysql://root:havens@127.0.0.1:3306/game"; //"mysql://root:it1S4Qn5xW7y@10.66.118.188:3306/game";
//	}
//	public function get_game_user(){
//		return "mysql://root:havens@127.0.0.1:3306/game_user";
//	}

// 	Public function _initialize(){
// 		// 初始化的时候检查用户权限
// 		if ($_SESSION['root'] != 'admin') {
// 			$this->redirect('/home/user/login');
// 		}
// 	}


	public function load_menu(){
		$admin = session('admin');
		$username=$admin['username'];
		$roleid=$admin['roleid'];

		//判断权限
		$role= M('role')->where('id='.$roleid)->select();
		if(count($role)<=0)
			$this->redirect('/home/user/login');
		//列表采用的是前台分页
		$DBuser = M('Menu');
		$menus = $DBuser->query('select * from menu where role<='.$role[0]['id']." order by pid asc,id asc");
		$this->assign('menus',$menus);


		// 		$cache = S(array('type'=>'Redis','prefix'=>'think','expire'=>600));
		// 		host 服务器地址（默认由REDIS_HOST参数配置或者127.0.0.1）
		// 		port端口（默认由REDIS_PORT参数配置或者6379）
		// 		timeout 超时时间（默认由DATA_CACHE_TIME配置或者false）
		// 		persistent长连接（默认为false）
		//		$cache = cache(array('type'=>'xcache','prefix'=>'think','expire'=>600));
		// 		$cache->menus = $menus; // 设置缓存
		// 		$value = $cache->name; // 获取缓存
		// 		unset($cache->name); // 删除缓存
// 		$cache = S(array('type'=>'Redis','host'=>'127.0.0.1','port'=>6379,'timeout'=>600,'persistent'=>false));
// 		$cache->menus = $menus; // 设置缓存
// 		$cache->admin = $admin;
// 		$cache->username = $username;

//		F('username',$username);//保存数据到缓存
//		F('admin',$admin);//保存数据到缓存
//		F('menus',$menus);//保存数据到缓存

		S('username_'.$username,$username,86400);
		S('admin_'.$username,$admin,86400);
		S('menus_'.$username,$menus,86400);

		//S('list',$list,3600);
		//F('data');//读取缓存
		//F('data',NULL);//删除缓存数据
	}

	public function menu() {
// 		$cache = S(array('type'=>'Redis','host'=>'127.0.0.1','port'=>6379,'timeout'=>600,'persistent'=>false));
// 		$menus = S('menus');
// 		$admin = S('admin');
// 		$username = S('username');

		$username=$this->getUsername();

		$menus = S('menus_'.$username);
		$admin = S('admin_'.$username);
		$username = S('username_'.$username);

// 		$menus = F('menus');
// 		$admin = F('admin');
// 		$username = F('username');

		//$DBuser = M('Menu');
		//$menus = $DBuser->select();
//		$this->assign('menus',$menus);

		$this->assign('menus',$menus);
		$this->assign('admin',$admin);
		$this->assign('username',$username);

		if ($username == "") {
			$this->redirect('/home/user/login');
		}

		//列表采用的是前台分页
// 		$DBuser = M('Menu');
// 		$menus = $DBuser->select();
// 		$this->assign('menus',$menus);

// 		$admin = session('admin');
// 		$username=$admin['username'];
	}

	public function get_menu_from_url($url){
		$username=$this->getUsername();
		$menus= S('menus_'.$username);
		foreach($menus as $menu){
			if(md5($menu['url'])==md5($url)){
				return $menu;
			}
		}
	}

	public function getType($type) {
		if($type==1){
			return "将军";
		}else if($type==2){
			return "鬼武";
		}else if($type==3){
			return "忍女";
		}else {
			return "未知";
		}
	}

	public function getUsername(){
		$admin = session('admin');
		$username=$admin['username'];
		return $username;
	}

	public function conn_server($host,$port,$sendData){
		$status = Array ();
		if (! extension_loaded ( 'amf3' ))
			die ( 'skip: amf3 extension not available' );
		$socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP ) or $status['result']= -1;
		$conn = socket_connect ( $socket, $host, $port ) or $status['result']= -1 ;

		if ($conn) {
			socket_write ( $socket, amf3_encode ( $sendData ) ) or $status['result']=-1 ;
			$buffer = socket_read ( $socket, 2048 );
			$status = amf3_decode ( $buffer );
		}
		socket_close ( $socket );
		return $status;
	}

	public function reload_server(){
		$username=$this->getUsername();
		F('server_list_'.$username,NULL);
		// $server_list = S('server_list_'.$username);//从缓存获取已选择服务器
		$server_list= M('Server')->select();
		F('server_list_'.$username,$server_list);
	}

	public function get_all_server(){
		$username=$this->getUsername();
		F('server_list_'.$username,NULL);
		// $server_list = S('server_list_'.$username);//从缓存获取已选择服务器
		$server_list = F('server_list_'.$username);//从缓存获取已选择服务器
		if(empty($server_list)){
			$server_list= M('Server')->select();
			F('server_list_'.$username,$server_list);
			return $server_list;
		}
		return $server_list;
	}

	public function get_all_platforms(){
		F('platforms',NULL);
		$platforms = F('platforms');
		if(empty($server_list)){
			$platforms = M ( 'platforms', '', $this->get_gmserver())->query ( "SELECT  *  from platforms where enable=1");
			F('platforms',$platforms);
			return $platforms;
		}
		return $platforms;
	}

	public function get_db(){
		F('db_list',NULL);
		$db_list = F('db_list');//从缓存获取已选择服务器
		if(empty($db_list)){
			$db_list= $this->get_db_from_config();
			F('db_list',$db_list);
			return $db_list;
		}
		return $db_list;
	}

	public function get_db_from_config(){
		$dbs = M('db')->query("select * from db ");
		return $dbs;
		// $conf = require('DBConfigController.class.php');//读取配置文件  DBConfigController
		// return $conf;
	}

	public function get_dbuser(){
		F('dbuser_list',NULL);
		$dbuser_list = F('dbuser_list');//从缓存获取已选择服务器
		if(empty($dbuser_list)){
			$dbuser_list= $this->get_dbuser_from_config();
			F('dbuser_list',$dbuser_list);
			return $dbuser_list;
		}
		return $dbuser_list;
	}

	public function get_dbuser_from_config(){
		$conf = require('DBUserController.class.php');//读取配置文件
		return $conf;
	}

	public function get_gmserver(){
		F('gmserver',NULL);
		$gmserver = F('gmserver');//从缓存获取已选择服务器
		if(empty($gmserver)){
			$gmserver= $this->get_gmserver_from_config();
			F('gmserver',$gmserver);
			return $gmserver;
		}
		return $gmserver;
	}

	public function get_gmserver_from_config(){
		$conf = require('DBgmController.class.php');//读取配置文件
		return $conf;
	}

	public function get_db_from_platform($platform){
		$servers=$this->get_all_server();
		$server_id=0;
		foreach($servers as $server){
			if($server['platform']==$platform){
				$server_id=$server['server_id'];
				break;
			}
		}

		$dbs=$this->get_db();
		foreach($dbs as $db){
			if($db['server_id']==$server_id){
				return $db;
			}
		}
	}

	public function get_platform($platform){
		$platforms=$this->get_all_platforms();
		foreach($platforms as $plat){
			if($platform==$plat['platform']){
				return $plat;
			}
		}
	}

	public function get_db_from_serverid($server_id){
		$dbs=$this->get_db();
		foreach($dbs as $db){
			if($db['server_id']==$server_id){
				return $db;
			}
		}
	}

	public function get_server_from_platform($platform){
		$servers=$this->get_all_server();
		foreach($servers as $server){
			if($server['platform']==$platform){
				return $server;
			}
		}
	}

	public function get_server_from_id($server_id){
		$servers=$this->get_all_server();
		foreach($servers as $server){
			if($server['server_id']==$server_id){
				return $server;
			}
		}
	}

	public function get_Dungeons(){
		$conf = require('DungeonController.class.php');//读取配置文件
 		return $conf;
	}

	public function get_items(){
		$conf = require('ItemController.class.php');//读取配置文件
		return $conf;
	}

	public function get_material_bless(){
		$conf = require('MaterialBlessController.class.php');//读取配置文件
		return $conf;
	}

	public function get_currency_types(){
		$conf = require('CurrencyController.class.php');//读取配置文件
		return $conf;
	}

	public function get_role_from_roleid($roleid){
		$role_list=$this->get_roles();
		foreach($role_list as $role){
			if($role['id']==$roleid){
				return $role;
			}
		}
	}

	public function get_roles(){
		$username=$this->getUsername();
		$role_list = S('role_list_'.$username);//从缓存获取已选择服务器
		if(empty($role_list)){
			$role_list= M('Role','',$this->get_gmserver())->select();
			return $role_list;
		}
		return $role_list;
	}

	public function get_period($period){
		return ($period-1).":00 ~ ".$period.":00";
	}

	public function get_pay_part($pay){
		switch($pay){
			case 1:return "0-1元";
			case 2:return "1-5元";
			case 3:return "5-10元";
			case 4:return "10-50元";
			case 5:return "50-100元";
			case 6:return "100-500元";
			case 7:return "500-1000元";
			case 8:return "1000-10000元";
			case 9:return "10000元以上";
			default:return "0-１元";
		}
	}

	public function get_retention_day(){
		$days=array(
			"0"=>0,
			"1"=>1,
			"2"=>2,
			"3"=>3,
			"4"=>4,
			"5"=>5,
			"6"=>6,
			"13"=>13,
			"20"=>20,
			"29"=>29,
			"59"=>59,
			"89"=>89,
			"179"=>179,
			"364"=>364
		);
		return $days;
	}

}
