<?php

namespace Backend\SsoPortal;

use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Core;
use Backend\Foundation\Bases\Container;

class Portal extends Core
{
    private PortalService $portalService; 
    function __construct(Container $container,PortalService $portalService)
    {
        parent::__construct($container);
        $this->portalService = $portalService;
    }

    public function getInfo():Response
    {
        $request = Request::get();
        $user = $this->portalService->getUser();
        $site = $this->portalService->getSite();

        $response = Response::get();
        $response->json([
            'user' => $user,
            'site' => $site
        ]);
        return $response;
    }
}
?>
