<?php

if ( !function_exists('time_taken') )
{
    function time_taken()
    {
        $now = microtime(true);

        $time = round(($now - ENGINE_START)*1000000)/1000000;

        if ( $time <= 3600 ) {

            $out = $time . ' seconds';

        } else {

            $out = explode('.', round(($time / 60 / 60), 2));

            $hour = (float)($out[0]);
            $min = isset($out[1]) ? (float) $out[1] : 0;
            $min = $min % 60;

            return "{$hour} hour(s) {$min} min(s)";
        }

        return round($out, 4);
    }
}

