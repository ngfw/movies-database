<?php

class MovieController extends MovieDatabaseWrapperController {

    public function covtime($youtube_time, $format = "time") {
        preg_match_all('/(\d+)/',$youtube_time,$parts);

        // Put in zeros if we have less than 3 numbers.
        if (count($parts[0]) == 1) {
            array_unshift($parts[0], "0", "0");
        } elseif (count($parts[0]) == 2) {
            array_unshift($parts[0], "0");
        }

        $sec_init = $parts[0][2];
        $seconds = $sec_init%60;
        $seconds_overflow = floor($sec_init/60);

        $min_init = $parts[0][1] + $seconds_overflow;
        $minutes = ($min_init)%60;
        $minutes_overflow = floor(($min_init)/60);

        $hours = $parts[0][0] + $minutes_overflow;
        if($format == "time"):
            if($hours != 0):
                return $hours.':'.$minutes.':'.$seconds;
            else:
                return $minutes.':'.$seconds;
            endif;
        else:
            if($hours != 0):
                return $hours  * $minutes * $seconds;
            else:
                return $minutes * $seconds;
            endif;
        endif;
    }
    /**
     * Movies Page
     * @access public
     * @return void
     */
    public function IndexAction() {
        if(isset($this->requests['value'])):
            $pos = strpos($this->requests['value'], "?");
            if($pos !== false):
                $id = explode("?", $this->requests['value']);
                $this->requests['value'] = $id[0];
            endif;
        endif;
        if (isset($this->requests['key']) and is_numeric($this->requests['key']) and isset($this->requests['value']) and !empty($this->requests['value'])):
            if ($this->requests['key'] == "0000"):
                $this->requests['key'] = null;
            endif;
            $movieID = $this->requests['key'];
            if($this->config["EnableCaching"]):
                $movie = $this->fileCache->get("Movie-".$movieID."-".$this->view->requestedLanguage);
            endif;
            if(!$this->config["EnableCaching"] OR !isset($movie) OR !$movie):
                $movie = $this->tmdb->getMoviePage($movieID);
                if(!isset($movie['title']) and !isset($movie['release_date'])):
                    ngfw\Route::redirect(ngfw\Uri::baseUrl() . "en/error", "404");
                    exit();
                endif;

                if ($movie['adult'] == false):
                    if(urldecode(substr($this->requests['value'],0,-5)) !=  $movie['title']):
                        if(!isset($movie['release_date']) or empty($movie['release_date'])):
                            $movie['release_date'] = "0000";
                        endif;
                        ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->view->requestedLanguage . "/movie/" . $movieID ."/". urlencode($movie['title']."-".substr($movie['release_date'], 0, 4)), "301");
                        exit();
                    endif;
                    
                    if (isset($movie['overview']) and !empty($movie['overview'])):
                        $movie['metaDescription'] = mb_strimwidth(str_replace('"', "", $movie['overview']), 0, 247, '...', 'utf-8');
                        $keywords = "";
                        $keyword_arr = array_unique(explode(" ", $movie['metaDescription']));
                        foreach ($keyword_arr as $value):
                            $n = strlen($value);
                            if ($n > 4):
                                $strip_arr = array(",", ".", ";", ":", "...", '"');
                                $keyword = str_replace($strip_arr, "", $value);
                                $keywords.= $keyword . ",";
                            endif;
                        endforeach;
                        $movie['metaKeywords'] = substr($keywords, 0, -1);
                        
                        $movie['ogTags'] = array(
                            "title" => $movie['title'],
                            "type" => "movie",
                            "url" => ngfw\Uri::baseUrl() . $this->view->requestedLanguage . "/movie/" . $movieID ."/". urlencode($movie['title']."-".substr($movie['release_date'], 0, 4)),
                            "image" => $this->view->originalImage . $movie['poster_path'],
                            "site_name" => ngfw\Uri::baseUrl(),
                            "description" => $movie['metaDescription'],
                        );
                    endif;
                    foreach ($movie['casts']['crew'] as $key => $crew):
                        if ($crew["department"] == 'Directing' and $crew["job"] == 'Director'):
                            $movie["directors"][] = $movie['casts']['crew'][$key];
                        elseif ($crew["department"] == 'Writing' and $crew["job"] == 'Screenplay'):
                            $movie["writers"][] = $movie['casts']['crew'][$key];
                        endif;
                    endforeach;
                    $youtube = new ngfw\Youtube($this->objects["settings"]["Youtube_API_Key"]);
                    $youtube->setLimit(3);                    
                    $trailers = json_decode(json_encode($youtube->search($movie['original_title'] . " ". substr($movie['release_date'], 0, 4) . " trailer")), true);
                    $movie['trailer']['videoID'] = false;
                    if(isset($trailers) and is_array($trailers)):
                        foreach($trailers as $trailer):
                            if(!$movie['trailer']['videoID']):
                                $vdata = $youtube->getVideoInfo($trailer['id']["videoId"]);
                                if($this->covtime($vdatap['contentDetails']['duration']) < 600):
                                    $movie['trailer']['videoID'] = $trailer['id']['videoId'];
                                endif;
                            endif;
                        endforeach;
                    endif;
                    if(isset($movie['trailer']['videoID']) and !empty($movie['trailer']['videoID'])):
                        $movie['trailer']['video'] = "http://www.youtube.com/v/".$movie['trailer']['videoID']."?showinfo=0";
                    else:
                        $movie['trailer']['video'] = false;
                    endif;
                    if($this->config["EnableCaching"]):
                        $this->fileCache->set("Movie-".$movieID."-".$this->view->requestedLanguage, $movie);
                    endif;
                endif;
            endif;//cache

            // disable Erotic Category
            if(isset($this->config['DisableErotic']) and $this->config['DisableErotic'] == "1"):
                foreach($movie['genres'] as $genre):
                    if($genre['id'] == "2916"):
                        ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "en/error/notfound", "404");
                        exit();
                    endif;
                endforeach;
            endif;
            
            $this->view->movie = $movie;
            $this->view->title = $movie['title'];
            $this->view->canonical = ngfw\Uri::baseUrl() . $this->view->requestedLanguage . "/movie/" . $movieID ."/". urlencode($movie['title']."-".substr($movie['release_date'], 0, 4));
            $this->view->metaDescription = isset($movie['metaDescription']) ? $movie['metaDescription'] : $this->view->translation['defaultMetaDescription'];
            $this->view->metaKeywords = isset($movie['metaKeywords']) ? $movie['metaKeywords'] : $this->view->translation['defaultMetaKeywords'];
            $this->view->og = isset($movie['ogTags']) ? $movie['ogTags'] : false;
            $this->view->directors = isset($movie["directors"]) ? $movie['directors'] : false;
            $this->view->writers = isset($movie["writers"]) ? $movie['writers'] : false;
            if(isset($movie['trailer']['videoID']) and !empty($movie['trailer']['videoID'])
                and isset($movie['trailer']['video']) and !empty($movie['trailer']['video'])):
                $this->view->youtubeVideoID = $movie['trailer']['videoID'];
                $this->view->youtubeVideo = $movie['trailer']['video'];
            else:
                $this->view->youtubeVideo = $this->view->youtubeVideoID = false;
            endif;

        else:
            //error, Return 404 error and Redirect to error page
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "en/error/notfound", "404");
            exit();
        endif;
    }

}