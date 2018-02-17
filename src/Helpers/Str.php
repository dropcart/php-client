<?php
/**
 * =========================================================
 *                        DROPCART
 *                      ------------
 * This file is part of the source code of Dropcart and is
 * subject to the terms and conditions defined in the license
 * file include in this package.
 *
 * CONFIDENTIAL:
 * All information contained herein is, and remains the property
 * of Dropcart and its suppliers, if any.  The intellectual and
 * technical concepts contained herein are proprietary to Dropcart
 * and its suppliers and may be covered by Dutch and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Dropcart.
 *
 * =========================================================
 *
 * File: Str.php
 * Date: 16-01-18 10:55
 * Copyright: © [2016 - 2018] Dropcart - All rights reserved.
 * Version: v3.0.0
 *
 * =========================================================
 */


namespace Dropcart\PhpClient\Helpers;


class Str {

    /**
     * Split a string by custom seperator
     * @param $string
     * @param $seperator
     *
     * @return string
     */
    public static function toSeperatorCase($string, $seperator)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode($seperator, $ret);
    }

    /**
     * Converts a string to snake_case
     * @param $string
     *
     * @return string
     */
    public static function toSnakeCase($string)
    {
        return static::toSeperatorCase($string, '_');
    }

    /**
     * Converts a string to kebab-case
     * @param $string
     *
     * @return string
     */
    public static function toKebabCase($string)
    {
        return static::toSeperatorCase($string, '-');
    }

    /**
     * Make a string CamelCase.
     *
     * @param      $string
     * @param bool $ucfirst Does the first letter needs to be uppercase. Default true.
     *
     * @return string
     */
    public static function toCamelCase($string, $ucfirst = true)
    {
        $string = ucwords($string, " \t\r\n\f\v_");
        if(!$ucfirst)
            $string = lcfirst($string);

        return str_replace([" ","\t","\r","\n","\f","\v","_"], '', $string);
    }
}