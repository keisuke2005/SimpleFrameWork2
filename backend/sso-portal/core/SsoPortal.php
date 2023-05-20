<?php
namespace Backend\SsoPortal;

require_once __DIR__.'/../foundation/bases/loader.php';
require_once(__DIR__."/../configs/Config.php");

use Backend\Foundation\Bases\Router;
use Backend\Foundation\Bases\Route;
use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Path;
use Backend\Foundation\Bases\Logger;
use Backend\Foundation\Bases\StaticPageAction;
use Backend\Foundation\Bases\DynamicPageAction;
use Backend\Foundation\Bases\Scope;

class SsoPortal extends Router
{
    private array $userScopes = array();

    protected function prologue(): void
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $request = Request::get();
     
        if(!is_null($request->getRequestParameter('authCode')))
        {
            $authenticatedService = $this->container->instance('AuthenticatedService');
            $res = $authenticatedService->verifyCodeAuthorization($request);
            if(! $res->result)
            {
                header('Location: /sso-portal/index');
                exit;
            }
            if(isset($res->accessToken))
            {
                $authenticatedService->setCookieForAnyRealm(
                    'accessToken',
                    $res->accessToken->token,
                    $res->accessToken->expires,
                $res->realm);
    
                $authenticatedService->setCookieForAnyRealm(
                    'refreshToken',
                    $res->refreshToken->token,
                    $res->refreshToken->expires,
                $res->realm);
            }

            if(isset($res->scope))
            {
                $this->userScopes = array_map(
                    fn($scope) => Scope::def($scope),
                    $res->scope
                );
            }
            
            
        }

        if(! $this->verification())
        {
            Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
            header('Location: '.Config::LOGIN_URL);

        }
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }
    
    private function verification():bool
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');

        $accessToken = isset($_COOKIE['accessToken']) ? $_COOKIE['accessToken'] : "";
        $refreshToken = isset($_COOKIE['refreshToken']) ? $_COOKIE['refreshToken'] : "";
        $authenticatedService = $this->container->instance('AuthenticatedService');
        $res = $authenticatedService->verifyToken($accessToken,$refreshToken);

        if(! $res->result)
        {
            return false;
        }
        if(isset($res->accessToken))
        {
            $authenticatedService->setCookieForAnyRealm(
                'accessToken',
                $res->accessToken->token,
                $res->accessToken->expires,
            $res->realm);
        }

        if(isset($res->scope))
        {
            $this->userScopes = $res->scope;
        }
            
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return true;
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
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return __DIR__.'/../configs/routes/';
    }

    protected function scopeCheck(Route $route):bool
	{
        foreach($this->userScopes as $s)
        {
            $scope = $this->getScope($s);
            if(is_null($scope)) continue;

            if($scope->isScope($route))
            {
                return true;
            }
        }
		return false;
	}
}