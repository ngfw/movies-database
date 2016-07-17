<?php

/**
 * IndexController
 * @copyright (c) 2013, Nick Gejadze
 */
class FeedController extends MovieDatabaseWrapperController {

    public function IndexAction() {
        $this->view->enableLayout(false);
        $this->view->enableView(false);
        if(method_exists(__CLASS__, $this->requests['value'])):
            call_user_func(array(__CLASS__, $this->requests['value']));
        endif;

    }

    private function nowplaying(){
        if($this->config["EnableCaching"]):
            $tmdbData = $this->fileCache->get("indexPageData", $this->config['CacheTime']);
        endif;
        if(!$this->config["EnableCaching"] OR !$tmdbData):
            $tmdbData['NowPlaying'] = $this->tmdb->getNowPlaying();
            $tmdbData['Upcoming'] = $this->tmdb->getUpcomingMovies();
            if($this->config["EnableCaching"]):
                $this->fileCache->set("indexPageData", $tmdbData);
            endif;
        endif;
        $tmdbNowPlaying = $tmdbData['NowPlaying']['results'];
        
        $rssfeed = '<?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0"
                 xmlns:content="http://purl.org/rss/1.0/modules/content/"
                xmlns:wfw="http://wellformedweb.org/CommentAPI/"
                xmlns:dc="http://purl.org/dc/elements/1.1/"
                xmlns:atom="http://www.w3.org/2005/Atom"
                xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
                xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
                xmlns:georss="http://www.georss.org/georss" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:media="http://search.yahoo.com/mrss/">';
        $rssfeed .= '<channel>';
        $rssfeed .= '<title>'.$this->view->translation['nowPlaying'].'</title>';
        $rssfeed .= '<link>'.ngfw\Uri::baseUrl().'</link>';
        $rssfeed .= '<atom:link href="'.ngfw\Uri::baseUrl() . ngfw\Registry::get("requestedLanguage") .'/feed/rss/nowplaying" rel="self" type="application/rss+xml" />';
        $rssfeed .= '<description>'.$this->view->translation['nowPlaying'].' RSS feed from '.ngfw\Uri::baseUrl().'</description>';
        $rssfeed .= '<language>en-us</language>';
        $rssfeed .= '<copyright>Copyright (C) '.date("Y").' '.ngfw\Uri::baseUrl().'</copyright>';
        
        foreach($tmdbNowPlaying as $nowplaying):

            $rssfeed .= '<item>';
            $rssfeed .= '<title>' . htmlspecialchars($nowplaying['title'], ENT_QUOTES, "UTF-8") . '</title>';
            $rssfeed .= '<dc:creator>' . ngfw\Uri::baseUrl() .'</dc:creator>';
            $rssfeed .= '<media:content url="'.$this->view->originalImage . $nowplaying['backdrop_path'].'"
                     type="image/jpeg" medium="image" width="1208" height="679">
                  </media:content>';
            if($this->config["EnableCaching"]):
                $movie = $this->fileCache->get("Movie-".$nowplaying['id']);
            endif;
            if(!$this->config["EnableCaching"] OR !isset($movie) OR !$movie):
                $movie = $this->tmdb->getMoviePage($nowplaying['id']);
                if($this->config["EnableCaching"]):
                    $this->fileCache->set("Movie-".$nowplaying['id'], $movie);
                endif;
            endif;
            $info = $movie;
            $rssfeed .= '<description><![CDATA[<img align="left" hspace="5" src="'.$this->view->originalImage . $nowplaying['poster_path'].'"/>'. htmlspecialchars($info["overview"], ENT_QUOTES, "UTF-8") . ']]></description>';
            $rssfeed .= '<link>' . ngfw\Uri::baseUrl() .ngfw\Registry::get("requestedLanguage") . "/movie/" . $nowplaying['id'] ."/".urlencode($nowplaying['title']."-".substr($nowplaying['release_date'], 0, 4)) . '</link>';
            $today = strtotime('00:00:00');
            $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", $today) . '</pubDate>';
            $rssfeed .= '</item>';
        endforeach;
        $rssfeed .= '</channel>';
        $rssfeed .= '</rss>';
        header("Content-Type: text/xml; charset=UTF-8");
        echo $rssfeed;
    }

