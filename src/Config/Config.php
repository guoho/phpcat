<?php

/**
 * @author: ahuazhu@gmail.com
 * Date: 16/7/20  上午11:49
 */

namespace PhpCat\Config;

class Config
{
    public static $domain;

    public static $servers;

    private static $_inited = false;

    /**
     * 域名配置
     * @return mixed
     */
    public static function getDomain() {
        self::checkInit();
        return self::$domain;
    }

    //TODO load configurations from file
    public static function getServers() {
        self::checkInit();
        return self::$servers;
    }


    private static function checkInit() {
        if (! self::$_inited) {
            self::init();
        }
    }

    private static function init() {
        self::$_inited = true;
    }
}