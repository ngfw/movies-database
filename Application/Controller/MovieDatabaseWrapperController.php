<?php
class MovieDatabaseWrapperController extends ngfw\Controller {
    
    protected $config;
    protected $translation;
    protected $requestedLanguage;
    protected $availableLanguages;
    protected $objects;
    protected $tmdb;
    protected $requests;
    /**
     * __construct method for class
     * Gets Configuration from registry and sets as object
     * @access public
     * @return void
     */
    public function init() {
        $this->SetupObjects();
    }
    /**
     * Setup objects
     * @access public
     * @return void
     */
    public function SetupObjects() {
        $this->config = ngfw\Registry::get('config');
        $this->objects = $this->view->objects = ngfw\Registry::get("objects");
        $this->requests = ngfw\Route::getRequests();
        $this->tmdb = new Themoviedb($this->objects["settings"]['TMPDB_Api_Key']);
        $requestedLanguage = ngfw\Registry::get('requestedLanguage');
        $this->tmdb->setLanguage($requestedLanguage);
        $this->fileCache = new Filecache();
        if ($this->config["EnableCaching"]):
            $tmdbConfig = $this->fileCache->get("tmdbConfig-" . $requestedLanguage, $this->config['CacheTime']);
        endif;
        if (!$this->config["EnableCaching"] OR !$tmdbConfig):
            $tmdbConfig['config'] = $this->tmdb->getConfiguration();
            $tmdbConfig['genres'] = $this->tmdb->getGenres();
            if ($this->config["EnableCaching"]):
                $this->fileCache->set("tmdbConfig-" . $requestedLanguage, $tmdbConfig);
            endif;
        endif;
        $this->view->tmdbConfig = $tmdbConfig['config'];
        if(isset($this->config['DisableErotic']) and $this->config['DisableErotic'] == "1"):
            foreach($tmdbConfig["genres"]["genres"] as $k => $genres):
                        if($genres['id'] == "2916"):
                            unset($tmdbConfig["genres"]["genres"][$k]);
                        endif;
            endforeach;
        endif;
        $this->view->genres = $tmdbConfig['genres'];
        $this->view->smallThumb = $tmdbConfig["config"]["images"]["base_url"] . $tmdbConfig["config"]["images"]["poster_sizes"][0];
        $this->view->mediumThumb = $tmdbConfig["config"]["images"]["base_url"] . $tmdbConfig["config"]["images"]["poster_sizes"][2];
        $this->view->originalImage = $tmdbConfig["config"]["images"]["base_url"] . end($tmdbConfig["config"]["images"]["poster_sizes"]);
        $this->view->profileThumbPath = $tmdbConfig["config"]["images"]["base_url"] . $tmdbConfig["config"]["images"]["profile_sizes"][0];
        $this->view->profileMediumPath = $tmdbConfig["config"]["images"]["base_url"] . $tmdbConfig["config"]["images"]["profile_sizes"][1];
        $this->view->profileOriginalImage = $tmdbConfig["config"]["images"]["base_url"] . end($tmdbConfig["config"]["images"]["profile_sizes"]);
        $this->view->translation = $this->objects['translation'];
        $this->view->requestedLanguage = $requestedLanguage;
        $this->view->availableLanguages = $this->objects['availableLanguages'];
    }
}
