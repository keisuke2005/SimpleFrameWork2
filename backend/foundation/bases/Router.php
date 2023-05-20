<?php

namespace Backend\Foundation\Bases;

use \ArrayIterator;

/**
* Router Class
* アクセスURLに対してのルーティングを制御する
* @access public
* @author keisuke <ukei2021@gmail.com>
* @version 1.0
* @abstract
* @package Backend\Foundation\Bases
*/
abstract class Router extends Core
{
	/**
	* 決定済みRouteオブジェクト
	* @access private
	* @var Route
	* @see Route
	*/
	protected Route $determineRoute;

	protected ?AbstractAction $determineAction = null;

	/**
	* 全ての経路情報配列
	* @access private
	* @var array
	*/
	private static array $routes = [];

	/**
	* 全てのスコープ情報配列
	* @access private
	* @var array
	*/
	private static array $scopes = [];

	/**
	* 最終的なレスポンスオブジェクト
	* @access private
	* @var Response
	*/
	private ?Response $ultimateResponse = null;

	/**
	* コア処理実行
	*
	* 本クラスのコア処理
	* @access protected
	* @return void
	*/
	protected function execute():void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		// 序章
		$this->prologue();
		// ルート定義読み込み
		$this->definedRoute();
		// ルート定義加工
		$this->routePreprocessing();

		// ルート決定
		if(is_null($this->determineAction))
		{
			$this->determineRoute();
		}

		// ルート決定後の別の考慮
		if(is_null($this->determineAction))
		{
			$this->anotherConsideration();
		}

		// デフォルトルート
		if(is_null($this->determineAction))
		{
			$this->defaultRoute();
		}
		
		// ルートアクション実行
		$this->routeAction();
		// 出力
		$this->output();
		// 終章
		$this->epilogue();
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	/**
	* 序章
	*  
	* Routerクラスのコア処理の冒頭に実行される
	* オーバーライドして使ってください。
	* @access protected
	* @return void
	*/
	protected function prologue():void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	/**
	* 終章
	*  
	* Routerクラスのコア処理の最後に実行される
	* オーバーライドして使ってください。
	* @access protected
	* @return void
	*/
	protected function epilogue():void{}

	/**
	* URIプレフィックス
	* URIのプレフィックスを返してください。
	* @abstract
	* @access protected
	* @return Path
	*/
	abstract protected function uriPrefix():Path;

	protected function basePath():Path
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $basePath = $this->uriPrefix();
        $basePath->joinForward(Request::get()->getContextDocumentRoot());
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return $basePath;
    }

	protected function routePreprocessing():void
	{
		$routes = self::$routes;
		$tmp = array_map(function($r){
			$action = $r->getAction();
			if($action::class === StaticPageAction::class)
			{
				$action->addPrefix($this->basePath()->toString());
			}

			if($action::class === DynamicPageAction::class)
			{
				$action->addPrefix($this->basePath()->toString());
			}
			return $r;
		}, $routes);

		self::$routes = $tmp;
	}

	protected function anotherConsideration():void
	{
		return;
	}

	/**
	* ルート決定
	*
	* リクエストURIとRouteオブジェクトを比較し、一致したRouteオブジェクトを返却する。
	* @access protected
	* @return void
	*/
	protected function determineRoute(): void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		foreach(self::$routes as $route)
		{
			assert(get_class($route) === Route::class);
			assert(get_class($route->getEndpoint()) === Endpoint::class);
			assert(is_subclass_of($route->getAction(),AbstractAction::class));
			if(! $route->getEndpoint()->requestMatch($this->uriPrefix()))
			{
				continue;
			}

			if(! $this->scopeCheck($route))
			{
				continue;
			}

			$this->determineAction = $route->getAction();
			break;
		}
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	/**
	* デフォルトルート設定
	*
	* デフォルトルート用のRouteオブジェクトを返す。
	*
	* 違うものにしたい時はオーバーライドで変更してください。
	* @access protected
	* @return Route $default
	*/
	protected function defaultRoute():void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		$this->determineAction = ClassAction::def($this->container,'Test','testFunc');
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	/**
	* ルート定義読み込み
	*
	* ルート定義列挙ファイルを実行して、すべて読み込む
	* @access protected
	* @return array<Route>
	*/
	protected function definedRoute():void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		$routeFiles = $this->getRouteFiles();
		assert(gettype($routeFiles) === 'array');
		$iterator = new ArrayIterator((array)$routeFiles);
		foreach($iterator as $file)
		{
			if(is_file($file))
			{
				require_once($file);
			}
		}
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}


	/**
	* ルーティング定義ファイル取得
	*
	* 一応分解しておきます。
	* @access protected
	* @return string
	*/
	protected function getRouteFiles(): array
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		$paths = array_filter(glob($this->getRouteConfigDir().'/*.php'), 'is_file');
		assert(gettype($paths) === 'array');
		assert(count($paths) >  0);
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
		return $paths;
	}

	/**
	* getRouteConfig
	*
	* ルーティング設定ディレクトリ選定
	* オーバーライド義務化
	* @access protected
	* @static
	* @abstract
	*/
	abstract public static function getRouteConfigDir();
	

	/**
	* Actionオブジェクト実行
	*
	* Routeオブジェクトに保持されているアクションを実行する
	* 振る舞いの違いはActionオブジェクトの中身で吸収
	* @access protected
	* @return void
	*/
	protected function routeAction(): void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		$response = $this->determineAction->operation();
		$this->ultimateResponse = $response;
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	/**
	* 出力
	*
	* 最終的にレスポンスはこのメソッドで出力する（しましょう）
	* プロローグやオーバライドでもこのメソッド実行で終端
	* @access protected
	* @return void
	*/
	protected function output(): void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		$this->ultimateResponse->output();
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}


	/**
	* Route定義追加
	*
	* Route::defで生成されたRouteオブジェクトを配列でまとめる
	* @access public
	* @static
	* @param Route|array ...$routes
	* @return void
	*/
	public static function push(Route|array ...$routes):void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		Router::$routes = array_reduce($routes,function($x,$y){
			if(is_array($y))
			{
				assert(Util::arrayAllTypeCheck($y,Route::class));
				return array_merge($x,$y);
			}
			return array_merge($x,[$y]);
		},Router::$routes);
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	/**
	* Route定義ダンプ
	*
	* 出力用
	* @access public
	* @static
	* @return void
	*/
	public static function routesDump()
	{
		for($i = 0;$i < count(Router::$routes);$i++)
		{
			$dump = Router::$routes[$i]->dump();
			echo <<<EOL
			[$i] $dump
EOL;
		}
	}

	/**
	* 起動
	*
	* Routerクラスを継承するクラスを実行する
	* 順序を制限する為、外部から実行できるのは基本的にはこのクラスのみ
	* @access public
	* @static
	* @param Router $router
	* @return void
	*/
	public static function routing(Router $router)
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		try
		{
			$router->execute();
		}
		catch (\AssertionError $e)
		{
			Util::assertionView($e);
			Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
			exit;
		}
		catch(\Exception $e)
		{
			echo $e->getMessage();
			Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
		}
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	public static function scopes(Scope ...$scopes):void
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		Router::$scopes = array_merge(Router::$scopes,$scopes);
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	public static function getScope(string $key):?Scope
	{
		foreach(Router::$scopes as $scope)
		{
			if($scope->getName() === $key)
			{
				return $scope;
			}
		}
		return null;
	}

	protected function scopeCheck(Route $route):bool
	{
		return true;
	}
}
