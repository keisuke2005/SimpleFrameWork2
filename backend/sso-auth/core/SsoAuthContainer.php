<?php
namespace Backend\SsoAuth;

use Backend\Foundation\Bases\Container;
use Backend\Foundation\Bases\Logger;
use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Dao;

require_once(__DIR__."/../classes/Authenticate.php");
require_once(__DIR__."/../classes/AuthenticateService.php");
require_once(__DIR__."/../classes/User.php");
require_once(__DIR__."/../classes/UserService.php");

class SsoAuthContainer extends Container
{
    protected function blueprint(){
        Logger::create(Config::LOG_PATH);
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        Dao::create(
            'auth',
            Config::DB_TYPE,
            Config::DB_HOST,
            Config::DB_PORT,
            Config::DB_NAME,
            Config::DB_USER,
            Config::DB_PSWD
        );
        
        $this->containers['Request'] = fn() => Request::get();

        $this->containers['Response'] = fn() => Response::get();

        $this->containers['SsoAuth'] = fn() => new SsoAuth($this);

        $this->containers['AuthenticateService'] = fn() => new AuthenticateService;
        
        $this->containers['Authenticate'] = fn() => new Authenticate($this,$this->instance('AuthenticateService'));

        $this->containers['UserService'] = fn() => new UserService;
        
        $this->containers['User'] = fn() => new User($this,$this->instance('UserService'));

        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

}