<?php

/**
 * UserButtons
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */

class UserButtons {

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
     * getUserButtons
     * @param mixed $userID
     * @param mixed $movieID
     * @access public
     * @return mixed
     */
    public function getUserButtons($userID, $movieID){
		$sql = "SELECT 
		 (SELECT `FavoriteID` FROM `Favorite` WHERE `Favorite`.`MovieID` = '".$movieID."' and `Favorite`.`UserID`= '".$userID."') as Favorite,
		 (SELECT `WantToWatchID` From `WantToWatch` WHERE `WantToWatch`.`MovieID` = '".$movieID."' and `WantToWatch`.`UserID`= '".$userID."') as WantToWatch,
		 (SELECT `WatchedID` FROM `Watched` WHERE `Watched`.`MovieID` = '".$movieID."' and `Watched`.`UserID`= '".$userID."') as Watched;";
		$result = $this->db->fetchRow($sql);
		foreach($result as $k => $v):
			if(is_numeric($v)):
				$result[$k] = true;
			else:
				$result[$k] = false;
			endif;
		endforeach;
		return $result;
    }

}