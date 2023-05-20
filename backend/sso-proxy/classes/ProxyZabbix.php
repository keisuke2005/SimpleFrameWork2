<?php

namespace Backend\SsoProxy;

use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Logger;
use Backend\Foundation\Bases\Container;
use Backend\Foundation\Bases\Core;

/**
* Zabbix代理認証及び代理アクセスクラス
* 
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\SsoProxy
*/
class ProxyZabbix extends Core
{
    /**
    * ProxyServiceクラスオブジェクト
    * @access private
    * @var ProxyService
    * @see ProxyService
    */
    private ProxyService $proxyService;


    /**
    * コンストラクタ
    *
    * @access public
    * @param Container
    * @param ProxyService
    * @return void
    */
    function __construct(Container $container,ProxyService $proxyService)
    {   
        parent::__construct(container:$container);
        $this->proxyService = $proxyService;
    }

    /**
    * 認証
    *
    * Zabbixの認証を模倣して、セッションキーを得る
    * @access public
    * @return Response
    */
    public function login():Response
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        /*
        ◆参考リクエスト情報
        リクエスト URL: https://xxx.xxx.xxx.xxx/zabbix/index.php
        リクエスト メソッド: POST
        ステータス コード: 302 Found
        リモート アドレス: xxx.xxx.xxx.xxx:443
        */
        // url作成
        $url = Config::SYSTEM_MONNITOR_SCHEME.Config::SYSTEM_MONNITOR_DOMAIN.Config::SYSTEM_MONNITOR_PORT;
        $url .= Config::SYSTEM_MONITOR_LOGIN_URL;
        // フォームデータ作成
        $formData = [
            'name' => Config::SYSTEM_MONITOR_LOGIN_USER,
            'password' => Config::SYSTEM_MONITOR_LOGIN_PSWD,
            'autologin' => 1,
            'enter' => 'サインイン'
        ];
        // Postログイン
        $response = $this->proxyService->loginToSubmitForm(url:$url,formData:$formData);
        // レスポンスデータのクッキーのpath部分のみ書き換え
        $response->cookieEditor('path','/sso-proxy/systemMonitor');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return $response;
    }

    /**
    * 認証後の通常アクセス
    *
    * 通常アクセスのリクエストの模倣
    * @access public
    * @access public
    * @return Response
    */
    public function access():Response
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        // リクエストの処理は全てサービスに任せる
        $response = $this->proxyService->bridgingHttp(
            request:Request::get(),
            host:Config::SYSTEM_MONNITOR_SCHEME.Config::SYSTEM_MONNITOR_DOMAIN.Config::SYSTEM_MONNITOR_PORT,
            baseUri:Config::SYSTEM_MONNITOR_BASEURI
        );
        // Transfer-Encodingが付いてたらレスポンス失敗するので外しました。
        $response->headerDeleter('Transfer-Encoding');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');     
        return $response;
    }
}
?>