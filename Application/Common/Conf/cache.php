<?php
return array(
    //'配置项'=>'配置值'
    'LAYOUT_ON'        => true,
    'HTML_CACHE_ON'    => strpos($_SERVER['HTTP_HOST'], '.') !== false, // 开启静态缓存 默认为 true 本地不开启
    'HTML_CACHE_TIME'  => 10, // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.shtml', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(
        '*' => array('{:module}/{:controller}/{:action}/{$_SERVER.REQUEST_URI|md5}', 10, 'trimSW'),
    )
);

/**
 * @author      default7@zbphp.com
 * @description 去除 空格 和非\w 字符串，用于cache 配置
 *
 * @param        $str
 * @param string $emptyValue
 *
 * @return mixed|string
 */
function trimSW($str, $emptyValue = '_empty_')
{
    $str = preg_replace('/([^\w\/]+)/', '-', $str);
    if (empty($str)) {
        $str = $emptyValue;
    }

    return $str;
}