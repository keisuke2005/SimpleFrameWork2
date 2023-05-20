<?php

namespace Backend\Foundation\Bases;

/**
* リクエストオブジェクト
* 
* リクエスト情報まとめたオブジェクト
* 最初にRouterが生成したオブジェクトが永続的に受け渡されて行きたいので、シングルトンです。
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
class Request
{
	/**
	* @access private
	* @var Request 自身を管理するRequestオブジェクト格納変数
	* @see Request
	*/
	private static Request $request;

	/**
    * $_SERVERマジック変数から取得できる且つ、使いそうなものを変数にまとめてます。
    * @access protected
    * @var ?string
    */
	protected ?string $serverAddr;
	protected ?string $serverProtocol;
	protected ?string $requestMethod;
	protected ?string $requestTime;
	protected ?string $documentRoot;
	protected ?string $httpReferer;
	protected ?string $httpUserAgent;
	protected ?string $https;
	protected ?string $remotePort;
	protected ?string $remoteUser;
	protected ?string $serverPort;
	protected ?string $requestUri;
	protected ?string $contextDocumentRoot;
	protected ?string $contextPrefix;

	/**
	* @access private
	* @var array リクエスト時のパラメータ(Form/Json)をまとめる変数
	*/
	private array $requestParameter = [];

	/**
	* @access private
	* @var array パスパラメータをまとめる変数
	*/
	private array $pathParameter = [];

	private array $headers = [];

	/**
    * コンストラクタ
    * @access public
    * @return void
    */
	function __construct()
	{
		$this->setServerInfo();
		$this->setRequestParameter();
		$this->setHeaderInfo();
	}

	/**
    * ヘッダーセット
    * @access private
    * @return void
    */
	private function setHeaderInfo()
	{
		$this->headers = getallheaders();
	}

	/**
    * 指定キーのヘッダーを取得
    * @access public
	* @param string キー
    * @return mixed 値
    */
	public function getHeader(string $key):mixed
	{
		return $this->headers[$key];
	}

	/**
	* インスタンス取得
	*
	* 外部からこのクラスを扱うときは、こちらを利用してインスタンスを生成する。
	* @access public
	* @return Request
	*/
	public static function get(): Request
	{
		if(!isset(self::$request)) self::$request = new Request();
		return self::$request;
	}

	/**
	* $_SERVERマジック変数から抽出
	*
	* @access private
	* @return void
	*/
	private function setServerInfo()
	{
		$this->serverAddr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : "";
		$this->serverProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : "";
		$this->requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "";
		$this->requestTime = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : "";
		$this->documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : "";
		$this->httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
		$this->httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
		$this->https = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : "";
		$this->remotePort = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : "";
		$this->remoteUser = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "";
		$this->serverPort = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : "";
		$this->requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
		$this->contextDocumentRoot = isset($_SERVER['CONTEXT_DOCUMENT_ROOT']) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] : "";
		$this->contextPrefix = isset($_SERVER['CONTEXT_PREFIX']) ? $_SERVER['CONTEXT_PREFIX'] : "";
	}

	/**
	* リクエストパラメータ生成メソッド
	*
	* @access private
	* @return void
	*/
	private function setRequestParameter()
	{
		$parameter = array();

		if(array_key_exists("Content-Type",getallheaders()) && getallheaders()["Content-Type"] === "application/x-www-form-urlencoded")
		{
			$parameter = $_REQUEST;
		}
		else
		{
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'GET':
					$parameter = $_REQUEST;
					break;
				case 'POST':
				case 'PUT':
				case 'DELETE':
				default:
					$parameter = json_decode(file_get_contents('php://input'));
					break;
			}
		}

		if(is_null($parameter)) return;
		foreach($parameter as $key => $req)
		{
			$this->requestParameter[$key] = $req;
		}
	}

	public function getOriginBody():string
	{
		return file_get_contents('php://input');
	}

	/**
	* リクエストパラメータ取得メソッド
	*
	* @access public
	* @param string
	* @return mixed
	*/
	public function getRequestParameter(string|null $key = null):mixed
	{
		if(is_null($key)) return $this->requestParameter;
		if(!isset($this->requestParameter[$key])) return null; 
		return $this->requestParameter[$key];
	}

	/**
	* パスパラメータ設定メソッド
	*
	* ルート決定中にパスパラメータは確定し、設定される
	* @access public
	* @param array
	* @return void
	*/
	public function setPathParameter(array $parameter):void
	{
		$this->pathParameter = $parameter;
	}

	/**
	* パスパラメータ取得メソッド
	*
	* @access public
	* @param string
	* @return mixed
	*/
	public function getPathParameter(string $key):mixed
	{
		if(!isset($this->pathParameter[$key])) return null; 
		return $this->pathParameter[$key];
	}

	/**
	* requestParameterのkey存在確認
	*
	* @access public
	* @param string
	* @return bool
	*/
	public function reqParamExist(string $key):bool
	{
		return array_key_exists($key,$this->requestParameter) ? true : false;
	}

	/**
	* pathParameterのkey存在確認
	*
	* @access public
	* @param string
	* @return bool
	*/
	public function pathParamExist(string $key):bool
	{
		return array_key_exists($key,$this->requestParameter) ? true : false;
	}

	/**
	* プロパティゲッター
	*
	* @access public
	*/	
	public function __call($name, $arguments) {
        $prefix = substr($name, 0, 3);
        $propertyName = lcfirst(substr($name, 3));
        if ($prefix === 'get' && property_exists($this, $propertyName)) {
            return $this->{$propertyName};
        }
    }
}