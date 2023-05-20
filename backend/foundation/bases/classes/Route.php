<?php

namespace Backend\Foundation\Bases;

/**
* Router Classで扱うRoute詳細オブジェクト
* @access public
* @author keisuke <ukei2021@gmail.com>
* @version 1.0
* @abstract
* @package Backend\Foundation\Bases
*/
class Route implements DefineInterface,DumpInterface
{
	/**
	* ルート名　エイリアス
	* @var string
	*/
	private string $name = "";
	/**
	* エンドポイント定義
	* @var Endpoint
	*/
	private Endpoint $endpoint;

	/**
	* アクション定義
	* @var AbstractAction
	*/
	private AbstractAction $action;

	/**
	* $endpoint Getter
	* @access public
	* @return Endpoint
	*/
	public function getEndpoint(): Endpoint { return $this->endpoint; }

	/**
	* $action Getter
	* @access public
	* @return Action
	*/
	public function getAction(): AbstractAction { return $this->action; }

	/**
	* $action Getter
	* @access public
	* @return string
	*/
	public function getName(): string { return $this->name; }	

	/**
	* インスタンス生成時にリクエストURIパスとController名を引数として渡す
	* @access public
	* @param Endpoint $endpoint
	* @param Action $action
	* @return void
	*/
	private function __construct(Endpoint $endpoint, AbstractAction $action)
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		$this->endpoint = $endpoint;
		$this->action = $action;
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
	}

	/**
	* ルート定義
	*
	* 簡易用
	* @static
	* @access public
	* @param array $args
	* @return DefineInterface
	*/
	public static function def(...$args):DefineInterface|array
	{
		Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
		assert(count($args) === 2);
		if(is_array($args[0]) && is_subclass_of($args[1],AbstractAction::class))
        {
            return Route::defRoutesByAnyEndpoints($args[0],$args[1]);
        }
		Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
		return new Route($args[0],$args[1]);
	}

    /**
    * 定義メソッド（endpoint拡張）
    *
    * endpointが配列だった場合、複数のRouteを作成する
    * @access private
    * @param array $endpoints
    * @param AbstractAction $action
    * @return array
    */  
	private static function defRoutesByAnyEndpoints(array $endpoints,AbstractAction $action):array
	{
		assert(Util::arrayAllTypeCheck($endpoints,Endpoint::class));
		$routes = array(); 
        foreach($endpoints as $endpoint)
        {
            assert(get_class($endpoint) === Endpoint::class);
            $routes[] = new Route($endpoint,$action);
        }
		assert(Util::arrayAllTypeCheck($routes,Route::class));
        return $routes;
	}

	/**
    * ダンプ
    * @access public
    * @return string
    */
	public function dump():string
	{
		$ep = $this->endpoint->dump();
		$at = $this->action->dump();
		return <<<EOL
		Route情報<br>
		$ep
		$at
		<br>
EOL;
	}
	/**
	* 名前設定及びチェーン用
	* @access public
	* @return Route
	*/
	public function name(string $name):Route
	{
		$this->name = $name;
		return $this;
	}
}
