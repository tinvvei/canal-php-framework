<?php

namespace core;

use PDO;
use core\lib\SingletonTrait;
use Medoo\Medoo;

/**
 * Class Db
 * @package core
 * @author 徐庭威
 * @mixin Medoo 代理Medoo实现断线重连
 */
class Db
{

    use SingletonTrait;

    /**
     * 连接池
     * @var array
     */
    public $connections = [];

    /**
     * 连接名
     * @var mixed|string
     */
    protected $linkName = 'default';


    /**
     * 切换连接
     * @param $linkName
     * @return $this
     */
    public function link($linkName)
    {
        $this->linkName = $linkName;
        return $this;
    }


    public function connect()
    {
        if (isset($this->connections[$this->linkName])) {
            return $this->connections[$this->linkName];
        }

        $config = config('db.' . $this->linkName);

        if (empty($config)) {
            throw new \Exception('Mysql连接配置无效！');
        }

        $config['database_type'] = 'mysql';
        $config['error'] = PDO::ERRMODE_EXCEPTION;
        $this->connections[$this->linkName] = new Medoo($config);
        return $this->connections[$this->linkName];
    }


    /**
     * 释放当前数据库连接
     * @return $this
     */
    public function free()
    {
        $this->connections[$this->linkName] = null;
        return $this;
    }


    /**
     * 代理一层 主要为了实现断线重连
     * @param $method
     * @param $args
     * @return mixed
     * @throws \Throwable
     */
    public function __call($method, $args)
    {
        try {
            return $this->connect()->{$method}(...$args);
        } catch (\PDOException|\Throwable|\Exception $e) {
            if ($this->isBreak($e)) {
                Log::info('断线重连:' . json_encode($args));
                return $this->free()->connect()->{$method}(...$args);
            }
            throw $e;
        }
    }


    /**
     * 判断是否断线
     * @param $e
     * @return bool
     */
    protected function isBreak($e)
    {
        $info = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'failed with errno',
        ];

        $error = $e->getMessage();

        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }


}