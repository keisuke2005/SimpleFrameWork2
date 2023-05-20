<?php

namespace Backend\Foundation\Bases;

/**
* ダンプインターフェース
*
* dumpメソッドを保証する
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
interface DumpInterface
{
    /**
    * ダンプ関数
    * 
    * @access public
    * @return string
    */
    public function dump():string;
}