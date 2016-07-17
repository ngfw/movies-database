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
 * App
 * @package ngfw
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class App {

    /**
     * debug()
     * Catches var_dump and prints output with nice layout
     * @access public
     * @param float|array|string|object|null|bool $var
     */
    public static function debug($var) {
        list($debugfile) = debug_backtrace();
        echo '<pre style="background:#999; padding:5px; color:#FFF">Debugging file:' . $debugfile['file'] . ' at line: ' . $debugfile['line'];
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace('/=>(\s+)/', ' &rarr; ', $output);
        $output = preg_replace('/ => NULL/', ' &rarr; <b style="color: #000"><strong>NULL</strong></b>', $output);
        $output = preg_replace('/}\n(\s+)\[/', "}\n\n" . '$1[', $output);
        $output = preg_replace('/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>($1)</span> <b style='color: brown'>$2</b>", $output);
        $output = preg_replace('/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>(array)</span> <b style='color: brown'>[]</b>", $output);
        $output = preg_replace('/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>(string)</span> <b style='color: brown'>'$2'</b>", $output);
        //$str = preg_replace('/\[\"(.+)\"\] => /', "<span style='color: #888'>['$1']</span> &rarr; ", $str);
        $output = preg_replace('/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>(object)</span> <b style='color: #0C9136'>$1[$3]</b> {", $output);
        $output = str_replace("bool(false)", "<span style='color:#888'>(boolean) </span><span style='color: red'>false</span>", $output);
        $output = str_replace("bool(true)", "<span style='color:#888'>(boolean) </span><span style='color: green'>true</span>", $output);
        echo '<pre style="background: #fff; color: #000; border: 1px solid #000; padding: 8px; margin: 10px">' . $output . '</pre>';
        echo '</pre>';
    }

    /**
     * isAjax()
     * determines if request type is ajax
     * @access public
     * @return boolean
     */
    public static function isAjax() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'):
            return true;
        else:
            return false;
        endif;
    }

}

