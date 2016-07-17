<?php

/* 
 * Admins
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */
class Admins{
	
	/**
	* holds Table name
	* @access proteted
	*/
	protected $_table = "Admin";

	/**
	* holds db connection
	* @access proteted
	*/
	protected $db;

	/**
	 * sets DB object
	 * @access public
	 * @return void
	 */

	public function __construct(){
		$this->db = ngfw\Registry::get('db');
	}

	/**
	 * Gets all admins from database
	 * @access public
	 * @return mixed
	 */
	public function getAllAdmins(){
		$query = new ngfw\Query();
		$query->select()->from($this->_table);
		return $this->db->fetchAll($query->getQuery());
	}

    /**
     * getAdmin
     * @param mixed $adminID 
     * @access public
     * @return mixed
     */
	public function getAdmin($adminID) {
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("AdminID = ?", $adminID);
        return $this->db->fetchRow($query->__toString());
    }

    /**
     * add
     * @param mixed $username
     * @param mixed $password
     * @param mixed $email   
     * @access public
     * @return mixed 
     */
	public function add($username, $password, $email) {
        $query = new ngfw\Query();
        $data = array(
            "Username" => $username,
            "Password" => md5($password),
            "Email" => $email
        );
        $query->insert($this->_table, $data);
        return $this->db->query($query->__toString());
    }

    /**
     * edit
     * @param mixed $adminID  
     * @param mixed $username 
     * @param mixed $password 
     * @param mixed $email    
     * @access public
     * @return mixed 
     */
    public function edit($adminID, $username, $password, $email) {
        $query = new ngfw\Query();
        $data = array(
            "Username" => $username,
            "Email" => $email
        );
        if (isset($password) and !empty($password)):
            $data["Password"] = md5($password);
        endif;
        $query->update($this->_table, $data)->where("AdminID = ?", $adminID);        
        return $this->db->query($query->__toString());
    }

    /**
     * delete
     * @param mixed $adminID 
     * @access public
     * @return mixed 
     */
    public function delete($adminID) {
        $query = new ngfw\Query();
        $query->delete()->from($this->_table)->where("AdminID = ?", $adminID);
        return $this->db->query($query->__toString());
    }


}