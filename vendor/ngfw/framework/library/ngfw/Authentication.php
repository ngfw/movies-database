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
 * Authentication
 * @package ngfw
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class Authentication {

    /**
     * $dbAdapter
     * @access protected
     * @var object
     */
    protected $dbAdapter;

    /**
     * @table
     * @access protected
     * @var string
     */
    protected $table;

    /**
     * $identityColumn
     * @access protected
     * @var string
     */
    protected $identityColumn;

    /**
     * $identity
     * @access protected
     * @var string
     */
    protected $identity;

    /**
     * $credentialColumn
     * @access protected
     * @var string
     */
    protected $credentialColumn;

    /**
     * $credential
     * @access protected
     * @var string
     */
    protected $credential;

    /**
     * $sessionName
     * @access protected
     * @var string
     */
    protected $sessionName = "NG_AUTH";

    /**
     * __construct()
     * Sets dbAdapter, Table Identity Column and Credential Column if passed
     * @param object $dbAdapter
     * @param string $table
     * @param string $identityColumn
     * @param string $credentialColumn
     * @see setDBAdapter()
     * @see setDBTable()
     * @see setIdentityColum()
     * @see setCredentialColumn()
     * @access public
     * @return \ngfw\Authentication
     */
    public function __construct($dbAdapter = null, $table = null, $identityColumn = null, $credentialColumn = null) {
        if (isset($dbAdapter)):
            $this->setDBAdapter($dbAdapter);
        endif;
        if (isset($table)):
            $this->setDBTable($table);
        endif;
        if (isset($identityColumn)):
            $this->setIdentityColumn($identityColumn);
        endif;
        if (isset($credentialColumn)):
            $this->setCredentialColumn($credentialColumn);
        endif;
        return $this;
    }

    /**
     * setDBAdapter()
     * if isset $dbAdapter sets dbAdapter object otherwise returns false
     * @access public
     * @param object $dbAdapter
     * @return boolean|\ngfw\Authentication
     */
    public function setDBAdapter($dbAdapter) {
        if (!isset($dbAdapter)):
            return false;
        endif;
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    /**
     * setDBTable()
     * if isset $table sets Table object otherwise returns false
     * @access public
     * @param string $table
     * @return boolean|\ngfw\Authentication
     */
    public function setDBTable($table) {
        if (!isset($table)):
            return false;
        endif;
        $this->table = $table;
        $this->sessionName = $this->sessionName.$this->table;
        return $this;
    }

    /**
     * setIdentityColumn()
     * if isset $identityColumn sets identityColumn object otherwise returns false
     * @access public
     * @param string $identityColumn
     * @return boolean|\ngfw\Authentication
     */
    public function setIdentityColumn($identityColumn) {
        if (!isset($identityColumn)):
            return false;
        endif;
        $this->identityColumn = $identityColumn;
        return $this;
    }

    /**
     * setIdentity()
     * if isset $identity sets identity object otherwise returns false
     * @access public
     * @param string $identity
     * @return boolean|\ngfw\Authentication
     */
    public function setIdentity($identity) {
        if (!isset($identity)):
            return false;
        endif;
        $this->identity = $identity;
        return $this;
    }

    /**
     * setCredentialColumn()
     * sets credential column object
     * @access public
     * @param string $credentialColumn
     * @return boolean|\ngfw\Authentication
     */
    public function setCredentialColumn($credentialColumn) {
        if (!isset($credentialColumn)):
            return false;
        endif;
        $this->credentialColumn = $credentialColumn;
        return $this;
    }

    /**
     * setCredential
     * sets credential object
     * @access public
     * @param string $credential
     * @return boolean|\ngfw\Authentication
     */
    public function setCredential($credential) {
        if (!isset($credential)):
            return false;
        endif;
        $this->credential = $credential;
        return $this;
    }

    /**
     * isValid()
     * Checks if user is authenticated
     * @access public
     * @return boolean
     */
    public function isValid() {
        $auth = \ngfw\Session::get($this->sessionName);
        if ($auth):
            return true;
        endif;
        if (isset($this->dbAdapter)
                AND isset($this->table)
                AND isset($this->identityColumn)
                AND isset($this->identity)
                AND isset($this->credentialColumn)
                AND isset($this->credential)):
            $user = $this->checkUserInDB();        
            if (isset($user) and is_array($user)):
                $this->setSessionIdentity($user);
                return true;
            endif;            
        endif;               
        return false;
    }

    /**
     * checkUserInDB()
     * Builds select query to check user in DB and returns result as an array
     * @access private
     * @return array|false
     */
    private function checkUserInDB() {
        return $this->dbAdapter->fetchRow("SELECT * FROM `" . $this->table . "`
                    WHERE `" . $this->identityColumn . "` = '" . $this->identity . "'
                    AND `" . $this->credentialColumn . "` = '" . $this->credential . "'
                    LIMIT 1");
    }

    /**
     * setSessionIdentity
     * sets identity in the session
     * @access private
     * @param array $identity
     */
    private function setSessionIdentity($identity) {
        \ngfw\Session::set($this->sessionName, serialize($identity));
    }

    /**
     * getIdentity()
     * checks if user is authenticated and return user data from session
     * @see isValid()
     * @access public
     * @return array|boolean
     */
    public function getIdentity() {
        if ($this->isValid()):
            return unserialize(\ngfw\Session::get($this->sessionName));
        endif;
        return false;
    }

    /**
     * clearIdentity()
     * sets auth session to null
     * @access public
     * @return void
     */
    public function clearIdentity() {
        \ngfw\Session::set($this->sessionName, NULL);
    }

}
