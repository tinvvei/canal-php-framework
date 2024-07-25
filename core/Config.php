<?php

namespace core;

use core\lib\SingletonTrait;

/**
 * 框架配置单例 在引导框架的时候实例化
 * Class Canal
 * @package core
 * @author 徐庭威
 */
class Config
{
    /**
     * 单例化
     */
    use SingletonTrait;

    public $config;

    public function load()
    {
        $configPath = CONFIG_PATH;
        $files = is_dir($configPath) ? scandir($configPath) : [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $filename = $configPath . DIRECTORY_SEPARATOR . $file;
                $this->set(pathinfo($file, PATHINFO_FILENAME), include $filename);
            }
        }
    }


    private function set($name, $value)
    {
        $name = strtolower($name);

        if (!is_array($value)) {
            return;
        }

        if (!isset($this->config[$name])) {
            $this->config[$name] = $value;
            return;
        }

        $this->config[$name] = array_merge($this->config[$name], $value);
    }


    public function get($key)
    {
        $key = strtolower($key);
        [$p, $s] = explode('.', $key);
        if ($s) {
            return $this->config[$p][$s];
        }
        return $this->config[$p];
    }

}