<?php

namespace Backend\Foundation\Bases;

/**
* コンテナ
*
* コンテナの基底クラス
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @abstract
* @package Backend\Foundation\Bases
*/
abstract class Container
{
    /**
    * コンテナ保管配列
    * @access protected
    * @var array
    */
    protected array $containers = array();

    protected array $singletons = array();

    /**
    * コンストラクタ
    *
    * @return void
    */
    function __construct()
    {
        $this->blueprint();
    }

    /**
    * 設計図
    *
    * インスタンスの作り方を定義していく
    * @access protected
    * @abstract
    */
    abstract protected function blueprint();

    /**
    * インスタンス生成
    *
    * 設計図を基に作成
    * @access public
    * @param string キー
    * @return mixed
    */
    public function instance($key):mixed
    {
        if(!isset($this->containers[$key]))
        {
            //throw new \Exception("keyが存在しません。");
            // デバッグ用
            foreach(array_keys($this->containers) as $k)
            {
                echo $k;
                echo "<br>";
            }
            exit;
        }
        $closure = $this->containers[$key];
        return $closure();
    }
}