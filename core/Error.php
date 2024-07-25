<?php

namespace core;

class Error
{
    public static function register()
    {
        error_reporting(E_ALL);
        set_exception_handler([__CLASS__, 'appException']);
        set_error_handler([__CLASS__, 'appError']);
    }



    public static function appException($e)
    {
        Log::error($e->getMessage());
    }


    public static function appError($errno, $errstr, $errfile = '', $errline = 0)
    {
        Log::emergency(json_encode([
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline
        ]));
    }


}