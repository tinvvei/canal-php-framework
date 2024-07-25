<?php

namespace core;

use core\canal\Canal;

/**
 * 框架引导
 * Class Canal
 * @package core
 * @author 徐庭威
 */
class App
{
    /**
     * @var Canal
     */
    protected $canal;

    private function __construct(Canal $canal)
    {
        $this->canal = $canal;
    }


    public static function init(\closure $closure)
    {
        // 加载环境变量
        if (is_file(ROOT_PATH .  DS . '.env')) {
            Env::instance()->load(ROOT_PATH . DS  . '.env');
        }

        // 加载公共函数
        if (is_file(APP_PATH . '/common.php')) {
            include_once APP_PATH . DS . 'common.php';
        }

        // 注册错误处理
        Error::register();

        // 加載配置文件
        Config::instance()->load();

        // 注册日志组件
        Log::instance()->load();

        // 实例化canal客户端 注入APP
        return new self($closure());
    }


    public function run()
    {
        $this->canal->run();
    }
}