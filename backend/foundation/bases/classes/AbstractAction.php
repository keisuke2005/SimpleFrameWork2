<?php

namespace Backend\Foundation\Bases;

/**
* 基底アクションクラス
* 
* 
* @abstract
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
abstract class AbstractAction implements DefineInterface,OperationInterface,DumpInterface
{
    /**
    * Action実行メソッド
    * @abstract
    * @access public
    * @param Logger
    * @param Request
    */
    abstract public function operation():Response;

    /**
    * 定義メソッド
    * @abstract
    * @access public
    * @param ...$args
    * @return DefineInterface
    */
    abstract public static function def(...$args):DefineInterface|array;

    /**
    * 定義メソッド
    * @abstract
    * @access public
    * @param ...$args
    * @return DefineInterface
    */
    abstract public function dump():string;
}