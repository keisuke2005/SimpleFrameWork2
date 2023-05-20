<?php
namespace Backend\SsoAuth;

require_once __DIR__.'/../foundation/bases/loader.php';
require_once(__DIR__."/../configs/Config.php");

use Backend\Foundation\Bases\Router;
use Backend\Foundation\Bases\Path;
use Backend\Foundation\Bases\StaticPageAction;
use Backend\Foundation\Bases\DynamicPageAction;
use Backend\Foundation\Bases\Logger;
use Backend\Foundation\Bases\Request;

class SsoAuth extends Router
{


    protected function prologue(): void
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

    protected function anotherConsideration(): void
    {
        $this->existInWebroot();
        return;
    }

    private function existInWebroot():void
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $request = Request::get();
        $basePath = $this->basePath()->toString().'/webroot';
        $pathParts = explode(Config::APPLCATION_DIRECTORY,$request->getRequestUri());
        $path =  Path::def($basePath.$pathParts[1]);

        if(! $path->fileExists())
        {
            Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
            return;
        }

        if(in_array($path->getExtension(),array_merge(
            StaticPageAction::JS_EXT,
            StaticPageAction::CSS_EXT,
            StaticPageAction::IMAGE_EXT,
        )))
        {
            $this->determineAction = StaticPageAction::def($path);
            Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
            return;
        }

        if(in_array($path->getExtension(),DynamicPageAction::PHP_EXT))
        {
            $this->determineAction = DynamicPageAction::def($path);
            Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
            return;
        }
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

    protected function uriPrefix():Path
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return Path::def(Config::URI_PREFIX);
    }

    public static function getRouteConfigDir():string
    {
        return __DIR__.'/../configs/routes/';
    }
}