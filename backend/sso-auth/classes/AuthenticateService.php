<?php

namespace Backend\SsoAuth;

use Backend\Foundation\Bases\Dao;
use Backend\Foundation\Bases\Request;
use Backend\Foundation\Bases\Util;

class AuthenticateService
{
    public function loginAuthentication(Request $request):array
    {
        $dao = Dao::get('auth');
        $user = $request->getRequestParameter('user');
        $pswd = $request->getRequestParameter('pswd');
        $select = $dao->selectOneRow(Dao::read(__DIR__.'/../sqls/user_auths/select/authentication.sql'),[
            $user,
            $pswd
        ]);

        if(!$select['result'])
        {
            return [
                'result' => false,
                'message' => 'ユーザIDまたはパスワードが一致しません。'
            ];
        }

        $userAuthId = $select['data']->id;
        $authCode = Util::createToken('code');
        $insert = $dao->insert(Dao::read(__DIR__.'/../sqls/authorization_codes/insert/issueAuthorizationCode.sql'),[
            $userAuthId,
            $authCode,
            Config::AUTHORIZATION_CODE_EXPIRES
        ]);
        if(!$insert['result'])
        {
            return [
                'result' => false,
                'message' => '認証に失敗しました。'
            ];
        }
        
        return [
            'result' => true,
            'message' => 'success',
            'authCode' => $authCode
        ];
    }

    public function authorizationCode(Request $request):array
    {
        $dao = Dao::get('auth');

        $select = $dao->selectOneRow(Dao::read(__DIR__.'/../sqls/authorization_codes/select/validAuthorizationCode.sql'),[
            $request->getRequestParameter('authCode')
        ]);

        if(!$select['result'])
        {
            return [
                'result' => false
            ];
        }
        $userAuthId = $select['data']->user_auth_id;
        $authorizationCodeId = $select['data']->id;
        $dao->begin();
        try
        {
            $accessToken = Util::createToken('access');
            $refreshToken = Util::createToken('refresh');
            $insert = $dao->insert(Dao::read(__DIR__.'/../sqls/access_tokens/insert/issueAccessToken.sql'),[
                $accessToken,
                $userAuthId,
                Config::ACCESS_TOKEN_EXPIRES
            ]);
            if(!$insert['result']) throw new \PDOException('access_tokensインサート失敗');

            $insert = $dao->insert(Dao::read(__DIR__.'/../sqls/refresh_tokens/insert/issueRefreshToken.sql'),[
                $refreshToken,
                $userAuthId,
                Config::REFRESH_TOKEN_EXPIRES
            ]);
            if(!$insert['result']) throw new \PDOException('refresh_tokensインサート失敗');

            $update = $dao->modify(Dao::read(__DIR__.'/../sqls/authorization_codes/update/invalidAuthorizationCode.sql'),[
                $authorizationCodeId
            ]);
            
            if(!$insert['result']) throw new \PDOException('authorization_codesアップデート失敗');

            $select = $dao->selectAnyRows(
                Dao::read(__DIR__.'/../sqls/users/select/getUserAuthority.sql'),
                [$accessToken]
            );

            $dao->commit();
            return [
                'result' => true,
                'scope' => array_map(
                    fn($x) => $x->authority_code
                    ,$select['data']
                ), 
                'accessToken' => [
                    'token' => $accessToken,
                    'expires' => Config::ACCESS_TOKEN_EXPIRES
                ],
                'refreshToken' => [
                    'token' => $refreshToken,
                    'expires' => Config::REFRESH_TOKEN_EXPIRES
                ],
                'realm' => [
                    [
                        'domain' => '192.168.56.119',
                        'path' => '/sso-auth'
                    ],
                    [
                        'domain' => '192.168.56.119',
                        'path' => '/sso-portal'
                    ],
                    [
                        'domain' => '192.168.56.119',
                        'path' => '/sso-proxy'
                    ]
                ]
            ];
        }
        catch(\PDOException $e)
        {
            $dao->rollback();
        }
    }

    public function access(Request $request):array
    {
        $accessToken = $request->getRequestParameter('accessToken');
        $dao = Dao::get('auth');
        try
        {
            $cnt = $dao->countRow(Dao::read(__DIR__.'/../sqls/access_tokens/select/validAccessToken.sql'),[
                $accessToken
            ]);
            if($cnt < 1)
            {
                return ['result' => false];
            }

            $select = $dao->selectAnyRows(
                Dao::read(__DIR__.'/../sqls/users/select/getUserAuthority.sql'),
                [$accessToken]
            );
            return [
                'result' => true,
                'scope' => array_map(
                    fn($x) => $x->authority_code
                    ,$select['data']
                )
            ];
        }
        catch(\PDOException $e)
        {
            
        }
    }

    public function refresh(Request $request):array
    {
        $refreshToken = $request->getRequestParameter('refreshToken');
        $dao = Dao::get('auth');
        try
        {
            $select = $dao->selectOneRow(Dao::read(__DIR__.'/../sqls/refresh_tokens/select/validRefreshToken.sql'),[
                $refreshToken
            ]);

            if(!$select['result'])
            {
                return ['result' => false];
            }

            $userAuthId = $select['data']->user_auth_id;
            $accessToken = Util::createToken('access');
            $insert = $dao->insert(Dao::read(__DIR__.'/../sqls/access_tokens/insert/issueAccessToken.sql'),[
                $accessToken,
                $userAuthId,
                Config::ACCESS_TOKEN_EXPIRES
            ]);
            if(!$insert['result']) throw new \PDOException('access_tokensインサート失敗');

            $select = $dao->selectAnyRows(
                Dao::read(__DIR__.'/../sqls/users/select/getUserAuthority.sql'),
                [$accessToken]
            );
            return [
                'result' => true,
                'scope' => array_map(
                    fn($x) => $x->authority_code
                    ,$select['data']
                ), 
                'accessToken' => [
                    'token' => $accessToken,
                    'expires' => Config::ACCESS_TOKEN_EXPIRES
                ],
                'realm' => [
                    [
                        'domain' => '192.168.56.119',
                        'path' => '/sso-auth'
                    ],
                    [
                        'domain' => '192.168.56.119',
                        'path' => '/sso-portal'
                    ],
                    [
                        'domain' => '192.168.56.119',
                        'path' => '/sso-proxy'
                    ]
                ]
            ];
        }
        catch(\PDOException $e)
        {
            
        }
        return array();
    }
}
?>
