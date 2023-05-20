<?php

namespace Backend\Foundation\Bases;

/**
* HttpMethods Enum
*
* Httpメソッド列挙型
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
enum HttpMethods:string implements DumpInterface
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';

    /**
    * ダンプ
    * @access public
    * @return string
    */
    public function dump(): string
    {
        return match($this) {
            self::GET => 'HTTPメソッド '.self::GET->value,
            self::POST => 'HTTPメソッド '.self::POST->value,
            self::PUT => 'HTTPメソッド '.self::PUT->value,
            self::DELETE => 'HTTPメソッド '.self::DELETE->value,
        };
    }

    /**
    * オールメソッド取得
    * @access public
    * @static
    * @return array
    */
    public static function all():array
    {
        return array(
            self::GET,
            self::POST,
            self::PUT,
            self::DELETE,
        );
    }
}
