<?php

namespace Backend\Foundation\Bases;

/**
* Logger Class
*
* ログ出力管理のユーティリティ
* @final
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
final class Logger
{
	/**
	* @access private
	* @static
	* @var string 出力先のログパス
	*/
	private string $logPath;

	/**
	* @access private
	* @static
	* @var Logger
	* @see Logger
	*/
	private static Logger $logger;

	/**
	* 作成
	*
	* シングルトンで作成
	* @access private
	* @static
	* @param string $logPath
	* @return void
	*/
	public static function create(string $logPath):void
	{
		if(!isset(self::$logger))
		{
			self::$logger = new Logger($logPath);
		}
	}

	/**
	* コンストラクタ
	*
	* ユーザ定義Configクラスに定義したパスとアプリ基準パスを連結し、
	* 出力先ログパスをメンバ変数に格納
	*
	* @access private
	* @static
	* @param string $logPath
	* @return void
	*/
	private function __construct(string $logPath)
	{
		$this->logPath = $logPath;
	}

	/**
	* 書き込み
	*
	* 日付、ログレベル、メッセージ、出力クラス、出力メソッドをいい感じに出力する。
	*
	* 使用者は意識しないで良い関数。
	* @access private
	* @static
	* @param string $level
	* @param string $message
	* @param string $class
	* @param string $function
	* @return void
	*/
	private static function write(
		string $level,
		string $message,
		string $class,
		string $function
	):void
	{
        self::logFileCreate();
		$now  = date('Y/m/d H:i:s');
		$now .= sprintf('.%03d', substr(explode(".", (string)microtime(true))[1], 0, 3));
		$pid = getmypid();
		error_log(
			"[{$now}][{$level}][pid:{$pid}][fnc:{$function}]:{$message}\n",
			3,
			self::$logger->logPath
		);
	}

	/**
	* ログ出力実行ログレベルFATAL用
	*
	* [YYYY/MM/DD hh:mm:ss.xxx][FATAL][cls:ClassName][fnc:FuncName]:Message
	* @access public
	* @static
	* @param string $message
	* @return void
	*/
	public static function fatal(string $message):void
	{
		assert(isset(self::$logger));
		$dbg = debug_backtrace();
		self::write(
			'FATAL',
			$message,
			$dbg[1]['class'],
			$dbg[1]['function']
		);
	}

	/**
	* ログ出力実行ログレベルERROR用
	*
	* [YYYY/MM/DD hh:mm:ss.xxx][ERROR][cls:ClassName][fnc:FuncName]:Message
	* @access public
	* @static
	* @param string $message
	* @return void
	*/
	public static function error(string $message):void
	{
		assert(isset(self::$logger));
		$dbg = debug_backtrace();
		self::write(
			'ERROR',
			$message,
			$dbg[1]['class'],
			$dbg[1]['function']
		);
	}

	/**
	* ログ出力実行ログレベルWARNING用
	*
	* [YYYY/MM/DD hh:mm:ss.xxx][WARNING][cls:ClassName][fnc:FuncName]:Message
	* @access public
	* @static
	* @param string $message
	* @return void
	*/
	public static function warn(string $message):void
	{
		assert(isset(self::$logger));
		$dbg = debug_backtrace();
		self::write(
			'WARNING',
			$message,
			$dbg[1]['class'],
			$dbg[1]['function']
		);

	}

	/**
	* ログ出力実行ログレベルINFO用
	*
	* [YYYY/MM/DD hh:mm:ss.xxx][INFO][cls:ClassName][fnc:FuncName]:Message
	* @access public
	* @static
	* @param string $message
	* @return void
	*/
	public static function info(string $message):void
	{
		assert(isset(self::$logger));
		$dbg = debug_backtrace();
		self::write(
			'INFO',
			$message,
			$dbg[1]['class'],
			$dbg[1]['function']
		);
	}

	/**
	* ログ出力実行ログレベルDEBUG用
	*
	* [YYYY/MM/DD hh:mm:ss.xxx][DEBUG][cls:ClassName][fnc:FuncName]:Message
	* @access public
	* @static
	* @param string $message
	* @return void
	*/
	public static function debug(string $message):void
	{
		assert(isset(self::$logger));
		$dbg = debug_backtrace();
		self::write(
			'DEBUG',
			$message,
			$dbg[1]['class'],
			$dbg[1]['function']
		);
	}

	/**
	* ログファイル作成
	* @access private
	* @static
	* @return void
	*/
    private static function logFileCreate():void
    {
        $path = pathinfo(self::$logger->logPath);
        if(! file_exists($path['dirname']))
        {
            mkdir($path['dirname'], 0755);
        }

        if(! file_exists(self::$logger->logPath))
        {
            touch(self::$logger->logPath);
        }
    }
}
