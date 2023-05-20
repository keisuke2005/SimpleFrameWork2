<?php

namespace Backend\SsoProxy;

use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Logger;
use Backend\Foundation\Bases\Util;
use Backend\Foundation\Bases\HttpMethods;

class ProxyService
{
    /**
    * Form送信用ログイン
    *
    * Form送信してログインをするタイプのものに使用
    * @access public
    * @param string $url URL
    * @param array $formData フォームデータ
    * @return Response
    */
    public function loginToSubmitForm(string $url,array $formData):Response
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $data = [];
        foreach($formData as $k => $v){
            $data[] = $k.'='.$v;
        }
        $data = implode('&',$data);
        $result = Util::curlExec(
            url:$url,
            method:HttpMethods::POST->value,
            postField:$data
        );  

        $response = Response::get();
        $response->setHeaderNormalize($result['header']);
        $response->setBody($result['body']);
        
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return $response;   
    }

    /**
    * Http仲介
    *
    * 仲介になりリクエストやレスポンスを右から左へ受け流す
    * @access public
    * @param Request
    * @param string $host
    * @param string $baseUri
    * @return Response
    */
    public function bridgingHttp(Request $request,string $host,string $baseUri):Response
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $url = $host.str_replace($baseUri,'',$request->getRequestUri());
        $method = $request->getRequestMethod();
        $postField = null;
        if($method !== HttpMethods::GET->value)
        {
            $postField = $request->getOriginBody();
        }
        $headers = [
            $this->convertCookieToHeaderFormat(),
            'Accept: '.$request->getHeader('Accept')
        ];
        $result = Util::curlExec(
            url:$url,
            method:$method,
            headers:$headers,
            postField:$postField
        );

        $response = Response::get();
        $response->setHeaderNormalize($result['header']);
        $response->setBody($result['body']);

        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return $response;
    }

    /**
    * リダイレクト終了
    *
    * リダイレクトするとレスポンスの情報がよくわからなくなるので、リダイレクトさせなくする
    * @access private
    * @return void
    */
    private function disableRedirect():void
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $response = Response::get();
        $response->setStatusCode(200);
        $response->headerDeleter('Location');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

    /**
    * クッキーヘッダー形式フォーマット
    *
    * クッキーをヘッダーに付与するために整形する
    * @access private
    * @return string
    */
    private function convertCookieToHeaderFormat():string
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $cookie = [];
        foreach ($_COOKIE as $key => $val) {
            $cookie[] = $key.'='.$val.';';
        }
        return 'Cookie: '.implode(' ',$cookie);
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

}
?>