    private function upcoming(){
        if($this->config["EnableCaching"]):
            $tmdbData = $this->fileCache->get("indexPageData", $this->config['CacheTime']);
        endif;
        // If Cache is not found or expired
        if(!$this->config["EnableCaching"] OR !$tmdbData):
            $tmdbData['NowPlaying'] = $this->tmdb->getNowPlaying();
            $tmdbData['Upcoming'] = $this->tmdb->getUpcomingMovies();
            if($this->config["EnableCaching"]):
                $this->fileCache->set("indexPageData", $tmdbData);
            endif;
        endif;
        $tmdbUpcoming = $tmdbData['Upcoming']['results'];
        $rssfeed = '<?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0"
            xmlns:content="http://purl.org/rss/1.0/modules/content/"
            xmlns:wfw="http://wellformedweb.org/CommentAPI/"
            xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:atom="http://www.w3.org/2005/Atom"
            xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
            xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
            xmlns:georss="http://www.georss.org/georss" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:media="http://search.yahoo.com/mrss/"
            >';
        $rssfeed .= '<channel>';
        $rssfeed .= '<title>'.$this->view->translation['upcomingMovies'].'</title>';
        $rssfeed .= '<link>'.ngfw\Uri::baseUrl().'</link>';
        $rssfeed .= '<atom:link href="'.ngfw\Uri::baseUrl() . ngfw\Registry::get("requestedLanguage") .'/feed/rss/upcoming" rel="self" type="application/rss+xml" />';
        $rssfeed .= '<description>'.$this->view->translation['upcomingMovies'].' RSS feed from '.ngfw\Uri::baseUrl().'</description>';
        $rssfeed .= '<language>en-us</language>';
        $rssfeed .= '<copyright>Copyright (C) '.date("Y").' '.ngfw\Uri::baseUrl().'</copyright>';
        foreach($tmdbUpcoming as $upcoming):
            $rssfeed .= '<item>';
            $rssfeed .= '<title>' . htmlspecialchars($upcoming['title'], ENT_QUOTES, "UTF-8") . '</title>';
            $rssfeed .= '<dc:creator>' . ngfw\Uri::baseUrl() .'</dc:creator>';
            $rssfeed .= '<media:content url="'.$this->view->originalImage . $upcoming['backdrop_path'].'"
                     type="image/jpeg" medium="image" width="1208" height="679">
                  </media:content>';
            
            if($this->config["EnableCaching"]):
                $movie = $this->fileCache->get("Movie-".$upcoming['id']);
            endif;
            if(!$this->config["EnableCaching"] OR !isset($movie) OR !$movie):
                $movie = $this->tmdb->getMoviePage($upcoming['id']);
                if($this->config["EnableCaching"]):
                    $this->fileCache->set("Movie-".$upcoming['id'], $movie);
                endif;
            endif;
            $info = $movie;
            $rssfeed .= '<description><![CDATA[<img align="left" hspace="5" src="'.$this->view->originalImage . $upcoming['poster_path'].'"/>'. htmlspecialchars($info["overview"], ENT_QUOTES, "UTF-8") . ']]></description>';
            $rssfeed .= '<link>' . ngfw\Uri::baseUrl() .ngfw\Registry::get("requestedLanguage") . "/movie/" . $upcoming['id'] ."/".urlencode($upcoming['title']."-".substr($upcoming['release_date'], 0, 4)) . '</link>';
            $today = strtotime('00:00:00');
            $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", $today) . '</pubDate>';
            $rssfeed .= '</item>';
        endforeach;
        $rssfeed .= '</channel>';
        $rssfeed .= '</rss>';
        header("Content-Type: text/xml; charset=UTF-8");
        echo $rssfeed;
    }



}
