<?php

namespace core\lib;

/**
 * 单例 trait
 * 需要单例的类use此trait即可
 * Trait SingletonTrait
 * @package core\lib
 * @author 徐庭威
 */
trait SingletonTrait
{
    protected static $_instance;

    final public static function instance()
    {
        if(!isset(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $this->init();
    }

    protected function init() {}

}

