<?php

namespace Backend\Foundation\Bases;

/**
* 定義簡易関数インターフェース
*
* Route定義に使うクラスをいちいちnewするのが冗長になるので、これで定義するようにする。
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
interface DefineInterface
{
    /**
    * 簡易定義関数
    * 
    * @access public
    * @return DefineInterface
    */
    public static function def(...$args):DefineInterface|array;
}