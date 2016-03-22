<?php namespace Engine;

/**
 * Class Encoding
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    18/03/16 12:38
 *
 * @package Engine
 */
class Encoding
{
    public static function toUTF8($content)
    {
        if ( ! is_string($content) ) {
            var_dump('content is not a string');
            exit(1);
        }

        if (empty($content)) return $content;

        // return mb_convert_encoding($file, 'HTML-ENTITIES', "UTF-8");
        return mb_convert_encoding($content, 'UTF-8',
            mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    }
}
