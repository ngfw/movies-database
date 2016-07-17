<?php

/**
 * Settings
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */

class Settings {

    /**
     * holds Table name
     * @access proteted
     * @var string
     */
    protected $_table = "Settings";

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
     * Gets all settings from database
     * @access public
     * @return array
     */
    public function getAllSettings() {
        $query = new ngfw\Query();
        $query->select()->from($this->_table);
        return $this->db->fetchAll($query->getQuery());
    }

    /**
     * get single setting with setting name 
     * @param mixed $SettingName Setting Name
     * @access public
     * @return string
     */
    public function getSetting($SettingName) {
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("SettingName = ?", $SettingName);
        $result = $this->db->fetchRow($query->__toString());
        return $result["SettingValue"];
    }

    /**
     * get Setting with id
     * @param int $SettingID Setting ID
     * @access public
     * @return array
     */
    public function getSettingByID($SettingID) {
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("SettingID = ?", $SettingID);
        return $this->db->fetchRow($query->__toString());
    }

    /**
     * add new setting
     * @param mixed $SettingName  Setting Name.
     * @param mixed $SettingValue Setting Value.
     * @access public
     * @return mixed
     */
    public function add($SettingName, $SettingValue) {
        $query = new ngfw\Query();
        $data = array(
            "SettingName" => $SettingName,
            "SettingValue" => $SettingValue
        );
        $query->insert($this->_table, $data);
        return $this->db->query($query->__toString());
    }

    /**
     * edit setting
     * @param int $SettingID    Setting ID.
     * @param mixed $SettingName  Setting name.
     * @param mixed $SettingValue Setting Value.
     * @access public
     * @return mixed
     */
    public function edit($SettingID, $SettingName, $SettingValue) {
        $query = new ngfw\Query();
        $data = array(
            "SettingName" => $SettingName,
            "SettingValue" => $SettingValue
        );
        $query->update($this->_table, $data)->where("SettingID = ?", $SettingID);
        return $this->db->query($query->__toString());
    }

    /**
     * delete
     * @param mixed $SettingID Setting ID.
     * @access public
     * @return mixed
     */
    public function delete($SettingID) {
        $query = new ngfw\Query();
        $query->delete()->from($this->_table)->where("SettingID = ?", $SettingID);
        return $this->db->query($query->__toString());
    }

}