<?php

include_once "vendor/autoload.php";

use core\App;
use core\canal\Canal;

const DS = DIRECTORY_SEPARATOR;

const ROOT_PATH = __DIR__;

const APP_PATH = ROOT_PATH .  DS . "app";

const CONFIG_PATH = ROOT_PATH . DS . "config";


$closure = function () {
    return new Canal(
        config('canal.host'),
        config('canal.port'),
        config('canal.filter')
    );
};

// 用闭包注入是为了在框架引导完成之后再连接canal服务器
App::init($closure)->run();
