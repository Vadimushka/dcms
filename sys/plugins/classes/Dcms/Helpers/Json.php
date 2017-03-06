<?php
/**
 * Created by PhpStorm.
 * User: pavlovd
 * Date: 19.09.2015
 * Time: 15:22
 */

namespace Dcms\Helpers;


/**
 * Class Json
 * @package Hosting\Helpers
 */
abstract class Json
{
    /**
     * @param string $string
     * @param bool $as_array
     * @return mixed
     * @throws \Exception
     */
    public static function parse($string, $as_array = true)
    {
        $model = @json_decode($string, $as_array);

        $err = json_last_error();
        switch ($err) {
            case JSON_ERROR_DEPTH:
                throw new \Exception('Json parse error: Maximum stack depth exceeded');
            case JSON_ERROR_CTRL_CHAR:
                throw new \Exception('Json parse error: Unexpected control character found');
            case JSON_ERROR_SYNTAX:
                throw new \Exception('Json parse error: Syntax error, malformed JSON');
            case JSON_ERROR_NONE:
                return $model;
        }
        throw new \Exception('Json parse error');
    }

    public static function stringify($model, $format = true)
    {
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if ($format) {
            $flags = $flags | JSON_PRETTY_PRINT;
        }

        return json_encode($model, $flags);
    }
}