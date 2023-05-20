<?php

namespace Backend\Foundation\Bases;

/**
* Util Class
*
* ユーティリティ
* @final
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
final class Util
{
	/**
    * 配列の中身が全て同じ型かをチェックする
	*
	* 配列以外はメソッド引数で型チェックができるが、配列はできない為これを使って事前チェック
	* 主にassertと組み合わせて使用する
	* 例 assert(Util::arrayAllTypeCheck($endpoints,Endpoint::class));
    * @access public
    * @static
    * @param array $array
	* @param string $type
    * @return bool
    */
    public static function arrayAllTypeCheck(array $array,string $type):bool
	{
		foreach($array as $a)
		{
			if(get_class($a) !== $type)
			{
				return false;
			}
		}
		return true;
	}

	/**
    * アサーションビュー
    *
    * アサーションのExceptionを補足したときに画面表示する
    * @access public
    * @static
    * @param \AssertionError $e
    * @return void
    */
    public static function assertionView(\AssertionError $e):void
    {
        $fileLine = self::easyTagCreate('div',$e->getFile().'('.$e->getLine().')');
        $message = self::easyTagCreate('div',$e->getMessage());
        $trace = '';
        foreach($e->getTrace() as $t)
        {
            $trace .= self::easyTagCreate('div',$t['file'].'('.$t['line'].')'.'func:'.$t['function']);
        }
        echo <<<EOS
<h1>AssertionError</h1><br>
$fileLine
$message
<h2>Trace</h2>
$trace
EOS;
    }

    /**
    * 簡単タグ制作
    * @access public
    * @static
    * @param string
	* @param string
    * @return string
    */
    public static function easyTagCreate(string $tag,string $word):string
    {
        return '<'.$tag.'>'.$word.'</'.$tag.'>';
    }

	/**
    * curl実行
	*
	* ヘッダーとレスポンスを分けて返してくれる
    * @access public
    * @static
    * @param string $url Curlを打ち込む先のURL
	* @param string $method httpメソッド
	* @param array|null ヘッダー設定する場合
	* @param string|null ポストデータを設定する場合
    * @return array
    */
    public static function curlExec(
		string $url,
		string $method,
		array|null $headers = null,
		string|array|null $postField = null
	):array
    {
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $c = curl_init();
		// デフォルト（必須設定）設定
		// CURLOPT_URL				URL
		// CURLOPT_CUSTOMREQUEST	メソッド
		// CURLOPT_SSL_VERIFYPEER	サーバー証明書の検証を行わない
		// CURLOPT_SSL_VERIFYHOST	サーバー証明書の検証を行わない
		// CURLOPT_RETURNTRANSFER	返り値を文字列で返す
		// CURLOPT_HEADER			ヘッダーも取得する
        curl_setopt($c,CURLOPT_URL,$url);
        curl_setopt($c,CURLOPT_CUSTOMREQUEST,$method);
		curl_setopt($c,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($c,CURLOPT_SSL_VERIFYHOST,false);  
        curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($c,CURLOPT_HEADER,true);

		if(! empty($headers))
		{
			curl_setopt($c,CURLOPT_HTTPHEADER,$headers);
		}

		if(! empty($postField))
		{
			curl_setopt($c,CURLOPT_POSTFIELDS,$postField);
		}

		// 実行
        $response = curl_exec($c);
        $curlInfo = curl_getinfo($c);

		// ヘッダーとボディを分解する
        $headerSize = 0;
        if(isset($curlInfo["header_size"]) && $curlInfo["header_size"] != "")
		{
            $headerSize = $curlInfo["header_size"];
        }
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        curl_close($c);

		// ヘッダーとボディを配列にして返す
		return array(
			'header' => $header,
			'body' => $body,
            'info' => $curlInfo
		);
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }
    
    
	/**
    * 暗号化
	*
	* キーを使って暗号化
    * @access public
    * @static
    * @param string 平文
	* @param string 暗号化キー
    * @return string 暗号化文
    */
    public static function encryptWithKey(string $text,string $key):string
    {
        return openssl_encrypt($text,'AES-128-ECB',$key);
    }

	/**
    * 複合化
	*
	* キーを使って複合化
    * @access public
    * @static
    * @param string 暗号化分
	* @param string 暗号化キー
    * @return string 平文
    */
    public static function decryptWithKey(string $text,string $key):string
    {
        return openssl_decrypt($text,'AES-128-ECB',$key);
    }

	/**
    * キー取得
	*
	* ファイルに保管されたキー取得
    * @access public
    * @static
    * @param Path パス
    * @return string キー文
    */
    public static function getKey(Path $path = null):string
    {
        if(empty($path))
        {
            $path = Path::def(__DIR__.'/../../resources/.sercret');
        }
        
        if(!is_readable($path->toString()))
        {
            throw new \Exception("ファイルの読み取り権限がありません。");
        }
        $fileHandle = fopen($path->toString(),'r');
        $key = "";
        while($data = fgets($fileHandle))
        {
            $key = $data;
            break;
        }
        fclose($fileHandle);
        return $key;
    }

	/**
    * トークン生成
	*
	* ランダムで一意なトークンを生成する
    * @access public
    * @static
    * @param string プレフィックス
    * @return string 発行されたトークン
    */
    public static function createToken(string $prefix):string
    {
        $random = uniqid($prefix.'.',true);
        $str = array_merge(range('a','z'),range('0','9'),range('A','Z'));
        $strBuffer = null;
        for ($i = 0; $i < 23; $i++) {
            $strBuffer .= $str[rand(0, count($str) - 1)];
        }
        $token = $random.'.'.$strBuffer;
        return $token;
    }
}