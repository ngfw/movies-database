<?php

/**
 * MovieViews
 * @copyright (c) 2013, Nick Gejadze
 */
class MovieViews {

    /**
     * holds Table name
     * @access proteted
     * @var string
     */
    protected $_table = "MovieViews";

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
     * getMovieViews
     * @param mixed $movieID TMDB Movie ID
     * @access public
     * @return int
     */
    public function getMovieViews($movieID){
        $query = new ngfw\Query();
        $query->select("Views")
        ->from($this->_table)
        ->where("movieID = ?", $movieID)
        ->limit(1);
        $result = $this->db->fetchRow($query->getQuery());
        return (int) $result['Views'];
    }

    /**
     * insertCount
     * @param int $movieID TMDB Movie ID
     * @param int $views  Initial view count
     * @access public
     * @return mixed.
     */
    public function insertCount($movieID, $views = 1){
        $query = new ngfw\Query();
        $data['movieID'] = $movieID;
        $data['Views'] = $views;
        $query->insert($this->_table, $data);
        return $this->db->query($query->getQuery());
    }

    /**
     * updateViews
     * @param int $movieID TMDB Movie ID
     * @param int $views  View Count
     * @access public
     * @return mixed
     */
    public function updateViews($movieID, $views){
        $query = new ngfw\Query();
        $data['Views'] = $views;
        $query->update($this->_table, $data)->where("movieID = ?", $movieID);
        return $this->db->query($query->getQuery());
    }

    /**
     * getTotalMovieViews
     * @access public
     * @return int
     */
    public function getTotalMovieViews(){
        $query = new ngfw\Query();
        $query->select("SUM(Views) AS TotalViews")
        ->from($this->_table);
        $result = $this->db->fetchRow($query->getQuery());
        return (int) $result['TotalViews'];
    }

}