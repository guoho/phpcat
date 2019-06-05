<?php
namespace PhpCat;

use PhpCat\Message\Impl\DefaultMessageProducer;

/**
 * Class PhpCat
 */
class PhpCat
{

    private static $messageProducer;

    private static function init()
    {
        self::$messageProducer = new DefaultMessageProducer();
        self::$messageProducer->init();
    }

    /**
     * 代码运行情况监控：运行时间统计、次数、错误次数等等
     * @param $type
     * @param $name
     * @return mixed
     */
    public static function newTransaction($type, $name)
    {
        if (self::$messageProducer == null) {
            self::init();
        }
        return self::$messageProducer->newTransaction($type, $name);
    }


    /**
     * 记录程序中一个事件记录了多少次，错误了多少次。相比于Transaction，Event没有运行时间统计。
     *
     * @param $type
     * @param $name
     * @param null $key
     * @param null $value
     * @param string $status
     */
    public static function logEvent($type, $name, $key = null, $value = null, $status = \PhpCat\Message\Message::SUCCESS)
    {
        $event = self::newEvent($type, $name);
        $event->setStatus($status);
        $event->addData($key, $value);
        $event->complete();
    }

    /**
     *
     * @param $type
     * @param $name
     * @param Exception $error
     */
    public static function logError($type, $name, \Exception $error)
    {
        $event = self::newEvent($type, $name);
        $event->setStatus($error->getMessage());
        $trace = "\n" . $error->getMessage() . "\n";
        $trace .= $error->getTraceAsString() . "\n";
        $event->addData('Trace', $trace);
        $event->complete();
    }

    /**
     * 业务统计: 次数统计
     * @param $name
     * @param int $quantity
     */
    public static function logMetricForCount($name, $quantity = 1)
    {
        self::logMetricInternal($name, 'C', sprintf("%d", $quantity));
    }

    /**
     * 业务统计:总量统计
     * @param $name
     * @param float $value
     */
    public static function logMetricForSum($name, $value = 1.0)
    {
        self::logMetricInternal($name, 'S', sprintf("%.2f", $value));
    }

    /**
     * 业务统计
     * @param $name
     * @param $status
     * @param $keyValuePairs
     */
    private static function logMetricInternal($name, $status, $keyValuePairs)
    {
        if (self::$messageProducer == null) {
            self::init();
        }

        $type = '';
        $metric = self::$messageProducer->newMetric($type, $name);

        if (isset($keyValuePairs)) {
            $metric->addData($keyValuePairs);
        }

        $metric->setStatus($status);
        $metric->complete();
    }


    public static function newEvent($type, $name)
    {
        if (self::$messageProducer == null) {
            self::init();
        }
        return self::$messageProducer->newEvent($type, $name);
    }


}