<?php

namespace Backend\SsoPortal;

use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Response;
use Backend\Foundation\Bases\Util;


class AuthenticatedService
{
    public function verifyCodeAuthorization(Request $request):\stdClass
    {
        $url = 'https://localhost/sso-auth/api/verify/code/authorization';
        $method = "POST";
        $header = ['Content-type: application/json'];
        $data = json_encode(['authCode' => $request->getRequestParameter('authCode')]);
        $response = Util::curlExec($url,$method,$header,$data);
        $res = json_decode($response['body']);
        return $res;
    }


    public function verifyToken(string $accessToken,string $refreshToken):\stdClass
    {
        $url = 'https://localhost/sso-auth/api/verify/token/access';
        $method = "POST";
        $header = ['Content-type: application/json'];
        $data = json_encode(['accessToken' => $accessToken]);
        $response = Util::curlExec($url,$method,$header,$data);
        $res = json_decode($response['body']);
        if((bool)$res->result)
        {
            return $res;
        }

        $url = 'https://localhost/sso-auth/api/verify/token/refresh';
        $method = "POST";
        $header = ['Content-type: application/json'];
        $data = json_encode(['refreshToken' => $refreshToken]);
        $response = Util::curlExec($url,$method,$header,$data);
        $res = json_decode($response['body']);
        if((bool)$res->result)
        {
            $this->setCookieForAnyRealm(
                'accessToken',
                $res->accessToken->token,
                $res->accessToken->expires,
            $res->realm);
            return $res;
        }
        return $res;
    }

    public function setCookieForAnyRealm(string $key,string $value,int $expires,array $realm):void
    {
        $time = time() + $expires;
        foreach($realm as $r)
        {
            setcookie($key,$value,[
                'expires' => $time,
                'domain' => $r->domain,
                'path' => $r->path
            ]);
        }   
    }

    public function getAuthority(string $accessToken,string $refreshToken):bool
    {
        $url = 'https://localhost/sso-auth/api/verify/token/access';
        $method = "POST";
        $header = ['Content-type: application/json'];
        $data = json_encode(['accessToken' => $accessToken]);
        $response = Util::curlExec($url,$method,$header,$data);
        $res = json_decode($response['body']);
        if((bool)$res->result)
        {
            return true;
        }
    }
}
?>
