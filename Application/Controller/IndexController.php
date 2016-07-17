<?php

/**
 * IndexController
 * @copyright (c) 2013, Nick Gejadze
 */
class IndexController extends MovieDatabaseWrapperController {

    /**
     * Home Page
     * @access public
     * @return void
     */
    public function IndexAction() {
        if($this->config["EnableCaching"]):
            $tmdbData = $this->fileCache->get("indexPageData-".$this->view->requestedLanguage, $this->config['CacheTime']);
        endif;
        // If Cache is not found or expired
        if(!$this->config["EnableCaching"] OR !$tmdbData):
            // Get Now playing movies
            $tmdbData['NowPlaying'] = $this->tmdb->getNowPlaying();
            // get upcomming movies
            $tmdbData['Upcoming'] = $this->tmdb->getUpcomingMovies();
            if($this->config["EnableCaching"]):
                $this->fileCache->set("indexPageData-".$this->view->requestedLanguage, $tmdbData);
            endif;
        endif;
        // assign to view
        $this->view->nowPlaying = $tmdbData['NowPlaying']['results'];
        $this->view->upcoming = $tmdbData['Upcoming']['results'];
    }

}