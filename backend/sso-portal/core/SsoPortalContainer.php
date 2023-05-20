<?php
namespace Backend\SsoPortal;

use Backend\Foundation\Bases\Container;
use Backend\Foundation\Bases\Logger;
use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Message;

require_once(__DIR__."/../classes/AuthenticatedService.php");
require_once(__DIR__."/../classes/Portal.php");
require_once(__DIR__."/../classes/PortalService.php");



require_once(__DIR__."/../classes/Test.php");
require_once(__DIR__."/../classes/TestService.php");

class SsoPortalContainer extends Container
{
    protected function blueprint()
    {
        Logger::create(Config::LOG_PATH);
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');

        Message::create(Config::MESSAGE_PATH);

        $this->containers['Request'] = fn() => Request::get();

        $this->containers['Response'] = fn() => Response::get();

        $this->containers['AuthenticatedService'] = fn() => new AuthenticatedService;

        $this->containers['SsoPortal'] = fn() => new SsoPortal($this);
        
        $this->containers['TestService'] = fn() => new TestService();

        $this->containers['Test'] = fn() => new Test($this);

        $this->containers['PortalService'] = fn() => new PortalService;

        $this->containers['Portal'] = fn() => new Portal(
            $this,
            $this->instance('PortalService')
        );


        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

}