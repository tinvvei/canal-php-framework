<?php

namespace core\lib;

use core\canal\Change;


/**
 * 表记录变更逻辑处理接口
 * 不同的表变更逻辑需要继承该类
 * Class LogicAbstract
 * @package app\logic
 * @author 徐庭威
 */
abstract class LogicAbstract
{
    protected $change;

    public function __construct(Change $change)
    {
        $this->change = $change;
    }


    /**
     * 不同的表变 需要实现该方法
     * @return mixed
     */
    abstract public function handle() : bool;
}