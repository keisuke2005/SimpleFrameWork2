<?php

namespace Backend\Foundation\Bases;

/**
* スクラッチPHP実行Action
* 
* スクラッチPHPを実行するためのクラス
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
class DynamicPageAction extends AbstractAction
{
    const PHP_EXT = ['php'];
    /**
    * Pathオブジェクト
    * @access protected
    * @var Path
    * @see Path
    */
    private Path $filePath;

    /**
    * コンストラクタ
    * @access private
    * @param Path
    * @return void
    */
    private function __construct(Path $filePath)
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $this->filePath = $filePath;
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }
    /**
    * Action実行メソッド
    *
    * ファイルをrequireする
    * @access public
    * @return Response
    */
    public function operation():Response
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $response = Response::get();
        $filePath = $this->filePath;
        $response->setResponseClosure(function($r) use ($filePath)
        {
            assert(file_exists($filePath->toString()));
            require $filePath->toString();
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
        assert(count($args) === 1);
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return new DynamicPageAction($args[0]);
    }

    /**
    * ダンプ
    * @access public
    * @return string
    */
    public function dump():string
    {
        return <<<EOL
        アクション名 DynamicPageAction 実行ファイルパス $this->filePath<br>
EOL;
    }
}