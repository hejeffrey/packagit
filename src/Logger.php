<?php
namespace jeffrey;

use jeffrey\Handler\Logger as Monolog;
class Logger
{
    use Config;

    private static $log;

    public static function useDailyFiles($type){
        $path = self::$path.'/logs/'.date('Y-m-d', time());
        if(!file_exists($path)){
            $old_mask = umask(0);
            if(!mkdir($path, 02770, true)){
                return false;
            }
            umask($old_mask);
        }
        self::$log = new Writer(new Monolog($type));
        self::$log->useDailyFiles($path.'/laravel-'. php_sapi_name() . '-' . $type.'.log', 30);
    }

    public static function info($message, $type='', array $context = []){
        if(!empty($type)){
            self::useDailyFiles($type.'-'.__FUNCTION__);
        }else{
            self::useDailyFiles(__FUNCTION__);
        }
        self::$log->write(__FUNCTION__,$message,$context);
    }

    public function error($message, $type='', array $context = []){
        if(!empty($type)){
            self::useDailyFiles($type.'-'.__FUNCTION__);
        }else{
            self::useDailyFiles(__FUNCTION__);
        }
        self::$log->write(__FUNCTION__,$message,$context);
    }
}