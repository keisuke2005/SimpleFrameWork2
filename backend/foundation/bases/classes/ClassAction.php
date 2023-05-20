<?php

namespace Backend\Foundation\Bases;

/**
* クラス実行Action
* 
* クラスを実行するためのクラス
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package @package Backend\Foundation\Bases
*/
class ClassAction extends AbstractAction 
{
    /**
    * Containerオブジェクト
    * @access protected
    * @var Container
    * @see Container
    */
    protected Container $container;

    /**
    * コンテナ名保管変数
    * @access protected
    * @var string
    */
    protected string $class;

    /**
    * 実行メソッド名保管変数
    * @access protected
    * @var string
    */
    protected string $method;

    /**
    * コンストラクタ
    * @access private
    * @param Container
    * @param string
    * @param string
    * @return void
    */
    private function __construct(Container $container ,string $class,string $method)
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $this->container = $container;
        $this->class = $class;
        $this->method = $method;
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

    /**
    * Action実行メソッド
    *
    * Route定義されたクラスとメソッドを実行結果をResponseにセットする
    * Responseに保管されたbodyを出力する
    * @access public
    * @return Response
    */
    public function operation():Response
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        assert(!empty($this->class));
        assert(!empty($this->method));
        $instance = $this->container->instance($this->class);
        $method = $this->method;
        assert(method_exists($instance,$method));
        $response = $instance->$method();
        $response->setResponseClosure(function($r)
        {
            print $r->getBody();
        });

        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return $response;
    }

    /**
    * 定義メソッド
    *
    * 簡略化用
    * @access public
    * @param array $args
    * @return DefineInterface
    */
    public static function def(...$args):DefineInterface
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        assert(count($args) === 3);
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return new ClassAction($args[0],$args[1],$args[2]);
    }

    /**
    * ダンプ
    * @access public
    * @return string
    */
    public function dump():string
    {
        return <<<EOL
        アクション名 ClassAction 実行クラス $this->class 実行メソッド $this->method<br>
EOL;
    }
}