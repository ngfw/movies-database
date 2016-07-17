<?php
/**
 * GenreController
 * @copyright (c) 2013, Nick Gejadze
 */
class GenreController extends MovieDatabaseWrapperController {
    
    protected $page = 1;
    /**
     * Genres page
     * @acces public
     * @return void
     */
    public function IndexAction() {
        
        if (isset($this->requests['key']) and is_numeric($this->requests['key']) and isset($this->requests['value']) and !empty($this->requests['value'])):
            // check if page is requested
            if (isset($this->requests['key2']) and $this->requests['key2'] == "page" and isset($this->requests['value2']) and is_numeric($this->requests['value2'])):
                $this->page = $this->requests['value2'];
            endif;
             // disable Erotic Category
            if(isset($this->config['DisableErotic']) and $this->config['DisableErotic'] == "1"):
               if($this->requests['key'] == "2916"):
                    ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "en/error/notfound", "404");
                    exit();
                endif;
            endif;
            $filename = "genre-" . $this->requests['key'] . "-" . $this->page . "-" . $this->view->requestedLanguage;
            if ($this->config["EnableCaching"]):
                $tmdbData = $this->fileCache->get($filename, $this->config['CacheTime']);
            endif;
            if (!$this->config["EnableCaching"] OR !$tmdbData):
                $tmdbData = $this->tmdb->discover($this->page, $sort_by = "popularity.desc", $include_adult = "false", $year = false, $primary_release_year = false, $vote_count_gte = false, $vote_average_gte = false, $with_genres = $this->requests['key'], $release_date_gte = false, $release_date_lte = false, $certification_country = false, $certification_lte = false, $with_companies = false);
                if ($this->config["EnableCaching"]):
                    $this->fileCache->set($filename, $tmdbData);
                endif;
            endif;
            $this->view->movies = $tmdbData;
            
            foreach ($this->view->genres['genres'] as $genre):
                if ($genre['id'] == $this->requests['key']):
                    $this->view->selectedGenreName = $genre['name'];
                    $this->view->selectedGenreID = $genre['id'];
                endif;
            endforeach;
            // set page title
            $this->view->title = sprintf($this->view->translation['genrePageTitle'], $this->view->selectedGenreName, $this->page);
            // set page meda description
            $this->view->metaDescription = sprintf($this->view->translation['genrePageMetaDescription'], $this->view->selectedGenreName, $this->page);
            // set page meta keywords
            $this->view->metaKeywords = $this->view->selectedGenreName;
            // pass requested page to view
            $this->view->page = $this->page;
        else:
            //error, return 404 errpo and redirect to error page
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . "en/error/notfound", "404");
            exit();
        endif;
    }
}
