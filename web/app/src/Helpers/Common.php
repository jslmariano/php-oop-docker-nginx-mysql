<?php

namespace App\Josel\Helpers;

/**
 * This class describes a common.
 */
class Common
{
    /**
     * Convert camelcase to snake case
     *
     * @param      string  $input  The input
     *
     * @return     string  Converted case
     */
    public static function convertToSnakeCase($input)
    {
        $matches = array();
        preg_match_all(
            '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!',
            $input,
            $matches
        );
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * Convert array keys to snake case
     *
     * @param      array  $arrays  The arrays
     */
    public static function convertKeysToSnakeCase($arrays)
    {
        $new = array();
        foreach ($arrays as $key => $value) {
            $new[self::convertToSnakeCase($key)] = $value;
        }
        return $new;
    }
}
