<?php

/**
 * @author: ahuazhu@gmail.com
 * Date: 16/7/20  上午11:49
 */

namespace PhpCat\Config;

class Config
{
    private static $domain;

    private static $servers;

    private static $_inited = false;

    //TODO Load domain from Project
    public static function getDomain() {
        self::checkInit();
        return "account";
    }

    //TODO load configurations from file
    public static function getServers() {
        self::checkInit();
//        return self::$servers;
        return [["10.1.3.203", 2280]];
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