<?php
/**
 * PopularController
 * @copyright (c) 2013, Nick Gejadze
 */
class PopularController extends MovieDatabaseWrapperController {

    protected $page = 1;
    protected $pageLimit = 1000;

    /**
     * Popular list page
     * @access public
     * @return void
     */
    public function IndexAction() {
        // check if page is requested 
        if (isset($this->requests['key']) and $this->requests['key'] == "page" and 
            isset($this->requests['value']) and is_numeric($this->requests['value'])):
            $this->page = $this->requests['value'];
        endif;

        if ($this->page > $this->pageLimit):
            $this->page = $this->pageLimit;
        endif;

        if($this->config["EnableCaching"]):
            $tmdbData = $this->fileCache->get("popular-".$this->page."-".$this->view->requestedLanguage, $this->config['CacheTime']);
        endif;
        // If Cache is not found or expired
        if(!$this->config["EnableCaching"] OR !isset($tmdbData) OR !$tmdbData):
            // Get popular movies
            $tmdbData = $this->tmdb->getPopular($this->page);
            if($this->config["EnableCaching"]):
                $this->fileCache->set("popular-".$this->page."-".$this->view->requestedLanguage, $tmdbData);
            endif;
        endif;
        
        // Set Title
        $this->view->title = sprintf($this->view->translation['genrePageTitle'], $this->view->translation['popularMovies'], $this->page);
        // Set meta description
        $this->view->metaDescription = $this->view->translation['popularMoviesDescription'];
        // set meta keywords
        $this->view->metaKeywords = $this->view->translation['popularMoviesKeywords'];
        // get popular movies and pass to view
        $this->view->movies = $tmdbData;
        $this->view->pageLimit = $this->pageLimit;
        // pass page to view
        $this->view->page = $this->page;
        
    }

}