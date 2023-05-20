<?php

namespace Backend\Foundation\Bases;

/**
* Pathユーティリティクラス
*
* Pathの操作
* @final
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
final class Path implements DefineInterface
{
    /**
    * パス
    * ファイルパス、URIパスなど色々と使用する
    * @access private
    * @var string
    */
    private string $path;

    /**
    * コンストラクタ
    * @access public
    * @param string
    */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
    * ファイル名取得
    * @access public
    * @return string
    */
    public function getFileName():string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
    * ファイル拡張子取得
    * @access public
    * @param string
    */
    public function getExtension():string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
    * ディレクトリ取得
    * @access public
    * @param string
    */
    public function getParentDirectory():string
    {
        return dirname($this->path);
    }

    /**
    * ファイル存在確認
    * @access public
    * @param bool
    */
    public function fileExists():bool
    {
        return file_exists($this->path);
    }

    /**
    * パス分解
    * @access public
    * @return array
    */
    public function pathDisassembly():array
    {
        return explode(Symbol::SLASH,$this->path);
    }

    /**
    * パス連結(前方)
    * @access public
    * @param Path|string
    * @return void
    */
    public function joinForward(Path|string $joinPath):void
    {
        if(gettype($joinPath) ===  'object' && get_class($joinPath) === Path::class)
        {
            $pathStr = $joinPath->toString();
        }
        else
        {
            $pathStr = $joinPath;
        }
        $this->path = $pathStr.$this->path;
    }

    /**
    * パス連結(後方)
    * @access public
    * @param Path|string
    * @return void
    */
    public function joinBackward(Path|string $joinPath):void
    {
        if(gettype($joinPath) ===  'object' && get_class($joinPath) === Path::class)
        {
            $pathStr = $joinPath->toString();
        }
        else
        {
            $pathStr = $joinPath;
        }
        $this->path = $this->path.$pathStr;
    }

    /**
    * 出力
    * @access public
    * @param string
    */
    public function toString():string
    {
        return $this->path;
    }
    
    /**
    * 簡易インスタンス取得メソッド
    * @access public
    * @param string
    * @return DefineInterface
    */
    public static function def(...$args):DefineInterface
    {
        assert(count($args) === 1);
        return new Path($args[0]);
    }
}