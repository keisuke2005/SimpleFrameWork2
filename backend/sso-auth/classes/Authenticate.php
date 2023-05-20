<?php

namespace Backend\SsoAuth;

use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Core;
use Backend\Foundation\Bases\Container;

class Authenticate extends Core
{
    private AuthenticateService $authenticateService;

    public function __construct(Container $container,AuthenticateService $authenticateService)
    {
        $this->authenticateService = $authenticateService;
        parent::__construct($container);
        
    }

    public function authenticate():Response
    {
        $request = Request::get();
        $response = Response::get();
        $result = $this->authenticateService->loginAuthentication($request);
        $response->json($result);
        return $response;
    }

    public function verifyCodeAuthorization():Response
    {
        $request = Request::get();
        $response = Response::get();
        $result = $this->authenticateService->authorizationCode($request);
        $response->json($result);
        return $response;
    }

    public function verifyTokenAccess():Response
    {
        $request = Request::get();
        $response = Response::get();


        $result = $this->authenticateService->access($request);
        $response->json($result);
        return $response;
    }

    public function verifyTokenRefresh():Response
    {
        $request = Request::get();
        $response = Response::get();


        $result = $this->authenticateService->refresh($request);
        $response->json($result);
        return $response;
    }
}
?>
