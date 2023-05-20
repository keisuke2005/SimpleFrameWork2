<?php
namespace Backend\SsoProxy;

require_once __DIR__.'/../foundation/bases/loader.php';
require_once(__DIR__."/../configs/Config.php");

use Backend\Foundation\Bases\Router;
use Backend\Foundation\Bases\Path;
use Backend\Foundation\Bases\Logger;

class SsoProxy extends Router
{
    /**
    * 序章
    *
    * SsoProxyでは序章に以下の機構が入る
    ** トークン検証
    * @access protected
    * @return void
    */
    protected function prologue(): void
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        if(! $this->verification())
        {
            Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
            header('Location: '.Config::LOGIN_URL);
            exit;
        }
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

    /**
    * トークン検証
    *
    * proxy-authへのAPIコールを実装する
    * @access private
    * @return bool
    */
    private function verification():bool
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return true;
    }

    /**
    * 自アプリケーションURIプレフィックス
    *
    * 自身のアプリケーションのURIプレフィックスを返す
    * @access protected
    * @return Path
    */
    protected function uriPrefix():Path
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return Path::def(Config::URI_PREFIX);
    }

    public static function getRouteConfigDir():string
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return __DIR__.'/../configs/routes/';
    }
}