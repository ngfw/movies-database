<?php
/**
 * PopularpeopleController
 * @copyright (c) 2013, Nick Gejadze
 */
class PopularpeopleController extends MovieDatabaseWrapperController {

    protected $page = 1;
    protected $pageLimit = 1000;

    /**
     * Popular list page
     * @access public
     * @return void
     */
    public function IndexAction() {
        if (isset($this->requests['key']) and $this->requests['key'] == "page" and 
            isset($this->requests['value']) and is_numeric($this->requests['value'])):
            $this->page = $this->requests['value'];
        endif;
        if ($this->page > $this->pageLimit):
            $this->page = $this->pageLimit;
        endif;
        if($this->config["EnableCaching"]):
            $tmdbData = $this->fileCache->get("popularpeople-".$this->page."-".$this->view->requestedLanguage, $this->config['CacheTime']);
        endif;
        if(!$this->config["EnableCaching"] OR !isset($tmdbData) OR !$tmdbData):
            $tmdbData = $this->tmdb->getPopularPersons($this->page);
            if($this->config["EnableCaching"]):
                $this->fileCache->set("popularpeople-".$this->page."-".$this->view->requestedLanguage, $tmdbData);
            endif;
        endif;
        
        $this->view->title = sprintf($this->view->translation['genrePageTitle'], $this->view->translation['popularPeople'], $this->page);
        $this->view->metaDescription = $this->view->translation['popularPeopleDescription'];
        $this->view->metaKeywords = $this->view->translation['popularPeopleKeywords'];
        $this->view->person = $tmdbData;
        $this->view->pageLimit = $this->pageLimit;
        $this->view->page = $this->page;
    }

}