<?php

/**
 * SurpriseController
 * @copyright (c) 2013, Nick Gejadze
 */
class SurpriseController extends MovieDatabaseWrapperController {

    protected $page = 1;
    protected $pageLimit = 1000;

    /**
     * Popular list page
     * @access public
     * @return void
     */
    public function IndexAction() {
        
        $numbers = range(1, 50);
    	shuffle($numbers);
    	$pages = array_slice($numbers, 0, 4);
    	$movies = array();
    	foreach($pages as $page): 
    		if($this->config["EnableCaching"]):
	            $tmdbData = $this->fileCache->get("popular-".$page."-".$this->view->requestedLanguage, $this->config['CacheTime']);
	        endif;
	        // If Cache is not found or expired
	        if(!$this->config["EnableCaching"] OR !isset($tmdbData) OR !$tmdbData):
	            // Get popular movies
	            $tmdbData = $this->tmdb->getPopular($page);
	            if($this->config["EnableCaching"]):
	                $this->fileCache->set("popular-".$page."-".$this->view->requestedLanguage, $tmdbData);
	            endif;
	        endif;
	        foreach($tmdbData["results"] as $k => $v):
	        	if (!isset($v['poster_path']) or empty($v['poster_path'])):
	        		unset($tmdbData['results'][$k]);
	        	endif;
	        endforeach;
	        shuffle($tmdbData["results"]);
	        $PopularMovies = array_slice($tmdbData["results"], 0, 5);
	        //ngfw\App::debug($PopularMovies);
	        $movies = array_merge($movies, $PopularMovies);
	    endforeach;
	    
	    // Set Title
        $this->view->title = $this->view->translation['surprisePageTitle'];
        // Set meta description
        $this->view->metaDescription = $this->view->translation['surprisePageDescription'];
        // set meta keywords
        $this->view->metaKeywords = $this->view->translation['surprisePageKeywords'];

        $this->view->movies = $movies;
        
    }

}