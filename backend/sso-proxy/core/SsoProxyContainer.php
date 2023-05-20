<?php

namespace Backend\SsoProxy;

use Backend\Foundation\Bases\Container;
use Backend\Foundation\Bases\Logger;
use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Message;

require_once(__DIR__."/../classes/ProxyService.php");
require_once(__DIR__."/../classes/ProxyZabbix.php");

class SsoProxyContainer extends Container
{
    protected function blueprint()
    {
        // ロガーイニシャライズ
        Logger::create(Config::LOG_PATH);
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');

        // メッセージイニシャライズ
        Message::create(Config::MESSAGE_PATH);

        // リクエストオブジェクト
        $this->containers['Request'] = fn() => Request::get();

        // レスポンスオブジェクト
        $this->containers['Response'] = fn() => Response::get();

        // SsoProxyオブジェクト
        $this->containers['SsoProxy'] = fn() => new SsoProxy($this);
        
        // ProxyServiceオブジェクト
        $this->containers['ProxyService'] = fn() => new ProxyService;

        // ProxyZabbixオブジェクト
        $this->containers['ProxyZabbix'] = fn() => new ProxyZabbix(
            $this,
            $this->instance('ProxyService')
        );
        
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

}