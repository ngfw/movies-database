<?php

/**
 * Bootstrap
 * @project Movie Database 2
 * @copyright (c) 2014, Nick Gejadze
 */

use ngfw\Bootstrap as FrameworkBootstrap;

/**
 * Bootstrap
 * @package NG
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2014, Nick Gejadze
 */
class Bootstrap extends FrameworkBootstrap {

    /**
    * Init Configuration and set in registry
    * @access public
    * @return void
    */
    public function _initConfig() {
        $this->config = ngfw\Configuration::loadConfigFile(ROOT . DS . APPDIR . DS . 'Config' . DS . 'application.ini');
        ngfw\Registry::set("config", $this->config);
    }

    /**
    * Init Routes
    * @access public
    * @return void
    */
    public function _initRoute() {
        $route = ngfw\Configuration::loadConfigFile(ROOT . DS . APPDIR . DS . 'Config' . DS . 'route.ini');
        ngfw\Route::addRoute($route['routes']['home']);
        ngfw\Route::addRoute($route['routes']['controller']);
        ngfw\Route::addRoute($route['routes']['keyvalue']);
        ngfw\Route::addRoute($route['routes']['keyvalueparams']);
        ngfw\Route::addRoute($route['routes']['singleParameter']);
        ngfw\Route::addRoute($route['routes']['singleParameterPages']);
    }


    /**
    * init DB (for demo, if needed in future)
    * @access public
    * @return void
    */
    public function _initDB() {
        if(!file_exists(ROOT . DS . APPDIR . DS . 'Config' . DS . 'database.php')):
            exit("<center>Please install application, <a href='install.php'>Installation</a></center>");
        endif;
        $dbconfig = include(ROOT . DS . APPDIR . DS . 'Config' . DS . 'database.php');
        $db = new ngfw\Database($dbconfig);
        ngfw\Registry::set("db", $db);
    }

    /**
     * init application objects
     * @access public
     * @return void
     */
    public function _initObjects(){
        $fileCache = new Filecache();
        $translate = new Translate();
        
        $requests = ngfw\Route::getRequests();
        if(isset($requests['lang']) and strlen($requests['lang']) == 2):
            $translate->setLanguage($requests['lang']);
        else:
            $lang = ngfw\Cookie::get("lang");
            if(isset($lang) and !empty($lang) and strlen($lang) == 2):
                $requests['lang'] = $lang;
            else:
                $requests['lang'] = $this->config['DefaultLanguage'];
            endif;
            $translate->setLanguage($requests['lang']);
        endif;
        $filename = "objects-".$requests['lang'];
        if($this->config["EnableCaching"]):
            $cacheData = $fileCache->get($filename, $this->config['CacheTime']);
        endif;
        if(!$this->config["EnableCaching"] OR !isset($cacheData) OR !$cacheData):
            $settings = new Settings();
            $ads = new Ads();
            $page = new Page();
            $cacheData['pages'] = $page->getTitlesAndSlugs();
            $cacheData['ads'] = $ads->getAllAds();
            $cacheData['availableLanguages'] = $translate->getAvailableLanguages();
            $cacheData['translation'] = $translate->getTranslation();
            $settings = $settings->getAllSettings();
            foreach($settings as $setting):
                $cacheData['settings'][$setting['SettingName']] = $setting['SettingValue'];
            endforeach;
            if($this->config['EnableCaching']):
                $fileCache->set($filename, $cacheData);
            endif;
        endif;
        ngfw\Registry::set("objects", $cacheData);
        ngfw\Registry::set("requestedLanguage", $translate->getRequestedLanguage());
    }

    /**
     * _initSearch
     * Fix the wrong search URLs, that was not rewritten by javascript
     * @access public
     * @return void
     */
    public function _initSearch(){
        $uri = $_SERVER['REQUEST_URI'];
        if(strpos($uri, "?search=") !== false):
            $url_params = explode("=", $uri);
            $searchTerm = end($url_params);
            $requests = ngfw\Route::getRequests();
            if(isset($requests['lang']) and strlen($requests['lang']) == 2):
                $lang = $requests['lang'];
            else:
                //set default language
                $translate = new Translate();
                $lang = $translate->getRequestedLanguage();
            endif;
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $lang . "/search/" . $searchTerm, "301");
            exit();
        endif;
    }

}