<?php

namespace Backend\Foundation\Bases;

/**
* 基底
*
* Routerはこれを継承する
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @abstract
* @package Backend\Foundation\Bases
*/
class Core
{
    /**
    * Containerオブジェクト
    * @access protected
    * @var Container
    * @see Container
    */
    protected Container $container;

    /**
    * コンストラクタ
    *
    * Containerを保管
    * @access public
    * @param Container $container
    * @return void
    */
    public function __construct(Container $container)
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $this->container = $container;
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

    /**
    * $logger Getter
    * @access protected
    * @return Container
    */
    protected function container():Container {return $this->container;}
}