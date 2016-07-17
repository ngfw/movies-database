<?php


class PageController extends MovieDatabaseWrapperController{
	
	/**
     * Index Action
     * @access public
     * @return void
     */
    public function IndexAction() {
    	$page = new Page();
    	$selectedPage = $page->getPageWithSlug(preg_replace("/[^a-zA-Z0-9\-]/", "", $this->requests['query']));
    	if(!isset($selectedPage) or empty($selectedPage)):
    		ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/error/notfound", "404");
            exit();
    	endif;
    	$this->view->title = $selectedPage['Title'];
    	$this->view->page = $selectedPage;
    }

}