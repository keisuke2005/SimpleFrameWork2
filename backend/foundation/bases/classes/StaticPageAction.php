<?php

namespace Backend\Foundation\Bases;

/**
* 静的ページアクション
* 
* 対象は.html/.css/.js
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
class StaticPageAction extends AbstractAction 
{
    const JS_EXT = ['js'];
    const CSS_EXT = ['css'];
    const IMAGE_EXT = ['png','jpg','jpeg','gif'];

    /**
    * Pathオブジェクト
    * @access protected
    * @var Path
    * @see Path
    */
    private Path $filePath;

    /**
    * コンストラクタ
    * @access private
    * @param Path
    * @return void
    */
    private function __construct(Path $filePath)
    {
        $this->filePath = $filePath;
    }

    public function addPrefix(string $prefix):void
    {
        $this->filePath->joinForward($prefix);
    }

    /**
    * Action実行メソッド
    *
    * ファイルをreadfileする
    * @access public
    * @return Response
    */
    public function operation():Response
    {
        Logger::info("Start Function"." ".__FILE__.'('.__LINE__.')');
        $response = Response::get();
        $filePath = $this->filePath;
        $response->setResponseClosure(function($r) use ($filePath)
        {
            assert(file_exists($filePath->toString()));
            if(in_array($filePath->getExtension(),self::JS_EXT))
            {
                header('Content-Type: application/javascript');
            }
            if(in_array($filePath->getExtension(),self::CSS_EXT))
            {
                header('Content-Type: text/css');
            }
            if(in_array($filePath->getExtension(),self::IMAGE_EXT))
            {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($filePath->toString());
                header('Content-Type: '.$mimeType);
            }

            $content = file_get_contents($filePath->toString());
            preg_match_all('|<page-def>(.*)<\/page-def>|', $content, $matches);
            for($i = 0;$i < count($matches[0]);$i++)
            {
                $f = $filePath->getParentDirectory().'/'.$matches[1][$i];
                if(!file_exists($f))
                {
                    continue;
                }
                $content = str_replace($matches[0][$i],file_get_contents($f),$content);
            }
        
            print($content);

            //readfile($filePath->toString());
        });
        Logger::info("End Function"." ".__FILE__.'('.__LINE__.')');
        return $response;
    }

    /**
    * 定義メソッド
    *
    * 簡略化用
    * @access public
    * @param array $args
    * @return DefineInterface
    */
    public static function def(...$args):DefineInterface
    {
        assert(count($args) === 1);
        return new StaticPageAction($args[0]);
    }

	/**
    * ダンプ
    * @access public
    * @return string
    */
    public function dump():string
    {
        $f = $this->filePath->toString();
        return <<<EOL
        アクション名 StaticPageAction 実行ファイルパス $f<br>
EOL;
    }
}