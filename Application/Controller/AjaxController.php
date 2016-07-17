<?php

use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
/**
 * AjaxController
 * @copyright (c) 2014, Nick Gejadze
 */
class AjaxController extends MovieDatabaseWrapperController{

    protected $profileMoviesLimit = 10;
    protected $fbSession;

    /**
     * Index page Action
     * @access public
     * @return void
     */
    public function IndexAction() {
        $this->profileMoviesLimit = $this->objects["settings"]["Number_Of_Profile_Movies"];
        //disable view and layout
        $this->view->enableLayout(false);
        $this->view->enableView(false);
        // if ajax Request
        if (ngfw\App::isAjax()):

            //get page views
            if(isset($_POST['get']) and $_POST['get'] == "views" and 
                isset($_POST['page']) and is_numeric($_POST['page'])):
                $this->getMovieViews();
                exit();
            endif;

            if($this->objects['settings']['Allow_Facebook_Authentication'] == "true" and
                isset($this->objects['settings']['fb:app_id']) and !empty($this->objects['settings']['fb:app_id']) and
                isset($this->objects['settings']['fb:secret']) and !empty($this->objects['settings']['fb:secret'])):

                FacebookSession::setDefaultApplication($this->objects['settings']['fb:app_id'], $this->objects['settings']['fb:secret']);
                $this->fbSession = false;
                if(isset($_POST['accessToken']) and !empty($_POST['accessToken']) and $_POST['accessToken'] != "false"):
                    $this->fbSession = new FacebookSession($_POST['accessToken']);
                endif;

                if(isset($_POST['get']) and !empty($_POST['get'])):
                    switch($_POST['get']):
                        case "register":
                            $this->registerUser();
                            exit();
                        break;
                        case "userButtons":
                            if(isset($_POST['id']) and is_numeric($_POST['id'])):
                                $this->getUserButtons();
                                exit();
                            endif;
                        break;
                        case "userButtonClick":
                            if(isset($_POST['id']) and is_numeric($_POST['id']) and 
                                isset($_POST['type']) and !empty($_POST['type']) and 
                                isset($_POST['action']) and !empty($_POST['action'])):
                                $this->userButtonClicks();
                                exit();
                            endif;
                        break;
                        case "movie":
                            if(isset($_POST['id']) and is_numeric($_POST['id'])):
                                $this->getMovie($_POST['id']);
                            endif;
                            exit();
                        break;
                        case "profilePage":
                            if(isset($_POST['profileid']) and is_numeric($_POST['profileid'])):
                                $favoritePage = $wanttowatchPage = $watchedPage = false;
                                if(isset($_POST["favorite"]) and is_numeric($_POST["favorite"])):
                                    $favoritePage = $_POST["favorite"];
                                endif;
                                if(isset($_POST["wanttowatch"]) and is_numeric($_POST["wanttowatch"])):
                                    $wanttowatchPage = $_POST["wanttowatch"];
                                endif;
                                if(isset($_POST["watched"]) and is_numeric($_POST["watched"])):
                                    $watchedPage = $_POST["watched"];
                                endif;

                                if(isset($_POST["accessToken"]) and !empty($_POST["accessToken"]) and $_POST['accessToken'] != "false"):
                                    $fbUserData = (array) (new FacebookRequest(
                                      $this->fbSession, 'GET', '/me'
                                    ))->execute()->getGraphObject(GraphUser::className())->asArray();
                                    $users = new Users();
                                    $userData = $users->getUserWithFacebookID($fbUserData['id']);
                                    // if user Requests own profile
                                    if($userData['UserID'] == $_POST['profileid']):
                                        $returnArray['data'] = $this->getProfilePage($userData['UserID'], $favoritePage, $wanttowatchPage, $watchedPage, $selfProfile=1);
                                        $returnArray['status'] = "ok";
                                        echo json_encode($returnArray);
                                    else:
                                        $this->getPublicProfile($_POST['profileid'], $favoritePage, $wanttowatchPage, $watchedPage);
                                    endif;
                                else:
                                    $this->getPublicProfile($_POST['profileid'],$favoritePage, $wanttowatchPage, $watchedPage);
                                endif;//isset($_POST["accessToken"])
                            endif;//isset($_POST['profileid'])
                        break;
                        case "profilePageSettings":
                            $this->getProfilePrivacy();
                        break;
                        case "changePrivacy":
                            $this->changePrivacy();
                        break;
                    endswitch;
                endif;//if(isset($_POST['get']) and !empty($_POST['get'])):

            endif;//$this->objects['settings']['Allow_Facebook_Authentication'] == "true"
        endif;
    }

