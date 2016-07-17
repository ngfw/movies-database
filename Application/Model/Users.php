<?php

/**
 * Users
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */
class Users {

    /**
     * holds Table name
     * @access proteted
     * @var string
     */
    protected $_table = "Users";

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
     * Get single user with userid
     * @access public
     * @param int $UserID
     * @return array
     */
    public function getUserWithID($UserID) {
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("UserID = ?", $UserID);
        return $this->db->fetchRow($query->__toString());
    }

    /**
     * Get single user with facebookID
     * @access public
     * @param int $facebookID
     * @return array
     */
    public function getUserWithFacebookID($FacebookID) {
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("FacebookID = ?", $FacebookID);
        return $this->db->fetchRow($query->__toString());
    }

    /**
     * add new user
     * @param int $FacebookID
     * @param string $Name
     * @param string $FirstName
     * @param string $LastName
     * @param string $FacebookLink
     * @param string $FacebookUsername
     * @param string $Gender
     * @param string $Email
     * @param string $Timezone
     * @access public
     * @return mixed
     */
    public function add($FacebookID, $Name, $FirstName, $LastName, $FacebookLink, $FacebookUsername, $Gender, $Email, $Timezone) {
        $query = new ngfw\Query();
        $data = array(
            'FacebookID' => $FacebookID,
            'Name' => $Name,
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'FacebookLink' => $FacebookLink,
            'FacebookUsername' => $FacebookUsername,
            'Gender' => $Gender,
            'Email' => $Email,
            'Timezone' => $Timezone,
            'Private' => "0"
        );
        $query->insert($this->_table, $data);
        $this->db->query($query->__toString());
        return $this->db->lastInsertId();
    }

    /**
     * edit the user
     * @param int $UserID
     * @param string $FacebookID
     * @param string $Name
     * @param string $FirstName
     * @param string $LastName
     * @param string $FacebookLink
     * @param string $FacebookUsername
     * @param string $Gender
     * @param string $Email
     * @param string $Timezone
     * @access public
     * @return mixed
     */
    public function edit($UserID, $FacebookID, $Name, $FirstName, $LastName, $FacebookLink, $FacebookUsername, $Gender, $Email, $Timezone) {
        $query = new ngfw\Query();
        $data = array(
            'FacebookID' => $FacebookID,
            'Name' => $Name,
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'FacebookLink' => $FacebookLink,
            'FacebookUsername' => $FacebookUsername,
            'Gender' => $Gender,
            'Email' => $Email,
            'Timezone' => $Timezone
        );
        $query->update($this->_table, $data)->where("UserID = ?", $UserID);
        return $this->db->query($query->__toString());
    }

    /**
     * delete user
     * @access public
     * @param int $UserID
     * @return mixed
     */
    public function delete($UserID) {
        $query = new ngfw\Query();
        $query->delete()->from($this->_table)->where("UserID = ?", $UserID);
        return $this->db->query($query->__toString());
    }

    /**
     * getTotalUsersCount
     * @access public
     * @return int
     */
    public function getTotalUsersCount(){
        $query= new ngfw\Query();
        $query->select("COUNT(*) as TotalUsers")->from($this->_table);
        $result = $this->db->fetchRow($query->__toString());
        return $result['TotalUsers'];
    }

    /**
     * setPrivacy
     * @param int $userID 
     * @param int $private
     * @access public
     * @return mixed 
     */
    public function setPrivacy($userID, $private){
        $query = new ngfw\Query();
        $data['Private'] = $private;
        $query->update($this->_table, $data)->where("UserID = ?", $userID);
        return $this->db->query($query->getQuery());
    }

}