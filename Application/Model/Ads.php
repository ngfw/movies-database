<?php

/**
 * Ads
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */
class Ads {

    /**
     * holds Table name
     * @access proteted
     * @var string
     */
    protected $_table = "Ads";

    /**
     * holds db connection
     * @access proteted
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
     * Gets all ads from database
     * @access public
     * @return array
     */
    public function getAllAds() {
        $query = new ngfw\Query();
        $query->select()->from($this->_table);
        return $this->db->fetchAll($query->getQuery());
    }

    /**
     * get single ad with size
     * @param mixed $Size Ad Size
     * @access public
     * @return string
     */
    public function getAdWithSize($Size) {
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("adSize = ?", $Size)->limit(100);
        $result = $this->db->fetchAll($query->__toString());
        return $result;
    }

    /**
     * get ad with id
     * @param int $AdID Ad ID
     * @access public
     * @return array
     */
    public function getAdByID($AdID) {
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("adID = ?", $AdID);
        return $this->db->fetchRow($query->__toString());
    }

    /**
     * add new ad
     * @param mixed $Size  ad size
     * @param mixed $Value ad Value
     * @access public
     * @return mixed
     */
    public function add($Size, $Value) {
        $query = new ngfw\Query();
        $data = array(
            "adSize" => $Size,
            "adValue" => $Value
        );
        $query->insert($this->_table, $data);
        return $this->db->query($query->__toString());
    }

    /**
     * edit ads
     * @param int $AdID    
     * @param mixed $Size  
     * @param mixed $Value 
     * @access public
     * @return mixed
     */
    public function edit($AdID, $Size, $Value) {
        $query = new ngfw\Query();
        $data = array(
            "adSize" => $Size,
            "adValue" => $Value
        );
        $query->update($this->_table, $data)->where("adID = ?", $AdID);
        return $this->db->query($query->__toString());
    }

    /**
     * delete
     * @param mixed $AdID 
     * @access public
     * @return mixed
     */
    public function delete($AdID) {
        $query = new ngfw\Query();
        $query->delete()->from($this->_table)->where("adID = ?", $AdID);
        return $this->db->query($query->__toString());
    }

}