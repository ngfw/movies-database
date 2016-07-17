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
 * Database
 * @package ngfw
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class Database extends \PDO {
    /**
     * Default CHARSET
     */

    const CHARSET = 'UTF8';

    /**
     * $options
     * Database Parameters
     * @access private
     * @var array
     */
    private $options;

    /**
     * __construct()
     * sets Opetions and Connections to Database
     * @access public
     * @param type $options
     */
    public function __construct($options) {
        $this->options = $options;
        $this->connect($this->options);
    }

    /**
     * connect()
     * Connects to database
     * @access private 
     * @param array $options
     */
    private function connect($options) {
        $dsn = $this->createdsn($options);
        $attrs = !isset($options['charset']) ? array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::CHARSET) : array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $options['charset']);
        try {
            parent::__construct($dsn, $options['username'], $options['password'], $attrs);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /**
     * createdsn
     * Creates Data Source Name
     * @param array $options
     * @access private
     * @return string
     */
    private function createdsn($options) {
        return $options['dbtype'] . ':host=' . $options['host'] . ';dbname=' . $options['dbname'];
    }

    /**
     * fetchAll()
     * Fetches database and returns result as array
     * @param string $sql
     * @access public
     * @return array|boolean
     */
    public function fetchAll($sql) {
        try {
            $pdostmt = $this->prepare($sql);
            if ($pdostmt->execute() !== false):
                if (preg_match("/^(" . implode("|", array("SELECT", "DESCRIBE", "PRAGMA", "SHOW")) . ") /i", $sql)):
                    return $pdostmt->fetchAll(\PDO::FETCH_ASSOC);
                endif;
            endif;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * fetchRow()
     * Retuns single row from database and return result as array
     * @param string $sql
     * @access public
     * @return array|boolean
     */
    public function fetchRow($sql) {
        try {
            $pdostmt = $this->prepare($sql);
            if ($pdostmt->execute() !== false):
                if (preg_match("/^(" . implode("|", array("SELECT", "DESCRIBE", "PRAGMA", "SHOW")) . ") /i", $sql)):
                    return $pdostmt->fetch(\PDO::FETCH_ASSOC);
                endif;
            endif;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * run()
     * Executes Query 
     * @param string $sql
     * @access public
     * @return array|int|boolean
     */
    public function query($sql) {
        try {
            $pdostmt = $this->prepare($sql);
            if ($pdostmt->execute() !== false):
                if (preg_match("/^(" . implode("|", array("SELECT", "DESCRIBE", "PRAGMA", "SHOW", "DESCRIBE")) . ") /i", $sql)):
                    return $pdostmt->fetchAll(\PDO::FETCH_ASSOC);
                elseif (preg_match("/^(" . implode("|", array("DELETE", "INSERT", "UPDATE")) . ") /i", $sql)):
                    return $pdostmt->rowCount();
                endif;
            endif;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function escape($value, $parameter_type = \PDO::PARAM_STR) {
        return $this->quote($value, $parameter_type);
    }

    public function quote($value, $parameter_type = \PDO::PARAM_STR) {
        if (is_null($value)) {
            return "NULL";
        }
        return substr(parent::quote($value, $parameter_type), 1, -1);
    }

    public function lastInsertId($name = null) {
        return parent::lastInsertId($name);
    }

    /**
     * ping()
     * Pings Database
     * @access public
     * @return boolean
     */
    public function ping() {
        try {
            $this->query('SELECT 1');
        } catch (PDOException $e) {
            $this->connect($this->options);
        }
        return true;
    }

}

