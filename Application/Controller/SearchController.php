<?php
/**
 * SearchController
 * @copyright (c) 2013, Nick Gejadze
 */
class SearchController extends MovieDatabaseWrapperController {

    protected $page = 1;

    /**
     * Seach Result page
     * @access public
     * @return void
     */
    public function IndexAction() {
        if (isset($this->requests['query']) and !empty($this->requests['query'])):
            
            // Check if page is requested
            if (isset($this->requests['page']) and is_numeric($this->requests['page'])):
                $this->page = $this->requests['page'];
            endif;
            
            $this->view->searchTerm = preg_replace("/[^a-zA-Z0-9 ]/", "", urldecode($this->requests['query']));
           
            $this->view->title = $this->view->translation['searchResultFor'].": ".urldecode($this->view->searchTerm);
            // Pass requested page to view
            $this->view->page = $this->page;
            // Perform Search and pass result to view            
            $this->view->search = $this->tmdb->search(urlencode($this->view->searchTerm), $this->page, $adult = "false");
            if($this->page == 1):
                $this->view->searchPerson = $this->tmdb->searchPerson(urlencode($this->view->searchTerm), $this->page, $adult = "false");
            endif;
            // if empty result trigger error
            if ($this->view->search['total_results'] == 0 AND (isset($this->view->searchPerson['total_results']) and $this->view->searchPerson['total_results'] == 0)):
                $this->view->error = "resultNotFound";
            endif;
        else:
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->view->requestedLanguage ."/surprise", "302");
            exit();
        endif;
    }

}