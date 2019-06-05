<?php
/**
 * @author: ahuazhu@gmail.com
 * Date: 16/7/19  下午6:00
 */

namespace PhpCat\Transfer\Impl;


use PhpCat\Codec\PlainTextCodec;
use PhpCat\Config\Config;
use PhpCat\Transfer\Sender;

class SingleThreadSender implements Sender
{

    private $m_codec;

    public function __construct()
    {
        $this->m_codec = new PlainTextCodec();
    }

    function send($messageTree)
    {
        //TODO: performance consider, should keep the connection.
      echo  $data = $this->m_codec->encode($messageTree);
        $len = strlen($data);

        $len_bin = pack('N', $len);
        $data_bin = pack("a{$len}", $data);

        $_data = $len_bin . $data_bin;

        list($ip,$port) = Config::getServers()[0];

        $socket = SocketConnection::getInstance([
            'ip' => $ip,
            'port' => $port
        ]);
        $socket->write($_data);
    }
}

