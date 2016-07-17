<?php

/**
 * SortController
 * @copyright (c) 2013, Nick Gejadze
 */
class SortController extends MovieDatabaseWrapperController {
    
    protected $page = 1;

    /**
     * Sort page
     * @acces public
     * @return void
     */
    public function IndexAction() {

        // check if Genre ID and Name is Requested
        if (isset($this->requests['key']) and !empty($this->requests['key']) and isset($this->requests['value']) and is_numeric($this->requests['value'])):

            $this->page = 1;
            // check if page is requested 
            if (isset($this->requests['key2']) and $this->requests['key2'] == "page" and isset($this->requests['value2']) and is_numeric($this->requests['value2'])):
                $this->page = $this->requests['value2'];
            endif;
            $filename = "sort-".$this->requests['value']."-".$this->page."-".$this->view->requestedLanguage;
            if($this->config["EnableCaching"]):
                $tmdbData = $this->fileCache->get($filename, $this->config['CacheTime']);
            endif;
            if(!$this->config["EnableCaching"] OR !$tmdbData):
                $tmdbData = $this->tmdb->discover($this->page, $sort_by="popularity.desc", $include_adult="false",$year=$this->requests['value'],$primary_release_year=$this->requests['value'],$vote_count_gte=false,$vote_average_gte=false,$with_genres=false,$release_date_gte=false,$release_date_lte=false,$certification_country=false,$certification_lte=false,$with_companies=false);
                if($this->config["EnableCaching"]):
                    $this->fileCache->set($filename, $tmdbData);
                endif;
            endif;

            $this->view->title = sprintf($this->view->translation['genrePageTitle'], $this->requests['value'], $this->page);
            // set page meda description
            $this->view->metaDescription = sprintf($this->view->translation['genrePageMetaDescription'], $this->requests['value'], $this->page);
            // set page meta keywords
            $this->view->metaKeywords = $this->requests['value'];
            // pass requestd page to view
            $this->view->page = $this->page;
            $this->view->year = $this->requests['value'];
            $this->view->movies = $tmdbData;
        else:
            //error, return 404 errpo and redirect to error page
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . "error/notfound", "404");
            exit();
        endif;
    }

}