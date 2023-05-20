<?php

namespace Backend\Foundation\Bases;

/**
* Message Class
*
* メッセージ出力管理のユーティリティ
* @final
* @access public
* @author keisuke.ueda <ukei2021@gmail.com>
* @version 1.0
* @package Backend\Foundation\Bases
*/
final class Message
{
    private string $messagePath;

    private static Message $message;

    public static function create(string $messagePath):void
    {
        if(!isset(self::$message))
        {
            self::$message = new Message($messagePath);
        }
    }

    private function __construct(string $messagePath)
    {
        $this->messagePath = $messagePath;
    }

    /**
    * メッセージ読み込み
    *
    * @access public
    * @static
    * @param string $id
    * @param array $items
    * @return string
    */
    public static function get(string $id, ...$items)
    {
        assert(isset(self::$message));
        $message = '';
        // messageIdがnullの場合終了
        if (is_null($id) || $id === '') {
            return $message;
        }
        $messageIni = parse_ini_file(self::$message->messagePath, false);

        // メッセージID(キー)存在チェック
        if (array_key_exists($id, $messageIni)) {
            $message = $messageIni[$id];
            if(! empty($items))
            {
                $targets = array_map(function($key){
                    $num = (int)$key + 1;
                    return "[item{$num}]";
                },array_keys($items));
                $message = str_replace($targets, $items, $message);
            }
        }
        // 改行コードを変換
        $message = str_replace("\\n", "\n", $message);
        return $message;
    }

}
