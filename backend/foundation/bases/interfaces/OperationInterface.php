<?php

namespace Backend\Foundation\Bases;

/**
* オペレーションインターフェース
* 
* アクションクラスが色々なファイル実行するための差分吸収メソッド提供
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
interface OperationInterface
{
    /**
    * Action実行メソッド
    *
    * Responseを返却します。
    * @access public
    * @return Response
    */
    public function operation():Response;
}