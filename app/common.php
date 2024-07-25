<?php

/**
 * 下划虚线转小驼峰
 * @param $string
 * @return string
 */
function toCamel($string)
{
    $parts = explode('_', $string);
    foreach ($parts as $index => $part) {
        $parts[$index] = ucfirst($part);
    }
    return implode('', $parts);
}


/**
 * 获取配置文件
 * @param $key
 * @return mixed
 */
function config($key)
{
    return \core\Config::instance()->get($key);
}

/**
 * 获取环境变量
 * @param $key
 * @param $default
 * @return array|bool|mixed|null
 */
function env($key = null, $default = null)
{
    return \core\Env::instance()->get($key, $default);
}