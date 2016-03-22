<?php namespace Engine;

/**
 * Class NormalizeContent
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    18/03/16 14:19
 *
 * @package Engine
 */
class NormalizeContent
{
    /**
     * Normalize content
     *
     * @param string $content The content to be normalized
     * @return string
     */
    public static function cleanAuthority($content)
    {
        if ( empty($content) ) return $content;

        $normalize_text = array(
            //'#http://www.adrianorosa.com#i' => '', // make links absolutes
            //'#http://adrianorosa.com#i' => '', // make links absolutes
        );

        $content = preg_replace(array_keys($normalize_text), array_values($normalize_text), $content);

        return $content;
    }
}
