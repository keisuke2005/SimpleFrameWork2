<?php

namespace Backend\Foundation\Bases;

/**
* Scopeオブジェクトクラス
* @access public
* @author keisuke <ukei2021@gmail.com>
* @version 1.0
* @abstract
* @package Backend\Foundation\Bases
*/
class Scope implements DefineInterface
{
    private string $name;
    private array $allowRouteName = [];
    private array $denyRouteName = [];

    
    public function getName():string
    {
        return $this->name;
    }

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function allow(array $routeNames):Scope
    {
        foreach($routeNames as $rn)
        {
            if(in_array($rn,$this->allowRouteName)) continue;
            $this->allowRouteName[] = $rn;
        }
        return $this;
    }

    public function deny(array $routeNames):Scope
    {
        foreach($routeNames as $rn)
        {
            if(in_array($rn,$this->denyRouteName)) continue;
            $this->denyRouteName[] = $rn;
        }
        return $this;
    }

    public static function def(...$args):DefineInterface|array
    {
        return new Scope($args[0]);
    }

    public function isScope(Route $route):bool
    {
        if(in_array($route->getName(),$this->denyRouteName,true))
        {
            return false;
        }
        if(in_array($route->getName(),$this->allowRouteName,true))
        {
            return true;
        }
        return false;
    }
}
