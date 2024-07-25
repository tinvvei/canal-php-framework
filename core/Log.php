<?php

namespace core;

use core\lib\SingletonTrait;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


/**
 * 日志单例 在引导框架的时候实例化
 * Class Log
 * @package core
 * @author 徐庭威
 *
 * @method void debug($msg) static 记录调试日志
 * @method void warning($msg) static 记录警告日志
 * @method void error($msg) static 记录错误日志
 * @method void info($msg) static 记录一般信息日志
 * @method void notice($msg) static 记录提示日志
 * @method void alert($msg) static 记录报警日志
 * @method void critical($msg) static 记录关键日志
 * @method void emergency($msg) static 记录突发日志
 *
 */
class Log
{
    /**
     * 单例化
     */
    use SingletonTrait;

    /**
     * 日志驱动
     * @var Logger
     */
    public $log;


    /**
     * 支持的日志级别
     * @var string[]
     */
    protected static $level = [
        'info',
        'notice',
        'alert',
        'debug',
        'error',
        'critical',
        'warning',
        'emergency'
    ];

    public function load()
    {
        $this->log = new Logger('canal');
        $logFile = config('app.log_path') .  date('Ymd') . '.log';
        $format = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
        $formatter = new LineFormatter($format, 'Y-m-d H:i:s');
        $stream = new StreamHandler($logFile);
        $stream->setFormatter($formatter);
        $this->log->pushHandler($stream);
    }


    public static function __callStatic($method, $args)
    {
        if (in_array($method, self::$level)) {
            self::instance()->log->$method(...$args);
        }
    }
}