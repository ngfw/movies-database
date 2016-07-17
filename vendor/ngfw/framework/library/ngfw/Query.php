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
 * Query
 * @package ngfw
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class Query {

    /**
     * $query
     * Holds full query string
     * @access protected
     * @var string
     */
    protected $query = '';

    /**
     * $select
     * Holds select variables, can be string or array
     * @access private
     * @var array|string
     */
    private $select;

    /**
     * $table
     * Holds table name
     * @access private
     * @var string
     */
    private $table;

    /**
     * $insertData
     * Holds inset data
     * @access private
     * @var array
     */
    private $insertData;

    /**
     * $updateData
     * Holds update data
     * @access private
     * @var array
     */
    private $updateData;

    /**
     * $deleteTable
     * Holds boolean value if delete query is requested or not
     * @access private
     * @var boolean
     */
    private $deleteTable;

    /**
     * $from
     * Holds from string or array
     * @access private 
     * @var string|array
     */
    private $from;

    /**
     * $join
     * Holds join data as an array
     * @access private
     * @var array
     */
    private $join;

    /**
     * $innerJoin
     * Holds innerJoin data as an array
     * @access private
     * @var array
     */
    private $innerJoin;

    /**
     * $leftJoin
     * Holds leftJoin data as an array
     * @access private
     * @var array
     */
    private $leftJoin;

    /**
     * $rightJoin
     * Holds rightJoin data as an array
     * @access private
     * @var array
     */
    private $rightJoin;

    /**
     * $where
     * Holds where clause
     * @access private
     * @var string
     */
    private $where;

    /**
     * $andWhere
     * Holds where clause
     * @access private
     * @var array
     */
    private $andWhere;

    /**
     * $orWhere
     * Holds where clause
     * @access private
     * @var array
     */
    private $orWhere;

    /**
     * $groupBy
     * Holds groupdBy clause
     * @access private
     * @var string
     */
    private $groupBy;

    /**
     * $having
     * Holds having clause
     * @access private
     * @var string
     */
    private $having;

    /**
     * select()
     * Starts select statement
     * @access public
     * @param string $select Default '*' , The array of strings to select from database
     * @return object \ngfw\Query()
     */
    public function select($select = "*") {
        $this->select = $select;
        if (isset($this->select)):
            if (is_array($this->select)):
                foreach ($this->select as $key => $value):
                    $this->select[$key] = $this->escapeField($value);
                endforeach;
                $this->select = implode(", ", $this->select);
            endif;
            $this->query = "SELECT " . $this->select . " ";
        endif;
        return $this;
    }

    /**
     * insert()
     * Builds insert statement 
     * @access public     
     * @param string $table Table name you want to insert into
     * @param array $data Array of strings, example array("fieldname" => "value")
     * @return object \ngfw\Query()
     * @throws \ngfw\Exception
     */
    public function insert($table, $data) {
        $this->table = $table;
        if (!isset($data) or !is_array($data)):
            throw new \ngfw\Exception("Insert Values are required to build query");
        endif;
        $this->insertData = $data;
        if (isset($this->table) and isset($this->insertData)):
            $this->query = "INSERT INTO `" . $this->table . "` ";
            $fields = implode("`, `", array_keys($this->insertData));
            $this->query.= "(`" . $fields . "`)";
            $values = implode("', '", $this->escapeValue($this->insertData));
            $this->query.= " VALUES ('" . $values . "')";
        endif;
        return $this;
    }

    /**
     * update()
     * Builds update statement
     * @access public     
     * @param string $table Table name you want to update
     * @param type $data Array of strings that needs to be updated, example array("fieldname" => "value");
     * @return object \ngfw\Query()
     * @throws \ngfw\Exception
     */
    public function update($table, $data) {
        $this->table = $table;
        if (!isset($data) or !is_array($data)):
            throw new \ngfw\Exception("Update Values are required to build query");
        endif;
        $this->updateData = $data;
        if (isset($this->table) and isset($this->updateData)):
            $this->query = "UPDATE `" . $this->table . "` SET ";
            if (is_array($this->updateData)):
                foreach ($this->updateData as $field => $value):
                    $this->query .= "`" . $field . "` = '" . $this->escapeValue($value) . "', ";
                endforeach;
                $this->query = substr($this->query, 0, -2) . " ";
            endif;
        endif;
        return $this;
    }

    /**
     * delete()
     * Starts delete statement 
     * @access public     
     * @return object \ngfw\Query()
     */
    public function delete() {
        $this->deleteTable = true;
        if (isset($this->deleteTable)):
            $this->query = "DELETE ";
        endif;
        return $this;
    }

    /**
     * from()
     * Sets from object
     * @access public     
     * @param string|array $from Table name as a string or Array of strings, example: array("table1 a", "table2 b", "table3 c")
     * @return object \ngfw\Query()
     * @throws \ngfw\Exception
     */
    public function from($from) {
        if (!isset($from)):
            throw new \ngfw\Exception("FROM is Required to build query");
        endif;
        $this->from = $from;
        if (isset($this->from)):
            if (is_array($this->from)):
                foreach ($this->from as $key => $value):
                    $this->from[$key] = $this->escapeField($value);
                endforeach;
                $this->from = implode(", ", $this->from);
            else:
                $this->from = $this->escapeField($this->from);
            endif;
            $this->query.= "FROM " . $this->from . " ";
        endif;
        return $this;
    }

    /**
     * join()
     * Sets join object
     * @access public     
     * @param string $table Table name as a string
     * @param string $clause Clause as a string, example "a.fieldname = b.fieldname"
     * @return object \ngfw\Query()
     */
    public function join($table, $clause) {
        $k = (count($this->join) > 0) ? count($this->join) + 1 : 0;
        $this->join[$k]['table'] = $this->escapeField($table);
        $this->join[$k]['clause'] = $clause;
        $this->query.="JOIN " . $this->join[$k]['table'] . " ON " . $this->join[$k]['clause'] . " ";
        return $this;
    }

    /**
     * innerJoin()
     * Sets inner join object 
     * @access public     
     * @param string $table Table name as a string
     * @param string $clause Clause as a string, example "a.fieldname = b.fieldname"
     * @return object \ngfw\Query()
     */
    public function innerJoin($table, $clause) {
        $k = (count($this->innerJoin) > 0) ? count($this->innerJoin) + 1 : 0;
        $this->innerJoin[$k]['table'] = $this->escapeField($table);
        $this->innerJoin[$k]['clause'] = $clause;
        $this->query.="INNER JOIN " . $this->innerJoin[$k]['table'] . " ON " . $this->innerJoin[$k]['clause'] . " ";
        return $this;
    }

    /**
     * leftKoin()
     * Sets left join object 
     * @access public     
     * @param string $table Table name as a string
     * @param string $clause Clause as a string, example "a.fieldname = b.fieldname"
     * @return object \ngfw\Query()
     */
    public function leftJoin($table, $clause) {
        $k = (count($this->leftJoin) > 0) ? count($this->leftJoin) + 1 : 0;
        $this->leftJoin[$k]['table'] = $this->escapeField($table);
        $this->leftJoin[$k]['clause'] = $clause;
        $this->query.="LEFT JOIN " . $this->leftJoin[$k]['table'] . " ON " . $this->leftJoin[$k]['clause'] . " ";
        return $this;
    }

    /**
     * rightJoin()
     * Sets right join object
     * @access public     
     * @param string $table Table name as a string
     * @param string $clause Clause as a string, example "a.fieldname = b.fieldname"
     * @return object \ngfw\Query()
     */
    public function rightJoin($table, $clause) {
        $k = (count($this->leftJoin) > 0) ? count($this->leftJoin) + 1 : 0;
        $this->rightJoin[$k]['table'] = $this->escapeField($table);
        $this->rightJoin[$k]['clause'] = $clause;
        $this->query.="RIGHT JOIN " . $this->rightJoin[$k]['table'] . " ON " . $this->rightJoin[$k]['clause'] . " ";
        return $this;
    }

    /**
     * where()
     * Sets where object         
     * @param string $where where statement, example: ("fieldname = ?")
     * @param string $value string value to be replaced in where statement
     * @return object \ngfw\Query()
     */
    public function where($where, $value = null) {
        $where = $this->escapeField($where);
        $this->where = str_replace("?", "'" . addslashes($value) . "'", $where);
        $this->query.="WHERE " . $this->where . " ";
        return $this;
    }

    /**
     * andWhere()
     * Sets and where object     
     * @param string $where where statement, example: ("fieldname = ?")
     * @param string $value string value to be replaced in where statement
     * @return object \ngfw\Query()
     */
    public function andWhere($where, $value = null) {
        $where = $this->escapeField($where);
        $this->andWhere[] = str_replace("?", "'" . addslashes($value) . "'", $where);
        $this->query.="AND " . end($this->andWhere) . " ";
        return $this;
    }

    /**
     * orWhere()
     * Sets or where object
     * @param string $where where statement, example: ("fieldname = ?")
     * @param string $value string value to be replaced in where statement
     * @return object \ngfw\Query()
     */
    public function orWhere($where, $value = null) {
        $where = $this->escapeField($where);
        $this->orWhere[] = str_replace("?", "'" . addslashes($value) . "'", $where);
        $this->query.="OR " . end($this->orWhere) . " ";
        return $this;
    }

    /**
     * having()
     * Set having object     
     * @param string $condition having statement, example: ("fieldname = ?")
     * @param string $value string value to be replaced in having statement
     * @return object \ngfw\Query()
     */
    public function having($condition, $value = null) {
        $condition = $this->escapeField($condition);
        $this->having[] = str_replace("?", "'" . addslashes($value) . "'", $condition);
        if (count($this->having) > 1):
            $this->query.="AND " . $this->having . " ";
        else:
            $this->query.="HAVING " . $this->having . " ";
        endif;
        return $this;
    }

    /**
     * group()
     * Sets groupBy object     
     * @param string $field Name of field to group by
     * @return object \ngfw\Query()
     */
    public function group($field) {
        $this->groupBy = $field;
        if (isset($this->groupBy)):
            if (is_array($this->groupBy)):
                $this->groupBy = implode("`, `", $this->groupBy);
            endif;
            $this->query.="GROUP BY `" . $this->groupBy . "` ";
        endif;
        return $this;
    }

    /**
     * order()
     * Sets orderBy Object     
     * @access public     
     * @param string $field fieldname to order by, exampe "Fieldname" or "RAND(" . date("Ymd") . ")"
     * @param string $clause order clause, example: "DESC" or "ASC"
     * @return object \ngfw\Query()
     */
    public function order($field, $clause = null) {
        if (strpos($field, "(") === false):
            $field = $this->escapeField($field);
        endif;
        $this->orderBy[] = $field . " " . $clause;
        if (count($this->orderBy) > 1):
            $this->query.=", " . end($this->orderBy) . " ";
        else:
            $this->query.="ORDER BY " . end($this->orderBy) . " ";
        endif;
        return $this;
    }

    /**
     * limit()
     * Sets limit object
     * @access public     
     * @param int $int Must be numeric 
     * @return object \ngfw\Query()
     */
    public function limit($int) {
        $this->limit = $int;
        if (isset($this->limit)):
            $this->query.="LIMIT " . $this->limit;
        endif;
        return $this;
    }

    /**
     * escapeField
     * will identify and escape first field
     * @access private
     * @param string $str example a.fieldname 
     * @return string
     */
    private function escapeField($str) {
        if (strpos($str, '`') === false):
            $tmpstr = explode(" ", $str);
            if (strpos($tmpstr[0], ".") === false):
                $tmpstr[0] = "`" . $tmpstr[0] . "`";
            else:
                $strD = explode(".", $tmpstr[0]);
                $tmpstr[0] = $strD[0] . ".`" . $strD[1] . "`";
            endif;
            $str = implode(" ", $tmpstr);
        endif;
        return $str;
    }

    /**
     * escapeValue
     * will escape string or array
     * @access private
     * @param mixed $value
     * @return mixed
     */
    private function escapeValue($value){
        if(is_array($value)):
            foreach($value as $key => $val):
                $value[$key] = $this->escapeValue($val);
            endforeach;
            return $value;
        else:
            $search=array("\\","\0","\n","\r","\x1a","'",'"');
            $replace=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
            return str_replace($search,$replace,$value);
        endif;
    }

    /**
     * getQuery()
     * Alias to __toString() Function.
     * Returns query as a string
     * @see __toString()
     * @access public
     * @return string
     */
    public function getQuery() {
        return $this->__toString();
    }

    /**
     * __toString()
     * Returns query as a string
     * @access public
     * @return string 
     */
    public function __toString() {
        return trim($this->query);
    }

}

?>
