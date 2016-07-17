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
 * Session
 * @package ngfw
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class Session {

    /**
     * $instance
     * Holds Class Instance
     * @access protected 
     * @var object
     */
    protected static $instance = null;

    /**
     * init()
     * if $instance is not set and headers_sent() == false, starts new session and starts new \ngfw\Session and return instance
     * @access public
     * @return object
     */
    public static function init() {
        if (self::$instance === null):
            if (!headers_sent() and !isset($_SESSION)):
                session_start();
            endif;
            self::$instance == new Session();
        endif;
        return self::$instance;
    }

    /**
     * set()
     * sets PHP session
     * @access public
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function set($key, $value) {
        self::init();
        $_SESSION[$key] = $value;
    }

    /**
     * get()
     * Gets PHP Session, Returns false if Session not set
     * @access public
	 * @param string $key
     * @return string|boolean
     */
    public static function get($key) {
        self::init();
        if (isset($_SESSION[$key])):
            return $_SESSION[$key];
        endif;
        return false;
    }

}