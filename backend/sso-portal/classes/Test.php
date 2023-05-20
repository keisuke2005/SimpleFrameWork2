<?php

namespace Backend\SsoPortal;

use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Core;
use Backend\Foundation\Bases\Util;

class Test extends Core
{
    public function testFunc():Response
    {
        $testService = $this->container->instance('TestService');
        $aaa = $testService->getTestUser('hoge');
        $response = Response::get();
        $key = Util::getKey();

        $text = 'plaintext';
        $encrypt = Util::encryptWithKey($text,$key);
        $decrypt = Util::decryptWithKey($encrypt,$key);

        $response->json(['plain' => $text,'encrypt'=>$encrypt,'decrypt'=>$decrypt]);
        return $response;
        
    }
}
?>
