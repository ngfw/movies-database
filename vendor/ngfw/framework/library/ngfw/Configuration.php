<?php

/**
 * ngfw
 * ---
 * Copyright (c) 2014, Nick Gejadze
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace ngfw;

/**
 * Configuration
 * @package ngfw
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class Configuration {

    /**
     * loadConfigFile()
     * Opens *.ini file and parses into array
     * @see convertINIToArray()
     * @param string $filename
     * @return array
     * @throws \ngfw\Exception
     */
    public static function loadConfigFile($filename) {
        if (!isset($filename) or !is_string($filename)):
            throw new \ngfw\Exception("Filename is Required For Configuration");
        endif;
        $ini = parse_ini_file($filename);
        $ini_array = self::convertINIToArray($ini);
        return $ini_array;
    }

    /**
     * convertINIToArray()
     * translates parsed array into multidimensional array
     * e.g.:
     * array {
     * ["routes.post.route"] => "post/:id/:title",
     * ["routes.post.defaults.controller"] => "Index",
     * ["routes.post.defaults.action"] => "Index"
     * }
     * is translated to
     * *array {
     *  ["routes"] => array {
     *    ["post"] => array {
     *      ["route"] => "post/:id/:title"
     *      ["defaults"] => array{
     *        ["controller"] => "Index"
     *        ["action"] => "Index"
     * }}}}
     * @access private
     * @param array $ini_arr
     * @return array
     */
    private static function convertINIToArray($ini_arr) {
        $ini = array();
        foreach ($ini_arr as $key => $value) {
            $p = &$ini;
            foreach (explode('.', $key) as $k):
                $p = &$p[$k];
            endforeach;
            $p = $value;
        }
        unset($p);
        return $ini;
    }

}
