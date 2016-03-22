<?php namespace Engine;

/**
 * Class Dir
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    18/03/16 04:07
 *
 * @package Engine
 */
class Dir
{
    public static function make($path)
    {
        if ( ! is_dir($path) ) {
            exec("mkdir -p ".escapeshellarg($path));
        }
    }
}
