<?php

namespace Backend\Foundation\Bases;

/**
* エンドポイント
* 
* エンドポイントのHttpメソッドとリソースパスの構造体
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
class Endpoint implements DefineInterface,DumpInterface
{
    /**
    * Httpメソッド変数
    * @access private
    * @var HttpMethods
    * @see HttpMethods
    */
    private HttpMethods $httpMethod;

    /**
    * リソースパス変数
    * @access private
    * @var Path
    * @see Path
    */
    private Path $resourcePath;

    public function getHttpMethod():HttpMethods{return $this->httpMethod;}
    public function getResourcePath():Path{return $this->resourcePath;}

    /**
    * コンストラクタ
    * @access private
    * @param HttpMethods
    * @param Path
    * @return void
    */
    private function __construct(HttpMethods $httpMethod,Path $resourcePath)
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $this->httpMethod = $httpMethod;
        $this->resourcePath = $resourcePath;
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
    }

    /**
    * RequestとEndpointマッチ
    * 
    * RequestパスとEndpoint::$resourcePathがマッチしているかの比較
    * @access public
    * @param Request
    * @param Path
    * @return bool
    */
    public function requestMatch(Path $uriPrefix):bool
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $request = Request::get();
        if($this->httpMethod->value !== $request->getRequestMethod())
        {
            Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
            return false;
        }
        
        if(! $this->requestUriMatch($request,$uriPrefix))
        {
            Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
            return false;
        }
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return true;
    }

    /**
    * RequestのURIパスとEndpointのリソースパス比較
    * 
    * パスパラメータも考慮して、URIが同一なものかを比較する
    * @access public
    * @param Request
    * @param Path
    * @return bool
    */
    private function requestUriMatch(Request $request,Path $uriPrefix):bool
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        Logger::debug($this->resourcePath->toString()." について検証".__FILE__.'('.__LINE__.')');

        $requestUriArray = Path::def(strtok($request->getRequestUri(),'?'))->pathDisassembly();
        $this->resourcePath->joinForward($uriPrefix);
        $defineUriArray = $this->resourcePath->pathDisassembly();

        // スラッシュ区切りで配列にしたときに、配列の個数が同じかどうかを判定
        if(!$this->containAnyPath($defineUriArray) && count($defineUriArray) !== count($requestUriArray))
        {
            Logger::debug($this->resourcePath->toString()." 配列の個数が同じではない（ワイルドカードは入っていない）".__FILE__.'('.__LINE__.')');
            Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
            return false;
        }

        // 各部位が同じか判定していく
        $pathParameter = [];
        for($i = 1;$i < count($requestUriArray);$i++)
        {
            // ワイルドカード的な記述があった場合、以降パスを判定せずに一致してると判断
            if($this->isAnyPath($defineUriArray[$i]))
            {
                Logger::debug($this->resourcePath->toString()." ワイルドカードを発見".__FILE__.'('.__LINE__.')');
                Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
                return true;
            }

            // パスパラメータ部分は一致してなくてもオッケー
            assert($this->isPathParameter('{key}'));
            if($this->isPathParameter($defineUriArray[$i]))
            {
                assert($this->extractParameterKey('{key}') === 'key');
                $key = $this->extractParameterKey($defineUriArray[$i]);
                if(array_key_exists($key,$pathParameter)) return false;
                $pathParameter[$key] = $requestUriArray[$i];
                Logger::debug($this->resourcePath->toString()." パスパラメータの為スキップ".__FILE__.'('.__LINE__.')');
                continue;
            }

            // 一致してなければその時点でreturn
            if($requestUriArray[$i] !== $defineUriArray[$i])
            {
                Logger::debug($this->resourcePath->toString()." 分割の一部が不一致 ".__FILE__.'('.__LINE__.')');
                Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
                return false;
            }
        }
        // Request::$pathParameterに配列をセットしておく
        $request->setPathParameter($pathParameter);
        Logger::debug($this->resourcePath->toString()." 全て一致".__FILE__.'('.__LINE__.')');
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return true;
    }

    /**
    * パスパラメータ判定
    * 
    * {xxx}こんな感じなものを発見したらtrue
    * @access public
    * @param string
    * @return bool
    */
    private function isPathParameter(string $word):bool
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        // {}で囲われていたら、パスパラメータ部分と判定
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        $l = Symbol::BACK_SLASH.Symbol::LEFT_BRACE;
        $r = Symbol::BACK_SLASH.Symbol::RIGHT_BRACE;
        $regex = '/^'.$l.'.*'.$r.'$/u';
        return preg_match($regex,$word) === 1;
    }

    private function extractParameterKey(string $word):string
    {
        return rtrim(ltrim($word,Symbol::LEFT_BRACE),Symbol::RIGHT_BRACE);
    }

    private function isAnyPath(string $word):bool
    {
        return $word === Symbol::ASTERISK;
    }

    private function containAnyPath(array $array):bool
    {
        return in_array(Symbol::ASTERISK,$array);
    }


    /**
    * 定義メソッド
    *
    * 簡略化用
    * @access public
    * @param array $args
    * @return DefineInterface
    */
    public static function def(...$args):DefineInterface|array
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        assert(count($args) === 2);
        if(is_array($args[0]) && get_class($args[1]) === Path::class)
        {
            return Endpoint::defEndpointsByAnyHttpMethods($args[0],$args[1]);
        }
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return new Endpoint($args[0],$args[1]);
    }

    /**
    * 定義メソッド（httpMethod拡張）
    *
    * httpMethodsが配列だった場合、複数のEndpointを作成する
    * @access private
    * @param array $httpMethods
    * @param Path $resourcePath
    * @return array
    */    
    private static function defEndpointsByAnyHttpMethods(array $httpMethods, Path $resourcePath):array
    {
        assert(Util::arrayAllTypeCheck($httpMethods,HttpMethods::class));
        $endpoints = array(); 
        foreach($httpMethods as $httpMethod)
        {
            assert(get_class($httpMethod) === HttpMethods::class);
            $endpoints[] = new Endpoint($httpMethod,$resourcePath);
        }
        assert(Util::arrayAllTypeCheck($endpoints,Endpoint::class));
        return $endpoints;
    }

    /**
    * ダンプ
    * @access public
    * @return string
    */
    public function dump():string
    {
        $httpMethodDump = $this->httpMethod->dump();
        $resourcePath = 'リソースパス '.$this->resourcePath->toString();
        return <<<EOL
        $httpMethodDump $resourcePath<br>
EOL;
    }
}