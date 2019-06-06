<?php
namespace PhpCat;
use PhpCat\Message\Impl\DefaultMessageProducer;

/**
 * Class PhpCat
 */
class PhpCat
{
    private static $messageProducer;
    /**
     * 设置配置
     */
    function __construct($domain, $servers){
        Config\Config::$domain = $domain;
        Config\Config::$servers = $servers;
        self::$messageProducer = new DefaultMessageProducer();
        self::$messageProducer->init();
    }

    /**
     * 代码运行情况监控：运行时间统计、次数、错误次数等等
    Transaction
     * 大小写敏感的字符串. 常见的Transaction type有 "URL", "SQL", "Email", "Exec"等
    a).transaction适合记录跨越系统边界的程序访问行为，比如远程调用，数据库调用，也适合执行时间较长的业务逻辑监控
    b).某些运行期单元要花费一定时间完成工作, 内部需要其他处理逻辑协助, 我们定义为Transaction.
    c).Transaction可以嵌套(如http请求过程中嵌套了sql处理).
    d).大部分的Transaction可能会失败, 因此需要一个结果状态码.
    e).如果Transaction开始和结束之间没有其他消息产生, 那它就是Atomic Transaction(合并了起始标记).
     * 示例:
     * $transaction = PhpCat::newTransaction("URL", "/Test");
    {
    $t1 = PhpCat::newTransaction('Invoke', 'method1()');
    sleep(2);
    $t1->setStatus(Message::SUCCESS);
    $t1->addData("Hello", "world");
    $t1->complete();
    }

    {
    $t2 = PhpCat::newTransaction('Invoke', 'method2()');
    sleep(2);
    $t2->setStatus(Message::SUCCESS);
    $t2->complete();
    }

    {
    $t3 = PhpCat::newTransaction('Invoke', 'method3()');
    sleep(1);
    {
    $t4 = PhpCat::newTransaction('Invoke', 'method4()');
    sleep(2);
    $t4->setStatus(Message::SUCCESS);
    $t4->complete();
    }

    $t3->setStatus(Message::SUCCESS);
    $t3->complete();
    }

    $transaction->setStatus(Message::SUCCESS);
    $transaction->addData("Hello, world!");
    $transaction->complete();
     *
     * @param $type
     * @param $name
     * @return mixed
     */
    public function newTransaction($type, $name)
    {
        return self::$messageProducer->newTransaction($type, $name);
    }


    /**
     * Event用来记录次数，表名单位时间内消息发生次数，比如记录系统异常，它和transaction相比缺少了时间的统计，开销比transaction要小
     * 常见的Event type有 "Info", "Warn", "Error", 还有"Cat"用来表示Cat内部的消息
     * @param $type
     * @param $name
     * @param null $key
     * @param null $value
     * @param string $status
     */
    public function logInfo($name, $key = null, $value = null, $status = \PhpCat\Message\Message::SUCCESS)
    {
        $type = "Info";
        $event = self::newEvent($type, $name);
        $event->setStatus($status);
        $event->addData($key, $value);
        $event->complete();
    }

    /**
     * Event用来记录次数，表名单位时间内消息发生次数，比如记录系统异常，它和transaction相比缺少了时间的统计，开销比transaction要小
     * 常见的Event type有 "Info", "Warn", "Error", 还有"Cat"用来表示Cat内部的消息
     * @param $type
     * @param $name
     * @param null $key
     * @param null $value
     * @param string $status
     */
    public function logWarn($name, $key = null, $value = null, $status = \PhpCat\Message\Message::SUCCESS)
    {
        $type = "Warn";
        $event = self::newEvent($type, $name);
        $event->setStatus($status);
        $event->addData($key, $value);
        $event->complete();
    }


    /**
     * Event用来记录次数，表名单位时间内消息发生次数，比如记录系统异常，它和transaction相比缺少了时间的统计，开销比transaction要小
     * 常见的Event type有 "Info", "Warn", "Error", 还有"Cat"用来表示Cat内部的消息
     * @param $type
     * @param $name
     * @param null $key
     * @param null $value
     * @param string $status
     */
    public function logError($name, $key = null, $value = null, $status = \PhpCat\Message\Message::SUCCESS)
    {
        $type = "Error";
        $event = self::newEvent($type, $name);
        $event->setStatus($status);
        $event->addData($key, $value);
        $event->complete();
    }

    /**
     * Exception 错误追踪
     *
     * @param $type
     * @param $name
     * @param Exception $error
     */
    public function logException($name, \Exception $error)
    {
        $type = 'Error';
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
    public function logMetricForCount($name, $quantity = 1)
    {
        self::logMetricInternal($name, 'C', sprintf("%d", $quantity));
    }

    /**
     * 业务统计:总量统计
     * @param $name
     * @param float $value
     */
    public function logMetricForSum($name, $value = 1.0)
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
        $type = '';
        $metric = self::$messageProducer->newMetric($type, $name);

        if (isset($keyValuePairs)) {
            $metric->addData($keyValuePairs);
        }

        $metric->setStatus($status);
        $metric->complete();
    }


    private static function newEvent($type, $name)
    {
        return self::$messageProducer->newEvent($type, $name);
    }
}