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
 * View
 * @package ngfw
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class View {

    /**
     * $controller
     * Holds Conroller name
     * @access protected
     * @var string
     */
    protected $controller;

    /**
     * $action
     * Holds Action name
     * @access protected
     * @var string
     */
    protected $action;

    /**
     * $layout
     * @access protected
     * @var boolean
     */
    protected $layout = true;

    /**
     * $render
     * @access protected
     * @var boolean
     */
    protected $render = true;

    /**
     * $layoutFile
     * @access protected
     * @var string
     */
    protected $layoutFile = 'Layout';

    /**
     * __construct()
     * Sets controller and action object
     * @param string $controller
     * @param string $action
     * @access public
     */
    public function __construct($controller, $action) {
        $this->controller = $controller;
        $this->action = strtolower($action);
    }

    /**
     * enableLayout()
     * Sets layout object
     * @access public
     * @param boolean $bool
     */
    public function enableLayout($bool = true) {
        $this->layout = $bool;
    }

    /**
     * enableView()
     * Sets render object
     * @access public
     * @param boolean $bool
     */
    public function enableView($bool = true) {
        $this->render = $bool;
    }

    /**
     * setLayoutFile()
     * sets layout filename object
     * @access public
     * @param string $filename
     */
    public function setLayoutFile($filename) {
        $this->layoutFile = $filename;
    }

    /**
     * set()
     * Set object to be used from view
     * @access public
     * @param string $name
     * @param string $value
     */
    public function set($name, $value) {
        $this->{$name} = $value;
    }

    /**
     * loadLayout()
     * Includes layout file
     * @access public
     * @throws Exception
     */
    public function loadLayout() {
        if ($this->layout):
            try {
                $layoutFile = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "Application" . DIRECTORY_SEPARATOR . 'Layout' . DIRECTORY_SEPARATOR . $this->layoutFile . '.phtml';
                if (!file_exists($layoutFile)):
                    throw new Exception($layoutFile);
                endif;
                include ($layoutFile);
            } catch (\ngfw\Exception $e) {
                if(defined('DEVELOPMENT_ENVIRONMENT')):
                    if (DEVELOPMENT_ENVIRONMENT):
                        exit("Could not find view file: " . $e->getMessage());
                    endif;
                endif;
            }
        else:
            $this->render();
        endif;
    }

    /**
     * render()
     * Check is render is enabled and includes view file
     * @access public
     * @throws Exception
     */
    public function render() {
        if ($this->render):
            try {
                $viewFile = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "Application" . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . $this->controller . DIRECTORY_SEPARATOR . ucfirst(strtolower($this->action)) . '.phtml';
                if (!file_exists($viewFile)):
                    throw new Exception($viewFile);
                endif;
                include ($viewFile);
            } catch (Exception $e) {
                if(defined('DEVELOPMENT_ENVIRONMENT')):
                    if (DEVELOPMENT_ENVIRONMENT):
                        exit("Could not find view file: " . $e->getMessage());
                    endif;
                endif;
            }
        endif;
    }

}
