<?php

/**
 * Watched
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */
class Watched {

    /**
     * holds Table name
     * @access proteted
     * @var string
     */
    protected $_table = "Watched";

    /**
     * holds db connection
     * @access protected
     * @var object
     */
    protected $db;

    /**
     * sets DB object
     * @access public
     * @return void
     */
    public function __construct() {
        $this->db = ngfw\Registry::get('db');
    }

    /**
     * add
     * @param int $userID 
     * @param int $movieID
     * @access public
     * @return mixed
     */
    public function add($userID, $movieID){
		$query = new ngfw\Query();
        $data['MovieID'] = $movieID;
        $data['UserID'] = $userID;
        $query->insert($this->_table, $data);
        return $this->db->query($query->getQuery());
    }

    /**
     * remove
     * @param int $userID
     * @param int $movieID
     * @access public
     * @return mixed
     */
    public function remove($userID, $movieID){
        $query = new ngfw\Query();
        $query->delete()
        ->from($this->_table)
        ->where("MovieID = ?", $movieID)
        ->andWhere("UserID = ?", $userID);
        return $this->db->query($query->getQuery());
    }

    /**
     * getUsersData
     * @param int $userID
     * @param mixed $limit
     * @access public
     * @return mixed
     */
    public function getUsersData($userID, $from = null, $limit = null){
        $query = new ngfw\Query();
        $query->select("MovieID")
        ->from($this->_table)
        ->where("UserID = ?", $userID)
        ->order("WatchedID", "DESC");
        if(isset($from) and is_numeric($from) and isset($limit) and is_numeric($limit)):
            $query->limit($from.",".$limit);
        endif;
        return $this->db->fetchAll($query->getQuery());
    }

    /**
     * count of total rows
     * @access public
     * @return int
     */
    public function totalRecords(){
        $query = new ngfw\Query();
        $query->select('count(*) as Total')
        ->from($this->_table);
        $result = $this->db->fetchRow($query->getQuery());
        return $result['Total'];
    }

    /**
     * getLatestRecords
     * @param int $limit
     * @access public
     * @return mixed
     */
    public function getLatestRecords($limit = 10){
        $query = new ngfw\Query();
        $query->select()
        ->from($this->_table)
        ->join("Users", $this->_table.".UserID = Users.UserID")
        ->order("WatchedID", "DESC")
        ->limit($limit);
        return $this->db->fetchAll($query->getQuery());
    }

}