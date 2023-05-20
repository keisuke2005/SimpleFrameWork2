<?php

namespace Backend\Foundation\Bases;

/**
* レスポンスオブジェクト
* 
* レスポンス作成用オブジェクト
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
class Response
{
    /**
	* @access private
	* @var Response 自身を管理するRequestオブジェクト格納変数
	* @see Request
	*/
	private static Response $response;

	/**
	* @access private
	* @var string レスポンスボディ
	*/
	private string $body = "";

	/**
	* @access private
	* @var array レスポンスヘッダー
	*/
	private array $headers = [];

	/**
	* @access private
	* @var int レスポンスステータスコード
	*/
	private int $statusCode = 200;

	/**
	* @access private
	* @var \Closure レスポンスをどう実行するかのクロージャ
	*/
	private \Closure $responseClosure;

    /**
	* インスタンス取得
	*
	* 外部からこのクラスを扱うときは、こちらを利用してインスタンスを生成する。
	* @access public
	* @return Response
	*/
	public static function get(): Response
	{
		if(!isset(self::$response)) self::$response = new Response();
		return self::$response;
	}

	/**
	* レスポンス出力
	*
	* 最終的にRouterはこのメソッドを実行する
	* @access public
	* @return Response
	*/
    public function output():void
    {
		
		if($this->statusCode !== 200)
		{
			http_response_code($this->statusCode);
		}
		
		if(count($this->headers) > 0)
		{
			foreach($this->headers as $h)
			{
				if($h !== "")
				{
					header($h);
				}
				
			}
		}
        $closure = $this->responseClosure;
		$closure($this);
    }

	/**
	* Jsonエンコード及びボディへ格納
	*
	* @access public
	* @param array|Object
	* @return void
	*/
    public function json(array|Object $obj):void
    {
        $this->body = json_encode($obj);
    }

	/**
	* レスポンスクロージャ格納
	*
	* Endpointが考えたレスポンスクロージャを格納する
	* @access public
	* @param \Closure
	* @return void
	*/
	public function setResponseClosure(\Closure $responseClosure):void
	{
		$this->responseClosure = $responseClosure;
	}

	/**
	* bodyゲッター
	* @access public
	* @return string
	*/
	public function getBody():string{return $this->body;}

	/**
	* bodyセッター
	* @access public
	* @param string
	* @return void
	*/
    public function setBody(string $body):void{$this->body = $body;}

	/**
	* headersゲッター
	* @access public
	* @return array
	*/
	public function getHeaders():array{return $this->headers;}

	/**
	* headersセッター
	* @access public
	* @param array
	* @return void
	*/
	public function setHeaders(array $headers):void{$this->headers = $headers;}

	/**
	* statusCodeゲッター
	* @access public
	* @return int
	*/
	public function getStatusCode():int{return $this->statusCode;}

	/**
	* statusCodeセッター
	* @access public
	* @param int
	* @return void
	*/
	public function setStatusCode(int $statusCode):void{$this->statusCode = $statusCode;}

	/**
	* ヘッダーを正規化して格納
	* @access public
	* @param string
	* @return void
	*/
	public function setHeaderNormalize(string $header):void
    {
        $tmpHeader = explode("\n",str_replace("\r",'',$header));
		$statusCode = explode(" ",$tmpHeader[0])[1];
		$headers = array_splice($tmpHeader, 1);
		$this->statusCode = (int)$statusCode;
		$this->headers = $headers;
    }

	/**
	* クッキー編集
	*
	* 存在すれば上書き、存在しなければ追加
	* @access public
	* @param string
	* @param string
	* @return void
	*/	
	public function cookieEditor(string $k,string $v):void
	{
		$setCookieKey = 'Set-Cookie';
		$i = array_search(0,array_map(fn(string $x) => strpos($x,$setCookieKey.':'), $this->headers),true);
		if($i === false)
		{
			$this->headers[$setCookieKey] = $k.'='.$v.';';
			return;
		}

		if(preg_match('/'.$k.'=.*?;/',$this->headers[$i]) === 0)
		{
			$this->headers[$i] .= ';'.$k.'='.$v.';';
		}
		$this->headers[$i] = preg_replace('/'.$k.'.*?;/',$k.'='.$v.';',$this->headers[$i]);
	}

	/**
	* ヘッダー削除
	*
	* 存在すれば削除する
	* @access public
	* @param string
	* @return void
	*/	
	public function headerDeleter(string $k):void
	{
		$i = array_search(0,array_map(fn(string $x) => strpos($x,$k.':'), $this->headers),true);
		if($i === false) return;
		array_splice($this->headers,$i,1);
	}
}