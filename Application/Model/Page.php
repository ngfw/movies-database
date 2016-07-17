<?php

/**
 * Page
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */

class Page{
	/**
     * holds Table name
     * @access proteted
     * @var string
     */
    protected $_table = "Page";

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
     * getAllPages
     * @access public
     * @return mixed 
     */
    public function getAllPages(){
        $query = new ngfw\Query();
        $query->select()->from($this->_table);
        return $this->db->fetchAll($query->getQuery());
    }

    /**
     * getTitlesAndSlugs
     * @access public
     * @return mixed 
     */
    public function getTitlesAndSlugs(){
        $query = new ngfw\Query();
        $query->select( array('Slug','Title') )->from($this->_table)->where("Active = ?",1)->order("PageID", "ASC");
        return $this->db->fetchAll($query->getQuery());
    }

    /**
     * getPageWithSlug
     * @param mixed $slug
     * @access public
     * @return mixed
     */
    public function getPageWithSlug($slug){
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("Slug = ?", $slug)->andWhere("Active = ?",1)->limit(1);
        return $this->db->fetchRow($query->getQuery());
    }

    /**
     * getPageWithID
     * @param mixed $pageID 
     * @access public
     * @return mixed 
     */
    public function getPageWithID($pageID){
        $query = new ngfw\Query();
        $query->select()->from($this->_table)->where("PageID = ?", $pageID)->limit(1);
        return $this->db->fetchRow($query->getQuery());
    }

    /**
     * edit
     * @param mixed $pageid
     * @param mixed $title
     * @param mixed $slug
     * @param mixed $content
     * @param mixed $active
     * @access public
     * @return mixed
     */
    public function edit($pageid, $title, $slug, $content, $active){
        $query = new ngfw\Query();
        $data = array(
            "Title" => $title,
            "Slug" => $slug,
            "Content" => $content,
            "Created" => date("Y-m-d H:i:s"),
            "Active" => $active
        );
        $query->update($this->_table, $data)->where("PageID = ?", $pageid);
        return $this->db->query($query->getQuery());
    }

    /**
     * add
     * @param mixed $title
     * @param mixed $slug
     * @param mixed $content
     * @param mixed $active
     * @access public
     * @return mixed
     */
    public function add($title, $slug, $content, $active){
        $query = new ngfw\Query();
        $data = array(
            "Title" => $title,
            "Slug" => $slug,
            "Content" => $content,
            "Created" => date("Y-m-d H:i:s"),
            "Active" => $active
        );
        $query->insert($this->_table, $data);
        return $this->db->query($query->getQuery());
    }

    /**
     * delete
     * @param mixed $pageID
     * @access public
     * @return mixed
     */
    public function delete($pageID){
        $query = new ngfw\Query();
        $query->delete()->from($this->_table)->where("PageID = ?", $pageID);
        return $this->db->query($query->getQuery());
    }


}