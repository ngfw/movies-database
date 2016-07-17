<?php

/**
 * ngfw
 * Version 0.1 
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
 * Registry
 * @package ngfw
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class Registry extends \ArrayObject {

    /**
     * Holds Class Instance
     * @access protected 
     * @var object
     */
    private static $instance = null;

    /**
     * if $instance is not set starts new \ngfw\Registry and return instance
     * @access public 
     * @return object Class instance
     */
    public static function init() {
        if (self::$instance === null):
            self::$instance = new Registry;
        endif;
        return self::$instance;
    }

    /**
     * Sets key and value in registry
     * @param mixed $index Unique key identifier
     * @param mixed $value Value for the specified index
     * @access public
     * @return void No value is returned.
     */
    public static function set($index, $value) {
        self::init()->offsetSet($index, $value);
    }

    /**
     * Gets value for key from Registry
     * @param mixed $index Unique key identifier
     * @access public
     * @return mixed The value at the specified index or FALSE.
     */
    public static function get($index) {
        if (self::init()->checkIndex($index)):
            return self::init()->offsetGet($index);
        endif;
        return false;
    }

    /**
     * Checks if key is registered in the Registry
     * @param mixed $index  Unique key identifier
     * @return boolean TRUE is returned if key is found, otherwise false
     */
    public static function checkIndex($index) {
        if (self::init()->offsetExists($index)):
            return true;
        endif;
        return false;
    }

    /**
     * Returns instance object
     * @access public
     * @return object returns singelton registry object
     */
    public static function getInstance() {
        return self::init();
    }

}