    private function registerUser(){
        $users = new Users();
        $fbUserData = (array) (new FacebookRequest(
          $this->fbSession, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className())->asArray();
        if(isset($fbUserData['id']) and is_numeric($fbUserData['id'])):
            $checkUser = $users->getUserWithFacebookID($fbUserData['id']);
            if(!$checkUser):
                //register user
                $result = $users->add($fbUserData['id'], 
                            $fbUserData['name'], 
                            $fbUserData['first_name'], 
                            $fbUserData['last_name'],
                            $fbUserData['link'],
                            $fbUserData['username'],
                            $fbUserData['gender'],
                            $fbUserData['email'],
                            $fbUserData['timezone']);
                if(is_numeric($result)):
                    echo $result;
                else:
                    echo "0"; //error
                endif;
            else:
                //check if we need to update user data
                if($checkUser['Name'] != $fbUserData['name'] OR
                    $checkUser['FirstName'] != $fbUserData['first_name'] OR
                    $checkUser['LastName'] != $fbUserData['last_name'] OR 
                    $checkUser['FacebookLink'] != $fbUserData['link'] OR
                    $checkUser['Gender'] != $fbUserData['gender'] OR
                    $checkUser['Email'] != $fbUserData['email'] OR
                    $checkUser['Timezone'] != $fbUserData['timezone']):
                    $users->edit($checkUser['UserID'],
                            $checkUser['FacebookID'], 
                            $fbUserData['name'], 
                            $fbUserData['first_name'], 
                            $fbUserData['last_name'],
                            $fbUserData['link'],
                            $fbUserData['email'],
                            $fbUserData['gender'],
                            $fbUserData['email'],
                            $fbUserData['timezone']);
                endif;
                echo $checkUser['UserID'];
            endif;
        else:
            echo "0";
        endif;
    }

    private function getMovieViews(){
        $pageviewsModel = new MovieViews();
        $pageViews = $pageviewsModel->getMovieViews( $_POST['page'] );
        if(!isset($pageViews) OR empty($pageViews)):
            $pageviewsModel->insertCount( $_POST['page'], 1);
            echo "1";
        else:
            $pageviewsModel->updateViews( $_POST['page'], $pageViews+1 );
            echo $pageViews+1;
        endif;
    }

    private function getUserButtons(){
        $fbUserData = (array) (new FacebookRequest(
          $this->fbSession, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className())->asArray();
        if(isset($fbUserData['id']) and is_numeric($fbUserData['id'])):
            $users = new Users();
            $userButtons = new UserButtons();
            $userData = $users->getUserWithFacebookID($fbUserData['id']);
            $userButtonsData = $userButtons->getUserButtons($userData['UserID'], $_POST['id']);
            echo json_encode($userButtonsData);
        endif;
    }   

    private function getProfilePrivacy(){
        $users = new Users();
        $fbUserData = (array) (new FacebookRequest(
          $this->fbSession, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className())->asArray();
        if(isset($fbUserData['id']) and is_numeric($fbUserData['id'])):
            $userData = $users->getUserWithFacebookID($fbUserData['id']);
            if(isset($_POST['profileid']) and $_POST['profileid'] == $userData['UserID']):
                $result = array(
                    "status" => "ok",
                    "privacy" => $userData['Private']
                );
                echo json_encode($result);
            else:
                echo json_encode(array("status" => "error"));
            endif;
        else:
            echo json_encode(array("status" => "error"));
        endif;
    }

    private function changePrivacy(){
        $users = new Users();
        $fbUserData = (array) (new FacebookRequest(
          $this->fbSession, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className())->asArray();
        if(isset($fbUserData['id']) and is_numeric($fbUserData['id'])):
            $userData = $users->getUserWithFacebookID($fbUserData['id']);
            if(isset($_POST['profileid']) and $_POST['profileid'] == $userData['UserID']):
                if(isset($_POST['privacy']) and ($_POST['privacy'] == "0" OR $_POST['privacy'] == "1")):
                    $users->setPrivacy($userData['UserID'], $_POST['privacy']);
                endif;
                $result = array(
                    "status" => "ok"
                );
                echo json_encode($result);
            else:
                echo json_encode(array("status" => "error"));
            endif;
        else:
            echo json_encode(array("status" => "error"));
        endif;
    }

    private function userButtonClicks(){
        $users = new Users();
        $fbUserData = (array) (new FacebookRequest(
          $this->fbSession, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className())->asArray();
        if(isset($fbUserData['id']) and is_numeric($fbUserData['id'])):
            $userData = $users->getUserWithFacebookID($fbUserData['id']);
            $userID = $userData['UserID'];
            switch($_POST['type']):
                case "favorite":
                    $favorite = new Favorite();
                    if($_POST['action'] == "add"):
                        $favorite->add($userID, $_POST['id']);
                        echo json_encode(array("status"=>"ok"));
                    elseif($_POST['action'] == "remove"):
                        $favorite->remove($userID, $_POST['id']);
                        echo json_encode(array("status"=>"ok"));
                    endif;
                break;
                case "wanttowatch":
                    $WantToWatch = new WantToWatch();
                    if($_POST['action'] == "add"):
                        $WantToWatch->add($userID, $_POST['id']);
                        echo json_encode(array("status"=>"ok"));
                    elseif($_POST['action'] == "remove"):
                        $WantToWatch->remove($userID, $_POST['id']);
                        echo json_encode(array("status"=>"ok"));
                    endif;
                break;
                case "watched":
                    $Watched = new Watched();
                    if($_POST['action'] == "add"):
                        $Watched->add($userID, $_POST['id']);
                        echo json_encode(array("status"=>"ok"));
                    elseif($_POST['action'] == "remove"):
                        $Watched->remove($userID, $_POST['id']);
                        echo json_encode(array("status"=>"ok"));
                    endif;
                break;
            endswitch;
        endif;
    }

    private function getPublicProfile($profileid, $favoritePage=1, $wanttowatchPage=1, $watchedPage=1){
        $users = new Users();
        $profileData = $users->getUserWithID($profileid);
        if($profileData["Private"] == "0"):
            $returnArray['data'] = $this->getProfilePage($profileid, $favoritePage, $wanttowatchPage, $watchedPage);
            $returnArray['status'] = "ok";
            echo json_encode($returnArray);
        else:
            echo json_encode(array("status" => "error"));
        endif;
    }

    private function getProfilePage($userid, $favoritePage=1, $wanttowatchPage=1, $watchedPage=1, $selfProfile=0){
        $favorite = new Favorite();
        $wanttowatch = new WantToWatch();
        $watched = new Watched();
        if(isset($favoritePage) and is_numeric($favoritePage)):
            $result['favorite'] = $favorite->getUsersData($userid, (($favoritePage-1)*$this->profileMoviesLimit), $this->profileMoviesLimit);
        endif;
        if(isset($wanttowatchPage) and is_numeric($wanttowatchPage)):
            $result['wanttowatch'] = $wanttowatch->getUsersData($userid, (($wanttowatchPage-1)*$this->profileMoviesLimit), $this->profileMoviesLimit);
        endif;
        if(isset($watchedPage) and is_numeric($watchedPage)):
            $result['watched'] = $watched->getUsersData($userid, (($watchedPage-1)*$this->profileMoviesLimit), $this->profileMoviesLimit);
        endif;
        if($selfProfile == 1):
            $result['auth'] = "true";
        else:
            $result['auth'] = "false";
        endif;
        return $result;
    }

    private function getMovie($movieID){
        $result = array();
        if($this->config["EnableCaching"]):
            $movie = $this->fileCache->get("Movie-".$movieID."-".$this->view->requestedLanguage);
        endif;
        if(!$this->config["EnableCaching"] OR !isset($movie) OR !$movie):
            $movie = $this->tmdb->getMoviePage($movieID);
            if($this->config["EnableCaching"]):
                $this->fileCache->set("Movie-".$movieID."-".$this->view->requestedLanguage, $movie);
            endif;
        endif;
        $result['title'] = $movie['title'];
        $result['poster'] = $this->view->mediumThumb . $movie['poster_path'];
        $result['id'] = $movieID;
        echo json_encode($result);
    }
}


