<?php

/**
 * AdminController
 * @copyright (c) 2013, Nick Gejadze
 */
class AdminController extends ngfw\Controller {

    /**
     * Allowed Actions to be viewed without authentication 
     * @access protected
     * @var array 
     */
    protected $allowedMethods = array("Login", "Auth");

    /**
     * Authentication object
     * @access protected
     * @var object 
     */
    protected $auth;
    
    /**
     * User Object
     * @access protected
     * @var array
     */
    protected $user;

    /**
     * Servs as class __construct method
     * @access public
     * @return void
     */
    public function init() {
    	$request = ngfw\Route::getRequests();
    	$request['value'] = ucfirst(strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $request['value'])));
    	$translate = new Translate();
        
        $this->requestedLanguage = ngfw\Registry::get('requestedLanguage');
        
        if(!isset($request['value']) or empty($request['value'])):
    		ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/login", "405");
    		exit();
    	endif;
    	//override the view:
        $this->view = new ngfw\View(str_replace("Controller", "",__CLASS__), $request['value']);
        $this->view->setLayoutFile("Admin");
        $this->availableLanguages = $this->view->availableLanguages = $translate->getAvailableLanguages();
        $this->objects = ngfw\Registry::get("objects");
        $this->translation = $this->view->translation = $this->objects['translation'];
        $this->view->requestedLanguage = $this->requestedLanguage;

        //Check installation 
        if (file_exists(PUBLIC_PATH."/install.php")):
            $this->view->alertInstallationFile = true;
        endif;

        $this->config = ngfw\Registry::get('config');
        $this->tmdb = new Themoviedb($this->objects["settings"]['TMPDB_Api_Key']);
        $this->fileCache = new Filecache();
        
        
        $this->db = ngfw\Registry::get('db');
        $this->auth = new ngfw\Authentication($this->db, "Admin", "Username", "Password");
        
        // Check authentication
        if (!$this->auth->isValid()):
            // if user is not authenticated 
            if (in_array($request['value'], $this->allowedMethods)):
                call_user_func(array($this, $request['value']));
            	exit();
            else:
            	ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/login", "405");
            	exit();
            endif;
        else:
        	// user is authenticated 
            $this->user = $this->auth->getIdentity();
            $this->view->user = $this->user;
            call_user_func(array($this, $request['value']));
            //exit();
        endif;
        
        
    }


    /**
     * Login Action
     * @access public
     * @return void
     */
    public function Login() {
        // action simply prints login form
        // see Admin/Login.phtml view file
        // add Messages if needed
        $request = ngfw\Route::getRequests();
        if (isset($request['key2']) and $request['key2'] == "error"):
            $this->view->error = $request['value2'];
        endif;
        if (isset($request['key2']) and $request['key2'] == "msg"):
            $this->view->msg = $request['value2'];
        endif;
    }

    /**
     * Admin Authentication Action
     * @access public
     * @return void
     */
    public function Auth() {
    	// disable Layout and View
        $this->view->enableLayout(false);
        $this->view->enableView(false);
        // Check posted values
        if (isset($_POST['username']) and !empty($_POST['username']) and isset($_POST['password']) and !empty($_POST['password'])):
            //check in database
            $this->auth->setIdentity($_POST['username']);
            $this->auth->setCredential(md5($_POST['password']));
            // if admin is authenticated
            if ($this->auth->isValid()):
                ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/homepage/");
            	exit();
            endif;
        endif;
        // user not found
        ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/login/error/usernotfound");
        exit();
    }

    /**
     * Admin Logout Action
     * @access public
     * @return void
     */
    public function Logout() {
        // log out admin 
        $this->auth->clearIdentity();
        // disable Layout and View
        $this->view->enableLayout(false);
        $this->view->enableView(false);
        //redirect to login page
        ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/login/msg/byebye");
        exit();
    }

    /**
     * Index page Action
     * @access public
     * @return void
     */
    public function IndexAction() {
        //Dummy Action
    }

    public function Homepage(){
        $users = new Users();
        $movieViews = new MovieViews();
        $favorite = new Favorite();
        $wanttowatch = new WantToWatch();
        $watched = new Watched();
        
        
        if (ngfw\App::isAjax()):
            $this->view->enableLayout(false);
            if(isset($_POST['get']) and $_POST['get'] == "clearcache"):
                $this->view->enableView(false);
                $this->fileCache->delete_all();
            endif;
            if(isset($_POST['get']) and $_POST['get'] == 'favorite'):
                $latestFavorites = $favorite->getLatestRecords();
                foreach($latestFavorites as $key => $favorites):
                    if($this->config["EnableCaching"]):
                        $latestFavorites[$key]['Movie'] = $this->fileCache->get("Movie-".$favorites['MovieID']);
                    endif;
                    if(!$this->config["EnableCaching"] OR !isset($latestFavorites[$key]['Movie']) OR !$latestFavorites[$key]['Movie']):
                        $latestFavorites[$key]['Movie'] = $this->tmdb->getMoviePage($favorites['MovieID']);
                        if($this->config["EnableCaching"]):
                            $this->fileCache->set("Movie-".$favorites['MovieID'], $latestFavorites[$key]['Movie']);
                        endif;
                    endif;
                endforeach;
                $this->view->latestFavorites = $latestFavorites;
            endif;

            if(isset($_POST['get']) and $_POST['get'] == 'wanttowatch'):
                $latestwanttowatch = $wanttowatch->getLatestRecords();
                foreach($latestwanttowatch as $key => $wanttowatch):
                    if($this->config["EnableCaching"]):
                        $latestwanttowatch[$key]['Movie'] = $this->fileCache->get("Movie-".$wanttowatch['MovieID']);
                    endif;
                    if(!$this->config["EnableCaching"] OR !isset($latestwanttowatch[$key]['Movie']) OR !$latestwanttowatch[$key]['Movie']):
                        $latestwanttowatch[$key]['Movie'] = $this->tmdb->getMoviePage($wanttowatch['MovieID']);
                        if($this->config["EnableCaching"]):
                            $this->fileCache->set("Movie-".$wanttowatch['MovieID'], $latestwanttowatch[$key]['Movie']);
                        endif;
                    endif;
                endforeach;
                $this->view->latestwanttowatch = $latestwanttowatch;
            endif;
            
            if(isset($_POST['get']) and $_POST['get'] == 'watched'):
                $latestwatched = $watched->getLatestRecords();
                foreach($latestwatched as $key => $watched):
                    if($this->config["EnableCaching"]):
                        $latestwatched[$key]['Movie'] = $this->fileCache->get("Movie-".$watched['MovieID']);
                    endif;
                    if(!$this->config["EnableCaching"] OR !isset($latestwatched[$key]['Movie']) OR !$latestwatched[$key]['Movie']):
                        $latestwatched[$key]['Movie'] = $this->tmdb->getMoviePage($watched['MovieID']);
                        if($this->config["EnableCaching"]):
                            $this->fileCache->set("Movie-".$watched['MovieID'], $latestwatched[$key]['Movie']);
                        endif;
                    endif;
                endforeach;
                $this->view->latestwatched = $latestwatched;
            endif;

        else:
            //not ajax
            if($this->objects['settings']['Allow_Facebook_Authentication'] !== "true"):
                $this->view->facebookEnabled = false;
            else:
                $this->view->facebookEnabled = true;
            endif;
            $this->view->totalFavorites = $favorite->totalRecords();
            $this->view->totalMovieViews = $movieViews->getTotalMovieViews();
            $this->view->totalusers = $users->getTotalUsersCount();
            $this->view->totalWanttowatch = $wanttowatch->totalRecords();
            $this->view->totalWatched = $watched->totalRecords();

        endif; //is ajax
    }

    /**
     * Pages
     * @access public
     * @return void
     */
    public function Page(){
        $page = new Page();
        $request = ngfw\Route::getRequests();
        if (isset($request['key2']) and $request['key2'] == "delete" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            //delete setting
            $page->delete($request['value2']);
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/page");
            exit();
        endif;
        // if edit is requested
        if (isset($request['key2']) and $request['key2'] == "edit" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            $this->view->role = "edit";
            $this->view->page = $page->getPageWithID($request['value2']);
            if (isset($_POST['pageID']) and !empty($_POST['pageID']) and is_numeric($_POST['pageID'])):
                //edit the page
                if($_POST['Active'] == "on"):
                    $active = 1;
                else:
                    $active = 0;
                endif;
                $page->edit($_POST['pageID'], $_POST['Title'], $_POST['Slug'], $_POST['Content'], $active);
                ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/page");
                exit();
            endif;
        endif;
        // add new page
         if (isset($_POST['Title']) and !empty($_POST['Title']) and isset($_POST['Slug']) and !empty($_POST['Slug']) and isset($_POST['Content']) and !empty($_POST['Content'])):
            if($_POST['Active'] == "on"):
                $active = 1;
            else:
                $active = 0;
            endif;
            $page->add($_POST['Title'], $_POST['Slug'], $_POST['Content'], $active);
            //ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/page");
            //exit();
        endif;
        $this->view->pages = $page->getAllPages();
    }

    /**
     * ADs page Action
     * @access public
     * @return void
     */
    public function Ads() {
        // get ads method
        $ads = new Ads();
        //get requests 
        $request = ngfw\Route::getRequests();
        if (isset($request['key2']) and $request['key2'] == "delete" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            //delete setting
            $ads->delete($request['value2']);
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/ads");
            exit();
        endif;
        // if edit is requested
        if (isset($request['key2']) and $request['key2'] == "edit" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            // get single ad
            $this->view->ad = $ads->getAdByID($request['value2']);
            $this->view->role = "edit";
            if (isset($_POST['adID']) and !empty($_POST['adID']) and is_numeric($_POST['adID'])):
                //edit the ad
                $ads->edit($_POST['adID'], $_POST['adSize'], $_POST['adValue']);
                ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/ads");
                exit();
            endif;
        endif;
        if (isset($_POST['size']) and !empty($_POST['size']) and isset($_POST['value']) and !empty($_POST['value'])):
            //add new setting
            $ads->add($_POST['size'], $_POST['value']);
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/ads");
            exit();
        endif;
        //get all ads 
        $this->view->ads = $ads->getAllAds();
    }

    /**
     * Users page Action
     * @access public
     * @return void
     */
    public function Users(){
        $users = new Users();
        $request = ngfw\Route::getRequests();
        if (isset($request['key2']) and $request['key2'] == "sort" 
            and isset($request['value2']) and !empty($request['value2'])):
            ngfw\Session::set("usersSort", $request['value2']);
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/users");
            exit();
        endif;
        // remove the video
        if (isset($request['key2']) and $request['key2'] == "delete" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            $users->delete($request['value2']);
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/users");
            exit();
        endif;       
        // start pagination of users 
        $pagination = new ngfw\Pagination();
        // set DB adapter for paginator
        $pagination->setAdapter(ngfw\Registry::get('db'));
        // set table to paginate
        $pagination->setTable("Users");
        // set default page 
        $page = 1;
        // check if different page is requested
        if (isset($request['key2']) and $request['key2'] == "page" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            $page = $request['value2'];
        endif;
        // tell paginator which page to load
        $pagination->setCurrentPage($page);
        // check if sorting is requested
        $sort = ngfw\Session::get("usersSort");
        if (isset($sort) and !empty($sort)):
            // tell paginator what to sort
            $sortMethods = explode("-", $sort);
            $pagination->setOrderBy($sortMethods[0]);
            $pagination->setOrderClause($sortMethods[1]);
        endif;
        $this->view->users = $pagination->getResult();
        $this->view->pagination = $pagination->getPagination();        
    }

    /**
     * Admins page Action
     * @access public
     * @return void
     */
    public function Admins() {
        // get Admins
        $admin = new Admins();
        // get requests 
        $request = ngfw\Route::getRequests();
        // if Username, password and email is posted add it to database
        if (isset($_POST['Username']) and !empty($_POST['Username']) and
                isset($_POST['Password']) and !empty($_POST['Password']) and
                isset($_POST['Email']) and !empty($_POST['Email']) and !isset($_POST['userid'])):
            // add admin
            $admin->add($_POST['Username'], $_POST['Password'], $_POST['Email']);
        endif;
        // if delete is requested
        if (isset($request['key2']) and $request['key2'] == "delete" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            //delete admin
            $admin->delete($request['value2']);
        endif;
        // if edit is requested
        if (isset($request['key2']) and $request['key2'] == "edit" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            // get single admin
            $this->view->adminuser = $admin->getAdmin($request['value2']);
            // set page role to edit
            $this->view->role = "edit";
            if (isset($_POST['userid']) and !empty($_POST['userid']) and
                    isset($_POST['Username']) and !empty($_POST['Username']) and
                    isset($_POST['Email']) and !empty($_POST['Email'])):
                // check if we are changingt he password 
                if (isset($_POST['Password']) and !empty($_POST['Password'])):
                    $password = $_POST['Password'];
                else:
                    $password = null;
                endif;
                //edit admin
                $admin->edit($_POST['userid'], $_POST['Username'], $password, $_POST['Email']);
                //redirect back
                ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/admins");
                exit();
            endif;
        endif;
        // get all administrators
        $this->view->admins = $admin->getAllAdmins();
    }

    /**
     * Translation page Action
     * @access public
     * @return void
     */
    public function Translation() {
        // start translate model
        $translate = new Translate();

        // get all requests
        $request = ngfw\Route::getRequests();
        // delete translation
        if (isset($request['value2']) and is_numeric($request['value2'])
             and isset($request['key2']) and $request['key2'] == "delete"):
            $translate->deleteTranslation($request['value2']);
            ngfw\Route::redirect($_SERVER["HTTP_REFERER"]);
            exit();
        endif;
        //delete language
        if (isset($request['value2']) and !empty($request['value2'])
             and isset($request['key2']) and $request['key2'] == "deletelanguage"):
            $translate->deleteLanguage($request['value2']);
            ngfw\Route::redirect($_SERVER["HTTP_REFERER"]);
            exit();
        endif;
        
        // add new language
        if (isset($_POST['newLanguage']) and !empty($_POST['newLanguage'])):
            $translate->addLanguage($_POST['newLanguage']);
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/translation/");
            exit();
        endif;
        
        // add new translation
        if (isset($request['value2']) and $request['value2'] == "new"):
            if (isset($_POST["source"]) and !empty($_POST["source"]) and isset($_POST["translation"]) and !empty($_POST["translation"]) and isset($_POST["language"]) and !empty($_POST["language"])
            ):
                $translate->addTranslation($_POST["source"], $_POST["translation"], $_POST["language"]);
                ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/translation/edit/" . $_POST['language']);
                exit();
            endif;
        endif;
        // check if request is NOT new 
        if (isset($request['value2']) and $request['value2'] !== "new"):
            // if it's ajax request
            if (ngfw\App::isAjax()):
                $this->view->enableLayout(false);
                $this->view->enableView(false);
                // update translation
                if (isset($_POST['translationID']) and is_numeric($_POST['translationID'])):
                    if ($translate->updateTranslation($_POST['translationID'], $_POST['source'], $_POST['translation'])):
                        echo "ok";
                    else:
                        echo "error";
                    endif;
                endif;
            endif;
            // set role as edit
            $this->view->role = "edit";
            //get translations
            $this->view->translationData = $translate->getTranslation($request['value2'], $fullArray = true);
            $this->view->selectedLanguage = $request['value2'];
        endif;
        // get all languages
        $availableLanguages = $translate->getAvailableLanguages();
        if (isset($availableLanguages) and is_array($availableLanguages)):
            $this->view->availableLanguages = $availableLanguages;
            foreach ($this->view->availableLanguages as $key => $value):
                $this->view->languageStats[$value] = $translate->getTranslationCount($value);
            endforeach;
        endif;
    }

    /**
     * Settings page Action
     * @access public
     * @return void
     */
    public function Settings() {
        // get setting method
        $settings = new Settings();
        //get requests 
        $request = ngfw\Route::getRequests();
        // if name and value is posted add new setting
        if (isset($_POST['name']) and !empty($_POST['name']) 
            and isset($_POST['value']) and !empty($_POST['value'])):
            //add new setting
            $settings->add($_POST['name'], $_POST['value']);
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/settings");
            exit();
        endif;
        // if detele is requested
        if (isset($request['key2']) and $request['key2'] == "delete" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            //delete setting
            $settings->delete($request['value2']);
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/settings");
        endif;
        // if edit is requested
        if (isset($request['key2']) and $request['key2'] == "edit" 
            and isset($request['value2']) and is_numeric($request['value2'])):
            // get single setting
            $this->view->setting = $settings->getSettingByID($request['value2']);
            $this->view->role = "edit";
            if (isset($_POST['settingID']) and !empty($_POST['settingID']) and is_numeric($_POST['settingID'])):
                //edit the setting
                $settings->edit($_POST['settingID'], $_POST['SettingName'], $_POST['SettingValue']);
                ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/admin/action/settings");
            endif;
        endif;
        //get all settings 
        $this->view->settings = $settings->getAllSettings();
    }

}