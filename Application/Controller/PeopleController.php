<?php

class PeopleController extends MovieDatabaseWrapperController {

    /**
     * Persons Page
     * @access public
     * @return void
     */
    public function IndexAction() {
        
        $pos = strpos($this->requests['value'], "?");
        if($pos !== false):
            $id = explode("?", $this->requests['value']);
            $this->requests['value'] = $id[0];
        endif;
              
        if (isset($this->requests['key']) and is_numeric($this->requests['key']) and 
            isset($this->requests['value']) and !empty($this->requests['value'])):
            $personID = $this->requests['key'];
            if($this->config["EnableCaching"]):
                $personData = $this->fileCache->get("Person-".$personID."-".$this->view->requestedLanguage);
            endif;
            if(!$this->config["EnableCaching"] OR !isset($personData) OR !$personData):
                $personData = $this->tmdb->getPersonPage($personID);
                
                if (isset($personData['biography']) and !empty($personData['biography'])):
                    $personData['metaDescription'] = mb_strimwidth(str_replace('"', "", $personData['biography']), 0, 247, '...', 'utf-8');
                    $keywords = "";
                    $keyword_arr = array_unique(explode(" ", $personData['metaDescription']));
                    foreach ($keyword_arr as $value):
                        $n = strlen($value);
                        if ($n > 4):
                            $strip_arr = array(",", ".", ";", ":", "...", '"');
                            $keyword = str_replace($strip_arr, "", $value);
                            $keywords.= $keyword . ",";
                        endif;
                    endforeach;
                    $personData['metaKeywords'] = substr($keywords, 0, -1);
                    $personData['ogTags'] = array(
                        "title" => $personData['name'],
                        "type" => "profile",
                        "url" => ngfw\Uri::baseUrl() . ngfw\Registry::get("requestedLanuage") . "/people/" . $this->requests['key'] . "/" . $this->requests['value'],
                        "image" => $this->view->originalImage . $personData['profile_path'],
                        "site_name" => ngfw\Uri::baseUrl(),
                        "description" => $personData['metaDescription'],
                    );
                endif;
                if($this->config["EnableCaching"]):
                    $this->fileCache->set("Person-".$personID."-".$this->view->requestedLanguage, $personData);
                endif;
            endif;
            if(isset($personData) and is_array($personData) and !$personData['adult']):
                $this->view->title = $personData['name'];
                $this->view->metaDescription = isset($personData['metaDescription']) ? $personData['metaDescription'] : $this->view->translation['defaultMetaDescription'];
                $this->view->metaKeywords = isset($personData['metaKeywords']) ? $personData['metaKeywords'] : $this->view->translation['defaultMetaKeywords'];
                $this->view->person = $personData;
                $this->view->og = isset($personData['ogTags']) ? $personData['ogTags'] : false;
            else:
                $this->view->person = false;
                $this->view->error = "personNotFound";
            endif;
        else:
            //error, Return 404 error and Redirect to error page
            ngfw\Route::redirect(ngfw\Uri::baseUrl() . $this->requestedLanguage . "/error/notfound", "404");
            exit();
        endif;
    }

}