<?php
$conf=array();
$c = array ();
$c ['id'] ='10001';
$c ['user'] ='root';
$c ['pwd'] ='hank';
$c ['host'] ="192.168.2.135";
$c ['port'] =3306;
$c ['db_hero'] ='game';
$c ['db_user'] ='game_user';
array_push ($conf, $c);

//$c = array ();
//$c ['id'] ='10002';
//$c ['user'] ='root';
//$c ['pwd'] ='hank';
//$c ['host'] ="192.168.2.135";
//$c ['port'] =3306;
//$c ['db_hero'] ='game';
//$c ['db_user'] ='game_user';
//array_push ($conf, $c);
return $conf;
?>